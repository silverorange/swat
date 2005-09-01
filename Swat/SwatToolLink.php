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
	 * <code>
	 * $my_link->link = 'MySection/MyPage?id=%s';
	 * </code>
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
	 * The custom CSS class of this tool link
	 *
	 * This optional class is added on top of the default 'swat-tool-link'
	 * class.
	 *
	 * @var string
	 *
	 * @see SwatToolLink::setFromStock()
	 */
	public $class = null;

	/**
	 * A value to substitute into the link
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Displays this tool link
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$anchor_tag = new SwatHtmlTag('a');

		if ($this->value === null)
			$anchor_tag->href = $this->link;
		else
			$anchor_tag->href = sprintf($this->link, $this->value);

		if ($this->class !== null)
			$anchor_tag->class = 'swat-tool-link';
		else 
			$anchor_tag->class = 'swat-tool-link '.$this->class;

		$anchor_tag->content = $this->title;
		$anchor_tag->display();
	}

	/**
	 * Sets the values of this tool link to a stock type
	 *
	 * Valid stock type ids are:
	 *
	 * - edit
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 *
	 * @throws SwatException
	 */
	public function setFromStock($stock_id)
	{
		switch ($stock_id) {
		case 'edit':
			$this->class = 'swat-tool-link-edit';
			break;
		default:
			throw new SwatException(sprintf("%s: no stock type with the id ".
				"of '%s' exists.",
				__CLASS__,
				$stock_id));
		}
	}
}

?>
