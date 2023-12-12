<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\imagekit\src;

use limb\core\src\exception\lmbException;
use limb\fs\src\exception\lmbFileNotFoundException;

/**
 * @package imagekit
 * @version $Id: lmbImageKit.php 8110 2010-01-28 14:20:12Z
 */
class lmbImageKit
{
    static function create($library = 'gd', $params = array())
    {
        $image_class_name = 'limb\\imagekit\\src\\' . $library . '\\lmb' . ucfirst($library) . 'ImageConvertor';

        try {
            $convertor = new $image_class_name($params);
        } catch (lmbException $e) {
            throw new lmbFileNotFoundException($image_class_name, 'image library not found');
        }

        return $convertor;
    }

    static function load($file_name, $type = '', $library = 'gd', $params = array())
    {
        $convertor = self::create($library, $params);
        $convertor->load($file_name, $type);
        return $convertor;
    }

}
