<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatUIObject.php';

/**
 * Object for rendering a single cell
 *
 * Subclasses add public class variable to store data they need for rendering.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatCellRenderer extends SwatUIObject
{
	// {{{ public properties

	/**
	 * A non-visible unique id for this cell renderer, or null
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Sensitive
	 *
	 * Whether this renderer is sensitive. If a renderer is sensitive it reacts
	 * to user input. Unsensitive renderers should display "grayed-out" to
	 * inform the user they are not sensitive. All renderers that react to
	 * user input should respect this property in their display() method.
	 *
	 * @var boolean
	 */
	public $sensitive = true;

	// }}}
	// {{{ abstract public function render()

	/**
	 * Renders this cell
	 *
	 * Renders this cell using the values currently stored in class variables.
	 *
	 * Cell renderer subclasses should implement this method to do all
	 * output neccessary to display the cell.
	 */
	abstract public function render();

	// }}}
	// {{{ public function init()

	/**
	 * Called during the init phase
	 *
	 * Sub-classes can redefine this method to perform any necessary processing.
	 */
	public function init()
	{
	}

	// }}}
	// {{{ public function process()

	/**
	 * Called during processing phase
	 *
	 * Sub-classes can redefine this method to perform any necessary processing.
	 */
	public function process()
	{
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from this cell renderer
	 *
	 * By default, cell renderers do not have messages. Subclasses may override
	 * this method to return messages.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		return array();
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets whether or not this cell renderer has messages
	 *
	 * By default, cell renderers do not have messages. Subclasses may override
	 * this method if they have messages.
	 *
	 * @return boolean true if this cell renderer has one or more messages and
	 *                  false if it does not.
	 */
	public function hasMessage()
	{
		return false;
	}

	// }}}
	// {{{ public function getPropertyNameToMap()

	/**
	 * Get a property name to use for mapping
	 *
	 * This method can be overridden by sub-classes that need to modify the
	 * name of a property mapping.  This allows cell renderers which conatin
	 * multiple SwatUIObject object to mangle property names if necessary to
	 * avoid conflicts.
	 *
	 * @param SwatUIObject $object the object containing the property that is
	 *                            being mapped. Usually this is the cell 
	 *                            renderer itself, but not necessarily. It 
	 *                            could be a UIObject within the cell renderer.
	 * @param string $name the name of the property being mapped.
	 *
	 * @return string the name of the property to actually map. This property
	 *                 should either exist as a public property of the cell
	 *                 renderer or be handled by a magic __set() method.
	 */
	public function getPropertyNameToMap(SwatUIObject $object, $name)
	{
		return $name;
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets ths inline JavaScript required by this cell renderer
	 *
	 * @return string the inline JavaScript required by this cell renderer.
	 */
	public function getInlineJavaScript()
	{
		return '';
	}

	// }}}
	// {{{ public function getBaseCSSClassNames()

	/** 
	 * Gets the base CSS class names for this cell renderer
	 *
	 * This is the recommended place for cell-renderer subclasses to add extra
	 * hard-coded CSS classes.
	 *
	 * @return array the array of base CSS class names for this cell renderer.
	 */
	public function getBaseCSSClassNames()
	{
		return array();
	}

	// }}}
	// {{{ public function getDataSpecificCSSClassNames()

	/** 
	 * Gets the data specific CSS class names for this cell renderer
	 *
	 * This is the recommended place for cell-renderer subclasses to add extra
	 * hard-coded CSS classes that depend on data-bound properties of this
	 * cell-renderer.
	 *
	 * @return array the array of base CSS class names for this cell renderer.
	 */
	public function getDataSpecificCSSClassNames()
	{
		return array();
	}

	// }}}
	// {{{ public final function getInheritanceCSSClassNames()

	/** 
	 * Gets the CSS class names of this cell renderer based on the inheritance
	 * tree for this cell renderer
	 *
	 * For example, a class with the following ancestry:
	 *
	 * SwatCellRenderer -> SwatTextCellRenderer -> SwatNullTextCellRenderer
	 *
	 * will return the following array of class names:
	 *
	 * <code>
	 * array(
	 *    'swat-cell-renderer',
	 *    'swat-text-cell-renderer',
	 *    'swat-null-text-cell-renderer',
	 * );
	 * </code>
	 *
	 * @return array the array of CSS class names based on an inheritance tree
	 *                for this cell renderer.
	 */
	public final function getInheritanceCSSClassNames()
	{
		$php_class_name = get_class($this);
		$css_class_names = array();

		// get the ancestors that are swat classes
		while (strcmp($php_class_name, 'SwatCellRenderer') !== 0) {
			if (strncmp($php_class_name, 'Swat', 4) === 0) {
				$css_class_name = strtolower(preg_replace('/([A-Z])/u',
					'-\1', $php_class_name));

				if (substr($css_class_name, 0, 1) === '-')
					$css_class_name = substr($css_class_name, 1);

				array_unshift($css_class_names, $css_class_name);
			}
			$php_class_name = get_parent_class($php_class_name);
		}

		return $css_class_names;
	}

	// }}}
}

?>
