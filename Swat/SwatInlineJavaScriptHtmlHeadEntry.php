<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/Swat.php';
require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * @package   Swat
 * @copyright 2012-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInlineJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
	// {{{ protected properties

	/**
	 * @var string
	 */
	protected $script;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $script the script of this entry.
	 */
	public function __construct($script)
	{
		parent::__construct(md5($script));
		$this->script = $script;
	}

	// }}}
	// {{{ protected function display()

	protected function displayInternal($uri_prefix = '', $tag = null)
	{
		Swat::displayInlineJavaScript($this->script);
	}

	// }}}
	// {{{ protected function displayInlineInternal()

	protected function displayInlineInternal($path)
	{
		$this->displayInternal();
	}

	// }}}
}

?>
