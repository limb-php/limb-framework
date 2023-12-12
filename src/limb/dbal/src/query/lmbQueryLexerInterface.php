<?php

namespace limb\dbal\src\query;

/**
 * interface lmbQueryLexerInterface.
 *
 * @package dbal
 * @version $Id: lmbQueryLexerInterface 7486 2009-01-26 19:13:20Z
 */
interface lmbQueryLexerInterface
{
    function getSelectQueryTemplate();

    function getInsertQueryTemplate();

    function getUpdateQueryTemplate();

    function getDeleteQueryTemplate();

    function getInsertOnDuplicateQueryTemplate();
}

