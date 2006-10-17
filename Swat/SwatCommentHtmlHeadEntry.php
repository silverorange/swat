<?php

require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * Stores and outputs an HTML head entry for an XML comment
 *
 * @package   Swat
 * @copyright 2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCommentHtmlHeadEntry extends SwatHtmlHeadEntry
{
	// {{{ protected properties

	protected $comment;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $comment the comment of this entry.
	 * @param integer $package_id the package id of the package this HTML head
	 *                             entry belongs to.
	 */
	public function __construct($comment, $package_id = null)
	{
		parent::__construct(md5($comment), $package_id);
		$this->comment = $comment;
	}

	// }}}
	// {{{ public function display()

	public function display($uri_prefix = '')
	{
		// double dashes are not allowed in XML comments
		$comment = str_replace('--', '—', $this->comment);
		printf('<!-- %s -->', $comment);
	}

	// }}}
	// {{{ public function displayInline()

	public function displayInline($path)
	{
		$this->display();
	}

	// }}}
}

?>
