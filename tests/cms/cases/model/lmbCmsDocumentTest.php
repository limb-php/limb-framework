<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\model;

use tests\cms\cases\lmbCmsTestCase;

class lmbCmsDocumentTest extends lmbCmsTestCase
{
    protected $tables_to_cleanup = array('lmb_cms_document');

    function testGetUri()
    {
        $parent = $this->_createDocument('parent');
        $child = $this->_createDocument('child', $parent);

        $this->assertEquals($parent->getUri(), '/parent');
        $this->assertEquals($child->getUri(), '/parent/child');
    }
}
