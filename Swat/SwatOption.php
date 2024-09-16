<?php

/**
 * A simple class for storing options used in various Swat controls
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatOption extends SwatObject
{


    /**
     * Option title
     *
     * @var string
     */
    public $title = null;

    /**
     * Optional content type for title
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    /**
     * Option value
     *
     * @var mixed
     */
    public $value = null;



    /**
     * Creates an option
     *
     * @param mixed $value the value of this option.
     * @param string $title the user visible title of this option.
     * @param string $content_type an optional content type of for this
     *                              option's title. The content type defaults
     *                              to 'text/plain'.
     */
    public function __construct($value, $title, $content_type = 'text/plain')
    {
        $this->value = $value;
        $this->title = $title;
        $this->content_type = $content_type;
    }

}
