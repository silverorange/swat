<?php

require_once 'DemoPage.php';

/**
 * A demo using pagination widgets
 *
 * This page sets the current page in pagination widgets
 *
 * @package   SwatDemo
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Pagination extends DemoPage
{
	// {{{ public function initUI()

	public function initUI()
	{
		$this->ui->getWidget('medium')->setCurrentPage(4);

		$this->ui->getWidget('beginning')->setCurrentPage(4);
		$this->ui->getWidget('middle')->setCurrentPage(49);
		$this->ui->getWidget('end')->setCurrentPage(94);

		$this->ui->getWidget('small')->setCurrentPage(50);
	}

	// }}}
}

?>
