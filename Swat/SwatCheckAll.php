<?php

require_once 'Swat/SwatControl.php';

/**
 * A "check all" javascript checkbox
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckAll extends SwatControl
{
	/**
	 * Series Name
	 *
	 * The name of the series of checkboxes that the check all triggers.
	 *
	 * @var string
	 */
	public $series_name = null;	
	
	/**
	 * Title
	 *
	 * Optional text to display next to the checkbox, by default "Check All".
	 * The default title gets set in init().
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Initialize this check-all widget
	 *
	 * Sets the widget label and enforces a unique identifier.
	 */
	public function init()
	{
		$this->title = Swat::_('Check All');
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}

	/**
	 * Displays this check-all widget
	 */
	public function display()
	{
		if ($this->series_name === null)
			throw new SwatException(__CLASS__.': A series name referencing '.
				'the series of checkboxes to apply to must be defined.');

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->id = $this->id;

		$label_tag = new SwatHtmlTag('label');
		$label_tag->for = $this->id;

		$label_tag->open();
		$input_tag->display();
		echo $this->title;
		$label_tag->close();
		
		$this->displayJavascript();
	}

	/**
	 * Displays the javascript for this check-all widget
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		include_once 'javascript/swat-check-all.js';
		echo "new SwatCheckAll('{$this->id}', '{$this->series_name}[]');";
		echo '</script>';
	}
}

?>
