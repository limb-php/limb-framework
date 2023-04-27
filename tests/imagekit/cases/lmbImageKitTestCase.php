<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace tests\imagekit\cases;

use PHPUnit\Framework\TestCase;

require_once('.setup.php');

abstract class lmbImageKitTestCase extends TestCase
{
  protected $driver;

    function tearDown(): void
    {
        @unlink($this->_getOutputImage());

        parent::tearDown();
    }

  protected function _getInputImage()
  {
    return dirname(__FILE__) . '/../fixture/images/input.jpg';
  }

  protected function _getInputImageType()
  {
    return 'jpeg';
  }

  protected function _getInputPalleteImage()
  {
    return dirname(__FILE__) . '/../fixture/images/water_mark.gif';
  }

  protected function _getOutputImage($type = 'jpg')
  {
    return lmb_var_dir() . '/output.' . $type;
  }

  protected function _getClass($template)
  {
    return 'limb\\imagekit\\src\\'.$this->driver.'\\'.str_replace('%', lmb_camel_case($this->driver), $template);
  }

  protected function _getFilterClass($template)
  {
    return 'limb\\imagekit\\src\\'.$this->driver.'\\filters\\'.str_replace('%', lmb_camel_case($this->driver), $template);
  }

  function _getConvertor($params = array())
  {
    $class_name = $this->_getClass('lmb%ImageConvertor');

    return new $class_name($params);
  }

  function _getContainer()
  {
    $class_name = $this->_getClass('lmb%ImageContainer');

    $cont = new $class_name;
    $cont->load($this->_getInputImage());
    return $cont;
  }

  function _getPalleteContainer()
  {
    $class_name = $this->_getClass('lmb%ImageContainer');

    $cont = new $class_name;
    $cont->load($this->_getInputPalleteImage());
    return $cont;
  }
}
