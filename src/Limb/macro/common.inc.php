<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package macro
 * @version $Id$
 */

use limb\core\lmbEnv;

$LIMB_MACRO_TAGS_INCLUDE_PATH = lmbEnv::get('LIMB_MACRO_TAGS_INCLUDE_PATH', []);
$LIMB_MACRO_FILTERS_INCLUDE_PATH = lmbEnv::get('LIMB_MACRO_FILTERS_INCLUDE_PATH', []);

lmbEnv::set('LIMB_MACRO_TAGS_INCLUDE_PATH', $LIMB_MACRO_TAGS_INCLUDE_PATH + array(__DIR__ . '/src/tags', __DIR__ . '/../*/src/macro'));
lmbEnv::set('LIMB_MACRO_FILTERS_INCLUDE_PATH', $LIMB_MACRO_FILTERS_INCLUDE_PATH + array(__DIR__ . '/src/filters', __DIR__ . '/../*/src/macro'));

require_once(__DIR__ . '/filters/lmbMacroDefaultFilterFunction.php');
require_once(__DIR__ . '/filters/lmbMacroI18nWordDeclensionFilterFunction.php');
require_once(__DIR__ . '/filters/lmbMacroRecognizeUrlsFilterFunction.php');
require_once(__DIR__ . '/filters/lmbMacroStripPhoneFilterFunction.php');
require_once(__DIR__ . '/filters/lmbMacroTimeLeftFilterFunction.php');
require_once(__DIR__ . '/filters/lmbMacroWordDeclensionFilterFunction.php');
