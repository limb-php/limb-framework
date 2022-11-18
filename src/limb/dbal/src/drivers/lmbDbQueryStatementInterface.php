<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
namespace limb\dbal\src\drivers;

/**
 * interface lmbDbQueryStatement.
 *
 * @package dbal
 * @version $Id: lmbDbStatementInterface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface lmbDbQueryStatementInterface extends lmbDbStatementInterface
{
  function getOneRecord();
  function getOneValue();
  function getOneColumnAsArray();
  function getRecordSet();
}
