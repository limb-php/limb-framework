<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace Limb\Macro;

use Limb\Macro\Compiler\lmbMacroTagDictionary;
use Limb\Macro\Compiler\lmbMacroFilterDictionary;
use Limb\Macro\Compiler\lmbMacroCompiler;
use Limb\Macro\Compiler\lmbMacroTemplateExecutor;

/**
 * class lmbMacroTemplate.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroTemplate
{
    protected $file;
    protected $compiled_file;
    /** @var lmbMacroTemplateExecutor $executor */
    protected $executor;
    protected $vars = array();
    /** @var lmbMacroTemplateExecutor $child_executor */
    protected $child_executor;
    protected $config;
    protected $locator;

    function __construct($file, $config = array(), lmbMacroTemplateLocatorInterface $locator = null)
    {
        $this->file = $file;
        if ($config instanceof lmbMacroConfig)
            $this->config = $config;
        else
            $this->config = new lmbMacroConfig($config);
        $this->locator = $locator ?? new lmbMacroTemplateLocatorSimple($this->config);
    }

    function setVars($vars)
    {
        $this->vars = $vars;
    }

    function set($name, $value)
    {
        $this->vars[$name] = $value;
    }

    function setChildExecutor($executor)
    {
        $this->child_executor = $executor;
    }

    function render($vars = array())
    {
        if (!$this->executor) {
            list($this->compiled_file, $macro_executor_class) = $this->compile($this->file);

            include($this->compiled_file);

            $this->executor = new $macro_executor_class($this->config);
        }

        $this->executor->setVars($this->vars);

        //in case of dynamic wrapping we need to ask parent for all unknown variables
        if ($this->child_executor)
            $this->child_executor->setContext($this->executor);

        ob_start();
        $this->executor->render($vars);
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    function compile($source_file)
    {
        //var_dump(__CLASS__);
        //var_dump($source_file);
        $compiled_file = $this->locator->locateCompiledTemplate($source_file);

        $macro_executor_class = null;

        if ($this->config->forcecompile || !file_exists($compiled_file)) {
            $macro_executor_class = 'MacroTemplateExecutor' . md5(uniqid(rand(), true));//think about evaluating this instance

            $compiler = $this->_createCompiler();
            $compiler->compile($source_file, $compiled_file, $macro_executor_class, 'render');

            //appending macro executor class
            file_put_contents($compiled_file, file_get_contents($compiled_file) .
                "\n\$macro_executor_class='$macro_executor_class';");
        }

        return array($compiled_file, $macro_executor_class);
    }

    protected function _createCompiler()
    {
        $tag_dictionary = lmbMacroTagDictionary::instance();
        $filter_dictionary = lmbMacroFilterDictionary::instance();
        $tag_dictionary->load($this->config);
        $filter_dictionary->load($this->config);

        return new lmbMacroCompiler($tag_dictionary, $this->locator, $filter_dictionary);
    }

    static function encodeCacheFileName($file_name)
    {
        return basename(dirname($file_name)) . '-' . basename($file_name) . '.' . crc32($file_name) . '.php';
    }
}
