<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatState.php';

/**
 * Simple color selector widget.
 *
 * This color selector displays a simple palette to the user with a set of
 * predefined color choices. It requires JavaScript to work correctly.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
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
	 * @var array
	 *
	 * @todo create a default palette for the simple color entry widget.
	 */
	public $colors = array(
		'eae8e3', 'bab5ab', '807d74', '565248', 'ffffff', '000000',
		'c5d2c8', '83a67f', '5d7555', '445632', '46a046', '267726',
		'e0b6af', 'c1665a', '884631', '663822', 'df421e', '990000',
		'efe0cd', 'e0c39e', 'b39169', '826647', 'eed680', 'd1940c',
		'ada7c8', '887fa3', '635b81', '494066',
		'9db8d2', '7590ae', '4b6983', '314e6c',
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

		$this->addJavaScript('packages/swat/javascript/swat-simple-color-entry.js');
		$this->addJavaScript('packages/swat/javascript/swat-z-index-manager.js');
		$this->addStyleSheet('packages/swat/styles/swat-color-entry.css');
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

		$container_div_tag = new SwatHtmlTag('div');
		$container_div_tag->id = $this->id;
		$container_div_tag->class = $this->getCSSClassString();
		$container_div_tag->open();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'hidden';
		$input_tag->id = $this->id.'_value';
		$input_tag->name = $this->id;
		$input_tag->value = $this->value;
		if (strlen($this->access_key) > 0)
			$input_tag->accesskey = $this->access_key;

		$input_tag->display();

		$swatch_div = new SwatHtmlTag('div');
		$swatch_div->class = 'swat-simple-color-entry-swatch';
		$swatch_div->id = $this->id.'_swatch';
		$swatch_div->setContent('&nbsp;');
		$swatch_div->display();

		$anchor_tag = new SwatHtmlTag('a');
		$anchor_tag->href = "javascript:{$this->id}_obj.toggle();";
		$anchor_tag->open();

		$img_tag = new SwatHtmlTag('img');
		$img_tag->src = 'packages/swat/images/color-palette.png';
		$img_tag->class = 'swat-simple-color-entry-toggle';
		$img_tag->id = $this->id.'_toggle';

		$img_tag->display();

		$anchor_tag->close();

		echo '<br />';

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id.'_palette';
		$div_tag->class = 'swat-simple-color-palette-hidden';
		$div_tag->setContent('&nbsp;');
		$div_tag->display();

		$container_div_tag->close();

		$this->displayJavaScript();
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
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ private function displayJavaScript()

	/**
	 * Displays simple color selector JavaScript
	 *
	 * The JavaScript is the majority of the simple color selector code
	 */
	private function displayJavaScript()
	{
		$colors = "'".implode("', '", $this->colors)."'";

		echo '<script type="text/javascript">'."\n";

		echo "{$this->id}_obj = new SwatSimpleColorEntry(".
			"'{$this->id}', [{$colors}]);";

		echo "\n</script>";
	}

	// }}}
}

?>
