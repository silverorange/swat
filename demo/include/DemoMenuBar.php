<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoMenuBar extends SwatControl
{
	// {{{ protected properties

	protected $entries = array();

	// }}}
	// {{{ public function display()

	public function display()
	{
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'demo-menu-bar';
		$div_tag->open();

		echo '<h3>', Swat::_('Demos:'), '</h3><ul>';

		foreach ($this->entries as $demo => $title) {
			echo '<li><a href="index.php?demo='.$demo.'">'.$title.'</a></li>';
		}


		echo '</ul>';
		$div_tag->close();
	}

	// }}}
	// {{{ public function setEntries()

	public function setEntries(array $entries)
	{
		$this->entries = $entries;
	}

	// }}}
}

?>
