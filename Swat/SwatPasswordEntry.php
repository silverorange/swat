<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatEntry.php';

/**
 * A password entry widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPasswordEntry extends SwatEntry
{
	/**
	 * Creates a new password entry and defaults the size to 20
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->size = 20;
	}

	/**
	 * Displays this password entry widget
	 *
	 * @see SwatEntry::display()
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$this->html_input_type = 'password';
		parent::display();
	}
}

?>
