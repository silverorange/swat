<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A control for recording a rating out of four values
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatRating extends SwatFlydown
{
	// {{{ public function __construct()

	/**
	 * Creates a new rating control
	 *
	 * @param string $id optional. A non-visible unique id for this rating
	 *                    control.
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
	 * Initializes this rating control
	 */
	public function init()
	{
		parent::init();

		$ratings = array(
			1 => Swat::_('One Star'),
			2 => Swat::_('Two Stars'),
			3 => Swat::_('Three Stars'),
			4 => Swat::_('Four Stars'));

		$this->addOptionsByArray($ratings);
		$this->serialize_values = false;
		$this->unique_values = true;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this rating control
	 *
	 * Processes this rating control and converts the value if it is a string.
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
	 * Displays this rating control
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$div = new SwatHtmlTag('div');
		$div->id = $this->id.'_rating_div';
		$div->class = $this->getCSSClassString();
		$div->open();
		parent::display();
		$div->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this rating control
	 *
	 * @return array the array of CSS classes that are applied to this rating
	 *                control.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-rating');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this rating control
	 *
	 * @return string the inline JavaScript required for this rating control.
	 */
	protected function getInlineJavaScript()
	{
		return "var {$this->id}_obj = new SwatRating('{$this->id}');";
	}

	// }}}
}

?>
