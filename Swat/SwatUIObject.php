<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';
require_once 'Swat/SwatStyleSheetHtmlHeadEntry.php';

/**
 * A base class for Swat user-interface elements
 *
 * TODO: describe our conventions on how CSS classes and XHTML ids are
 * displayed.
 *
 * @package   Swat
 * @copyright 2006 silverorange
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
	 * A user-specified array of CSS classes that are applied to this
	 * user-interface object
	 *
	 * See the class-level documentation for SwatUIObject for details on how
	 * CSS classes and XHTML ids are displayed on user-interface objects.
	 *
	 * @var array
	 */
	public $classes = array();

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
	public function addStyleSheet($stylesheet, $display_order = 0)
	{
		if ($this->html_head_entry_set === null)
			throw new SwatException(sprintf("Child class '%s' did not ".
				'instantiate a HTML head entry set. This should be done in  '.
				'the constructor either by calling parent::__construct() or '.
				'by creating a new HTML head entry set.', get_class($this)));

		$this->html_head_entry_set->addEntry(
			new SwatStyleSheetHtmlHeadEntry($stylesheet, $display_order));
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
	public function addJavaScript($java_script, $display_order = 0)
	{
		if ($this->html_head_entry_set === null)
			throw new SwatException(sprintf("Child class '%s' did not ".
				'instantiate a HTML head entry set. This should be done in  '.
				'the constructor either by calling parent::__construct() or '.
				'by creating a new HTML head entry set.', get_class($this)));

		$this->html_head_entry_set->addEntry(
			new SwatJavaScriptHtmlHeadEntry($java_script, $display_order));
	}

	// }}}
	// {{{ public function getUniqueId()

	/**
	 * Generates a unique id
	 *
	 * Gets the an id that may be used for the id property of this widget.
	 * Ids are auto-generated.
	 *
	 * @return string a unique identifier.
	 */
	protected function getUniqueId()
	{
		static $counter = 0;

		$counter++;

		return get_class($this).$counter;
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
	 * @see SwatContainer::getFirstDescendant()
	 */
	public function getFirstAncestor($class_name)
	{
		if (!class_exists($class_name))
			return null;

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
	 * Gets the SwatHtmlHeadEntry objects needed by this control
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this control.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		return new SwatHtmlHeadEntrySet($this->html_head_entry_set);
	}

	// }}}
	// {{{ public function __toString()

	/**
	 * Gets this object as a string
	 *
	 * @see SwatObject::__toString()
	 * @return string this object represented as a string.
	 */
	public function __toString()
	{
		// prevent recusrion up the widget tree for UI objects
		$parent = $this->parent;
		$this->parent = get_class($parent);

		return parent::__toString();

		// set parent back again
		$this->parent = $parent;
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

	// }}}
	// {{{ protected function getCSSClassString()

	/**
	 * Gets the string representation of this user-interface object's list of
	 * CSS classes
	 *
	 * @return string the string representation of the CSS classes that are
	 *                 applied to this user-interface object.
	 *
	 * @see SwatUIObject::getCSSClassNames()
	 */
	protected function getCSSClassString()
	{
		return implode(' ', $this->getCSSClassNames());
	}

	// }}}
}

?>
