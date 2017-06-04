<?php

/**
 * A widget to display field-value pairs
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
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

	/**
	 * The fields of this details-view indexed by their unique identifier
	 *
	 * A unique identifier is not required so this array does not necessarily
	 * contain all fields in the view. It serves as an efficient data
	 * structure to lookup fields by their id.
	 *
	 * The array is structured as id => field reference.
	 *
	 * @var array
	 */
	private $fields_by_id = array();

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

		$this->addStyleSheet('packages/swat/styles/swat-details-view.css');
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
	// {{{ public function process()

	/**
	 * Processes this details-view
	 */
	public function process()
	{
		parent::process();

		foreach ($this->fields as $field)
			$field->process();
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
	// {{{ public function appendField()

	/**
	 * Appends a field to this details view
	 *
	 * The field is appended below existing fields.
	 *
	 * @param SwatDetailViewField $field the field to append.
	 *
	 * @throws SwatDuplicateIdException if the field has the same id as a field
	 *                                  already in this details-view.
	 */
	public function appendField(SwatDetailsViewField $field)
	{
		$this->insertField($field);
	}

	// }}}
	// {{{ public function insertFieldBefore()

	/**
	 * Inserts a field before an existing field in this details-view
	 *
	 * @param SwatDetailsViewField $field the field to insert.
	 * @param SwatDetailsViewField $reference_field the field before which the
	 *                                             field will be inserted.
	 *
	 * @throws SwatWidgetNotFoundException if the reference field does not
	 *                                     exist in this details-view.
	 * @throws SwatDuplicateIdException if the field has the same id as a field
	 *                                  already in this details-view.
	 */
	public function insertFieldBefore(SwatDetailsViewField $field,
		SwatDetailsViewField $reference_field)
	{
		$this->insertField($field, $reference_field, false);
	}

	// }}}
	// {{{ public function insertFieldAfter()

	/**
	 * Inserts a field after an existing field in this details-view
	 *
	 * @param SwatDetailsViewField $field the field to insert.
	 * @param SwatDetailsViewField $reference_field the field after which the
	 *                                             field will be inserted.
	 *
	 * @throws SwatWidgetNotFoundException if the reference field does not
	 *                                     exist in this details-view.
	 * @throws SwatDuplicateIdException if the field has the same id as a field
	 *                                  already in this details-view.
	 */
	public function insertFieldAfter(SwatDetailsViewField $field,
		SwatDetailsViewField $reference_field)
	{
		$this->insertField($field, $reference_field, true);
	}

	// }}}
	// {{{ public function getFieldCount()

	/**
	 * Gets the number of fields of this details-view
	 *
	 * @return integer the number of fields in  this details-view.
	 */
	public function getFieldCount()
	{
		return count($this->fields);
	}

	// }}}
	// {{{ public function getFields()

	/**
	 * Get the fields of this details-view
	 *
	 * @return array the fields of this details-view.
	 */
	public function getFields()
	{
		return $this->fields;
	}

	// }}}
	// {{{ public function getField()

	/**
	 * Gets a field in this details view by the field's id
	 *
	 * @param string $id the id of the field in this details-view to get.
	 *
	 * @return SwatDetailsViewField the field in this details-view with the
	 *                               specified id.
	 *
	 * @throws SwatWidgetNotFoundException if no field with the given id exists
	 *                                     in this details view.
	 */
	public function getField($id)
	{
		if (!array_key_exists($id, $this->fields_by_id))
			throw new SwatWidgetNotFoundException(
				"Field with an id of '{$id}' not found.");

		return $this->fields_by_id[$id];
	}

	// }}}
	// {{{ public function hasField()

	/**
	 * Whether a field exists in this details view
	 *
	 * @param string $id the id of the field in this details-view to check.
	 *
	 * @return boolean whether a field with the specified id exists.
	 */
	public function hasField($id)
	{
		return array_key_exists($id, $this->fields_by_id);
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
	 * Gets the SwatHtmlHeadEntry objects needed by this details-view
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this details-view.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		foreach ($this->fields as $field) {
			$set->addEntrySet($field->getHtmlHeadEntrySet());
		}

		return $set;
	}

	// }}}
	// {{{ public function getAvailableHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects that may be needed by this
	 * details-view
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
	 *                               needed by this details-view.
	 *
	 * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
	 */
	public function getAvailableHtmlHeadEntrySet()
	{
		$set = parent::getAvailableHtmlHeadEntrySet();

		foreach ($this->fields as $field) {
			$set->addEntrySet($field->getAvailableHtmlHeadEntrySet());
		}

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
	 * @param string $id_suffix optional. A suffix to append to copied UI
	 *                           objects in the UI tree.
	 *
	 * @return SwatUIObject a deep copy of the UI tree starting with this UI
	 *                       object.
	 *
	 * @see SwatUIObject::copy()
	 */
	public function copy($id_suffix = '')
	{
		$copy = parent::copy($id_suffix);

		$copy->fields_by_id = array();
		foreach ($this->fields as $key => $field) {
			$copy_field = $field->copy($id_suffix);
			$copy_field->parent = $copy;
			$copy->fields[$key] = $copy_field;
			if ($copy_field->id !== null) {
				$copy->fields_by_id[$copy_field->id] = $copy_field;
			}
		}

		return $copy;
	}

	// }}}
	// {{{ protected function validateField()

	/**
	 * Ensures a field added to this details-view is valid for this
	 * details-view
	 *
	 * @param SwatDetailsViewField $field the field to check.
	 *
	 * @throws SwatDuplicateIdException if the field has the same id as a
	 *                                  field already in this details-view.
	 */
	protected function validateField(SwatDetailsViewField $field)
	{
		// note: This works because the id property is set before children are
		// added to parents in SwatUI.
		if ($field->id !== null) {
			if (array_key_exists($field->id, $this->fields_by_id))
				throw new SwatDuplicateIdException(
					"A field with the id '{$field->id}' already exists ".
					'in this details-view.',
					0, $field->id);
		}
	}

	// }}}
	// {{{ protected function insertField()

	/**
	 * Helper method to insert fields into this details-view
	 *
	 * @param SwatDetailsViewField $field the field to insert.
	 * @param SwatDetailsViewField $reference_field optional. An existing field
	 *                                               within this details-view to
	 *                                               which the inserted field
	 *                                               is relatively positioned.
	 *                                               If not specified, the
	 *                                               field is inserted at the
	 *                                               beginning or the end of
	 *                                               this details-view's list of
	 *                                               fields.
	 * @param boolean $after optional. If true and a reference field is
	 *                        specified, the field is inserted immediately
	 *                        before the reference field. If true and no
	 *                        reference field is specified, the field is
	 *                        inserted at the beginning of the field list. If
	 *                        false and a reference field is specified, the
	 *                        field is inserted immediately after the reference
	 *                        field. If false and no reference field is
	 *                        specified, the field is inserted at the end of
	 *                        the field list. Defaults to false.
	 *
	 * @throws SwatWidgetNotFoundException if the reference field does not
	 *                                     exist in this details-view.
	 * @throws SwatDuplicateIdException if the field to be inserted has the
	 *                                  same id as a field already in this
	 *                                  details-view.
	 *
	 * @see SwatDetailsView::appendField()
	 * @see SwatDetailsView::insertFieldBefore()
	 * @see SwatDetailsView::insertFieldAfter()
	 */
	protected function insertField(SwatDetailsViewField $field,
		SwatDetailsViewField $reference_field = null, $after = true)
	{
		$this->validateField($field);

		if ($reference_field !== null) {
			$key = array_search($reference_field, $this->fields, true);

			if ($key === false) {
				throw new SwatWidgetNotFoundException('The reference field '.
					'could not be found in this details-view.');
			}

			if ($after) {
				// insert after reference field
				array_splice($this->fields, $key, 1,
					array($reference_field, $field));
			} else {
				// insert before reference field
				array_splice($this->fields, $key, 1,
					array($field, $reference_field));
			}
		} else {
			if ($after) {
				// append to array
				$this->fields[] = $field;
			} else {
				// prepend to array
				array_unshift($this->fields, $field);
			}
		}

		if ($field->id !== null)
			$this->fields_by_id[$field->id] = $field;

		$field->view = $this; // deprecated reference
		$field->parent = $this;
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
			if ($field_javascript != '')
				$javascript.= "\n".$field_javascript;
		}

		foreach ($this->fields as $field) {
			$field_javascript = $field->getInlineJavaScript();
			if ($field_javascript != '')
				$javascript.= "\n".$field_javascript;
		}

		return $javascript;
	}

	// }}}
	// {{{ protected function displayContent()

	/**
	 * Displays each field of this view
	 *
	 * Displays each field of this view as an XHTML table row.
	 */
	protected function displayContent()
	{
		$count = 1;

		foreach ($this->fields as $field) {
			$odd = ($count % 2 == 1);

			ob_start();
			$field->display($this->data, $odd);
			$content = ob_get_clean();

			if ($content != '') {
				echo $content;
				$count++;
			}
		}
	}

	// }}}
}

?>
