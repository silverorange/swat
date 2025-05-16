<?php

/**
 * Entry for the navbar navigation tool.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatNavBar
 */
class SwatNavBarEntry extends SwatObject
{
    // {{{ public properties

    /**
     * The visible title of this entry.
     *
     * @var string
     */
    public $title;

    /**
     * The the web address that this navbar entry points to.
     *
     * This property is optional. If it is not present this entry will not
     * display as a hyperlink.
     *
     * @var string
     */
    public $link;

    /**
     * Optional content type for title.
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $content_type = 'text/plain';

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new navbar entry.
     *
     * @param string $title        the title of this entry
     * @param string $link         the web address this entry points to
     * @param string $content_type an optional content type for the entry title
     */
    public function __construct(
        $title,
        $link = null,
        $content_type = 'text/plain',
    ) {
        $this->title = $title;
        $this->link = $link;
        $this->content_type = $content_type;
    }

    // }}}
}
