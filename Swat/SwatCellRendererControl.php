<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A renderer for navigational control.
 */
class SwatCellRendererControl extends SwatCellRenderer {

	public $href;
	public $src;
	public $alt;
	public $id;

	public function render() {
		$anchor = new SwatHtmlTag('a');
		$anchor->href = sprintf($this->href, $this->id);

		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;
		$image_tag->alt = _S($this->alt);

		$anchor->open();
		$image_tag->display();
		$anchor->close();
	}
}
