<?php
/*
 * Limb PHP Framework
 *
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
//    function getInsertOnDuplicateQueryTemplate()
//    {
//        return 'INSERT INTO %table% (%fields%) VALUES (%values%) ON DUPLICATE KEY UPDATE (%new_values%)';
//    }

    function getInsertQueryTemplate()
    {
        return 'INSERT INTO %table% (%fields%) VALUES (%values%) %primary_key_name%';
    }
}
