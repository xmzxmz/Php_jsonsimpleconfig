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

include('JscSection.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class JscData
{

    private $jsc = [];

    public function addSectionData($sectionName, $sectionData)
    {
        if (substr($sectionName, 0, 1) === '[') {
            $sectionName = ltrim($sectionName, '[');
        }
        if (substr($sectionName, -1, 1) === ']') {
            $sectionName = rtrim($sectionName, ']');
        }

        foreach ($sectionData as $key => $value) {
            if (empty($this->jsc[$sectionName])) {
                $this->jsc[$sectionName] = [];
            }
            $this->jsc[$sectionName][$key] = $value;
        }
    }

    public function addSectionJsonString($sectionName, $sectionJsonString)
    {
        if (!empty($sectionJsonString)) {
            $sectionData = json_decode($sectionJsonString, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->addSectionData($sectionName, $sectionData);
            }
        }
    }

    public function getValue($sectionName, $key, $default = null)
    {
        $value = $default;
        $sectionData = $this->getSection($sectionName);
        if (isset($sectionData[$key])) {
            $value = $sectionData[$key];
        }

        return $value;
    }

    public function getSection($sectionName = null)
    {
        if (
            (empty($sectionName))
            || ($sectionName === JscSection::GLOBAL_SECTION_NAME)
        ) {
            $sectionName = JscSection::GLOBAL_SECTION_NAME;
        } elseif ($sectionName !== JscSection::GLOBAL_SECTION_NAME) {
            $sectionName = trim($sectionName);
            if (substr($sectionName, 0, 1) !== '"') {
                $sectionName = '"' . $sectionName;
            }
            if (substr($sectionName, -1, 1) !== '"') {
                $sectionName = $sectionName . '"';
            }
        }

        if (!empty($this->jsc[$sectionName])) {
            return $this->jsc[$sectionName];
        }

        return null;
    }

    public function getSectionNames()
    {
        if (!empty($this->jsc)) {
            return array_keys($this->jsc);
        }

        return null;
    }

    public function merge($jscData)
    {
        if (!is_null($jscData) && ($jscData instanceof self)) {
            $sectionNames = $jscData->getSectionNames();
            if (!empty($sectionNames)) {
                foreach ($sectionNames as $sectionName) {
                    $sectionData = $jscData->getSection($sectionName);
                    foreach ($sectionData as $key => $value) {
                        if (empty($this->jsc[$sectionName])) {
                            $this->jsc[$sectionName] = [];
                        }
                        $this->jsc[$sectionName][$key] = $value;
                    }
                }
            }
        }
    }

    public function toString()
    {
        $jscDataString = PHP_EOL;
        $sectionNames = $this->getSectionNames();
        if (!empty($sectionNames)) {
            foreach ($sectionNames as $sectionName) {
                $sectionPrint = "* Section";
                if ($sectionName === JscSection::GLOBAL_SECTION_NAME) {
                    $sectionPrint .= " (Global):";
                } else {
                    $sectionPrint .= sprintf(" - %s:", ($sectionName));
                }
                $jscDataString .= $sectionPrint . PHP_EOL;
                $sectionData = $this->getSection($sectionName);
                if (is_array($sectionData)) {
                    foreach ($sectionData as $key => $value) {
                        $jscDataString .= '*** [' . strval($key) . '] : [' . ((is_array($value)) ? json_encode($value)
                                : $value) . ']' . PHP_EOL;
                    }
                }
            }
        }

        return $jscDataString;
    }

    public function toStringHtml()
    {
        return str_replace(PHP_EOL, "<br>" . PHP_EOL, $this->toString());
    }

    public function print()
    {
        echo($this->toString());
    }

    public function printHtml()
    {
        echo($this->toStringHtml());
    }
}

################################################################################
#                                End of file                                   #
################################################################################
