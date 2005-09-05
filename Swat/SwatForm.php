<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Toplevel which can contain other widgets
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatForm extends SwatContainer
{
	const METHOD_POST = 'post';
	const METHOD_GET  = 'get';

	/**
	 * The action attribute of the HTML form tag
	 *
	 * @var string
	 */
	public $action = '#';

	/**
	 * Encoding type of the form
	 *
	 * Used for multipart forms for file uploads.
	 *
	 * @var string
	 */
	public $encoding_type = null;

	/**
	 * Whether or not to automatically focus the a default SwatControl when
	 * this form loads
	 *
	 * Autofocusing is good for applications that are keyboard driven as it
	 * immediatly places the focus on the form.
	 *
	 * @var boolean
	 */
	public $autofocus = true;

	/**
	 * A reference to the default control to focus when the form loads
	 *
	 * If this is not set then it defaults to the first SwatControl
	 * in the form.
	 *
	 * @var SwatControl
	 */
	public $default_focused_control = null;

	/**
	 * A reference to the button that was clicked to submit the form,
	 * or null if the button is not set.
	 *
	 * You usually do not want to explicitly set this in your code because
	 * other parts of Swat set this proprety automatically.
	 *
	 * @var SwatButton
	 */
	public $button = null;

	/**
	 * Hidden form fields
	 *
	 * An array of the form:
	 *    name => value
	 * where all the values are passed as hidden fields in this form.
	 *
	 * @var array
	 */
	protected $hidden_fields = array();

	/**
	 * The method to use for this form
	 *
	 * Is one of SwatForm::METHOD_* constants.
	 *
	 * @var string
	 */
	private $method = SwatForm::METHOD_POST;

	/**
	 * Whether this form has been processed
	 *
	 * @var boolean
	 */
	private $processed = false;

	/**
	 * Creates a new form
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('swat/javascript/swat-form.js');
	}

	/**
	 * Returns true if this form has been processed
	 *
	 * @return boolean true if the form has been processed.
	 */
	public function hasBeenProcessed()
	{
		return $this->processed;
	}

	/**
	 * Sets the HTTP method this form uses to send data
	 *
	 * @param string $method a method constant. Must be one of
	 *                        SwatForm::METHOD_* otherwise an error is thrown.
	 *
	 * @throws SwatException
	 */
	public function setMethod($method)
	{
		$valid_methods = array(SwatForm::METHOD_POST, SwatForm::METHOD_GET);

		if (!in_array($method, $valid_methods))
			throw new SwatException(sprintf(__CLASS__."'%s' is not a valid ".
				'form method.', $method));

		$this->method = $method;
	}

	/**
	 * Displays this form
	 *
	 * Outputs the HTML form tag and calls the display() method on each child
	 * widget of this form. Then, after all the child widgets are displayed,
	 * displays all hidden fields.
	 *
	 * This method also adds a hidden field called 'process' that is given
	 * the unique identifier of this form as a value.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->addHiddenField('process', $this->id);

		$form_tag = new SwatHtmlTag('form');
		$form_tag->id = $this->id;
		$form_tag->method = $this->method;
		$form_tag->enctype = $this->encoding_type;
		$form_tag->action = $this->action;
		$form_tag->class = 'swat-form';

		$form_tag->open();

		foreach ($this->children as $child)
			$child->display();

		$this->displayHiddenFields();
		$form_tag->close();

		$this->displayJavascript();
	}

	/**
	 * Processes this form
	 *
	 * If this form has been submitted then calls the process() method on
	 * each child widget. Then processes hidden form fields.
	 *
	 * @return true if this form was actually submitted, false otherwise.
	 */
	public function process()
	{
		$raw_data = $this->getRawFormData();

		if (!isset($raw_data['process']) || $raw_data['process'] != $this->id)
			return false;

		$this->processed = true;

		foreach ($this->children as &$child)
			$child->process();

		$this->processHiddenFields();

		return true;
	}

	/**
	 * Adds a hidden form field
	 *
	 * Adds a form field to this form that is not shown to the user.
	 * Hidden form fields are outputted as type="hidden" input tags.
	 *
	 * @param string $name the name of the field.
	 * @param mixed $value the value of the field, either a string or an array.
	 */
	public function addHiddenField($name, $value)
	{
		$this->hidden_fields[$name] = $value;
	}

	/**
	 * Gets the value of a hidden form field
	 *
	 * @param string $name the name of the field whose value to get.
	 *
	 * @return mixed $value the value of the field, either a string or an 
	 *        array, or null if the field does not exist.
	 */
	public function getHiddenField($name)
	{
		if (isset($this->hidden_fields[$name]))
			return $this->hidden_fields[$name];

		return null;
	}

	/**
	 * Clears all hidden fields
	 */
	public function clearHiddenFields()
	{
		$this->hidden_fields = array();
	}

	/**
	 * Adds a widget within a new SwatFormField
	 *
	 * This is a convenience method that does the following:
	 * - creates a new SwatFormField,
	 * - adds the widget as a child of the form field,
	 * - and then adds the SwatFormField to this form.
	 *
	 * @param SwatWidget $widget a reference to a widget to add.
	 * @param string $title the visible title of the form field.
	 */
	public function addWithField(SwatWidget $widget, $title)
	{
		$field = new SwatFormField();
		$field->add($widget);
		$field->title = $title;
		$this->add($field);
	}

	/**
	 * Adds a widget within a new SwatDiv
	 *
	 * This is a convenience method that does the following:
	 * - creates a new SwatDiv,
	 * - adds the widget as a child of the div,
	 * - and then adds the SwatDiv to this form.
	 *
	 * @param SwatWidget $widget a reference to a widget to add.
	 * @param string $title the CSS class of the HTML div tag.
	 */
	public function addWithDiv(SwatWidget $widget, $class)
	{
		$field = new SwatDiv();
		$field->add($widget);
		$field->class = $class;
		$this->add($field);
	}
	
	/**
	 * Returns the super-global array with this form's data
	 *
	 * Returns a reference to the super-global array containing this
	 * form's data. The array is chosen based on this form's method.
	 *
	 * @return array a reference to the super-global array containing this
	 *                form's data.
	 */
	public function getRawFormData()
	{
		$data = null;

		switch ($this->method) {
		case SwatForm::METHOD_POST:
			$data = &$_POST;
			break;
		case SwatForm::METHOD_GET:
			$data = &$_GET;
			break;
		}

		return $data;
	}

	/**
	 * Checks submitted form data for hidden fields
	 *
	 * Checks submitted form data for hidden fields. If hidden fields are
	 * found, properly re-adds them to this form.
	 */
	protected function processHiddenFields()
	{
		$raw_data = $this->getRawFormData();

		if (isset($raw_data[$this->id.'_hidden_fields']))
			$fields = $raw_data[$this->id.'_hidden_fields'];
		else
			return;

		if (!is_array($fields))
			return;

		foreach ($fields as $name) {
			if (isset($raw_data[$name])) {
				$value = $raw_data[$name];
				$this->addHiddenField($name, $value);
			}
		}
	}

	/**
	 * Notifies this widget that a widget was added
	 *
	 * If any of the widgets in the added subtree are file entry widgets then
	 * set this form's encoding accordingly.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatFileEntry')) {

			if ($widget instanceof SwatFileEntry) {
				$this->encoding_type = 'multipart/form-data';
			} elseif ($widget instanceof SwatContainer) {
				$descendants = $widget->getDescendants();
				foreach ($descendants as $sub_widget) {
					if ($sub_widget instanceof SwatFileEntry) {
						$this->encoding_type = 'multipart/form-data';
						break;
					}
				}
			}
			
		}
	}

	/**
	 * Displays hidden form fields
	 *
	 * Displays hiden form fields as <input type="hidden" /> XHTML elements.
	 * This method automatically handles array type values so they will be
	 * returned correctly as arrays.
	 *
	 * This methods also generates an array of hidden field names and passes
	 * them as hidden fields as well.
	 */
	private function displayHiddenFields()
	{
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
			} elseif ($value !== null) {
				$input_tag->name = $name;
				$input_tag->value = $value;
				$input_tag->display();
			}

			// array of field names
			$input_tag->name = $this->id.'_hidden_fields[]';
			$input_tag->value = $name;
			$input_tag->display();
		}

		echo '</div>';
	}

	/**
	 * Displays javascript required for this form
	 *
	 * Right now, this javascript focuses the first SwatControl in the form.
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">'."\n";

		echo "var {$this->id}_obj = new SwatForm('{$this->id}');\n";

		if ($this->autofocus) {
			if ($this->default_focused_control === null)
				$focus_id = $this->getFirstDescendent('SwatControl')->id;
			else
				$focus_id = $this->default_focused_control->id;

			echo "{$this->id}_obj.setDefaultFocus('{$focus_id}');\n";
		}

		echo '</script>';
	}
}

?>
