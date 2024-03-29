<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\search\cases\indexer;

use PHPUnit\Framework\TestCase;
use limb\search\src\indexer\lmbSearchTextNormalizer;

class lmbSearchTextNormalizerTest extends TestCase
{
    function testProcess()
    {
        $normalizer = new lmbSearchTextNormalizer();
        $result = $normalizer->process('"mysql"
      wow-it\'s JUST \'so\' `cool` i can\'t believe it <b>root</b>"he-he"');

        $this->assertEquals($result, "mysql wow it's just so cool i can't believe it root he he");
    }

    function testProcessIsMultiByteAware()
    {
        $normalizer = new lmbSearchTextNormalizer();
        $result = $normalizer->process('Привет растения');

        $this->assertEquals($result, "привет растения");
    }
}
