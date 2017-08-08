<?php

/**
 * @package   Swat
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInlineJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{

	/**
	 * @var string
	 */
	protected $script;

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

	protected function displayInternal($uri_prefix = '', $tag = null)
	{
		Swat::displayInlineJavaScript($this->script);
	}

	protected function displayInlineInternal($path)
	{
		$this->displayInternal();
	}

}

?>
