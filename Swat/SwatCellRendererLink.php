<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatCellRenderer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A link renderer.
 */
class SwatCellRendererLink extends SwatCellRenderer {

	public $text;
	public $href;
	public $href_value = null;

	public function render() {
		$anchor = new SwatHtmlTag('a');
		$anchor->content = $this->text;

		if ($this->href_value == null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->href_value);

		$anchor->display();
	}
}
