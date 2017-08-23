<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Converter
{

    function is_safe($fileurl)
    {
        $handle = fopen($fileurl, 'rb');
        $fileSize = filesize($fileurl);
        fseek($handle, 0);
        if ($fileSize > 512)
        { // 取头和尾
            $hexCode = bin2hex(fread($handle, 512));
            fseek($handle, $fileSize - 512);
            $hexCode .= bin2hex(fread($handle, 512));
        } else
        { // 取全部
            $hexCode = bin2hex(fread($handle, $fileSize));
        }
        fclose($handle);
        /* 匹配16进制中的 <% ( ) %> */
        /* 匹配16进制中的 <? ( ) ?> */
        /* 匹配16进制中的 <script | /script> 大小写亦可 */
        //匹配表示有木马
        return !preg_match("/(3c25.*?28.*?29.*?253e)|(3c3f.*?28.*?29.*?3f3e)|(3C534352495054)|(2F5343524950543E)|(3C736372697074)|(2F7363726970743E)/is", $hexCode);
    }

    static $show_files = "find /var/data/ppt |grep jpg";

    function convert($func)
    {
        $start = time();
        $cmd = `$show_files`;
        exec($cmd, $files);
        foreach ($files as $key => $filename)
        {
            $outname = "/home/wuxing/tmp/$key.jpg";
            self::$func($filename, $outname);
        }
        $end = time();
        echo "$func: " . ($end - $start) . "s\n";
    }

    static function convert_resize($filename, $outname)
    {
        $cmd = "/usr/bin/convert $filename -resize 200x200 $outname";
        exec($cmd);
        echo $cmd . "\n";
    }

    static function convert_thumbnail($filename, $outname)
    {
        $cmd = "/usr/bin/convert $filename -thumbnail 200x200 $outname";
        exec($cmd);
        echo $cmd . "\n";
    }

    static function convert_hint($filename, $outname)
    {
        $cmd = "/usr/bin/convert -define jpeg:size=200x200 $filename -thumbnail 200x200 $outname";
        exec($cmd);
        echo $cmd . "\n";
    }

    static function imagick_resize($filename, $outname)
    {
        $thumbnail = new Imagick($filename);
        $thumbnail->resizeImage(200, 200, imagick::FILTER_LANCZOS, 1);
        $thumbnail->writeImage($outname);
        unset($thumbnail);
        echo $filename . "\n";
    }

    static function imagick_scale($filename, $outname)
    {
        $thumbnail = new Imagick($filename);
        $thumbnail->scaleImage(200, 200);
        $thumbnail->writeImage($outname);
        unset($thumbnail);
        echo $filename . "\n";
    }

    static function gmagick_resize($filename, $outname)
    {
        $thumbnail = new Gmagick($filename);
        $thumbnail->scaleImage(200, 200);
        $thumbnail->writeImage($outname);
        unset($thumbnail);
        echo $filename . "\n";
    }

    static function gmagick_sacle($filenmame, $outname)
    {
        $thumbnail = new Gmagick($filename);
        $thumbnail->scaleImage(200, 200);
        $thumbnail->writeImage($outname);
        unset($thumbnail);
        echo $filename . "\n";
    }

    static function gd_scale($filename, $outname)
    {
        $size = getimagesize($filename);
        $thumb_width = "200";
        $thumb_height = (int) (( $thumb_width / $size[0] ) * $size[1] );
        $thumbnail = ImageCreateTrueColor($thumb_width, $thumb_height);
        $src_img = ImageCreateFromJPEG($filename);
        ImageCopyResampled($thumbnail, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $size[0], $size[1]);
        ImageJPEG($thumbnail, $outname);
        ImageDestroy($thumbnail);
        echo $filename . "\n";
    }

    public function main()
    {

        $this->convert("convert_resize");
        $this->convert("convert_thumbnail");
        $this->convert("convert_hint");

        $this->convert("imagick_resize");
        $this->convert("imagick_scale");
        $this->convert("gmagick_resize");
        $this->convert("gmagick_scale");
        $this->convert("gd_scale");
    }

}

$convert = new Converter();
$convert->main();

