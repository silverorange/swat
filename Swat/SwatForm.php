<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * Toplevel which can contain other widgets.
 */
class SwatForm extends SwatContainer {

	/**
	 * The action attribute of the HTML form tag.
	 * @var string
	 */
	public $action = '#';

	/**
	 * Add a widget within a new SwatFormField.
	 * Convenience function to create a new SwatFormField, add the widget as a
	 * child of the form field, and then add the formfield to this form.
	 *
	 * @param $widget SwatWidget A reference to a widget to add.
	 * @param $title string The visible name of the form field.
	 */
	public function addWithField(SwatWidget $widget, $title) {
		$field = new SwatFormField();
		$field->add($widget);
		$field->title = $title;
		$this->add($field);
	}

	/**
	 * Add a widget within a new SwatDiv.
	 * Convenience function to create a new SwatDiv, add the widget as a child
	 * of the div, and then add the div to this form.
	 *
	 * @param $widget SwatWidget A reference to a widget to add.
	 * @param $title string The class of the HTML div tag.
	 */
	public function addWithDiv(SwatWidget $widget, $class) {
		$field = new SwatDiv();
		$field->add($widget);
		$field->class = $class;
		$this->add($field);
	}

	public function display() {
		$form_tag = new SwatHtmlTag('form');
		$form_tag->id = $this->name;
		$form_tag->method = 'post';
		$form_tag->action = $this->action;

		$form_tag->open();

		foreach ($this->children as &$child)
			$child->display();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';
		$input_tag->name = 'process';
		$input_tag->value = $this->name;
		echo '<div class="swat-input-hidden">';
		$input_tag->display();
		echo '</div>';

		$form_tag->close();
	}

	public function process() {
		if (!isset($_POST['process']) || $_POST['process'] != $this->name)
			return false;

		foreach ($this->children as &$child)
			$child->process();

		return true;
	}

}

?>
