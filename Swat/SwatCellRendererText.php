<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A text renderer.
 */
class SwatCellRendererText extends SwatCellRenderer {

	/**
	 * The content to place within the HTML anchor tag. In a SwatUI XML file 
	 * this comes from the content of the SwatCellRendererLink tag.
	 * @var string
	 */
	public $content = '';

	public function render() {
		echo $this->content;
	}
}
