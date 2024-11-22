<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\drivers;

use limb\dbal\src\exception\lmbDbException;
use limb\dbal\src\query\lmbQueryLexerInterface;

/**
 * class lmbDbBaseLexer
 *
 * @package dbal
 * @version $Id$
 */
class lmbDbBaseLexer implements lmbQueryLexerInterface
{
    function getSelectQueryTemplate()
    {
        return 'SELECT %fields% FROM %tables% %left_join% %where% %group% %having% %order%';
    }

    function getBulkInsertQueryTemplate()
    {
        return 'INSERT INTO %table% (%fields%) VALUES %values%';
    }

    function getInsertQueryTemplate()
    {
        return 'INSERT INTO %table% (%fields%) VALUES (%values%)';
    }

    function getUpdateQueryTemplate()
    {
        return 'UPDATE %table% SET %fields% %where%';
    }

    function getDeleteQueryTemplate()
    {
        return 'DELETE FROM %table% %where%';
    }

    function getInsertOnDuplicateQueryTemplate()
    {
        throw new lmbDbException('Not supported on this DB');
    }
}
