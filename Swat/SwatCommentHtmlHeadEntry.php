<?php

/**
 * Stores and outputs an HTML head entry for an XML comment
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCommentHtmlHeadEntry extends SwatHtmlHeadEntry
{

	protected $comment;

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $comment the comment of this entry.
	 */
	public function __construct($comment)
	{
		parent::__construct(md5($comment));
		$this->comment = $comment;
	}

	protected function displayInternal($uri_prefix = '', $tag = null)
	{
		// double dashes are not allowed in XML comments
		$comment = str_replace('--', '—', $this->comment);
		printf('<!-- %s -->', $comment);
	}

	protected function displayInlineInternal($path)
	{
		$this->displayInternal();
	}

}

?>
