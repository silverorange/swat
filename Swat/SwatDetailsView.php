<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatDetailsViewField.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A widget to display field-value pairs
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsView extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * An object containing values to display
	 *
	 * A data object contains properties and values. The SwatDetailsViewField
	 * objects inside this SwatDetailsView contain mappings between their
	 * properties and the properties of this data object. This allows the
	 * to display specific values from this data object.
	 *
	 * @var object
	 *
	 * @see SwatDetailsViewField
	 */
	public $data = null;

	// }}}
	// {{{ private properties

	/**
	 * An array of fields to be displayed by this details view
	 *
	 * @var array
	 */
	private $fields = array();

	// }}}
	// {{{ public function __construct()
	/**
	 * Creates a new details view
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('packages/swat/styles/swat-details-view.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this details-view
	 *
	 * This initializes all fields.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		parent::init();

		foreach ($this->fields as $field)
			$field->init();
	}

	// }}}
	// {{{ public function appendField()

	/**
	 * Appends a field to this details view
	 *
	 * @param SwatDetailViewField $field the field to append
	 */
	public function appendField(SwatDetailsViewField $field)
	{
		$this->fields[] = $field;

		$field->view = $this;
		$field->parent = $this;
	}

	// }}}
	// {{{ public function getFieldCount()

	/**
	 * Gets the number of fields of this details view
	 *
	 * @return integer the number of fields of this details view.
	 */
	public function getFieldCount()
	{
		return count($this->fields);
	}

	// }}}
	// {{{ public function getFields()

	/**
	 * Get the fields from this details view
	 *
	 * @return array a reference to an array of fields from this view.
	 */
	public function &getFields()
	{
		return $this->fields;
	}

	// }}}
	// {{{ public function getField()

	/**
	 * Get a reference to a field
	 *
	 * @ return SwatDetailsViewField Matching field
	 */
	public function getField($id)
	{
		$fields = $this->getFields();
		foreach ($fields as $field)
			if ($id == $field->id)
				return $field;

		throw new SwatException("Field with an id of '$id' not found.");
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this details view
	 *
	 * Displays details view as tabular XHTML.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$table_tag = new SwatHtmlTag('table');
		$table_tag->id = $this->id;
		$table_tag->class = $this->getCSSClassString();

		$table_tag->open();
		$this->displayContent();
		$table_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object to this object
	 *
	 * @param SwatDetailsViewField $child the child object to add to this
	 *                                     object.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent::addChild()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatDetailsViewField) {
			$this->appendField($child);
		} else {
			$class_name = get_class($child);

			throw new SwatInvalidClassException(
				"Unable to add '{$class_name}' object to SwatDetailsView. ".
				'Only SwatDetailsViewField objects may be nested within '.
				'SwatDetailsView objects.', 0, $child);
		}
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this details view
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this details view.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		foreach ($this->fields as $field)
			$set->addEntrySet($field->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this details view. If
	 *                descendant objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		foreach ($this->fields as $field) {
			if ($class_name === null || $field instanceof $class_name) {
				if ($field->id === null)
					$out[] = $field;
				else
					$out[$field->id] = $field;
			}

			if ($field instanceof SwatUIParent)
				$out = array_merge($out, $field->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		foreach ($this->fields as $field) {
			if ($field instanceof $class_name) {
				$out = $field;
				break;
			}

			if ($field instanceof SwatUIParent) {
				$out = $field->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this details view.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * details view.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates(array $states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	// }}}
	// {{{ public function copy()

	/**
	 * Performs a deep copy of the UI tree starting with this UI object
	 *
	 * @param string $id_prefix optional. A prefix to prepend to copied UI
	 *                           objects in the UI tree.
	 *
	 * @return SwatUIObject a deep copy of the UI tree starting with this UI
	 *                       object.
	 *
	 * @see SwatUIObject::copy()
	 */
	public function copy($id_prefix = '')
	{
		$copy = parent::copy($id_prefix);

		foreach ($this->fields as $key => $field) {
			$copy_field = $field->copy($id_prefix);
			$copy_field->parent = $copy;
			$copy->fields[$key] = $copy_field;
		}

		return $copy;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this details view
	 *
	 * @return array the array of CSS classes that are applied to this details
	 *                view.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-details-view');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript needed by this details view as well as any
	 * inline JavaScript needed by fields
	 *
	 * @return string inline JavaScript needed by this details view.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = '';

		foreach ($this->fields as $field) {
			$field_javascript = $field->getRendererInlineJavaScript();
			if (strlen($field_javascript) > 0)
				$javascript.= "\n".$field_javascript;
		}

		foreach ($this->fields as $field) {
			$field_javascript = $field->getInlineJavaScript();
			if (strlen($field_javascript) > 0)
				$javascript.= "\n".$field_javascript;
		}

		return $javascript;
	}

	// }}}
	// {{{ private function displayContent()

	/**
	 * Displays each field of this view
	 *
	 * Displays each field of this view as an XHTML table row.
	 */
	private function displayContent()
	{
		$count = 0;

		foreach ($this->fields as $field) {
			$count++;
			$odd = ($count % 2 == 1);
			$field->display($this->data, $odd);
		}
	}

	// }}}
}

?>
