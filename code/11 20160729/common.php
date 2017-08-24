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
// Generated from file `common.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//


if(!isset($Space__t_IntSeq))
{
    $Space__t_IntSeq = IcePHP_defineSequence('::Space::IntSeq', $IcePHP__t_int);
}

if(!isset($Space__t_LongSeq))
{
    $Space__t_LongSeq = IcePHP_defineSequence('::Space::LongSeq', $IcePHP__t_long);
}

if(!isset($Space__t_StringSeq))
{
    $Space__t_StringSeq = IcePHP_defineSequence('::Space::StringSeq', $IcePHP__t_string);
}

if(!isset($Space__t_StringSeqSeq))
{
    $Space__t_StringSeqSeq = IcePHP_defineSequence('::Space::StringSeqSeq', $Space__t_StringSeq);
}

if(!isset($Space__t_Blob))
{
    $Space__t_Blob = IcePHP_defineSequence('::Space::Blob', $IcePHP__t_byte);
}

if(!isset($Space__t_BlobSeq))
{
    $Space__t_BlobSeq = IcePHP_defineSequence('::Space::BlobSeq', $Space__t_Blob);
}

if(!isset($Space__t_StringMap))
{
    $Space__t_StringMap = IcePHP_defineDictionary('::Space::StringMap', $IcePHP__t_string, $IcePHP__t_string);
}

if(!class_exists('Space_RuntimeError'))
{
    class Space_RuntimeError extends Ice_UserException
    {
        public function __construct($file='', $line=0, $code=0, $reason='')
        {
            $this->file = $file;
            $this->line = $line;
            $this->code = $code;
            $this->reason = $reason;
        }

        public function ice_name()
        {
            return 'Space::RuntimeError';
        }

        public function __toString()
        {
            global $Space__t_RuntimeError;
            return IcePHP_stringifyException($this, $Space__t_RuntimeError);
        }

        public $file;
        public $line;
        public $code;
        public $reason;
    }

    $Space__t_RuntimeError = IcePHP_defineException('::Space::RuntimeError', 'Space_RuntimeError', false, null, array(
        array('file', $IcePHP__t_string, false, 0),
        array('line', $IcePHP__t_int, false, 0),
        array('code', $IcePHP__t_int, false, 0),
        array('reason', $IcePHP__t_string, false, 0)));
}
?>