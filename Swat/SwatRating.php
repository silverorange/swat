<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A javascript widget for recording a four star rating
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRating extends SwatFlydown
{
	// {{{ public function __construct()

	/**
	 * Creates a new rating widget
	 *
	 * @param string $id a non-visible unique id for this rating widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addJavaScript('packages/swat/javascript/swat-rating.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-rating.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this rating widget
	 *
	 * Initializes this rating widget by creating and adding the options
	 * to the flydown.
	 */
	public function init()
	{
		parent::init();

		$ratings = array(
			1 => 'One Star',
			2 => 'Two Stars',
			3 => 'Three Stars',
			4 => 'Four Stars');

		$this->addOptionsByArray($ratings);
		$this->serialize_values = false;
		$this->unique_values = true;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this rating widget
	 *
	 * Process this rating widget and converts the value if it is a string
	 */
	public function process()
	{
		parent::process();

		if (is_string($this->value)) 
			$this->value = intval($this->value);
	}

	// }}}
	//  {{{ public function display()
	
	/**
	 * Displays this rating widget
	 *
	 * Displays this rating widget as a XHTML select
	 */
	public function display()
	{

		$id = sprintf('ratingdiv_%s', $this->id);
		$div = new SwatHtmlTag('div');
		$div->id = $id;
		$div->open();

		parent::display();

		$div->close();		
		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protceted function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this rating to function
	 * 
	 * The inline JavaScript creates an instance of the 
	 * SwatRating widget with the name $this->id_'obj'.
	 *
	 * @return string the inline JavaScript required for this control to 
	 *					function
	 */
	protected function getInlineJavaScript()
	{
		return "var {$this->id}_obj = new SwatRating('{$this->id}');";
	}

	// }}}
}

?>
