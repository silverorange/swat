<?php

/**
 * A tree node containing a value and a title
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDataTreeNode extends SwatTreeNode
{
    // {{{ public properties

    /**
     * The value of this node
     *
     * The value is used for processing. It is either a string or an integer.
     *
     * @var mixed
     */
    public $value;

    /**
     * The title of this node
     *
     * The title is used for display.
     *
     * @var string
     */
    public $title;

    /**
     * Optional content type
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new data node
     *
     * @param mixed $value the value of the node. It is either a string or an
     *                      integer.
     * @param string $title the title of the node.
     * @param string $content_type optional content-type
     */
    public function __construct($value, $title, $content_type = 'text/plain')
    {
        $this->value = $value;
        $this->title = $title;
        $this->content_type = $content_type;
    }

    // }}}
}
