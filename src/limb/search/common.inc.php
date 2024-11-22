<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package search
 * @version $Id: common.inc.php 7686 2009-03-04 19:57:12Z
 */

use limb\core\src\lmbEnv;

lmbEnv::setor('FULL_TEXT_SEARCH_INDEXER_TABLE', 'full_text_uri_content_index');

require_once(dirname(__FILE__) . '/../core/common.inc.php');
