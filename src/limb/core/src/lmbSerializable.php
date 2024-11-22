<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\core\src;

/**
 * class lmbSerializable.
 *
 * @package core
 * @version $Id$
 */
class lmbSerializable
{
    protected $subject;
    protected $serialized;

    function __construct($subject)
    {
        $this->subject = $subject;
    }

    function getSubject()
    {
        if ($this->serialized) {
            $this->subject = unserialize($this->serialized);
            $this->serialized = null;
        }
        return $this->subject;
    }

    function __sleep()
    {
        // here we're assuming that if object was lazy loaded with getSubject
        // then serialized property is null and we need to serialize subject,
        // otherwise there's no need to serialize it again, this way we don't need
        // to implement __wakeup method
        if (is_null($this->serialized)) {
            $this->serialized = serialize($this->subject);
        }

        return array('serialized');
    }

    static function serialize($raw_data)
    {
        $container = new lmbSerializable($raw_data);
        return serialize($container);
    }

    static function unserialize($serialized_data)
    {
        $container = unserialize($serialized_data);
        return $container->getSubject();
    }
}

