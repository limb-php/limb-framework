<?php
namespace limb\optimization\src\model;

use limb\core\src\lmbObject;
use limb\active_record\src\lmbActiveRecord;
use limb\validation\src\lmbValidator;
use limb\toolkit\src\lmbToolkit;
use limb\dbal\src\criteria\lmbSQLCriteria;
use limb\dbal\src\criteria\lmbSQLFieldCriteria;
use limb\cms\src\validation\rule\lmbCmsUniqueFieldRule;

class MetaInfo extends lmbActiveRecord
{
  protected $_db_table_name = 'meta_data';

  static protected $_meta;

  protected function _createValidator()
  {
    $validator = new lmbValidator();
    $validator->addRequiredRule('url');
    $validator->addRule(new lmbCmsUniqueFieldRule('url', __CLASS__, $this, '"Url" должен быть уникальным'));
    $validator->addRequiredRule('title');

    return $validator;
  }

  /* getters/setters */
  static function getCurrentUrl()
  {
    $request = lmbToolKit::instance()->getRequest();
    $uri = $request->getUri();

    $url = $uri->getPathToLevel($uri->countPath() - 1);
    $url = ($url[0] == '/') ? (string)substr($url, 1) : $url ;
    $url = ((strlen($url) > 1) && ($url[strlen($url)-1] == '/')) ? (string)substr($url, 0, -1) : $url ;

    $furl = filterPath($url);
    $query = $uri->toString( array('query') );

    return $furl . ($query ? '?' . $query : '');
  }

  protected static function _getMetadataForUrl($url = null)
  {
    if(!$url)
      $url = self::getCurrentUrl();

    $toolkit = lmbToolKit::instance();

    $criteria = new lmbSQLFieldCriteria('url', '/' . $url);
    $criteria->addOr( new lmbSQLFieldCriteria('url', $url) );
    if( $suffix = $toolkit->getUrlSuffix() )
    {
      $criteria->addOr( new lmbSQLFieldCriteria('url', '/' . $url . $suffix) );
      $criteria->addOr( new lmbSQLFieldCriteria('url', $url . $suffix) );
    }
    $meta = lmbActiveRecord::findFirst( __CLASS__, array('cache' => true,
                                                                   'criteria' => $criteria) );

    if( !empty($meta) )
      self::$_meta = $meta;
    else
      self::$_meta = new lmbObject(array('meta_title' => '', 'meta_description' => '', 'meta_keywords' => '', 'head_title' => ''));
  }

  public static function getMetaTitle()
  {
    if(empty(self::$_meta))
      self::_getMetaDataForUrl();

    return self::$_meta->get('title');
  }

  public static function getMetaKeywords()
  {
    if(empty(self::$_meta))
      self::_getMetaDataForUrl();

    return self::$_meta->get('keywords');
  }

  public static function getMetaDescription()
  {
    if(empty(self::$_meta))
      self::_getMetaDataForUrl();

    return self::$_meta->get('description');
  }

  public static function getHeadTitle()
  {
    if(empty(self::$_meta))
      self::_getMetaDataForUrl();

    return self::$_meta->get('head');
  }

  public static function getMetaForCurrentUrl()
  {
    if(empty(self::$_meta))
       self::_getMetaDataForUrl();

    return self::$_meta;
  }

  public static function getMetaForUrl($uri)
  {
    self::_getMetaDataForUrl($uri);
    return self::$_meta;
  }

  /* */
  static function findByUrl( $url )
  {
    $criteria = new lmbSQLFieldCriteria('url', $url);

    return lmbActiveRecord::findFirst( __CLASS__, array('criteria' => $criteria) );
  }

  static function findForAdmin( $params = array() )
  {
    $criteria = new lmbSQLCriteria();

    return lmbActiveRecord::find( __CLASS__, array('criteria' => $criteria,
                                                             'sort' => isset($params['sort']) ? $params['sort'] : null
                                                            ) );
  }
}

