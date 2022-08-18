<?php
use limb\cms\src\model\lmbCmsUserRoles;

$editor = array(array('title' => 'Content', 'icon' => '/shared/cms/images/icons/menu_content.png',  'children' => array(
  array(
    'title' => 'Pages',
    'url' => '/admin_document',
    'icon' => '/shared/cms/images/icons/page.png',
  ),
  array(
    'title' => 'Text blocks',
    'url' => '/admin_text_block',
    'icon' => '/shared/cms/images/icons/layout.png',
  ),
  array(
    'title' => 'SEO',
    'url' => '/admin_seo',
    'icon' => '/shared/cms/images/icons/page_white_stack.png',
  ),
)));

$only_admin = array(array('title' => 'Admin', 'icon' => '/shared/cms/images/icons/menu_service.png','children' => array(
  array(
    'title' => 'Users',
    'url' => '/admin_user',
    'icon' => '/shared/cms/images/icons/user.png',
  ),
)));

$conf = array(
  lmbCmsUserRoles::EDITOR  => $editor,
  lmbCmsUserRoles::ADMIN  => array_merge_recursive($editor, $only_admin)
);

