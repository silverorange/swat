<?php
require_once('Swat/SwatHtmlTag.php');

/**
 * A block of content in the widget tree
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatToolLink extends SwatControl {

	/**
	 * The title of the link
	 * @var string
	 */
	public $title = '';

	/**
	 * The href of the link
	 * @var string
	 */
	public $href = '';

	// TODO: add an optional image, and possibly stock images

	public function display() {
		$anchor = new SwatHtmlTag('a');
		$anchor->href = $this->href;
		$anchor->content = $this->title;
		$anchor->class = 'swat-tool-link';

		$anchor->display();
	}	

}

?>
