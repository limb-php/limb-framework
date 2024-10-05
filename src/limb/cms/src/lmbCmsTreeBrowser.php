<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cms\src;

use limb\active_record\lmbActiveRecord;
use limb\cms\src\model\lmbCmsNode;

/**
 * class lmbCmsTreeBrowser.
 *
 * @package cms
 * @version $Id: lmbCmsTreeBrowser.php 5988 2007-06-13 07:50:57Z
 */
class lmbCmsTreeBrowser
{
    protected $current_folder;

    function setCurrentFolderPath($path)
    {
        $id = $this->_getLastId($path);
        $this->current_folder = $this->_getNode($id);
    }

    protected function _getNode($id)
    {
        if ($id)
            return new lmbCmsNode($id);

        return new lmbCmsNode();
    }

    protected function _getLastId($path)
    {
        $path = rtrim($path, '/');
        $exp = explode('/', $path);
        return (int)end($exp);
    }

    function renderFolders()
    {
        $result = '';

        if ($this->current_folder->getId())
            $folders = lmbActiveRecord::find(lmbCmsNode::class, 'parent_id = ' . $this->current_folder->getId());
        else
            $folders = $this->current_folder->getRoots();

        foreach ($folders as $folder) {
            $title = htmlspecialchars($folder->getTitle(), ENT_QUOTES);
            $result .= "<Folder id='{$folder->getId()}' name='{$title}' url='{$folder->getUrlPath()}'/>";
        }

        return $result;
    }
}
