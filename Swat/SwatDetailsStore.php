<?php

/**
 * A data structure that can be used with the SwatDetailsView.
 *
 * A new details store is empty by default unless is it initialized with
 * another object.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @todo      Document parsePath().
 */
class SwatDetailsStore extends SwatObject
{
    // {{{ private properties

    /**
     * The base object for this details store.
     *
     * Properties of this details store are taken from this base object unless
     * they are manually specified.
     *
     * @var stdClass
     */
    private $base_object;

    /**
     * Manually set data of this details store.
     *
     * @var array
     */
    private $data = [];

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new details store.
     *
     * @param stdClass $base_object optional. The object to initialize this
     *                              details store with. Properties in this
     *                              details store will be taken from the base
     *                              object unless they are manually set on
     *                              this details store.
     */
    public function __construct($base_object = null)
    {
        if ($base_object !== null && is_object($base_object)) {
            $this->base_object = $base_object;
        }
    }

    // }}}
    // {{{ public function __get()

    /**
     * Gets a property of this details store.
     *
     * Properties are retrieved in the following manner:
     * 1. If the property name contains a dot (.) the results of
     *    {@link SwatDetailsStore::parsePath()} are returned.
     * 2. If the property was manually set on this details store, the manually
     *    set value is returned.
     * 3. If the property exists on the base object of this details store, the
     *    the base object's property value is returned.
     * 4. The property could not be found in this details store and an
     *    exception is thrown.
     *
     * @param string $name the name of the property to get
     *
     * @return mixed the value of the property
     *
     * @throws SwatInvalidPropertyException if the property does not exist in
     *                                      this details store
     */
    public function __get($name)
    {
        if (mb_strpos($name, '.') !== false) {
            return $this->parsePath($this, $name);
        }

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        if ($this->base_object !== null) {
            if (property_exists($this->base_object, $name)) {
                return $this->base_object->{$name};
            }

            if (method_exists($this->base_object, '__get')) {
                return $this->base_object->{$name};
            }
        }

        throw new SwatInvalidPropertyException(
            "Property '{$name}' does not exist in details store.",
            0,
            $this,
            $name,
        );
    }

    // }}}
    // {{{ public function __set()

    /**
     * Manually sets a property of this details store.
     *
     * If a base object is used, it is not modified by this method. Manually
     * set properties override properties of the base object when calling
     * {@link SwatDetailsStore::__get()} or {@link SwatDetailsStore::__isset()}.
     *
     * @param string $name  the name of the property to set
     * @param mixed  $value the value of the property
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    // }}}
    // {{{ public function __isset()

    /**
     * Gets whether or not a property is set for this details store.
     *
     * First, the manually set properties are checked. Then the properties of
     * the base object are checked if there is a base object.
     *
     * @param string $name the name of the property to check
     *
     * @return bool true if the property is set for this details store and
     *              false if it is not
     */
    public function __isset($name)
    {
        $is_set = isset($this->data[$name]);

        if (!$is_set && $this->base_object !== null) {
            $is_set = isset($this->base_object->{$name});
        }

        return $is_set;
    }

    // }}}
    // {{{ private function parsePath()

    private function parsePath($object, $path)
    {
        $pos = mb_strpos($path, '.');
        $name = mb_substr($path, 0, $pos);
        $rest = mb_substr($path, $pos + 1);
        $sub_object = $object->{$name};

        if ($sub_object === null) {
            return null;
        }
        if (mb_strpos($rest, '.') === false) {
            return $sub_object->{$rest};
        }

        return $this->parsePath($sub_object, $rest);
    }

    // }}}
}
