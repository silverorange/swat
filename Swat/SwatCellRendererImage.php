<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * An image renderer.
 */
class SwatCellRendererImage extends SwatCellRenderer {

	public $src;

	public function render() {
		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;

		$image_tag->display();
	}
}
