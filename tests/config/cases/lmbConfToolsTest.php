<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
*/

namespace tests\config\cases;

require_once (dirname(__FILE__) . '/init.inc.php');

use PHPUnit\Framework\TestCase;
use limb\toolkit\lmbToolkit;
use limb\config\toolkit\lmbConfTools;
use limb\fs\lmbFs;

class lmbConfToolsTest extends TestCase
{
    /**
     * @var lmbConfTools
     */
    protected $toolkit;

    protected $application_configs_dir;
    protected $package_configs_dir;

    function setUp(): void
    {
        lmbToolkit::save();
        $this->toolkit = lmbToolkit::merge(new lmbConfTools());

        $this->application_configs_dir = lmb_var_dir() . '/app/settings';
        lmbFs::mkdir($this->application_configs_dir);

        $this->package_configs_dir = lmb_var_dir() . '/package/settings';
        lmbFs::mkdir($this->package_configs_dir);

        $tests_include_apth = $this->application_configs_dir . ';' . $this->package_configs_dir;
        $this->toolkit->setConfIncludePath($tests_include_apth);
    }

    function tearDown(): void
    {
        lmbToolkit::restore();
        lmbFs::rm($this->application_configs_dir);
        lmbFs::rm($this->package_configs_dir);
    }

    function testSetGetConf()
    {
        $conf_name = 'foo';
        $key = 'bar';
        $value = 42;

        $this->toolkit->setConf($conf_name, array($key => $value));

        $conf = $this->toolkit->getConf($conf_name);
        $this->assertEquals($conf[$key], $value);
    }

    function testGetConfParam()
    {
        $content = '<?PHP $conf = array("bar" => 42); ';
        lmbFs::safeWrite($this->application_configs_dir . '/foo.conf.php', $content);

        $value = $this->toolkit->getConfParam('foo.bar');
        $this->assertEquals(42, $value);

        $value2 = $this->toolkit->getConfParam('foo');
        $this->assertEquals(['bar' => 42], $value2);

//        $value2 = $this->toolkit->getConfParam('foo_not_exists');
//        $this->assertEquals(['bar' => 42], $value2);
    }

    function testGetConfWithDotsParam()
    {
        $content = '<?PHP $conf = array("acme" => 52); ';
        lmbFs::safeWrite($this->application_configs_dir . '/foo.acme.conf.php', $content);

        $conf = $this->toolkit->getConf('foo.acme.conf.php');
        $this->assertEquals(['acme' => 52], $conf->export());

        $conf = $this->toolkit->getConf('foo.acme');
        $this->assertEquals(['acme' => 52], $conf->export());
    }

    function testHasConf()
    {
        $content = '<?PHP $conf = array("foo" => 42); ';
        lmbFs::safeWrite($this->application_configs_dir . '/has.conf.php', $content);

        $this->assertFalse($this->toolkit->hasConf('not_existed'));
        $this->assertTrue($this->toolkit->hasConf('has'));
    }

    function testGetConf_WithApplicationConfig()
    {
        $content = '<?PHP $conf = array("foo" => 42); ';
        lmbFs::safeWrite($this->application_configs_dir . '/with_app.conf.php', $content);

        $content = '<?PHP $conf = array("bar" => 101); ';
        lmbFs::safeWrite($this->package_configs_dir . '/with_app.conf.php', $content);

        $conf = $this->toolkit->getConf('with_app');

        $this->assertFalse($conf->has('bar'));
        $this->assertTrue($conf->has('foo'));
        $this->assertEquals(42, $conf->get('foo'));
    }

    function testGetConf_WithoutApplicationConfig()
    {
        $content = '<?PHP $conf = array("bar" => 101); ';
        lmbFs::safeWrite($this->package_configs_dir . '/without_app.conf.php', $content);

        $conf = $this->toolkit->getConf('without_app');

        $this->assertTrue($conf->has('bar'));
        $this->assertEquals(101, $conf->get('bar'));
    }

    function testGetYamlConf_WithoutApplicationConfig()
    {
        $content = "bar:\n  foo: 200\n  test: string with spaces\n\n";
        lmbFs::safeWrite($this->package_configs_dir . '/without_app.yml', $content);
        $conf = $this->toolkit->getConf('without_app.yml');

        if ($this->assertTrue($conf->has('bar'))) {
            $bar = $conf->get('bar');
            $this->assertEquals(200, $bar['foo']);
            $this->assertEquals('string with spaces', $bar['test']);
        }
    }

    function testGetYamlConf_WithApplicationConfig()
    {
        $content = "bar:\n  foo: 200\n  test: string with spaces\n\n";
        lmbFs::safeWrite($this->package_configs_dir . '/with_app.yml', $content);

        $content = "bar:\n  foo: 201\n  test: \"string_without_spaces\"\n\n";
        lmbFs::safeWrite($this->application_configs_dir . '/with_app.yml', $content);


        $conf = $this->toolkit->getConf('with_app.yml');

        if ($this->assertTrue($conf->has('bar'))) {
            $bar = $conf->get('bar');
            $this->assertEquals(201, $bar['foo']);
            $this->assertEquals('string_without_spaces', $bar['test']);
        }
    }

    function testGetYamlConf_WithNestedAndInlineProperies()
    {
        $content = "bar:\n  foo: 200\n  test: string with spaces\n\n  nested:  \n    inline: {prop: value}\n";
        lmbFs::safeWrite($this->package_configs_dir . '/without_app.yml', $content);
        $conf = $this->toolkit->getConf('without_app.yml');

        if ($this->assertTrue($conf->has('bar'))) {
            $bar = $conf->get('bar');
            $this->assertEquals(200, $bar['foo']);
            $this->assertEquals('string with spaces', $bar['test']);
            $this->assertEquals('value', $bar['nested']['inline']['prop']);
        }
    }

    function testGetYamlConf_WithPhpCodeInside()
    {
        $content = <<<YAML
test:
<?php for(\$i=1; \$i<4; \$i++){ ?>
  bar<?php echo \$i; ?>: <?php echo \$i*2; ?> #enshure that spase exists after closed php tag
<?php } ?>

YAML;
        lmbFs::safeWrite($this->package_configs_dir . '/php.yml', $content);
        $conf = $this->toolkit->getConf('php.yml');

        if ($this->assertTrue($conf->has('test'))) {
            $test = $conf->get('test');
            $this->assertCount(3, $test);
            $this->assertEquals(2, $test['bar1']);
            $this->assertEquals(4, $test['bar2']);
            $this->assertEquals(6, $test['bar3']);
            $this->assertFalse(isset($test['bar4']));
        }
    }
}
