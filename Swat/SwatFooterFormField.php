<?php

require_once 'Swat/SwatFormField.php';

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFooterFormField extends SwatFormField
{
	/**
	 * Constructor
	 *
	 * Sets the class to swat-footer-form-field
	 *
	 * @param string $id the id of this form field.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->class = 'swat-footer-form-field';
	}

	public function display()
	{
		$wrapper_tag = new SwatHtmlTag('div');
		$wrapper_tag->class = 'swat-footer-form-field-wrapper';

		$wrapper_tag->open();
		parent::display();
		$wrapper_tag->close();
	}
}

?>
