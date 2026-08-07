<?php

/**
 * Database field.
 *
 * Data class to represent a database field, a (name, type) pair.
 *
 * @copyright 2005-2026 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDBField extends SwatObject
{
    /**
     * The name of the database field.
     */
    public string $name;

    /**
     * The type of the database field.
     *
     * Any standard MDB2 datatype is valid here.
     */
    public string $type;

    /**
     * @param string $field        a string representation of a database field in the
     *                             form `[type:]name` where `name` is the name of the database
     *                             field and the optional `type` is any standard MDB2 datatype
     * @param string $default_type The type to use by default if it is not
     *                             specified in the `$field` string. Any standard MDB2 datatype
     *                             is valid here.
     */
    public function __construct(string $field, string $default_type = 'text')
    {
        $x = explode(':', $field);

        if (isset($x[1])) {
            $this->name = $x[1];
            $this->type = $x[0];
        } else {
            $this->name = $x[0];
            $this->type = $default_type;
        }
    }

    /**
     * Get the field as a string.
     *
     * @return string a string representation of a database field in the
     *                form `type:name` where `name` is the name of the database
     *                field and `type` is a standard MDB2 datatype
     */
    public function __toString(): string
    {
        return $this->type . ':' . $this->name;
    }
}
