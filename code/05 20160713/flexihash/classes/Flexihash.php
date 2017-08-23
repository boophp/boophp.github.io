<?php

require_once 'Flexihash/Exception.php';

/**
 * A simple consistent hashing implementation with pluggable hash algorithms.
 *
 * @author Paul Annesley
 * @package Flexihash
 * @licence http://www.opensource.org/licenses/mit-license.php
 */
class Flexihash
{

    /**
     * The number of positions to hash each target to.
     * 每一个缓存节点，虚拟出这么多位置出来
     * @var int
     */
    private $_replicas = 64;

    /**
     * The hash algorithm, encapsulated in a Flexihash_Hasher implementation.
     * 哈希算法
     * @var object Flexihash_Hasher
     * 
     */
    private $_hasher;

    /**
     * Internal counter for current number of targets.
     * 
     * @var int
     */
    private $_targetCount = 0;

    /**
     * Internal map of positions (hash outputs) to targets
     * 
     * @var array { position => target, ... }
     */
    private $_positionToTarget = array();

    /**
     * Internal map of targets to lists of positions that target is hashed to.
     * @var array { target => [ position, position, ... ], ... }
     */
    private $_targetToPositions = array();

    /**
     * Whether the internal map of positions to targets is already sorted.
     * @var boolean
     */
    private $_positionToTargetSorted = false;

    /**
     * Constructor
     * @param object $hasher Flexihash_Hasher
     * @param int $replicas Amount of positions to hash each target to.
     */
    public function __construct(Flexihash_Hasher $hasher = null, $replicas = null)
    {
        include_once 'Flexihash/Crc32Hasher.php';
        include_once 'Flexihash/Md5Hasher.php';
        $this->_hasher = $hasher ? $hasher : new Flexihash_Md5Hasher(); //new Flexihash_Md5Hasher();
        if (!empty($replicas))
            $this->_replicas = $replicas;
    }

    /**
     * Add a target.
     * 添加服务器
     * @param string $target
     * @param float $weight
     * @chainable
     */
    public function addTarget($target, $weight = 1)
    {
        if (isset($this->_targetToPositions[$target]))
        {
            throw new Flexihash_Exception("Target '$target' already exists.");
        }

        $this->_targetToPositions[$target] = array();
        //某台机器性能好，可以放更多的位置 
        // hash the target into multiple positions
        //64*10 = 640
        //
        for ($i = 0; $i < round($this->_replicas * $weight); $i++)
        {
            //用来通过Hash位置寻找位置 
            $position = $this->_hasher->hash($target . $i);
          //  echo $position . " AT AddTarget!\n";
            //正反保存，某个target，虚拟出哪些位置 。
            $this->_positionToTarget[$position] = $target; // lookup
            $this->_targetToPositions[$target] [] = $position; // target removal
        }

        $this->_positionToTargetSorted = false;
        $this->_targetCount++;

        return $this;
    }

    /**
     * Add a list of targets.
     * @param array $targets
     * @param float $weight
     * @chainable
     */
    public function addTargets($targets, $weight = 1)
    {
        foreach ($targets as $target)
        {
            $this->addTarget($target, $weight);
        }

        return $this;
    }

    /**
     * Remove a target.
     * @param string $target
     * @chainable
     */
    public function removeTarget($target)
    {
        //下线了，把所有内容都删除
        if (!isset($this->_targetToPositions[$target]))
        {
            throw new Flexihash_Exception("Target '$target' does not exist.");
        }

        foreach ($this->_targetToPositions[$target] as $position)
        {
            unset($this->_positionToTarget[$position]);
        }

        unset($this->_targetToPositions[$target]);

        $this->_targetCount--;

        return $this;
    }

    /**
     * A list of all potential targets
     * @return array
     */
    public function getAllTargets()
    {
        return array_keys($this->_targetToPositions);
    }

    /**
     * Looks up the target for the given resource.
     * @param string $resource
     * @return string
     */
    public function lookup($resource)
    {
        $targets = $this->lookupList($resource, 1);
        if (empty($targets))
            throw new Flexihash_Exception('No targets exist');
        return $targets[0];
    }

    /**
     * Get a list of targets for the resource, in order of precedence.
     * Up to $requestedCount targets are returned, less if there are fewer in total.
     *
     * @param string $resource
     * @param int $requestedCount The length of the list to return
     * @return array List of targets
     */
    public function lookupList($resource, $requestedCount)
    {
        if (!$requestedCount)
            throw new Flexihash_Exception('Invalid count requested');

        // handle no targets
        if (empty($this->_positionToTarget))
            return array();

        // optimize single target
        if ($this->_targetCount == 1)
            return array_unique(array_values($this->_positionToTarget));

        // hash resource to a position
        //先找到position
        $resourcePosition = $this->_hasher->hash($resource);
     //   echo $resourcePosition . " AT LookupList!\n";
        $results = array();
        $collect = false;
        //排序很关键
        //先排好序，否则查询比较麻烦
        $this->_sortPositionTargets();
        
        // search values above the resourcePosition
        foreach ($this->_positionToTarget as $key => $value)
        {
            // start collecting targets after passing resource position
            //只找第一个过了的位置 (1,1000万）=》500万
            if (!$collect && $key > $resourcePosition)
            {
                $collect = true;
            }

            // only collect the first instance of any target
            //如果要多个，还需要继续查找，连续取多个
            if ($collect && !in_array($value, $results))
            {
                $results [] = $value;
            }

            // return when enough results, or list exhausted
            //取满了之后返回
            if (count($results) == $requestedCount || count($results) == $this->_targetCount)
            {
                return $results;
            }
        }

        // loop to start - search values below the resourcePosition
        //如果大于的没有找够，继续从头开始找够
        //只找前面这几个来补足
        foreach ($this->_positionToTarget as $key => $value)
        {
            if (!in_array($value, $results))
            {
                $results [] = $value;
            }

            // return when enough results, or list exhausted
            if (count($results) == $requestedCount || count($results) == $this->_targetCount)
            {
                return $results;
            }
        }

        // return results after iterating through both "parts"
        return $results;
    }

    public function __toString()
    {
        return sprintf(
                '%s{targets:[%s]}', get_class($this), implode(',', $this->getAllTargets())
        );
    }

    // ----------------------------------------
    // private methods

    /**
     * Sorts the internal mapping (positions to targets) by position
     */
    private function _sortPositionTargets()
    {
        // sort by key (position) if not already
        if (!$this->_positionToTargetSorted)
        {
            ksort($this->_positionToTarget, SORT_REGULAR);
            $this->_positionToTargetSorted = true;
        }
    }

}
