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
	 * @var string A visible name for this frame, or null.
	 */
	public $title = null;

	public function display() {
		$outer_divtag = new SwatHtmlTag('div');
		$outer_divtag->class = 'swat-frame';

		$inner_divtag = new SwatHtmlTag('div');
		$inner_divtag->class = 'swat-frame-contents';

		$outer_divtag->open();

		if ($this->title != null) {
			// TODO: Can the header level be autocalculated based on the 
			// level of the frame?
			echo '<h2>', $this->title, '</h2>';
		}

		$inner_divtag->open();

		foreach ($this->children as &$child)
			$child->display();

		$inner_divtag->close();
		$outer_divtag->close();
	}
}

?>
