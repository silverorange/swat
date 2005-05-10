<?php

require_once('Swat/SwatContainer.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A container to show and hide child widgets
 *
 * A container with a disclosure widget that may be shown or hidden by the user.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatDisclosure extends SwatContainer {

	/**
	 * A visible name for the label
	 * @var string
	 */
	public $title = null;

	/**
	 * A flag to set the initial state of the disclosure
	 * @var bool
	 */
	public $open = true;

	public function init() {
		// An id is required for this widget.
		$this->generateAutoId();
	}

	public function display() {

		$this->displayJavascript();

		$control_div = new SwatHtmlTag('div');
		$control_div->class = 'swat-disclosure-control';

		$control_div->open();

		$anchor = new SwatHtmlTag('a');
		$anchor->href = sprintf("javascript:toggleDisclosureWidget('%s');",
			$this->id);

		$anchor->open();

		$img = new SwatHtmlTag('img');
	
		if ($this->open) {
			$img->src = 'swat/images/disclosure-open.png';
			$img->alt = 'close';
		} else {
			$img->src = 'swat/images/disclosure-closed.png';
			$img->alt = 'open';
		}

		$img->width = 16;
		$img->height = 16;
		$img->id = $this->id.'_img';

		$img->display();

		if ($this->title !== null)
			echo $this->title;

		$anchor->close();
		$control_div->close();

		$container_div = new SwatHtmlTag('div');
		$container_div->id = $this->id;

		if ($this->open)
			$container_div->class = 'swat-disclosure-container-opened';
		else
			$container_div->class = 'swat-disclosure-container-closed';

		$container_div->open();
		parent::display();
		$container_div->close();
	}

	public function displayJavascript() {
		echo '<script type="text/javascript">';
		include('Swat/javascript/swat-disclosure.js');
		echo '</script>';
	}
}

?>
