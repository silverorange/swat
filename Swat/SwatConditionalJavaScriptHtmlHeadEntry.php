<?php

require_once 'Swat/SwatJavaScriptHtmlHeadEntry.php';

/**
 * @package   Swat
 * @copyright 2014-2015 silverorange
 */
class SwatConditionalJavaScriptHtmlHeadEntry extends SwatJavaScriptHtmlHeadEntry
{
	// {{{ protected properties

	/**
	 * Conditional expression used to limit display for Internet Explorer
	 *
	 * For example, 'lte IE 8' would display this JavaScriptHtmlHeadEntry
	 * only in IE 8 and below.
	 *
	 * @var string
	 *
	 * @see setIECondition()
	 */
	protected $ie_condition = 'lte IE 9';

	// }}}
	// {{{ public function setIECondition()

	/**
	 * Sets the conditional expression used to limit display for Internet
	 * Explorer
	 *
	 * For example, 'lte IE 8' would display this JavaScriptHtmlHeadEntry
	 * only in IE 8 and below.
	 *
	 * @param string $condition the conditional expression to use. Use an
	 *                          empty string for no conditional (display in
	 *                          all IE versions).
	 */
	public function setIECondition($condition)
	{
		$this->ie_condition = $condition;
	}

	// }}}
	// {{{ public function display()

	public function display($uri_prefix = '', $tag = null)
	{
		if ($this->ie_condition != '') {
			printf(
				'<!--[if %s]>',
				SwatString::minimizeEntities($this->ie_condition)
			);
		}

		parent::display($uri_prefix, $tag);

		if ($this->ie_condition != '') {
			echo '<![endif]-->';
		}
	}

	// }}}
	// {{{ public function displayInline()

	public function displayInline($path)
	{
		if ($this->ie_condition != '') {
			printf(
				'<!--[if %s]>',
				SwatString::minimizeEntities($this->ie_condition)
			);
		}

		parent::displayInline($path);

		if ($this->ie_condition != '') {
			echo '<![endif]-->';
		}
	}

	// }}}
}

?>
