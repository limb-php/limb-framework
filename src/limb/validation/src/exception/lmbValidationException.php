<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\exception;

use limb\core\src\exception\lmbException;

/**
 * Validation exception.
 * Uses in some classes where validation process is very important for performing an operation successfully
 * @see lmbActiveRecord::save()
 * @package validation
 * @version $Id: lmbValidationException.php 7486 2009-01-26 19:13:20Z
 */
class lmbValidationException extends lmbException
{
    /**
     * @var lmbErrorList
     */
    protected $error_list;

    /**
     * Constructor
     * @param string Exception message
     * @param lmbErrorList List of validation errors
     * @param array List of extra exception params
     * @param int Exception code
     */
    function __construct($message, $error_list, $params = array(), $code = 0)
    {
        $this->error_list = $error_list;

        $errors = array();
        foreach ($this->error_list as $error)
            $errors[] = $error->getReadable();

        $message .= ' Errors list : ' . implode(', ', $errors);

        parent::__construct($message, $params, $code);
    }

    function getErrorList()
    {
        return $this->error_list;
    }
}
