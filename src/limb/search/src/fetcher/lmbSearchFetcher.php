<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\search\src\fetcher;

use limb\search\src\db\query\lmbMySQL4FullTextSearchQuery;
use limb\web_app\src\fetcher\lmbFetcher;
use limb\toolkit\src\lmbToolkit;

/**
 * class lmbSearchFetcher.
 *
 * @package search
 * @version $Id: lmbSearchFetcher.php 7686 2009-03-04 19:57:12Z
 */
class lmbSearchFetcher extends lmbFetcher
{
    protected $request;

    function setRequest($request)
    {
        $this->request = $request;
    }

    protected function _createDataSet()
    {
        $query = new lmbMySQL4FullTextSearchQuery('full_text_uri_content_index',
            $this->_getQueryWords(),
            true,
            lmbToolkit::instance()->getDefaultDbConnection());

        return $query->getRecordSet();
    }

    protected function _collectDecorators()
    {
        if ($words = $this->_getQueryWords())
            $this->addDecorator('limb\search\src\dataset\lmbSearchResultProcessor',
                array('words' => $words,
                    'matched_word_folding_radius' => 40,
                    'gaps_pattern' => '...',
                    'match_left_mark' => '<b>',
                    'match_right_mark' => '</b>',
                    'matching_lines_limit' => 4));
    }

    protected function _getQueryWords()
    {
        $query = $this->request->get('query_string');
        return explode(' ', htmlspecialchars($query));
    }

    public static function createFetcher(): lmbFetcher
    {
        return new static();
    }
}
