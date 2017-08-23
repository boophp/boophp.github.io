<?php

/**
 * 简单的模板引擎
 *
 * @package
 * @subpackage
 */
class DCore_Template
{

    public static $rootdir = TEMPLATE_DIR;
    public static $compiledir = COMPILED_TEMPLATE_DIR;

    static function parsecall($matches)
    {
        return '<?php include DCore_Template::template("' . $matches[1] . '"); ?>';
    }

    private static function _contentParse($fileContent)
    {
        $fileContent = preg_replace('/^(\xef\xbb\xbf)/', '', $fileContent); //EFBBBF   
        $fileContent = preg_replace("/\<\!\-\-\s*\\\$\{(.+?)\}\s*\-\-\>/ies", "DCore_Template::replace('<?php \\1; ?>')", $fileContent);
        $fileContent = preg_replace("/\{(\\\$[a-zA-Z0-9_\[\]\\\ \-\'\,\%\*\/\.\(\)\>\'\"\$\x7f-\xff]+)\}/s", "<?php echo htmlspecialchars(\\1); ?>", $fileContent);

        $fileContent = preg_replace("/\\\$\{(.+?)\}/ies", "DCore_Template::replace('<?php echo \\1; ?>')", $fileContent);
        $fileContent = preg_replace("/\<\!\-\-\s*\{else\s*if\s+(.+?)\}\s*\-\-\>/ies", "DCore_Template::replace('<?php } else if(\\1) { ?>')", $fileContent);
        $fileContent = preg_replace("/\<\!\-\-\s*\{elif\s+(.+?)\}\s*\-\-\>/ies", "DCore_Template::replace('<?php } else if(\\1) { ?>')", $fileContent);
        $fileContent = preg_replace("/\<\!\-\-\s*\{else\}\s*\-\-\>/is", "<?php } else { ?>", $fileContent);

        for ($i = 0; $i < 5; ++$i)
        {
            $fileContent = preg_replace("/\<\!\-\-\s*\{loop\s+(\S+)\s+(\S+)\s+(\S+)\s*\}\s*\-\-\>(.+?)\<\!\-\-\s*\{\/loop\}\s*\-\-\>/ies", "DCore_Template::replace('<?php if(is_array(\\1)){foreach(\\1 AS \\2=>\\3) { ?>\\4<?php }}?>')", $fileContent);
            $fileContent = preg_replace("/\<\!\-\-\s*\{loop\s+(\S+)\s+(\S+)\s*\}\s*\-\-\>(.+?)\<\!\-\-\s*\{\/loop\}\s*\-\-\>/ies", "DCore_Template::replace('<?php if(is_array(\\1)){foreach(\\1 AS \\2) { ?>\\3<?php }}?>')", $fileContent);
            $fileContent = preg_replace("/\<\!\-\-\s*\{if\s+(.+?)\}\s*\-\-\>(.+?)\<\!\-\-\s*\{\/if\}\s*\-\-\>/ies", "DCore_Template::replace('<?php if(\\1){?>\\2<?php }?>')", $fileContent);
        }

        //Add for call <!--{include othertpl}-->
        $fileContent = preg_replace("#<!--\s*{\s*include\s+([^\{\}]+)\s*\}\s*-->#i", '<?php include DCore_Template::template("\\1");?>', $fileContent);
        return $fileContent;
    }

    private static function _parse($tFile, $cFile)
    {
        $fileContent = false;

        if (!($fileContent = file_get_contents($tFile)))
            return false;

        $fileContent = self::_contentParse($fileContent);

        //Add value namespace
        if (!file_put_contents($cFile, $fileContent))
            return false;

        return true;
    }

    static function replace($string)
    {
        return str_replace('\"', '"', $string);
    }

    private static function _template($tFile)
    {

        $tFileN = preg_replace('/\.html$/', '', $tFile);
        $tFile = self::$rootdir . $tFileN . '.html';
        //SAE Storage 对存储模板不靠谱
        //$fileContent = file_get_contents($tFile);
        //$fileContent = self::_contentParse($fileContent);
        //return $fileContent;

        $cFile = self::$compiledir . str_replace('/', '_', $tFileN) . '.php';

        if (false === file_exists($tFile))
        {
            die("Templace File [$tFile] Not Found!");
        }

        if (false === file_exists($cFile)
            || @filemtime($tFile) > @filemtime($cFile))
        {
            self::_parse($tFile, $cFile);
        }

        return $cFile;
    }

    function template($tFile)
    {
        global $CONFS;
        if (0 === strpos($tFile, 'adm'))
        {
            return self::_template($tFile);
        }
        if (isset($CONFS['skin']['template']))
        {
            $templatedir = self::$rootdir . DS . $CONFS['skin']['template'];
            $checkfile = $templatedir . DS . 'html_header.html';
            if (file_exists($checkfile))
            {
                return self::_template($CONFS['skin']['template'] . DS . $tFile);
            }
        }
        return self::_template($tFile);
    }

    static function render($tFile, $vs=array())
    {
        ob_start();
        foreach ($GLOBALS AS $_k => $_v)
        {
            ${$_k} = $_v;
        }
        foreach ($vs AS $_k => $_v)
        {
            ${$_k} = $_v;
        }
        //$typeselecthtml = 1;
        include self::template($tFile);
        return ob_get_clean();
        //return self::render_hook(ob_get_clean());
    }

    static function render_hook($c)
    {
        global $CONFS;
        $c = preg_replace('#href="/#i', 'href="' . WEB_ROOT . '/', $c);
        $c = preg_replace('#src="/#i', 'src="' . WEB_ROOT . '/', $c);
        $c = preg_replace('#action="/#i', 'action="' . WEB_ROOT . '/', $c);


        return $c;
    }

}

?>
