<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\net\src;
/**
 * class lmbMimeType.
 *
 * @package net
 * @version $Id: lmbMimeType.php 7886 2009-04-17 07:39:04Z
 */
class lmbMimeType
{
    static protected $mime_types = array(
        'video/avi' => 'avi',
        'image/webp' => 'webp',
        'application/x-flash-video' => 'flv',
        'audio/x-aiff' => 'aif',
        'audio/x-aiff' => 'aifc',
        'audio/x-aiff' => 'aiff',
        'image/bmp' => 'bmp',
        'application/msword' => 'doc',
        'video/x-flv' => 'flv',
        'image/gif' => 'gif',
        'text/html' => 'html',
        'image/pjpeg' => 'jpeg',
        'image/jpeg' => 'jpg',
        'text/javascript' => 'js',
        'video/mpeg' => 'mpeg',
        'audio/mpeg' => 'mp3',
        'audio/mp3' => 'mp3',
        'video/mpeg' => 'mpg',
        'message/rfc822' => 'msg',
        'application/pdf' => 'pdf',
        'application/x-pdf' => 'pdf',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'application/vnd.ms-powerpoint' => 'ppt',
        'image/psd' => 'psd',
        'text/rtf' => 'rtf',
        'application/x-shockwave-flash' => 'swf',
        'text/plain' => 'txt',
        'audio/wav' => 'wav',
        'audio/x-wav' => 'wav',
        'video/ogg' => 'ogg',
        'application/vnd.ms-excel' => 'xls',
        'application/x-rar-compressed' => 'rar',
        'application/rar' => 'rar',
        'application/x-zip-compressed' => 'zip',
        'application/zip' => 'zip',
        'application/vnd.ms-word.document.macroEnabled.12' => 'docm',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-word.template.macroEnabled.12' => 'dotm',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'dotx',
        'application/vnd.ms-powerpoint.template.macroEnabled.12' => 'potm',
        'application/vnd.openxmlformats-officedocument.presentationml.template' => 'potx',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12' => 'ppam',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => 'ppsm',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => 'ppsx',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => 'pptm',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.ms-excel.addin.macroEnabled.12' => 'xlam',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => 'xlsb',
        'application/vnd.ms-excel.sheet.macroEnabled.12' => 'xlsm',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-excel.template.macroEnabled.12' => 'xltm',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'xltx',
        'image/svg+xml' => 'svg',
        'image/x-icon' => 'ico',
        'application/x-icon' => 'ico',
        'image/vnd.microsoft.icon' => 'ico',
    );

    static function getExtension($mime_type)
    {
        $mime_type = strtolower($mime_type);
        return isset(self::$mime_types[$mime_type])
            ? self::$mime_types[$mime_type]
            : null;
    }

    static function getMimeContentType($filename)
    {
        $finfo = finfo_open(FILEINFO_MIME);
        $mimetype = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mimetype;
    }

    static function getMimeType($extension)
    {
        $extension = ltrim(strtolower($extension), '.');
        $mime_type = array_search($extension, self::$mime_types);

        return $mime_type ? $mime_type : null;
    }

    static function getFileMimeType($file)
    {
        if ($info = pathinfo($file)) {
            if (isset($info['extension']))
                return self::getMimeType($info['extension']);
        }
    }
}
