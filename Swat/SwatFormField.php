<?php
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatFormField extends SwatContainer {

	/**
	 * The visible name for this field, or null
	 * @var string
	 */
	public $title = null;

	/*
	 * Display a visible indication that this field is required
	 * @var bool
	 */
	public $required = false;
	
	/*
	 * Optional note of text to display with the field
	 * @var bool
	 */
	public $note = null;

	/**
	 * CSS class to use on the HTML div tag
	 * @var string
	 */
	public $class = 'swat-form-field';

	/**
	 * CSS class to use on the HTML div where the note is displayed
	 * @var string
	 */
	public $note_class = 'swat-form-field-note';

	/**
	 * CSS class to use on outer HTML div when an error message is displayed
	 * @var string
	 */
	public $error_class = 'swat-form-field-error';

	/**
	 * CSS class to use on the HTML div where the error message is displayed.
	 * @var string
	 */
	public $errormsg_class = 'swat-form-field-errormsg';

	public function display() {
		$first_child = $this->getChild(0);

		if ($first_child == NULL)
			return;

		$messages = $this->gatherMessages();
		$container_div = new SwatHtmlTag('div');

		if (count($messages) > 0)
			// TODO: more classes based on message type?
			$container_div->class = $this->error_class;
		else
			$container_div->class = $this->class;

		$container_div->open();

		if ($this->title !== null) {
			$label_tag = new SwatHtmlTag('label');
			$label_tag->for = $first_child->name;
			$label_tag->open();
			echo $this->title, ':';

			if ($this->required)
				echo '<span class="required">*</span>';

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
				echo $msg->content, '<br />';

			$msg_div->close();
		}
		
		if ($this->note !== null) {
			$note_div = new SwatHtmlTag('div');	
			$note_div->class = $this->note_class;
			$note_div->open();
			echo $this->note;
			$note_div->close();
		}


		$container_div->close();
	}
}

?>
