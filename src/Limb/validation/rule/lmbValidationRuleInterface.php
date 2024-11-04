<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\validation\rule;

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
     * @param \limb\core\lmbSetInterface $datasource Datasource to validate
     * @param \limb\validation\lmbErrorList $error_list List of validation errors
     * @return void
     * @see lmbErrorList::addError()
     */
    function validate($datasource, $error_list);
}
