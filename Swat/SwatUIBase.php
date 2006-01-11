<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * A base class for Swat user-interface elements
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatUIBase extends SwatObject
{
	// {{{ protected properties

	/**
	 * An array of HTML head entries needed by this user-interface element
	 *
	 * Entries are stored in a data object called {@link SwatHtmlHeadEntry}.
	 * This property contains an array of such objects.
	 *
	 * @var array
	 */
	protected $html_head_entries = array();

	// }}}
	// {{{ public function addStyleSheet()

	/**
	 * Adds a stylesheet to the list of stylesheets needed by this
	 * user-iterface element
	 *
	 * @param string $stylesheet the uri of the style sheet.
	 */
	public function addStyleSheet($stylesheet)
	{
		$this->html_head_entries[$stylesheet] =
			new SwatHtmlHeadEntry($stylesheet, SwatHtmlHeadEntry::TYPE_STYLE);
	}

	// }}}
	// {{{ public function addJavaScript()

	/**
	 * Adds a JavaScript include to the list of JavaScript includes needed
	 * by this user-interface element
	 *
	 * @param string $javascript the uri of the JavaScript include.
	 */
	public function addJavaScript($javascript)
	{
		$this->html_head_entries[$javascript] =
			new SwatHtmlHeadEntry($javascript,
			SwatHtmlHeadEntry::TYPE_JAVASCRIPT);
	}

	// }}}
	// {{{ abstract public function getHtmlHeadEntries()

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this user-interface
	 * element
	 *
	 * Head entries are things like stylesheets and javascript includes that
	 * should go in the head section of html.
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this
	 *                user-interface element.
	 */
	abstract public function getHtmlHeadEntries();

	// }}}
}

?>
