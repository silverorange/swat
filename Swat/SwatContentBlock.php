<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

/**
 * A block of content in the widget tree
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatContentBlock extends SwatControl
{
	// {{{ public properties

	/**
	 * User visible textual content of this widget
	 *
	 * @var string
	 */
	public $content = '';

	/**
	 * Optional content type
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $content_type = 'text/plain';

	// }}}
	// {{{ public function display()

	/**
	 * Displays this content
	 *
	 * Merely performs an echo of the content.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		if ($this->content_type === 'text/plain')
			echo SwatString::minimizeEntities($this->content);
		else
			echo $this->content;
	}

	// }}}
}

?>
