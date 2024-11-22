<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\cli\src;

/**
 * abstract class lmbCliBaseCmd.
 *
 * @package cli
 * @version $Id$
 */
abstract class lmbCliBaseCmd
{
    /**
     * @var lmbCliResponse
     */
    protected $output;

    function __construct($output)
    {
        $this->output = $output;
    }

    function help($argv)
    {
        return 0;
    }

    function execute($argv)
    {
        return 0;
    }

    protected function _error($msg)
    {
        $this->output->write($msg);
        exit(1);
    }
}
