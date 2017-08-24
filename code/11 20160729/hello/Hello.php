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
// Generated from file `Hello.ice'
//
// Warning: do not edit this file.
//
// </auto-generated>
//


if(!interface_exists('Demo_Hello'))
{
    interface Demo_Hello
    {
        public function sayHello($delay);
        public function shutdown();
        public function printHello($hello);
    }

    class Demo_HelloPrxHelper
    {
        public static function checkedCast($proxy, $facetOrCtx=null, $ctx=null)
        {
            return $proxy->ice_checkedCast('::Demo::Hello', $facetOrCtx, $ctx);
        }

        public static function uncheckedCast($proxy, $facet=null)
        {
            return $proxy->ice_uncheckedCast('::Demo::Hello', $facet);
        }
    }

    $Demo__t_Hello = IcePHP_defineClass('::Demo::Hello', 'Demo_Hello', -1, true, false, $Ice__t_Object, null, null);

    $Demo__t_HelloPrx = IcePHP_defineProxy($Demo__t_Hello);

    IcePHP_defineOperation($Demo__t_Hello, 'sayHello', 2, 2, 0, array(array($IcePHP__t_int, false, 0)), null, null, null);
    IcePHP_defineOperation($Demo__t_Hello, 'shutdown', 0, 0, 0, null, null, null, null);
    IcePHP_defineOperation($Demo__t_Hello, 'printHello', 0, 0, 0, array(array($IcePHP__t_string, false, 0)), null, null, null);
}
?>