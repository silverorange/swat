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
		$outerdiv_tag = new SwatHtmlTag('div');
		$outerdiv_tag->class = 'swat-frame';

		$innerdiv_tag = new SwatHtmlTag('div');
		$innerdiv_tag->class = 'swat-frame-contents';

		$outerdiv_tag->open();

		if ($this->title != null) {
			// TODO: Can the header level be autocalculated based on the 
			// level of the frame?
			echo '<h2>', $this->title, '</h2>';
		}

		$innerdiv_tag->open();

		foreach ($this->children as &$child)
			$child->display();

		$innerdiv_tag->close();
		$outerdiv_tag->close();
	}
}

?>
