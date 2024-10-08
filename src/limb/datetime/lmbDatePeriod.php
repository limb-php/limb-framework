<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\datetime;

use limb\core\exception\lmbException;

/**
 * class lmbDatePeriod.
 *
 * @package datetime
 * @version $Id: lmbDatePeriod.php 7486 2009-01-26 19:13:20Z
 */
class lmbDatePeriod
{
    protected $start;
    protected $end;

    function __construct($start, $end)
    {
        $this->start = (is_object($start)) ? $start : new lmbDateTime($start);
        $this->end = (is_object($end)) ? $end : new lmbDateTime($end);

        if ($this->end->isBefore($this->start))
            throw new lmbException('Wrong period interval', array('start' => $this->start->toString(),
                'end' => $this->end->toString()));
    }

    function toString()
    {
        return $this->start->toString() . ' - ' . $this->end->toString();
    }

    function getDuration()
    {
        return $this->end->getStamp() - $this->start->getStamp();
    }

    function getStart()
    {
        return $this->start;
    }

    function getEnd()
    {
        return $this->end;
    }

    function isEqual($period)
    {
        return $this->start->isEqual($period->getStart()) &&
            $this->end->isEqual($period->getEnd());
    }

    function includes($period)
    {
        return ($this->start->isBefore($period->getStart()) &&
            $this->end->isAfter($period->getEnd()));
    }

    function isInside($period)
    {
        return $period->includes($this);
    }

    function intersects($period)
    {
        return $this->isEqual($period)
            ||
            ($this->start->isBefore($period->getStart()) && $this->end->isAfter($period->getStart()))
            ||
            ($this->start->isAfter($period->getStart()) && $this->start->isBefore($period->getEnd()));
    }
}
