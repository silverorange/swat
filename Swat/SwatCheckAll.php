<?php

/**
 * A an extra row containing a "check all" tool
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCheckAll extends SwatControl {
	/**
	 * Series Name
	 *
	 * The name of the series of checkboxes that the check all triggers.
	 * @var string
	 */
	public $series_name = null;	
	
	/**
	 * Title
	 *
	 * Optional text to display next to the checkbox, by default "Check All".
	 * @var string
	 */
	public $title;

	public function init() {
		$this->title = _S('Check All');
		$this->generateAutoName();
	}

	public function display() {
		if ($this->series_name == null)
			throw new SwatException('SwatCheckall: A series '.
				'name referencing the series of checkboxes '.
				'to apply to must be defined.');

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->name;
		$input_tag->onclick = "SwatCheckbox.checkAll(this,'{$this->series_name}')";

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->name;

		$label_tag->open();
		$input_tag->display();
		echo $this->title;
		$label_tag->close();
		
		$this->displayJavascript();

	}

	private function displayJavascript() {
		?>
		<script type="text/javascript" language="JavaScript">	
		<?php require_once('javascript/swat-check-all.js'); ?>	
		</script>
		<?php
	}

}
?>
