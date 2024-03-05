<?php

namespace Tests\wysiwyg\cases;

use PHPUnit\Framework\TestCase;
use limb\core\src\lmbSet;
use limb\wysiwyg\src\lmbWysiwygConfigurationHelper;
use limb\toolkit\src\lmbToolkit;

require_once dirname(__FILE__) . '/.setup.php';

class lmbWysiwygConfigurationHelperTest extends TestCase
{
    /**
     * @var lmbWysiwygConfigurationHelper
     */
    protected $_helper;

    function setUp(): void
    {
        parent::setUp();

        $config = new lmbSet(array(
            'default_profile' => 'foo_profile',
            'foo_profile' => array(
                'type' => 'fckeditor',
                'baz' => 42,
            ),
            'bar_profile' => array(
                'type' => 'tinymce',
                'baz' => 111,
            ),
        ));

        lmbToolkit::instance()->setConf('wysiwyg', $config);

        $this->_helper = new lmbWysiwygConfigurationHelper();
    }

    function testSetGetProfileName()
    {
        $this->assertEquals('foo_profile', $this->_helper->getProfileName());

        $this->_helper->setProfileName('bar_profile');
        $this->assertEquals('bar_profile', $this->_helper->getProfileName());
    }

    function testGetOption_DefaultProfile()
    {
        $this->assertEquals(42, $this->_helper->getOption('baz'));
    }

    function testGetOption_CustomProfile()
    {
        $this->_helper->setProfileName('bar_profile');
        $this->assertEquals(111, $this->_helper->getOption('baz'));
    }

    function testGetMacroWidgetInfo()
    {
        $this->assertEquals(
            array(
                'class' => 'limb\wysiwyg\src\macro\lmbMacroFCKEditorWidget'
            ),
            $this->_helper->getMacroWidgetInfo(),
        );
    }
}
