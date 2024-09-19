<?php

/**
 * A tree node containing a value and a title.
 *
 * @copyright 2005-2021 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDataTreeNode extends SwatTreeNode
{
    /**
     * The value of this node.
     *
     * The value is used for processing. It is either a string or an integer.
     *
     * @var mixed
     */
    public $value;

    /**
     * The title of this node.
     *
     * The title is used for display.
     *
     * @var string
     */
    public $title;

    /**
     * Optional content type.
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    /**
     * The sensitivity of this node.
     *
     * Used to mark this node as unselectable
     *
     * @var bool
     */
    public $sensitive = true;

    /**
     * Creates a new data node.
     *
     * @param mixed  $value        the value of the node. It is either a string or an
     *                             integer.
     * @param string $title        the title of the node
     * @param string $content_type optional content-type
     * @param mixed  $sensitive
     */
    public function __construct(
        $value,
        $title,
        $content_type = 'text/plain',
        $sensitive = true,
    ) {
        $this->value = $value;
        $this->title = $title;
        $this->content_type = $content_type;
        $this->sensitive = $sensitive;
    }
}
