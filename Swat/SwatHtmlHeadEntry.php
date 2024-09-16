<?php

/**
 * Stores and outputs an HTML head entry
 *
 * Head entries are things like scripts and styles that belong in the HTML
 * head section.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatHtmlHeadEntry extends SwatObject
{


    /**
     * The uri of this head entry
     *
     * @var string
     */
    protected $uri = '';

    /**
     * Conditional expression used to limit display for Internet Explorer
     *
     * For example, 'lte IE 8' would display this head entry only in IE 8 and
     * below. If set to an empty string, no conditional is included in this
     * head entry's output.
     *
     * @var string
     *
     * @see getIECondition()
     * @see setIECondition()
     */
    protected $ie_condition = '';



    /**
     * Creates a new HTML head entry
     *
     * @param string  $uri the uri of the entry.
     */
    public function __construct($uri)
    {
        $this->uri = $uri;
    }



    /**
     * Displays this html head entry
     *
     * Entries are displayed differently based on type.
     *
     * @param string $uri_prefix an optional string to prefix the URI with.
     * @param string $tag an optional tag to suffix the URI with. This is
     *                     suffixed as a HTTP get var and can be used to
     *                     explicitly refresh the browser cache.
     */
    public function display($uri_prefix = '', $tag = null)
    {
        $this->openIECondition();
        $this->displayInternal($uri_prefix, $tag);
        $this->closeIECondition();
    }



    /**
     * Displays the resource referenced by this html head entry inline
     *
     * Entries are displayed differently based on type.
     *
     * @param string $path the path containing the resource files.
     */
    public function displayInline($path)
    {
        $this->openIECondition();
        $this->displayInlineInternal($path);
        $this->closeIECondition();
    }



    /**
     * Gets the URI of this HTML head entry
     *
     * @return string the URI of this HTML head entry.
     */
    public function getUri()
    {
        return $this->uri;
    }



    /**
     * Gets the type of this HTML head entry
     *
     * @return string the type of this HTML head entry.
     */
    public function getType()
    {
        return get_class($this);
    }



    /**
     * Gets the conditional expression used to limit display for Internet
     * Explorer
     *
     * @return string the conditional expression used to display this head
     *                entry. If null or an empty string, no conditional is
     *                used to display this entry.
     */
    public function getIECondition()
    {
        return $this->ie_conditional;
    }



    /**
     * Sets the conditional expression used to limit display for Internet
     * Explorer
     *
     * For example, 'lte IE 8' would display this head entry only in IE 8 and
     * below. If set to an empty string, no conditional is included in this
     * head entry's output.
     *
     * @param string $condition the conditional expression to use. Use an
     *                          empty string for no conditional (display in
     *                          all IE versions).
     */
    public function setIECondition($condition)
    {
        $this->ie_condition = (string) $condition;
    }



    /**
     * Displays this html head entry
     *
     * Entries are displayed differently based on type.
     *
     * @param string $uri_prefix an optional string to prefix the URI with.
     * @param string $tag an optional tag to suffix the URI with. This is
     *                     suffixed as a HTTP get var and can be used to
     *                     explicitly refresh the browser cache.
     */
    abstract protected function displayInternal($uri_prefix = '', $tag = null);



    /**
     * Displays the resource referenced by this html head entry inline
     *
     * Entries are displayed differently based on type.
     *
     * @param string $path the path containing the resource files.
     */
    abstract protected function displayInlineInternal($path);



    /**
     * Renders the opening tag for the IE condditional if an IE conditional
     * is set
     *
     * @see getIECondition()
     * @see setIECondition()
     */
    protected function openIECondition()
    {
        if ($this->ie_condition != '') {
            // Double dashes are invalid inside comments.
            $ie_condition = str_replace('--', '—', $this->ie_condition);
            printf('<!--[if %s]>', SwatString::minimizeEntities($ie_condition));
        }
    }



    /**
     * Renders the closing tag for the IE condditional if an IE conditional
     * is set
     *
     * @see getIECondition()
     * @see setIECondition()
     */
    protected function closeIECondition()
    {
        if ($this->ie_condition != '') {
            echo '<![endif]-->';
        }
    }

}
