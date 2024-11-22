<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal\src\query;

use limb\core\src\exception\lmbException;
use limb\dbal\src\drivers\lmbDbConnectionInterface;
use limb\dbal\src\drivers\lmbDbStatementInterface;
use limb\toolkit\src\lmbToolkit;
use limb\core\src\lmbString;

/**
 * class lmbTemplateQuery.
 *
 * @package dbal
 * @version $Id: lmbTemplateQuery.php 7486 2009-01-26 19:13:20Z
 */
abstract class lmbTemplateQuery
{
    protected $_template_sql;
    protected $_no_hints_sql;
    protected $_conn = null;
    protected $_hints = array();
    protected $_lexer;

    function __construct($template_sql)
    {
        $this->_template_sql = $template_sql;
    }

    function _registerHint($hint)
    {
        $this->_hints[$hint] = $hint;
    }

    function getConnection(): lmbDbConnectionInterface
    {
        return $this->_conn ?? $this->_conn = lmbToolkit::instance()->getDefaultDbConnection();
    }

    function setConnection($conn)
    {
        $this->_conn = $conn;

        return $this;
    }

    protected function getLexer(): lmbQueryLexerInterface
    {
        return $this->_lexer ?? $this->_lexer = $this->getConnection()->getLexer();
    }

    protected function _findHintsInTemplateSql()
    {
        if (preg_match_all('~%([a-z_]+)%~', $this->_template_sql, $m))
            $result = $m[1];
        else
            $result = array();
        return $result;
    }

    function _wrapHint($hint)
    {
        return "%$hint%";
    }

    function _findAndWrapHintsFromTemplateSql()
    {
        return array_map(array($this, '_wrapHint'), $this->_findHintsInTemplateSql());
    }

    /**
     * @throws lmbException
     */
    function _fillHints()
    {
        $result = array();
        foreach ($this->_hints as $hint) {
            $method = '_get' . lmbString::camel_case($hint) . 'Hint';
            $wrapped_hint = $this->_wrapHint($hint);
            //if (!strpos($this->_template_sql, $wrapped_hint))
                //throw new lmbException('Hint ' . $wrapped_hint . ' is not found in template sql "' . $this->_template_sql . '"');
            if (strpos($this->_template_sql, $wrapped_hint))
                $result[$wrapped_hint] = $this->$method();
        }

        $hints_in_template_sql = $this->_findAndWrapHintsFromTemplateSql();
        foreach ($hints_in_template_sql as $hint)
            if (!isset($result[$hint]))
                $result[$hint] = "";

        return $result;
    }

    /**
     * @throws lmbException
     */
    function toString(): string
    {
        $hints = $this->_fillHints();
        return trim(strtr($this->_template_sql, $hints));
    }

    function getStatement(): lmbDbStatementInterface
    {
        $sql = $this->toString();

        return $this->getConnection()->newStatement($sql);
    }

    function execute()
    {
        $this->getStatement()->execute();
    }

    protected function _getNoHintsSQL()
    {
        if ($this->_no_hints_sql)
            return $this->_no_hints_sql;

        $result = array();
        foreach ($this->_findAndWrapHintsFromTemplateSql() as $hint)
            $result[$hint] = '';

        $this->_no_hints_sql = strtr($this->_template_sql, $result);
        return $this->_no_hints_sql;
    }
}
