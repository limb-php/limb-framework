<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package macro
 * @version $Id$
 */

use limb\core\src\lmbEnv;

$LIMB_MACRO_TAGS_INCLUDE_PATH = lmbEnv::get('LIMB_MACRO_TAGS_INCLUDE_PATH', []);
$LIMB_MACRO_FILTERS_INCLUDE_PATH = lmbEnv::get('LIMB_MACRO_FILTERS_INCLUDE_PATH', []);

lmbEnv::set('LIMB_MACRO_TAGS_INCLUDE_PATH', $LIMB_MACRO_TAGS_INCLUDE_PATH + array(__DIR__ . '/src/tags', __DIR__ . '/../*/src/macro'));
lmbEnv::set('LIMB_MACRO_FILTERS_INCLUDE_PATH', $LIMB_MACRO_FILTERS_INCLUDE_PATH + array(__DIR__ . '/src/filters', __DIR__ . '/../*/src/macro'));

require_once(__DIR__ . '/src/filters/lmbMacroDefaultFilterFunction.php');
require_once(__DIR__ . '/src/filters/lmbMacroI18nWordDeclensionFilterFunction.php');
require_once(__DIR__ . '/src/filters/lmbMacroRecognizeUrlsFilterFunction.php');
require_once(__DIR__ . '/src/filters/lmbMacroStripPhoneFilterFunction.php');
require_once(__DIR__ . '/src/filters/lmbMacroTimeLeftFilterFunction.php');
require_once(__DIR__ . '/src/filters/lmbMacroWordDeclensionFilterFunction.php');
