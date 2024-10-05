<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

function vypiska($skolko, $chego)
{
    if ($chego == 2)
        return 'year (s)';
    elseif ($chego == 1)
        return 'month (s)';
    elseif ($chego == 0)
        return 'day (s)';

}

/*function vypiska($skolko, $chego)
{
  $array = array(
      "",
      "день",
      "дня",
      "дней",
      "месяц",
      "месяца",
      "месяцев",
      "год",
      "года",
      "лет"
  );

  if ($skolko == 0)
      $skolko = 10;
  if ($skolko == 1)
      $a = 3 * $chego + 1;
  if ($skolko >= 2 && $skolko <= 4)
      $a = 3 * $chego + 2;
  if ($skolko >= 5 && $skolko <= 20)
      $a = 3 * $chego + 3;
  if ($skolko > 20 && $skolko < 100)
      return vypiska($skolko % 10, $chego);
  if ($skolko >= 100)
      return vypiska($skolko % 100, $chego);
  return $array[$a];
}*/

function time_left($std)
{
    $ed = time();
    $e = abs($ed - $std);
    $f = date('j-n-Y', $e);
    $dat = explode("-", $f);
    $day = $dat[0] - 1;
    $month = $dat[1] - 1;
    $year = $dat[2] - 1970;
    $soob = "";

    if ($year != 0)
        $soob = $soob . $year . " " . vypiska($year, 2);
    if ($year != 0 && $month != 0 && $day != 0)
        $soob = $soob . ", ";
    if ($year != 0 && $month != 0 && $day == 0)
        $soob = $soob . " and ";
    if ($month == 0 && $day != 0 && $year != 0)
        $soob = $soob . " and ";
    if ($month != 0)
        $soob = $soob . $month . " " . vypiska($month, 1);
    if ($month != 0 && $day != 0)
        $soob = $soob . " and ";
    if ($day != 0)
        $soob = $soob . $day . " " . vypiska($day, 0) . " ago";
    else
        $soob = "today";

    return $soob;
}
