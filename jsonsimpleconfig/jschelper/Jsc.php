<?php

# * ********************************************************************* *
# *   Copyright (C) 2018 by xmz                                           *
# * ********************************************************************* *

/**
 * @author: Marcin Zelek (marcin.zelek@gmail.com)
 *          Copyright (C) xmz. All Rights Reserved.
 */

################################################################################
# Namespace                                                                    #
################################################################################

namespace xmz\jsonsimpleconfig;

################################################################################
# Include(s)                                                                   #
################################################################################

include(JSC_ROOT_PATH . '/jscparser/JscParser.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class __Jsc
{

    private $jscFile = null;
    private $jscData = null;

    public function __construct($jscFile)
    {
        $this->jscFile = $jscFile;
        $jscParser = new JscParser();
        $this->jscData = $jscParser->parseFile($jscFile);
    }

    public function get()
    {
        return $this->jscData;
    }

    public function getFile()
    {
        return $this->jscFile;
    }
}

class Jsc
{

    private static $instance;

    private function __construct($jscFile)
    {
        if (self::$instance == null) {
            self::$instance = new __Jsc($jscFile);
        }
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function get($jscFile, $refresh = false)
    {
        if (
            $refresh
            || (is_null(self::$instance))
            || (self::$instance->getFile() != $jscFile)
        ) {
            self::$instance = null;
            new Jsc($jscFile);
        }

        return self::$instance->get();
    }

    public static function gets($jscData)
    {
        $jscParser = new JscParser();

        return $jscParser->parseString($jscData);
    }
}

################################################################################
#                                End of file                                   #
################################################################################
