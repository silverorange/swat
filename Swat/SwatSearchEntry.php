<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatEntry.php';

/**
 * A single line search entry widget
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSearchEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * An XHTML name for this search entry widget
	 *
	 * The name is used as the XHTML form element name. This is useful for
	 * HTTP GET forms where the input name is displayed in the request URI.
	 *
	 * @var string
	 */
	public $name;

	// }}}
	// {{{ public function __construct()

	public function __construct($id = null)
	{
		parent::__construct($id);
		
		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-search-entry.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-search-entry.css',
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
		if (!$this->visible)
			return;

		parent::display();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

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
	// {{{ protected function getInputTag()

	protected function getInputTag()
	{
		$tag = parent::getInputTag();
		$tag->name = $this->name;

		return $tag;
	}

	// }}}
}

?>
