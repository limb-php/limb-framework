<?php

namespace limb\web_app\src\macro;

use limb\macro\src\compiler\lmbMacroTag;
use limb\core\src\lmbEnv;
use limb\fs\src\lmbFs;
use limb\toolkit\src\lmbToolkit;
use limb\macro\src\lmbMacroException;

/**
 * @tag file:version
 * @req_attributes src
 * @forbid_end_tag
 */
class lmbFileVersionMacroTag extends lmbMacroTag
{
    protected function _generateContent($code_writer)
    {
        $url = $this->getVersionUrl();
        $type = $this->has('type') ? $this->get('type') : 'echo';

        if ($this->has('gzip_static_dir')) {
            $zlevel = $this->has('gzip_level') ? $this->get('gzip_level') : 3;
            $file_source = lmbFs::normalizePath($this->get('gzip_static_dir'), lmbFs::UNIX) . '/' . str_replace('/', '-', lmbFs::normalizePath($this->getFileUrl(), lmbFs::UNIX));
            lmbFs::cp($this->getFilePath(), $this->getRootDir() . '/' . $file_source);
            $file_gz = $file_source . '.gz';
            lmbFs::safeWrite($this->getRootDir() . '/' . $file_gz, gzencode(file_get_contents($this->getFilePath()), $zlevel, FORCE_DEFLATE));
            $url = $this->addVersion($file_source);
        }

        switch ($type) {
            case 'echo':
                if ($this->has('to_var'))
                    $code_writer->writePhp($this->get('to_var') . ' = \'' . addslashes($url) . '\';');
                else
                    $code_writer->writeHTML(htmlspecialchars($url, 3));
                break;
            case 'js':
                $code_writer->writeHTML('<script type="text/javascript" src="' . htmlspecialchars($url, 3) . '" ></script>');
                break;
            case 'css':
                $code_writer->writeHTML('<link rel="stylesheet" type="text/css" href="' . htmlspecialchars($url, 3) . '" />');
                break;
            default:
                throw new lmbMacroException('Unknown type ' . $type);
        }
    }

    function getVersionUrl()
    {
        return $this->addVersion($this->getFileUrl());
    }

    function addVersion($url)
    {
        return lmbToolkit::instance()->addVersionToUrl($url, $this->getBool('safe', false));
    }

    function getFileUrl()
    {
        return lmbFs::normalizePath($this->get('src'), lmbFs::UNIX);
    }

    function getFilePath()
    {
        return $this->getRootDir() . '/' . ltrim($this->getFileUrl(), '/');
    }

    function getRootDir()
    {
        if (!$root_dir = lmbEnv::get('LIMB_DOCUMENT_ROOT', false))
            throw new lmbMacroException('Not set require env LIMB_DOCUMENT_ROOT!');
        return $root_dir;
    }
}
