<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Simple color selector widget.
 *
 * This color selector displays a simple palette to the user with a set of
 * predefined color choices. It requires JavaScript to work correctly.
 *
 * @package   Swat
 * @copyright 2005-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSimpleColorEntry extends SwatInputControl implements SwatState
{
	// {{{ public properties

	/**
	 * Selected color
	 *
	 * The selected color in three or six digit hexidecimal representation.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Access key
	 *
	 * Access key for this simple color control, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	/**
	 * Array of colors to display in this color selector
	 *
	 * The array is flat and contains three or six digit hex color
	 * codes.
	 *
	 * The default palette is the
	 * {@link http://tango.freedesktop.org/Tango_Icon_Theme_Guidelines#Color_Palette Tango Project color palette}.
	 *
	 * @var array
	 */
	public $colors = array(
		'eeeeec', 'd3d7cf', 'babdb6', '888a85', '555753', '2e3436',
		'fce94f', 'edd400', 'c4a000', 'fcaf3e', 'f57900', 'ce5c00',
		'e9b96e', 'c17d11', '8f5902', '8ae234', '73d216', '4e9a06',
		'729fcf', '3465a4', '204a87', 'ad7fa8', '75507b', '5c3566',
		'ef2929', 'cc0000', 'a40000',
		);

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new simple color selection widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('dom', 'event', 'container'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addJavaScript(
			'packages/swat/javascript/swat-simple-color-entry.js',
			Swat::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-z-index-manager.js',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-color-entry.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this simple color selector widget
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$container_div_tag = new SwatHtmlTag('div');
		$container_div_tag->id = $this->id;
		$container_div_tag->class = $this->getCSSClassString();
		$container_div_tag->open();

		$swatch_div = new SwatHtmlTag('div');
		$swatch_div->class = 'swat-simple-color-entry-swatch';
		$swatch_div->id = $this->id.'_swatch';
		$swatch_div->setContent('&nbsp;');
		$swatch_div->display();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';
		$input_tag->id = $this->id.'_value';
		$input_tag->name = $this->id;
		$input_tag->value = $this->value;
		$input_tag->accesskey = $this->access_key;

		$input_tag->display();

		$container_div_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this simple color selector widget
	 *
	 * @return string the current state of this simple color selector widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this simple color selector widget
	 *
	 * @param string $state the new state of this simple color selector widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this simple color
	 * entry widget
	 *
	 * @return array the array of CSS classes that are applied to this simple
	 *                color entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-simple-color-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets simple color selector inline JavaScript
	 *
	 * The JavaScript is the majority of the simple color selector code
	 *
	 * @return string simple color selector inline JavaScript.
	 */
	protected function getInlineJavaScript()
	{
		$colors = "'".implode("', '", $this->colors)."'";
		$javascript = "var {$this->id}_obj = new SwatSimpleColorEntry(".
			"'{$this->id}', [{$colors}]);";

		$javascript.= sprintf("{$this->id}_obj.set_text = %s",
			SwatString::quoteJavaScriptString('Set'));

		return $javascript;
	}

	// }}}
}

?>
