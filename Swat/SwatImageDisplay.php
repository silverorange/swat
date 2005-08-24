<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatControl.php';

/**
 * Image Display Control
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatImageDisplay extends SwatControl {

	/**
	 * Image source
	 *
	 * @var string
	 */
	public $src;

	/**
	 * Image width
	 *
	 * @var integer
	 */
	public $width = null;

	/**
	 * Image height
	 *
	 * @var integer
	 */
	public $height = null;

	/**
	 * Image title
	 *
	 * @var string
	 */
	public $title = null;
	
	public function display() {
		$img = new SwatHtmlTag('img');
		$img->src = $this->src;

		$img->width = $this->width;
		$img->height = $this->height;
		$img->title = $this->title;

		$img->display();
	}
}

?>
