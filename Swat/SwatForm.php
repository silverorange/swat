<?php
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * Toplevel which can contain other widgets
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatForm extends SwatContainer {

	/**
	 * The action attribute of the HTML form tag
	 * @var string
	 */
	public $action = '#';

	/**
	 * The method attribute of the HTML form tag
	 * @var string
	 */
	public $method = 'post';

	/**
	 * Encoding type of the form
	 * @var string
	 */
	public $enctype = null;

	/**
	 * The button that was clicked to submit the form, or null (read only)
	 * @var SwatButton
	 */
	public $button = null;

	/**
	 * Whether the form has been processed
	 * @return boolean True if the form has been processed.
	 */
	public function hasBeenProcessed() {
		return $this->processed;
	}

	private $hidden_fields;
	private $processed = false;

	public function init() {
		$this->hidden_fields = array();
	}

	public function display() {
		if (!$this->visible)
			return;

		$this->addHiddenField('process', $this->name);

		$form_tag = new SwatHtmlTag('form');
		$form_tag->id = $this->name;
		$form_tag->method = $this->method;
		$form_tag->enctype = $this->enctype;
		$form_tag->action = $this->action;

		$form_tag->open();

		foreach ($this->children as $child)
			$child->display();

		$this->displayHiddenFields();
		$form_tag->close();
	}

	private function displayHiddenFields() {
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';

		echo '<div class="swat-input-hidden">';

		foreach ($this->hidden_fields as $name => $value) {
			if (is_array($value)) {
				foreach ($value as $v) {
					$input_tag->name = $name.'[]';
					$input_tag->value = $v;
					$input_tag->display();
				}
			} else {
				$input_tag->name = $name;
				$input_tag->value = $value;
				$input_tag->display();
			}

			// array of field names
			$input_tag->name = $this->name.'_hidden_fields[]';
			$input_tag->value = $name;
			$input_tag->display();
		}

		echo '</div>';
	}

	public function process() {
		if (!isset($_POST['process']) || $_POST['process'] != $this->name)
			return false;

		$this->processed = true;

		foreach ($this->children as &$child)
			$child->process();

		$this->processHiddenFields();

		return true;
	}

	private function processHiddenFields() {
		if (isset($_POST[$this->name.'_hidden_fields']))
			$fields = $_POST[$this->name.'_hidden_fields'];
		else
			return;

		if (!is_array($fields))
			return;

		foreach ($fields as $name) {
			if (isset($_POST[$name])) {
				$value = $_POST[$name];
				$this->addHiddenField($name, $value);
			}
		}
	}

	/**
	 * Add a hidden form field
	 *
	 * Add an HTML input type=hidden field to this form.
	 * @param string $name The name of the field.
	 * @param mixed $value The value of the field, either a string or an array.
	 */
	public function addHiddenField($name, $value) {
		$this->hidden_fields[$name] = $value;
	}

	/**
	 * Get the value of a hidden form field
	 *
	 * @param string $name The name of the field.
	 * @return mixed $value The value of the field, either a string or an 
	 *        array, or null if the field does not exist.
	 */
	public function getHiddenField($name) {
		if (isset($this->hidden_fields[$name]))
			return $this->hidden_fields[$name];

		return null;
	}

	/**
	 * Clear the hidden fields.
	 */
	public function clearHiddenFields() {
		$this->hidden_fields = array();
	}

	/**
	 * Add a widget within a new SwatFormField
	 *
	 * Convenience function to create a new SwatFormField, add the widget as a
	 * child of the form field, and then add the formfield to this form.
	 *
	 * @param SwatWidget $widget A reference to a widget to add.
	 * @param string $title The visible name of the form field.
	 */
	public function addWithField(SwatWidget $widget, $title) {
		$field = new SwatFormField();
		$field->add($widget);
		$field->title = $title;
		$this->add($field);
	}

	/**
	 * Add a widget within a new SwatDiv
	 *
	 * Convenience function to create a new SwatDiv, add the widget as a child
	 * of the div, and then add the div to this form.
	 *
	 * @param SwatWidget $widget A reference to a widget to add.
	 * @param string $title The class of the HTML div tag.
	 */
	public function addWithDiv(SwatWidget $widget, $class) {
		$field = new SwatDiv();
		$field->add($widget);
		$field->class = $class;
		$this->add($field);
	}
}

?>
