<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\macro\cases\compiler;

use PHPUnit\Framework\TestCase;
use limb\fs\lmbFs;
use limb\core\lmbEnv;
use limb\macro\compiler\lmbMacroAnnotationParser;
use limb\macro\compiler\lmbMacroAnnotationParserListenerInterface;

require(dirname(__FILE__) . '/../.setup.php');

class lmbMacroAnnotationParserTest extends TestCase
{
    function setUp(): void
    {
        lmbFs::rm(lmbEnv::get('LIMB_VAR_DIR') . '/tags/');
        lmbFs::mkdir(lmbEnv::get('LIMB_VAR_DIR') . '/tags/');
    }

    function testExtractOneFromFile()
    {
        $rnd = mt_rand();
        $contents = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}
EOD;
        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/tags/' . $rnd . '.tag.php', $contents);

        $listener = $this->createMock(lmbMacroAnnotationParserListenerInterface::class);
        $listener
            ->expects($this->once())
            ->method('createByAnnotations')
            ->with($file, "Foo{$rnd}Tag", array('tag' => "foo_{$rnd}"));
        $info = lmbMacroAnnotationParser::extractFromFile($file, $listener);
    }

    function testExtractSeveralFromFile()
    {
        $rnd = mt_rand();
        $contents = <<<EOD
<?php
/**
 * @tag foo_{$rnd}
 */
class Foo{$rnd}Tag extends lmbMacroTag{}

/**
 * @tag bar_{$rnd}
 */
class Bar{$rnd}Tag extends lmbMacroTag{}
EOD;
        file_put_contents($file = lmbEnv::get('LIMB_VAR_DIR') . '/tags/' . $rnd . '.tag.php', $contents);

        $listener = $this->createMock(lmbMacroAnnotationParserListenerInterface::class);
        $listener
            ->expects($this->exactly(2))
            ->method('createByAnnotations');

        $listener
            ->method('createByAnnotations')
            ->willReturnMap([
                [$file, "Foo{$rnd}Tag", array('tag' => "foo_{$rnd}")],
                [$file, "Bar{$rnd}Tag", array('tag' => "bar_{$rnd}")]
            ]);

        $info = lmbMacroAnnotationParser::extractFromFile($file, $listener);
    }
}
