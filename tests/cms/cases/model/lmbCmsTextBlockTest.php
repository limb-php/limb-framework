<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\cms\cases\model;

use limb\cms\src\model\lmbCmsTextBlock;
use tests\cms\cases\lmbCmsTestCase;

class lmbCmsTextBlockTest extends lmbCmsTestCase
{
    function testGetRawContent_Positive()
    {
        $block = new lmbCmsTextBlock();
        $block->setIdentifier($identifier = 'foo');
        $block->setContent($content = '<p>bar</p>');
        $block->save();

        $block_content = lmbCmsTextBlock::getRawContent($identifier);
        $this->assertEquals($block_content, $content);
    }

    function testGetRawContent_Negative()
    {
        $block_content = lmbCmsTextBlock::getRawContent('not_existed');
        $this->assertEquals($block_content, '');
    }
}
