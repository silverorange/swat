<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container to use around control widgets in a form.
 * Adds a label and space to output messages.
 */
class SwatFormField extends SwatContainer {

	/**
	 * The visible name for this field, or null.
	 * @var string
	 */
	public $title = null;

	/*
	 * Display a visible indication that this field is required.
	 * @var bool
	 */
	public $required = false;
	
	/**
	 * CSS class to use on the HTML div tag.
	 * @var string
	 */
	public $class = 'swat-form-field';

	/**
	 * CSS class to use on outer HTML div when an error message is displayed.
	 * @var string
	 */
	public $error_class = 'swat-form-field-error';

	/**
	 * @var string
	 * CSS class to use on the HTML div where the error message is displayed.
	 */
	public $errormsg_class = 'swat-form-field-errormsg';

	public function display() {
		$first_child = $this->getChild(0);

		if ($first_child == NULL)
			return;

		$error_messages = $this->gatherErrorMessages();
		
		$div_tag = new SwatHtmlTag('div');
		$div_tag->class = (count($error_messages) > 0) ? $this->error_class : $this->class;

		$div_tag->open();

		if ($this->title != null) {
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

		if (count($error_messages) > 0) {
			$errordiv_tag = new SwatHtmlTag('div');
			$errordiv_tag->class = $this->errormsg_class;
			
			$errordiv_tag->open();

			foreach ($error_messages as &$err)
				echo $err->message, '<br />';

			$errordiv_tag->close();
		}

		$div_tag->close();
	}
}

?>
