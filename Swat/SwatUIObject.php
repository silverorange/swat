<?php

/**
 * A base class for Swat user-interface elements
 *
 * TODO: describe our conventions on how CSS classes and XHTML ids are
 * displayed.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatUIObject extends SwatObject
{
    // {{{ public properties

    /**
     * The object which contains this object
     *
     * @var SwatUIObject
     */
    public $parent = null;

    /**
     * Visible
     *
     * Whether this UI object is displayed. All UI objects should respect this.
     *
     * @var boolean
     *
     * @see SwatUIObject::isVisible()
     */
    public $visible = true;

    /**
     * A user-specified array of CSS classes that are applied to this
     * user-interface object
     *
     * See the class-level documentation for SwatUIObject for details on how
     * CSS classes and XHTML ids are displayed on user-interface objects.
     *
     * @var array
     */
    public $classes = [];

    /**
     * Whether to clear out any Swat-defined classes (and only use those
     * supplied via the <property name="classes[]">...</property> tag).
     *
     * @var bool
     */
    public bool $clear_default_classes = false;

    /**
     * A user-specified key-value array of data attributes that are applied
     * to this user-interface object.
     *
     * @var array
     */
    public $data = [];

    // }}}
    // {{{ protected properties

    /**
     * A set of HTML head entries needed by this user-interface element
     *
     * Entries are stored in a data object called {@link SwatHtmlHeadEntry}.
     * This property contains a set of such objects.
     *
     * @var SwatHtmlHeadEntrySet
     */
    protected $html_head_entry_set;

    // }}}
    // {{{ public function __construct()

    public function __construct()
    {
        $this->html_head_entry_set = new SwatHtmlHeadEntrySet();
    }

    // }}}
    // {{{ public function addStyleSheet()

    /**
     * Adds a stylesheet to the list of stylesheets needed by this
     * user-iterface element
     *
     * @param string  $stylesheet the uri of the style sheet.
     * @param integer $display_order the relative order in which to display
     *                                this stylesheet head entry.
     */
    public function addStyleSheet($stylesheet)
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatStyleSheetHtmlHeadEntry($stylesheet),
        );
    }

    // }}}
    // {{{ public function addJavaScript()

    /**
     * Adds a JavaScript include to the list of JavaScript includes needed
     * by this user-interface element
     *
     * @param string  $java_script the uri of the JavaScript include.
     * @param integer $display_order the relative order in which to display
     *                                this JavaScript head entry.
     */
    public function addJavaScript($java_script)
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatJavaScriptHtmlHeadEntry($java_script),
        );
    }

    // }}}
    // {{{ public function addExternalJavaScript()

    public function addExternalJavaScript($url)
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatExternalJavaScriptHtmlHeadEntry($url),
        );
    }

    // }}}
    // {{{ public function addComment()

    /**
     * Adds a comment to the list of HTML head entries needed by this user-
     * interface element
     *
     * @param string  $comment the contents of the comment to include.
     */
    public function addComment($comment)
    {
        if ($this->html_head_entry_set === null) {
            throw new SwatException(
                sprintf(
                    "Child class '%s' did not " .
                        'instantiate a HTML head entry set. This should be done in  ' .
                        'the constructor either by calling parent::__construct() or ' .
                        'by creating a new HTML head entry set.',
                    get_class($this),
                ),
            );
        }

        $this->html_head_entry_set->addEntry(
            new SwatCommentHtmlHeadEntry($comment),
        );
    }

    // }}}
    // {{{ public function addInlineScript()

    public function addInlineScript($script)
    {
        $this->inline_scripts->add($script);
    }

    // }}}
    // {{{ public function getFirstAncestor()

    /**
     * Gets the first ancestor object of a specific class
     *
     * Retrieves the first ancestor object in the parent path that is a
     * descendant of the specified class name.
     *
     * @param string $class_name class name to look for.
     *
     * @return mixed the first ancestor object or null if no matching ancestor
     *                is found.
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstAncestor($class_name)
    {
        if (!class_exists($class_name)) {
            return null;
        }

        if ($this->parent === null) {
            $out = null;
        } elseif ($this->parent instanceof $class_name) {
            $out = $this->parent;
        } else {
            $out = $this->parent->getFirstAncestor($class_name);
        }

        return $out;
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this UI object
     *
     * If this UI object is not visible, an empty set is returned to reduce
     * the number of required HTTP requests.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                               this UI object.
     */
    public function getHtmlHeadEntrySet()
    {
        if ($this->isVisible()) {
            $set = new SwatHtmlHeadEntrySet($this->html_head_entry_set);
        } else {
            $set = new SwatHtmlHeadEntrySet();
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects that MAY needed by this UI object
     *
     * Even if this object is not displayed, all the resources that may be
     * required to display it are returned.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that MAY be
     *                               needed this UI object.
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        return new SwatHtmlHeadEntrySet($this->html_head_entry_set);
    }

    // }}}
    // {{{ public function isVisible()

    /**
     * Gets whether or not this UI object is visible
     *
     * Looks at the visible property of the ancestors of this UI object to
     * determine if this UI object is visible.
     *
     * @return boolean true if this UI object is visible and false if it is not.
     *
     * @see SwatUIObject::$visible
     */
    public function isVisible()
    {
        if ($this->parent instanceof SwatUIObject) {
            return $this->parent->isVisible() && $this->visible;
        } else {
            return $this->visible;
        }
    }

    // }}}
    // {{{ public function __toString()

    /**
     * Gets this object as a string
     *
     * @see SwatObject::__toString()
     * @return string this object represented as a string.
     */
    public function __toString(): string
    {
        // prevent recursion up the widget tree for UI objects
        $parent = $this->parent;
        $this->parent = get_class($parent);

        return parent::__toString();

        // set parent back again
        $this->parent = $parent;
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * To perform a shallow copy, use PHP's clone keyword.
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                           objects in the UI tree. This can be used to
     *                           ensure object ids are unique for a copied UI
     *                           tree. If not specified, UI objects in the
     *                           returned copy will have identical ids to the
     *                           original tree. This can cause problems if both
     *                           the original and copy are displayed during the
     *                           same request.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                       object. The returned UI object does not have a
     *                       parent and can be inserted into another UI tree.
     */
    public function copy($id_suffix = '')
    {
        $copy = clone $this;
        $copy->parent = null;
        return $copy;
    }

    // }}}
    // {{{ protected function getCSSClassNames()

    /**
     * Gets the array of CSS classes that are applied to this user-interface
     * object
     *
     * User-interface objects aggregate the list of user-specified classes and
     * may add static CSS classes of their own in this method.
     *
     * @return array the array of CSS classes that are applied to this
     *                user-interface object.
     *
     * @see SwatUIObject::getCSSClassString()
     */
    protected function getCSSClassNames()
    {
        return $this->classes;
    }

    protected function getDataAttributes()
    {
        $data_attributes = [];

        foreach ($this->data as $key => $value) {
            $data_attributes["data-{$key}"] = $value;
        }

        return $data_attributes;
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets inline JavaScript used by this user-interface object
     *
     * @return string inline JavaScript used by this user-interface object.
     */
    protected function getInlineJavaScript()
    {
        return '';
    }

    // }}}
    // {{{ protected final function getCSSClassString()

    /**
     * Gets the string representation of this user-interface object's list of
     * CSS classes
     *
     * @return string the string representation of the CSS classes that are
     *                 applied to this user-interface object. If this object
     *                 has no CSS classes, null is returned rather than a blank
     *                 string.
     *
     * @see SwatUIObject::getCSSClassNames()
     */
    final protected function getCSSClassString()
    {
        $class_string = null;

        $class_names = $this->getCSSClassNames();
        if (count($class_names) > 0) {
            $class_string = implode(' ', $class_names);
        }

        return $class_string;
    }

    // }}}
    // {{{ protected final function getUniqueId()

    /**
     * Generates a unique id for this UI object
     *
     * Gets a unique id that may be used for the id property of this UI object.
     * Each time this method id called, a new unique identifier is generated so
     * you should only call this method once and set it to a property of this
     * object.
     *
     * @return string a unique identifier for this UI object.
     */
    final protected function getUniqueId()
    {
        // Because this method is not static, this counter will start at zero
        // for each class.
        static $counter = 0;

        $counter++;

        return get_class($this) . $counter;
    }

    // }}}
}
