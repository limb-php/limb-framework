<?php
namespace tests\cms\cases\validation\rules;

use limb\validation\src\lmbErrorList;
use tests\cms\cases\lmbCmsTestCase;
use limb\cms\src\validation\rule\lmbTreeUniqueIdentifierRule;
use limb\cms\src\model\lmbCmsDocument;

class lmbTreeUniqueIdentifierFieldRuleTest extends lmbCmsTestCase
{
  protected $error_list; 
  protected $tables_to_cleanup = array('lmb_cms_document');

  function setUp(): void
  {
    parent::setUp();

    $this->error_list = $this->createMock(lmbErrorList::class);
    $this->_initCmsDocumentTable();
  }

  function testValidWithoutSettingParentId()
  { 
    $saved_document = $this->_createDocument($identifier = 'test');
    $new_document = $this->_generateDocument($identifier = 'test2');

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует');

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($new_document, $this->error_list);
  }

  function testNotValidWithoutSettingParentId()
  {
    $saved_document = $this->_createDocument($identifier = 'test');
    $new_document = $this->_generateDocument($identifier = 'test');

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует');

    $this->error_list
        ->expects($this->once())
        ->method('addError');

    $rule->validate($new_document, $this->error_list);
  }

  function testValidWithSettingParentId()
  {
    $saved_document1 = $this->_createDocument($identifier = 'test');
    $saved_document2 = $this->_createDocument($identifier = 'test2', $parent_document = $saved_document1);
    $new_document = $this->_generateDocument($identifier = 'test3', $parent_document = $saved_document1);

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует', $saved_document1->getId());

    $this->error_list
        ->expects($this->never())
        ->method('addError');

    $rule->validate($new_document, $this->error_list);

  }

  function testNotValidWithSettingParentId()
  {
    $saved_document1 = $this->_createDocument($identifier = 'test');
    $saved_document2 = $this->_createDocument($identifier = 'test2', $parent_document = $saved_document1);
    $new_document = $this->_generateDocument($identifier = 'test2', $parent_document = $saved_document1);

    $rule = new lmbTreeUniqueIdentifierRule('identifier', $new_document, 'документ с таким идентификатором уже существует', $saved_document1->getId());

    $this->error_list
        ->expects($this->once())
        ->method('addError');

    $rule->validate($new_document, $this->error_list);

  }


  /* function for just create an object of lmbCmsDocument but do not save it into DB */
  protected function _generateDocument($identifier, $parent_document = false)
  {
    $document = new lmbCmsDocument();

    $document->setIdentifier($identifier);
    $document->setTitle('title_'.microtime(true));
    $document->setContent('content_'.microtime(true));

    if(!$parent_document)
      $parent_document = lmbCmsDocument::findRoot();
    $document->setParent($parent_document);

    return $document;
  }
}
