<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');

/**
 * A renderer for a boolean value.
 */
class SwatCellRendererCheck extends SwatCellRenderer {

	public $value;

	public function render() {
		if ((boolean)$this->value) {
			$image_tag = new SwatHtmlTag('img');
			$image_tag->src = 'swat/images/check.png';
			$image_tag->alt = _S('Yes');
			$image_tag->height = '14';
			$image_tag->width = '14';
			$image_tag->display();
		} else {
			echo '&nbsp;';
		}
	}

	public function getTdAttribs() {
		return array('style' => 'text-align: center;');
	}
}
