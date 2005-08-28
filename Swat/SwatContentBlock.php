<?php

require_once 'Swat/SwatControl.php';

/**
 * A block of content in the widget tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatContentBlock extends SwatControl
{
	/**
	 * User visable textual content of this widget
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Displays this content
	 *
	 * Merely performs an echo of the content.
	 */
	public function display()
	{
		if (!$this->visible)
			return

		echo $this->content;
	}
}

?>
