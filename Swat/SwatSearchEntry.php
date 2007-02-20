<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';

/**
 * A single line search entry widget
 *
 * @package   Swat
 * @copyright 2004-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSearch extends SwatEntry
{
	// {{{ public properties

	/**
	 * SwatSearch title value
	 *
	 * Text content of the widget, or defaults to 'Enter Search'.
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ public function __construct()

	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->title = Swat::_('Enter Search...');
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Displays a value for SwatSearch
	 *
	 * The methond returns either the title or the correct search entry.
	 *
	 * @return string the display value
	 */
	protected function getDisplayValue()
	{
		$value = '';

		if ($this->value === null) {
			$value = $this->title;
		} else {
			$value = parent::getDisplayValue();
		}

		return $value;
	}

	// }}}
}

?>
