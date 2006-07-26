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
class SwatHeaderFormField extends SwatFormField
{
	// {{{ public function display()

	public function display()
	{
		$wrapper_tag = new SwatHtmlTag('div');
		$wrapper_tag->class =
			$this->getCssClasses('swat-header-form-field');

		$wrapper_tag->open();
		parent::display();
		$wrapper_tag->close();
	}

	// }}}
}

?>
