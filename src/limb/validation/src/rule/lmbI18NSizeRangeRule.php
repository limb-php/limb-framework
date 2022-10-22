<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\validation\src\rule;

use limb\i18n\src\charset\lmbI18nString;
use limb\i18n\src\lmbI18n;

/**
 * class lmbI18NSizeRangeRule.
 *
 * @package validation
 * @version $Id$
 */
class lmbI18NSizeRangeRule extends lmbSingleFieldRule
{
  /**
   * @var integer	min field value length in glyphs
   */
  protected $min_length;
  /**
   * @var integer max field value length in glyphs
   */
  protected $max_length;

  function __construct($field_name, $min_or_max_length, $max_length = null, $custom_error = null)
  {
    if(is_null($max_length))
    {
      $this->min_length = null;
      $this->max_length = $min_or_max_length;
    }
    else
    {
      $this->min_length = $min_or_max_length;
      $this->max_length = $max_length;
    }

    parent::__construct($field_name, $custom_error);
  }

  function check($value)
  {
    if(!is_null($this->min_length) && (lmbI18nString::strlen($value) < $this->min_length))
    {
      $this->error(lmbI18n::translate('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                   array('min' => $this->min_length, 'max' => $this->max_length));
    }

    if(lmbI18nString::strlen($value) > $this->max_length)
    {
      if(is_null($this->min_length))
        $this->error(lmbI18n::translate('{Field} must be less than {max} characters.', 'validation'),
                   array('min' => $this->min_length, 'max' => $this->max_length));
      else
        $this->error(lmbI18n::translate('{Field} must be less than {max} and greater than {min} characters.', 'validation'),
                   array('min' => $this->min_length, 'max' => $this->max_length));
    }
  }
}
