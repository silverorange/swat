<?php

require_once 'Swat/SwatCellRenderer.php';

/**
 * A renderer for a column of checkboxes
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxCellRenderer extends SwatCellRenderer
{

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

	public function render($prefix)
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $prefix.$this->id.'[]';
		$input_tag->value = $this->value;
		$input_tag->onclick = "SwatCheckbox.check(this);";

		$this->displayJavascript();

		if (isset($_POST[$prefix.$this->id]))
			if (in_array($this->value, $_POST[$prefix.$this->id]))
				$input_tag->checked = 'checked';

		$input_tag->display();
	}

	private function displayJavascript()
	{
		static $run_once = false;
		
		if ($run_once)
			return;

		$run_once = true;

		echo '<script type="text/javascript">';
		require_once 'Swat/javascript/swat-check-all.js';
		echo '</script>';
	}
}

?>
