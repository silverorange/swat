<?php

require_once 'Swat/SwatToolLink.php';

/**
 * A demo documentation link
 *
 * This widget links to the Swat documentation from other widgets.
 *
 * @package   SwatDemo
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoDocumentationLink extends SwatToolLink
{
	// {{{ public function __construct()

	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->class = 'demo-documentation';
	}

	// }}}
}

?>
