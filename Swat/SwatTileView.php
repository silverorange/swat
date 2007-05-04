<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A tile view widget for containing a {@link SwatTile} tile
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 * @see       SwatTile
 */
class SwatTileView extends SwatControl implements SwatUIParent
{
	// {{{ public properties

	/**
	 * A data structure that holds the data to display in this view
	 *
	 * The data structure used is some form of {@link SwatTableModel}
	 *
	 * @var SwatTabelModel
	 */
	public $model = null;

	// }}}
	// {{{ private properties
	
	/**
	 * The tile of this tile view
	 *
	 * @var SwatTile
	 */

	private $tile = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new tile view
	 *
	 * @param string $id a non-visable unique id for this widget.
	 *
	 * @see SwatWidget:__construct()
	 */
	public function __construct()
	{
	}
	// }}}
	// {{{ public function init()

	/**
	 * Initializes this tile-view
	 *
	 * This initializes the tile view and the tile contained in the view.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		$this->tile->init();
	}
	// }}}
	// {{{ public function getTile()

	/**
	 * Gets a reference to a tile contained in the view.
	 *
	 * @return SwatTile the requested tile
	 */
	public function getTile()
	{
		return $this->tile;
	}
	// }}}
	// {{{ public function setTile()

	/**
	 * Sets a tile of this tile view
	 *
	 * @param SwatTile $tile the tile to set
	 */
	public function setTile(SwatTile $tile)
	{
		$this->tile = $tile;
	}
	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.
	 *
	 * To add a tile use {@link SwatTileView::SwatTile()}
	 *
	 * @param mixed $child a reference to a child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent
	 * @see SwatTileView::setTile()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatTile)
			$this->setTile($child);
		else
		throw new SwatInvalidClassException('You can only'.
			' add SwatTiles to this widget.', 0, $child);
	}
	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this tile view
	 *
	 * @return array the array of CSS classes that are applied to this tile
	 *                view.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-tile-view');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this tile view
	 *
	 * The tile view is display as a section of div tags
	 */
	public function display()
	{
		$tile_view_tag = new SwatHtmlTag('div');
		$tile_view_tag->id = $this->id;
		$tile_view_tag->class = $this->getCSSClassString();
		$tile_view_tag->open();

		$datas = $this->model->getRows();
		foreach ($datas as $data)
			$this->tile->display($data);

		$tile_view_tag->close();
	}
	// }}}
	// {{{ public function process()

	/**
	 * Processes this tile view
	 *
	 * Process the tile contained by this tile view
	 */
	public function process()
	{
		parent::process();

		$this->tile->process();
	}
	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from this tile view
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		$messages = parent::getMessages();
		$messages = array_merge($messages. $this->tile->messages);

		return $messages;
	}
	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets whether or not this tile view has any messages
	 *
	 * @return boolean true if this tile view has one or more messages and
	 *						false if it does not.
	 */
	public function hasMessage()
	{
		$has_message = parent::hasMessages();

		if ($this->tile->hasMessages())
			$has_message = true;

		return $has_message;
	}
	// }}}
	
}

?>
