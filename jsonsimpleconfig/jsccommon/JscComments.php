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
# Class(es)                                                                    #
################################################################################

class JscComments
{

    public static function replacer($matches)
    {
        if (is_array($matches) && (count($matches) > 1)) {
            return $matches[1];
        } else {
            return '';
        }
    }

    public static function stripComments($text)
    {
        return preg_replace_callback(
            '/#.*?$|;.*?$|\/\/.*?$|\/\*[\s\S]*?\*\/|("(\\.|[^"])*")/m',
            function ($matches) {
                return JscComments::replacer($matches);
            },
            $text
        );
    }

    public static function stripCommentsFile($inFilePath, $outFilePath)
    {
        if (is_readable($inFilePath)) {
            file_put_contents($outFilePath, self::stripComments(file_get_contents($inFilePath)));
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
