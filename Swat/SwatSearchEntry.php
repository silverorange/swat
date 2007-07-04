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
class SwatSearchEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * SwatSearch title value
	 *
	 * Text content of the widget, or defaults to 'Enter Search …'.
	 *
	 * @var string
	 */
	public $title;

	// }}}
	// {{{ public function __construct()

	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->title = Swat::_('Enter Search …');
		
		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-search-entry.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()
	
	/**
	 * Displays this search entry
	 *
	 * Outputs an appropriate XHTML tag and JavaScript.
	 */
	public function display()
	{
		parent::display();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getDisplayValue()

	/**
	 * Formats a value to display
	 *
	 * The methond returns either the title or the correct search entry.
	 *
	 * @param string $value the value to format for display.
	 *
	 * @return string the formatted value.
	 */
	protected function getDisplayValue($value)
	{
		return ($value === null) ?
			$this->title : parent::getDisplayValue($value);
	}

	// }}}
	// {{{ protected function getInlineJavaScript

	/**
	 * Gets the inline JavaScript for this entry to function
	 * 
	 * The inline JavaScript creates an instance of the 
	 * SwatSearchEntry widget with the name $this->id_'obj'.
	 *
	 * @return srting the inline JavaScript required for this control to 
	 *					function
	 */
	protected function getInlineJavaScript()
	{
		return "var {$this->id}_obj = new SwatSearchEntry('{$this->id}');";
	}

	// }}}
}

?>
