<?php

require_once 'Demo.php';

/**
 * A demo using pagination widgets
 *
 * @package   SwatDemo
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class PaginationDemo extends Demo
{


	public function buildDemoUI(SwatUI $ui)
	{
		$ui->getWidget('medium')->setCurrentPage(4);

		$ui->getWidget('beginning')->setCurrentPage(4);
		$ui->getWidget('middle')->setCurrentPage(49);
		$ui->getWidget('end')->setCurrentPage(94);

		$ui->getWidget('small')->setCurrentPage(50);
	}

}

?>
