<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoMenuBar extends SwatControl
{
	// {{{ protected properties

	protected $entries = array();
	protected $selected_entry;

	// }}}
	// {{{ public function display()

	public function display()
	{
		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->id = $this->id;
		$ul_tag->class = 'demo-menu-bar';

		$a_tag = new SwatHtmlTag('a');
		$span_tag = new SwatHtmlTag('span');
		$li_tag = new SwatHtmlTag('li');

		$ul_tag->open();

		foreach ($this->entries as $demo => $title) {
			$li_tag->class = ($this->selected_entry == $demo) ?
				'demo-menu-bar-selected' : null;

			$li_tag->open();

			if ($this->selected_entry == $demo) {
				$span_tag->setContent($title);
				$span_tag->display();
			} else {
				$a_tag->href = 'index.php?demo='.$demo;
				$a_tag->setContent($title);
				$a_tag->display();
			}

			$li_tag->close();
		}

		$ul_tag->close();
	}

	// }}}
	// {{{ public function setEntries()

	public function setEntries(array $entries)
	{
		$this->entries = $entries;
	}

	// }}}
	// {{{ public function setSelectedEntry()

	public function setSelectedEntry($entry)
	{
		$this->selected_entry = $entry;
	}

	// }}}
}

?>
