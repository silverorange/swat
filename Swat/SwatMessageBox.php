<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A control to display page status messages  
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatMessageBox extends SwatControl {

	/**
	 * A visible name for this frame, or null
	 * @var string
	 */
	public $title = null;

	/**
	 * Content of the box
	 * @var string
	 */
	public $content = null;

	public function display() {
		parent::display();

		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title != null) {
			echo "<h2>{$this->title}</h2>";
		}

		$inner_div->open();

		echo $this->content;

		$inner_div->close();
		$outer_div->close();
	}
}

?>
