<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\gd;

use limb\imagekit\lmbAbstractImageConvertor;
use limb\fs\exception\lmbFileNotFoundException;
use limb\imagekit\exception\lmbImageLibraryNotInstalledException;

/**
 * GD image convertor
 *
 * @package imagekit
 * @version $Id: lmbGdImageConvertor.php 8110 2010-01-28 14:20:12Z
 */
class lmbGdImageConvertor extends lmbAbstractImageConvertor
{

    function __construct($params = array())
    {
        if (!function_exists('gd_info'))
            throw new lmbImageLibraryNotInstalledException('gd');

        if (!isset($params['filters_scan_dirs']))
            $params['filters_scan_dirs'] = dirname(__FILE__) . '/filters';

        parent::__construct($params);
    }

    protected function createFilter($name, $params)
    {
        $class = $this->loadFilter($name, 'Gd', $params);
        return new $class($params);
    }

    protected function createImageContainer($file_name, $type = '')
    {
        $container = new lmbGdImageContainer();
        $container->load($file_name, $type);
        return $container;
    }

    function isSupportConversion($file, $src_type = '', $dest_type = '')
    {
        if (!$src_type) {
            $imginfo = @getimagesize($file);
            if (!$imginfo)
                throw new lmbFileNotFoundException($file);
            $src_type = lmbGdImageContainer::convertImageType($imginfo[2]);
        }
        if (!$dest_type)
            $dest_type = $src_type;
        return lmbGdImageContainer::supportLoadType($src_type) &&
            lmbGdImageContainer::supportSaveType($dest_type);
    }
}
