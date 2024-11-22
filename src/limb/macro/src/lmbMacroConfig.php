<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\macro\src;

use limb\core\src\exception\lmbException;
use limb\core\src\lmbEnv;

/**
 * class lmbMacroConfig.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroConfig
{
    public $cache_dir;
    public $forcecompile = true;
    //duplicates forcecompile for BC
    public $is_force_compile;
    public $forcescan = false;
    //duplicates forcecompile for BC
    public $is_force_scan;
    public $tpl_scan_dirs;
    public $tags_scan_dirs;
    public $filters_scan_dirs;

    function __construct($options = array())
    {
        foreach ($options as $key => $val)
            $this->$key = $val;

        if (!$this->cache_dir)
            $this->cache_dir = lmbEnv::get('LIMB_VAR_DIR') . '/compiled';

        if (!$this->tpl_scan_dirs)
            $this->tpl_scan_dirs = array('templates');
        if (!is_array($this->tpl_scan_dirs))
            $this->tpl_scan_dirs = explode(';', $this->tpl_scan_dirs);

        if (!$this->tags_scan_dirs)
            $this->tags_scan_dirs = array('limb/macro/src/tags');
        if (!is_array($this->tags_scan_dirs))
            $this->tags_scan_dirs = explode(';', $this->tags_scan_dirs);

        if (!$this->filters_scan_dirs)
            $this->filters_scan_dirs = array('limb/macro/src/filters');
        if (!is_array($this->filters_scan_dirs))
            $this->filters_scan_dirs = explode(';', $this->filters_scan_dirs);

        //for possible BC breaks
        if (isset($this->is_force_compile))
            $this->forcecompile = $this->is_force_compile;
        else
            $this->is_force_compile = $this->forcecompile;

        if (isset($this->is_force_scan))
            $this->forcescan = $this->is_force_scan;
        else
            $this->is_force_scan = $this->forcescan;
    }

    function __get($name)
    {
        throw new lmbException("Invalid lmbMacroConfig option '$name'");
    }

    function __set($name, $value)
    {
        throw new lmbException("Invalid lmbMacroConfig option '$name'");
    }
}
