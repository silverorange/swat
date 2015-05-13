<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatHtmlHeadEntry.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * Stores and outputs an HTML head entry for an XHTML link element
 *
 * @package   Swat
 * @copyright 2008-2015 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatLinkHtmlHeadEntry extends SwatHtmlHeadEntry
{
	// {{{ protected properties

	/**
	 * The URI linked to by this link
	 *
	 * @var string
	 */
	protected $link_uri;

	/**
	 * The title of this link
	 *
	 * @var string
	 */
	protected $title;

	/**
	 * How this link relates to the containing document
	 *
	 * @var string
	 */
	protected $relationship;

	/**
	 * The media type of the content linked to by this link
	 *
	 * @var string
	 */
	protected $type;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new link HTML head entry
	 *
	 * @param string $uri the URI linked to by this link.
	 * @param string $relationship optional. How this link relates to the
	 *                              containing document.
	 * @param string $type optional. The media type of the content linked to by
	 *                      this link.
	 * @param string $title optional. The title of this link.
	 */
	public function __construct($uri, $relationship = null, $type = null,
		$title = null)
	{
		$hash = md5($uri.$relationship.$type.$title);
		parent::__construct($hash);

		$this->link_uri = $uri;
		$this->relationship = $relationship;
		$this->type = $type;
		$this->title = $title;
	}

	// }}}
	// {{{ protected function displayInternal()

	protected function displayInternal($uri_prefix = '', $tag = null)
	{
		$link = new SwatHtmlTag('link');
		$link->title = $this->title;
		$link->rel = $this->relationship;
		$link->type = $this->type;
		$link->href = $this->link_uri;
		$link->display();
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
