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
	 * Image src
	 *
	 * The src attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $src;

	/**
	 * Optional array of values to for $src
	 *
	 * Uses vsprintf() syntax, for example: $src = mydir/%s.%s; $values =
	 * array('myfilename', 'ext');
	 *
	 * @var array
	 */
	public $values = null;

	/**
	 * Image height
	 *
	 * The height attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $height = 0;

	/**
	 * Image width
	 *
	 * The width attribute in the XHTML img tag.
	 *
	 * @var integer
	 */
	public $width = 0;

	/**
	 * Image title
	 *
	 * The title attribute in the XHTML img tag.
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * Image alt text
	 *
	 * The alt attribute in the XHTML img tag.
	 */
	public $alt = '';
	
	public function display() {
		if (!$this->visible)
			return

		$image_tag = new SwatHtmlTag('img');
		$image_tag->src = $this->src;

		if ($this->height > 0)
			$image_tag->height = $this->height;

		if ($this->width > 0)
			$image_tag->width = $this->width;

		if (strlen($this->title) > 0)
			$image_tag->title = $this->title;

		if (strlen($this->alt) > 0)
			$image_tag->alt = $this->alt;

		$image_tag->display();
	}
}

?>
