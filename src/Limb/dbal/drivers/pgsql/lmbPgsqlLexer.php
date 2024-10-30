<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers\pgsql;

use limb\dbal\drivers\lmbDbBaseLexer;

/**
 * class lmbPgsqlLexer
 *
 * @package dbal
 * @version $Id$
 */
class lmbPgsqlLexer extends lmbDbBaseLexer
{
//    function getInsertOnDuplicateQueryTemplate()
//    {
//        return 'INSERT INTO %table% (%fields%) VALUES (%values%) ON DUPLICATE KEY UPDATE (%new_values%)';
//    }

    function getInsertQueryTemplate()
    {
        return 'INSERT INTO %table% (%fields%) VALUES (%values%) %primary_key_name%';
    }
}
