<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatAbstractOverlay.php';

/**
 * Simple color selector widget.
 *
 * This color selector displays a simple palette to the user with a set of
 * predefined color choices. It requires JavaScript to work correctly.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatSimpleColorEntry extends SwatAbstractOverlay
{
	// {{{ public properties

	/**
	 * Show "none" option
	 *
	 * Whether or not to show an option for selected no color
	 *
	 * @var boolean
	 */
	public $none_option = true;

	/**
	 * "None" option title
	 *
	 * @var string
	 */
	public $none_option_title = null;

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
		'ffffff', 'eeeeec', 'd3d7cf', 'babdb6', '888a85', '666666',
		'555753', '2e3436', '000000', 'fce94f', 'edd400', 'c4a000',
		'fcaf3e', 'f57900', 'ce5c00', 'e9b96e', 'c17d11', '8f5902',
		'8ae234', '73d216', '4e9a06', '729fcf', '3465a4', '204a87',
		'ad7fa8', '75507b', '5c3566', 'ef2929', 'cc0000', 'a40000',
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

		if ($this->none_option_title === null) {
			$this->none_option_title = Swat::_('None');
		}

		$this->addJavaScript(
			'packages/swat/javascript/swat-simple-color-entry.js'
		);

		$this->addJavaScript(
			'packages/swat/javascript/swat-abstract-overlay.js'
		);

		$this->addStyleSheet('packages/swat/styles/swat-color-entry.css');
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this color entry
	 *
	 * Ensures this color is a valid hex color.
	 */
	public function process()
	{
		parent::process();

		$data = &$this->getForm()->getFormData();
		if (isset($data[$this->id])) {
			$this->value = $data[$this->id];
		} else {
			$this->value = null;
		}

		$this->value = ltrim($this->value, '#');

		if ($this->value == '') {
			$this->value = null;
		}

		if ($this->required && $this->value === null) {
			$this->addMessage($this->getValidationMessage('required'));

		} elseif ($this->value !== null &&
			!$this->validateColor($this->value)) {
			$message = sprintf(Swat::_('“%s” is not a valid color.'),
				$this->value);

			$this->addMessage(new SwatMessage($message, 'error'));
		}
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
		$javascript = parent::getInlineJavaScript();

		$colors = "'".implode("', '", $this->colors)."'";

		if ($this->none_option) {
			$none_option = ($this->none_option_title === null) ? 'null' :
				SwatString::quoteJavaScriptString($this->none_option_title);
		} else {
			$none_option = 'null';
		}

		$js_class_name = $this->getJavaScriptClassName();

		$javascript.= "\nvar {$this->id}_obj = new {$js_class_name}(".
			"'{$this->id}', [{$colors}], {$none_option});\n";

		return $javascript;
	}

	// }}}
	// {{{ protected function validateColor()

	/**
	 * Validates a color
	 *
	 * A valid color is a 3 or 6 character hex string with values between
	 * 0 and 255.
	 *
	 * @param string $value the color to validate.
	 *
	 * @return boolean true if <i>$value</i> is a valid color and
	 *                  false if it is not.
	 */
	protected function validateColor($value)
	{
		$valid = false;

		if (strlen($value) == 3) {
			$regexp = '/^[0-9a-f]{3}/ui';
		} elseif (strlen($value) == 6) {
			$regexp = '/^[0-9a-f]{6}/ui';
		} else {
			$regexp = false;
		}

		if ($regexp !== false) {
			$valid = (preg_match($regexp, $this->value) === 1);
		}

		return $valid;
	}

	// }}}
	// {{{ protected function getJavaScriptClassName()

	/**
	 * Get the name of the JavaScript class for this widget
	 *
	 * @return string JavaScript class name.
	 */
	protected function getJavaScriptClassName()
	{
		return 'SwatSimpleColorEntry';
	}

	// }}}
}

?>
