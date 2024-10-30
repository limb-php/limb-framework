<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src\model;

use limb\active_record\lmbActiveRecord;
use limb\tree\lmbMPTree;
use limb\validation\lmbValidator;
use limb\cms\src\validation\rule\TreeIdentifierRule;
use limb\dbal\lmbDBAL;
use limb\dbal\criteria\lmbSQLCriteria;

/**
 * class lmbCmsDocument.
 *
 * @package cms
 * @version $Id$
 */
class lmbCmsDocument extends lmbActiveRecordTreeNode
{
    protected $_db_table_name = 'lmb_cms_document';
    protected $_lazy_attributes = array('content');
    protected $_is_being_destroyed = false;
    /**
     * @var lmbMPTree
     */
    protected $_tree;

    function _createValidator()
    {
        $validator = new lmbValidator();

        $validator->addRequiredRule('title', 'Field "Title" is required');
        $validator->addRequiredRule('content', 'Field "Content" is required');
        $validator->addRequiredRule('identifier', 'Field "Identifier" is required');

        $validator->addRule(new TreeIdentifierRule('identifier'));

        return $validator;
    }

    protected function _onBeforeSave()
    {
        $this->save();
    }

    function _onCreate()
    {
        $this->_setPriority();
    }

    protected function _setPriority()
    {
        if (!$parent_id = $this->getParentId())
            $parent_id = lmbCmsDocument::findRoot()->getId();

        $sql = "SELECT MAX(priority) FROM " . $this->_db_table_name . " WHERE parent_id = " . $parent_id;
        $max_priority = lmbDBAL::fetchOneValue($sql);
        $this->setPriority($max_priority + 10);
    }

    function getUri()
    {
        $uri = ($this->getParent() && !$this->getParent()->isRoot()) ? $this->getParent()->getUri() : '';
        return $uri . '/' . $this->identifier;
    }

    /**
     * @param string $uri
     * @return lmbCmsDocument|false
     */
    static function findByUri($uri)
    {
        $identifiers = explode('/', rtrim($uri, '/'));
        $criteria = new lmbSQLCriteria('level = 0');
        $level = 0;
        foreach ($identifiers as $identifier) {
            $identifier_criteria = lmbSQLCriteria::equal('identifier', $identifier);
            $identifier_criteria->addAnd(lmbSQLCriteria::equal('level', $level));
            $criteria->addOr($identifier_criteria);

            $level++;
        }

        $documents = lmbActiveRecord::find(lmbCmsDocument::class, $criteria);

        $parent_id = 0;
        foreach ($identifiers as $identifier) {
            if (!$document = self::_getNodeByParentIdAndIdentifier($documents, $parent_id, $identifier))
                return false;

            $parent_id = $document->getId();
        }
        return $document;
    }

    static function _getNodeByParentIdAndIdentifier($documents, $parent_id, $identifier)
    {
        foreach ($documents as $document) {
            if (($document->getParentId() == $parent_id) && ($document->getIdentifier() == $identifier)) {
                return $document;
            }
        }
        return false;
    }
}
