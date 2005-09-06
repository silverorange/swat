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
	 * The stock id of this tool link
	 *
	 * Specifying a stock id initialized this tool link with a set of
	 * stock values.
	 *
	 * @var string
	 *
	 * @see SwatToolLink::setFromStock()
	 */
	public $stock_id = null;

	/**
	 * Initializes this widget
	 *
	 * Loads properties from stock if $stock_id is set.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		if ($this->stock_id !== null) 
			$this->setFromStock($this->stock_id, false);
	}

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
	 * - create
	 * - edit
	 * - delete
	 * - preview
	 *
	 * @param string $stock_id the identifier of the stock type to use.
	 * @param boolean $overwrite_properties whether to overwrite properties if
	 *                                       they are already set.
	 *
	 * @throws SwatException
	 */
	public function setFromStock($stock_id, $overwrite_properties = true)
	{
		switch ($stock_id) {
		case 'create':
			$title = Swat::_('Create');
			$class = 'swat-tool-link-create';
			break;

		case 'edit':
			$title = Swat::_('Edit');
			$class = 'swat-tool-link-edit';
			break;

		case 'delete':
			$title = Swat::_('Delete');
			$class = 'swat-tool-link-delete';
			break;

		case 'preview':
			$title = Swat::_('Preview');
			$class = 'swat-tool-link-preview';
			break;

		default:
			throw new SwatException("Stock type with id of '{$stock_id}' not ".
				'found.');
		}
		
		if ($overwrite_properties || ($this->title === null))
			$this->title = $title;

		$this->class = $class;
	}
}

?>
