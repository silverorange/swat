<?php

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

	/**
	 * Renders this cell
	 *
	 * Renders this cell using the values currently stored in class variables.
	 *
	 * Cell renderer subclasses should implement this method to do all
	 * output neccessary to display the cell.
	 */
	abstract public function render();

	/**
	 * Called during processing phase
	 *
	 * Sub-classes can redefine this method to perform any necessary processing.
	 */
	public function process()
	{
	}

	/**
	 * Gets TH-tag attributes
	 *
	 * Sub-classes can redefine this to set attributes on the TH tag.
	 *
	 * The returned array is of the form 'attribute' => value.
	 *
	 * @return array an array of attributes to apply to the TH tag of the
	 *                column that contains this cell renderer.
	 */
	public function getThAttributes()
	{
		return array('class' => $this->getCSSClassName());
	}

	/**
	 * Gets TD-tag attributes
	 *
	 * Sub-classes can redefine this to set attributes on the TD tag.
	 *
	 * The returned array is of the form 'attribute' => value.
	 *
	 * @return array an array of attributes to apply to the TD tag of this cell
	 *                renderer.
	 */
	public function getTdAttributes()
	{
		return array('class' => $this->getCSSClassName());
	}

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this cell renderer
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this cell renderer.
	 *
	 * @see SwatUIObject::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

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

	private function getCSSClassName()
	{
		$php_class_name = get_class($this);

		// get the first ancestor that is a swat class
		while (strncmp($php_class_name, 'Swat', 4) !== 0)
			 $php_class_name = get_parent_class($php_class_name);

		$css_class_name = strtolower(ereg_replace('([A-Z])', '-\1', $php_class_name));

		if (substr($css_class_name, 0, 1) === '-')
			$css_class_name = substr($css_class_name, 1);

		return $css_class_name;
	}
}

?>
