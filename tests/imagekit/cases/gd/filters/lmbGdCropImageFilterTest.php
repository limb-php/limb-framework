<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\imagekit\cases\gd\filters;

use tests\imagekit\cases\filters\lmbBaseCropImageFilterTestCase;

/**
 * @package imagekit
 * @version $Id: lmbGdCropImageFilterTest.php 8065 2010-01-20 04:18:19Z
 */
class lmbGdCropImageFilterTest extends lmbBaseCropImageFilterTestCase
{
    protected $driver = 'gd';
}