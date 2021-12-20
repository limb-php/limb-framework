<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 /**
 *
 * @package zfsearch
 * @version $Id$
 */
set_include_path(get_include_path() . dirname(__FILE__).'/lib/');

require_once('limb/i18n/utf8.inc.php');
require_once('Zend/Search/Lucene.php');
@define('ZEND_SEARCH_ENCODING','utf-8');
Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8());

