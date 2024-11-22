<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace tests\core\cases\src;

use limb\core\src\lmbObject;

class lmbTestObject extends lmbObject
{
    public $id;
    public $title;

    protected $log = '';

    function __construct($id, $title)
    {
        parent::__construct([
            'id' => $id,
            'title' => $title
        ]);
    }

    public function getLog()
    {
        return $this->log;
    }

    protected function _mapPropertyToMethod($property)
    {
        $this->log .= ' |_mapPropertyToMethod(' . $property . ')';

        $result = parent::_mapPropertyToMethod($property);

        $this->log .= ' |result=' . $result;

        return $result;
    }

    function get($name, $default = null)
    {
        $this->log .= ' |get(' . $name . ')';

        $result = parent::get($name, $default);

        $this->log .= ' |result=' . $result;

        return $result;
    }

    function __get($property)
    {
        $this->log .= ' |__get(' . $property . ')';

        $result = parent::__get($property);

        $this->log .= ' |result=' . $result;

        return $result;
    }

    function __call($method, $args = array())
    {
        $this->log .= ' |__call(' . $method . ')';

        $result = parent::__call($method, $args);

        $this->log .= ' |result=' . $result;

        return $result;
    }
}
