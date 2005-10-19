<?php

require_once 'Swat/SwatContainer.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFormField extends SwatContainer
{
	/**
	 * The visible name for this field, or null
	 *
	 * @var string
	 */
	public $title = null;

	/*
	 * Display a visible indication that this field is required
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * Optional note of text to display with the field
	 *
	 * @var boolean
	 */
	public $note = null;

	/**
	 * CSS class to use on the HTML div where the note is displayed
	 *
	 * @var string
	 */
	public $note_class = 'swat-form-field-note';

	/**
	 * CSS class to use on outer HTML div when an error message is displayed
	 *
	 * @var string
	 */
	public $error_class = 'swat-form-field-error';

	/**
	 * CSS class to use on the HTML div where the error message is displayed.
	 *
	 * @var string
	 */
	public $errormsg_class = 'swat-form-field-errormsg';

	/**
	 * CSS class to use on the HTML div tag
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $class = 'swat-form-field';

	/**
	 * Displays this form field
	 *
	 * Associates a label with the first widget of this container.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$first_child = $this->getFirst();

		if ($first_child === null)
			return;

		$messages = &$this->getMessages();
		$container_div = new SwatHtmlTag('div');

		$container_div->class = $this->class;
		if (count($messages) > 0)
			// TODO: more classes based on message type?
			$container_div->class .= ' '.$this->error_class;

		$container_div->open();

		if ($this->title !== null) {
			$label_tag = new SwatHtmlTag('label');
			$label_tag->for = $first_child->id;
			$label_tag->open();
			echo $this->title, ':';

			// TODO: widgets that are marked as required don't tell their field parent
			if ($this->required) {
				echo '<span class="required">';
				echo ' (', Swat::_('required'), ')';
				echo '</span>';
			}

			$label_tag->close();
		}

		foreach ($this->children as &$child)
			$child->display();

		if (count($messages) > 0) {
			// TODO: more classes based on message type?
			$msg_div = new SwatHtmlTag('div');
			$msg_div->class = $this->errormsg_class;

			$msg_div->open();

			foreach ($messages as &$msg)
				echo $msg->primary_content, '<br />';

			$msg_div->close();
		}

		if ($this->note !== null) {
			$note_div = new SwatHtmlTag('div');
			$note_div->class = $this->note_class;
			$note_div->content = $this->note;
			$note_div->display();
		}

		$container_div->close();
	}

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This sets a special class on this form field if a checkbox is added.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatCheckbox') && $widget instanceof SwatCheckbox) {
			$this->class = 'swat-form-field-checkbox';
		}
	}
}

?>
