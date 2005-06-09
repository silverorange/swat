<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A block of content in the widget tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolLink extends SwatControl {

	/**
	 * The title of the link
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The href of the link
	 *
	 * @var string
	 */
	public $href = '';

	/**
	 * HREF value
	 *
	 * A value to substitute into the href using sprintf()
	 * example href: "MySection/MyPage?id=%s"
	 *
	 * @var string
	 */
	public $value = null;

	// TODO: add an optional image, and possibly stock images

	public function display() {
		if (!$this->visible)
			return;

		$anchor = new SwatHtmlTag('a');

		if ($this->value === null)
			$anchor->href = $this->href;
		else
			$anchor->href = sprintf($this->href, $this->value);

		$anchor->content = $this->title;
		$anchor->class = 'swat-tool-link';

		$anchor->display();
	}	

}

?>
