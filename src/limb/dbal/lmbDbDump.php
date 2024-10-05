<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\dbal;

use limb\dbal\dump\lmbSQLDumpLoader;
use limb\toolkit\lmbToolkit;

/**
 * class lmbDbDump.
 *
 * @package dbal
 * @version $Id: lmbDbDump.php 8070 2010-01-20 08:19:23Z
 */
class lmbDbDump
{
    protected $file;
    protected $loader;
    /**
     * @var \limb\dbal\src\drivers\lmbDbConnectionInterface
     */
    protected $connection;

    function __construct($file = null, $connection = null)
    {
        $this->file = $file;

        if ($connection)
            $this->connection = $connection;
        else
            $this->connection = lmbToolkit::instance()->getDefaultDbConnection();
    }

    function load($file = null)
    {
        $type = $this->connection->getType();

        $default_loader = lmbSQLDumpLoader::class;
        $loaderClass = 'limb\\dbal\\src\\dump\\lmb' . ucfirst($type) . 'DumpLoader';

        if (!class_exists($loaderClass, true))
            $loaderClass = $default_loader;

        $file = ($file) ?? $this->file;
        $this->loader = new $loaderClass($file);
        $this->loader->execute($this->connection);

        $this->connection->getDatabaseInfo()->loadTables();
    }

    function clean()
    {
        $this->loader->cleanTables($this->connection);
    }
}


