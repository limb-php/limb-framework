<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\macro\src\tags\core;

use limb\macro\src\compiler\lmbMacroTag;

/**
 * Repeat a portion of the template several times
 * @tag repeat
 * @req_attributes times
 * @package macro
 * @version $Id$
 */
class lmbMacroRepeatTag extends lmbMacroTag
{
  protected $counter_var;

  protected function _generateContent($code)
  {
    $this->counter_var = $code->generateVar();
    $code->writePHP($this->counter_var . ' = 0;');

    $times = $this->get('times');

    $code->writePhp("for (" . $this->counter_var . " = 0; " . $this->counter_var . " < $times; " . $this->counter_var . "++ ){ \n");
    if($user_counter = $this->get('counter'))
      $code->writePHP($user_counter . ' = ' . $this->counter_var . '+1;');

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
}

