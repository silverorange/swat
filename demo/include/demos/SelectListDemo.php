<?php

require_once 'Demo.php';

/**
 * A demo using a select list
 *
 * @package   SwatDemo
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SelectListDemo extends Demo
{


	public function buildDemoUI(SwatUI $ui)
	{
		$select_list_options = [
            0 => 'Apple',
            1 => 'Orange',
            2 => 'Banana',
            3 => 'Pear',
            4 => 'Pineapple',
            5 => 'Kiwi',
        ];

		$select_list = $ui->getWidget('select_list');
		$select_list->addOptionsByArray($select_list_options);
	}

}

?>
