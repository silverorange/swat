<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A a tool link in the widget tree
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatToolLink extends SwatControl
{
	/**
	 * Link href
	 *
	 * The link may include a sprintf substitution tag. For example:
	 *    "MySection/MyPage?id=%s"
	 *
	 * @var string
	 */
	public $link = '';

	/**
	 * The title of this link
	 *
	 * @var string
	 */
	public $title = '';


	/**
	 * Link value
	 *
	 * A value to substitute into the link.
	 *
	 * @var string
	 */
	public $value = null;

	// TODO: add an optional image, and possibly stock images

	/**
	 * Displays this tool link
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$anchor = new SwatHtmlTag('a');

		if ($this->value === null)
			$anchor->href = $this->link;
		else
			$anchor->href = sprintf($this->link, $this->value);

		$anchor->content = $this->title;
		$anchor->class = 'swat-tool-link';

		$anchor->display();
	}	
}

?>
