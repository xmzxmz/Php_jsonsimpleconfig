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

include(JSC_ROOT_PATH . '/jsccommon/JscComments.php');
include(JSC_ROOT_PATH . '/jscdata/JscData.php');

################################################################################
# Class(es)                                                                    #
################################################################################

class JscParser
{

    private $jscData = null;
    private $currentSection = null;
    private $currentSectionData = null;

    public function newSection($sectionName)
    {
        if (substr($sectionName, 0, 1) === '[') {
            $sectionName = ltrim($sectionName, '[');
        }
        if (substr($sectionName, -1, 1) === ']') {
            $sectionName = rtrim($sectionName, ']');
        }

        if ($sectionName !== JscSection::GLOBAL_SECTION_NAME) {
            $sectionName = trim($sectionName);
            if (substr($sectionName, 0, 1) !== '"') {
                $sectionName = '"' . $sectionName;
            }
            if (substr($sectionName, -1, 1) !== '"') {
                $sectionName = $sectionName . '"';
            }
        }

        $this->currentSection = $sectionName;
        $this->currentSectionData = '{';
    }

    public function endSection()
    {
        $this->currentSectionData .= '}';
        if (is_null($this->jscData)) {
            $this->jscData = new JscData();
        }

        $sectionName = $this->currentSection;
        $jsonString = $this->currentSectionData;

        $this->currentSectionData = $this->currentSection = null;
        $this->jscData->addSectionJsonString($sectionName, $jsonString);
    }

    public function parseLine($line)
    {
        $line = JscComments::stripComments($line);
        $line = trim($line);
        if (!empty($line)) {
            if (
                substr($line, 0, 1) === '['
                && substr($line, -1, 1) === ']'
            ) {
                if (
                    (!is_null($this->currentSection))
                    && (!is_null($this->currentSectionData))
                ) {
                    $this->endSection();
                }
                $this->newSection($line);
            } else {
                if (
                    (is_null($this->currentSection))
                    && (is_null($this->currentSectionData))
                ) {
                    $this->newSection(JscSection::GLOBAL_SECTION_NAME);
                }
                if ($this->currentSectionData !== '{') {
                    $this->currentSectionData .= ',';
                }
                $this->currentSectionData .= $line;
            }
        }
    }

    public function parseFile($jscFile, $parseLineByLine = false)
    {
        $this->jscData = null;
        try {
            if (is_readable($jscFile)) {
                if ($parseLineByLine) {
                    $fileHandle = fopen($jscFile, "r");
                    if ($fileHandle) {
                        while (($line = fgets($fileHandle)) !== false) {
                            $this->parseLine($line);
                        }
                        fclose($fileHandle);
                    }
                } else {
                    $jscText = JscComments::stripComments(file_get_contents($jscFile));
                    $lines = explode(PHP_EOL, $jscText);
                    foreach ($lines as $line) {
                        $this->parseLine($line);
                    }
                }
                $this->endSection();
            }
        } catch (Exception $e) {
            $this->jscData = null;
        } finally {
            return $this->jscData;
        }
    }

    public function parseString($jscString)
    {
        $this->jscData = null;
        try {
            $jscText = JscComments::stripComments($jscString);
            $lines = explode(PHP_EOL, $jscText);
            foreach ($lines as $line) {
                $this->parseLine($line);
            }
            $this->endSection();
        } catch (Exception $e) {
            $this->jscData = null;
        } finally {
            return $this->jscData;
        }
    }
}

################################################################################
#                                End of file                                   #
################################################################################
