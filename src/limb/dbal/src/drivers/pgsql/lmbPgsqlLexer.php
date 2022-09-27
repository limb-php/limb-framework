<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
namespace limb\dbal\src\drivers\pgsql;

use limb\dbal\src\drivers\lmbDbBaseLexer;

/**
 * class lmbPgsqlLexer
 *
 * @package dbal
 * @version $Id$
 */
class lmbPgsqlLexer extends lmbDbBaseLexer
{
    function getInsertOnDuplicateQueryTemplate()
    {
        return 'INSERT INTO %table% (%fields%) VALUES (%values%) ON CONFLICT (%conflict_fields%) DO UPDATE SET(%new_values%)';
    }
}
