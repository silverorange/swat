<?php

require_once 'Swat/SwatControl.php';

/**
 * A block of content in the widget tree
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
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
		echo $this->content;
	}	
}

?>
