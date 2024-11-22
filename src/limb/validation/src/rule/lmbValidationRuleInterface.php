<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\src\rule;

/**
 * Interface for defining rules to validate against
 * @package validation
 * @version $Id: lmbValidationRuleInterface.php 7486 2009-01-26 19:13:20Z
 */
interface lmbValidationRuleInterface
{
    /**
     * Performs validation
     * rules must call {@link lmbErrorList::addError()} to report about error
     * @param \limb\core\src\lmbSetInterface $datasource Datasource to validate
     * @param \limb\validation\src\lmbErrorList $error_list List of validation errors
     * @return void
     * @see lmbErrorList::addError()
     */
    function validate($datasource, $error_list);
}
