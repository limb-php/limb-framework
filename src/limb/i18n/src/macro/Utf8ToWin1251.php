<?php
namespace limb\i18n\src\macro;

use limb\macro\src\compiler\lmbMacroFunctionBasedFilter;

/**
 * @filter utf8_to_win1251
 */
class Utf8ToWin1251 extends lmbMacroFunctionBasedFilter
{
  protected $function = 'lmb_utf8_to_win1251';

}
