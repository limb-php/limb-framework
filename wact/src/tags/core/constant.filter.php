<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @filter const
 * @package wact
 * @version $Id: constant.filter.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactConstantFilter extends WactCompilerFilter
{
  function getValue()
  {
    $value = $this->base->getValue();
    return lmb_env_has($value) ? lmb_env_get($value) : constant($value);
  }

  function generateExpression($code_writer)
  {
    $code_writer->writePHP('lmb_env_has(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(') ? lmb_env_get(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(') : constant(');
    $this->base->generateExpression($code_writer);
    $code_writer->writePHP(')');
  }
}

