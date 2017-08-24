<?php
// **********************************************************************
//
// Copyright (c) 2003-2013 ZeroC, Inc. All rights reserved.
//
// This copy of Ice is licensed to you under the terms described in the
// ICE_LICENSE file included in this distribution.
//
// **********************************************************************
//
// Ice version 3.5.1
//
// <auto-generated>
//
// Generated from file `DBPQuery.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//

require_once 'common.php';

if(!class_exists('Space_DBAError'))
{
    class Space_DBAError extends Space_RuntimeError
    {
        public function __construct($file='', $line=0, $code=0, $reason='')
        {
            parent::__construct($file, $line, $code, $reason);
        }

        public function ice_name()
        {
            return 'Space::DBAError';
        }

        public function __toString()
        {
            global $Space__t_DBAError;
            return IcePHP_stringifyException($this, $Space__t_DBAError);
        }
    }

    $Space__t_DBAError = IcePHP_defineException('::Space::DBAError', 'Space_DBAError', false, $Space__t_RuntimeError, null);
}

if(!class_exists('Space_QResult'))
{
    class Space_QResult
    {
        public function __construct($affectedRowNumber=0, $insertId=0, $fields=null, $rows=null)
        {
            $this->affectedRowNumber = $affectedRowNumber;
            $this->insertId = $insertId;
            $this->fields = $fields;
            $this->rows = $rows;
        }

        public function __toString()
        {
            global $Space__t_QResult;
            return IcePHP_stringify($this, $Space__t_QResult);
        }

        public $affectedRowNumber;
        public $insertId;
        public $fields;
        public $rows;
    }

    $Space__t_QResult = IcePHP_defineStruct('::Space::QResult', 'Space_QResult', array(
        array('affectedRowNumber', $IcePHP__t_long), 
        array('insertId', $IcePHP__t_long), 
        array('fields', $Space__t_StringSeq), 
        array('rows', $Space__t_StringSeqSeq)));
}

if(!isset($Space__t_QResultSeq))
{
    $Space__t_QResultSeq = IcePHP_defineSequence('::Space::QResultSeq', $Space__t_QResult);
}

if(!interface_exists('Space_DBPQuery'))
{
    interface Space_DBPQuery
    {
        public function sQuery($kind, $hintId, $sql);
        public function allServers();
    }

    class Space_DBPQueryPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::Space::DBPQuery', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::Space::DBPQuery', $facet);
        }
    }

    $Space__t_DBPQuery = IcePHP_defineClass('::Space::DBPQuery', 'Space_DBPQuery', -1, true, false, $Ice__t_Object, null, null);

    $Space__t_DBPQueryPrx = IcePHP_defineProxy($Space__t_DBPQuery);

    IcePHP_defineOperation($Space__t_DBPQuery, 'sQuery', 0, 0, 0, array(array($IcePHP__t_string, false, 0), array($IcePHP__t_long, false, 0), array($IcePHP__t_string, false, 0)), null, array($Space__t_QResult, false, 0), array($Space__t_DBAError));
    IcePHP_defineOperation($Space__t_DBPQuery, 'allServers', 0, 0, 0, null, null, array($IcePHP__t_string, false, 0), array($Space__t_DBAError));
}
?>