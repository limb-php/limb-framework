<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\drivers;

use limb\dbal\exception\lmbDbException;
use limb\dbal\query\lmbQueryLexerInterface;

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
