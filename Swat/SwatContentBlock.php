<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatString.php';

/**
 * A block of content in the widget tree
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
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
	public function display(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		parent::display($context);

		if ($this->content_type === 'text/plain') {
			$context->out(SwatString::minimizeEntities($this->content));
		} else {
			$context->out($this->content);
		}
	}

	// }}}
}

?>
