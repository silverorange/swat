<?php

require_once 'DemoMenu.php';

/**
 * The menu for the Swat Demo Application
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoDocumentationMenu extends DemoMenu
{
	public function __construct($entries)
	{
		$this->entries = $entries;
	}

	public function display()
	{
		if (count($this->entries) > 0) {
			echo '<div id="documentation-menu"><span class="menutitle">Documentation Links</span>:<ul>';

			foreach ($this->entries as $class) {
				echo '<li><a href="http://docs.silverorange.com/Swat/'.$class.'.html">'.$class.'</a></li>';
			}

			echo '</ul></div>';
		}
	}
}

?>
