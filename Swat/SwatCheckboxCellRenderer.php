<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatCellRenderer.php';

/**
 * A renderer for a column of checkboxes
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxCellRenderer extends SwatCellRenderer
{
	// {{{ public properties

	/**
	 * Id of checkbox
	 *
	 * The name attribute in the HTML input tag.
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Value of checkbox
	 *
	 * The value attribute in the HTML input tag.
	 *
	 * @var string
	 */
	public $value;

	// }}}
	// {{{ public function render()

	public function render()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'[]';
		$input_tag->value = $this->value;

		if (!$this->sensitive) {
			$input_tag->disabled = 'disabled';
			// TODO: add a style class for internet explorer if insensitive
		}

		if (isset($_POST[$this->id]))
			if (in_array($this->value, $_POST[$this->id]))
				$input_tag->checked = 'checked';

		$input_tag->display();
	}

	// }}}
}

?>
