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
     */
    public int|string|null $value;

    /**
     * The title of this node.
     *
     * The title is used for display.
     */
    public string $title;

    /**
     * Optional content type.
     *
     * Default text/plain, use text/xml for XHTML fragments.
     */
    public string $content_type = 'text/plain';

    /**
     * The sensitivity of this node.
     *
     * Used to mark this node as unselectable
     */
    public bool $sensitive = true;

    /**
     * Creates a new data node.
     *
     * @param int|string|null $value        the value of the node. It is either a string or an
     *                                      integer.
     * @param string          $title        the title of the node
     * @param string          $content_type optional content-type
     * @param mixed           $sensitive
     */
    public function __construct(
        int|string|null $value,
        string $title,
        string $content_type = 'text/plain',
        bool $sensitive = true,
    ) {
        $this->value = $value;
        $this->title = $title;
        $this->content_type = $content_type;
        $this->sensitive = $sensitive;
    }
}
