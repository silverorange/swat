<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlHeadEntrySet.php';
require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';
require_once 'Swat/SwatStyleSheetHtmlHeadEntry.php';

/**
 * A base class for Swat user-interface elements
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

	// }}}
	// {{{ protected properties

	/**
	 * An array of HTML head entries needed by this user-interface element
	 *
	 * Entries are stored in a data object called {@link SwatHtmlHeadEntry}.
	 * This property contains an array of such objects.
	 *
	 * @var array
	 */
	protected $html_head_entries;

	// }}}
	// {{{ public function __construct()

	public function __construct()
	{
		$this->html_head_entries = new SwatHtmlHeadEntrySet();
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
		$this->html_head_entries->addEntry(
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
		$this->html_head_entries->addEntry(
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
	// {{{ abstract public function getHtmlHeadEntries()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this user-interface element
	 *
	 * Head entries are things like stylesheets and JavaScript includes that
	 * should go in the head section of HTML.
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this user-interface element.
	 */
	abstract public function getHtmlHeadEntries();

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
}

?>
