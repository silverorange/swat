<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container with a decorative frame and optional title.
 */
class SwatFrame extends SwatContainer {

	/**
	 * A visible name for this frame, or null.
	 * @var string
	 */
	public $title = null;

	public function display() {
		$outer_div = new SwatHtmlTag('div');
		$outer_div->class = 'swat-frame';

		$inner_div = new SwatHtmlTag('div');
		$inner_div->class = 'swat-frame-contents';

		$outer_div->open();

		if ($this->title != null) {
			// TODO: Can the header level be autocalculated based on the 
			// level of the frame?
			echo '<h2>', $this->title, '</h2>';
		}

		$inner_div->open();

		parent::display();

		$inner_div->close();
		$outer_div->close();
	}
}

?>
