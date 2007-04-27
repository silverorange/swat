<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

class SwatTileView extends SwatControl implements SwatUIParent
{
	// {{{ public properties
	public $model = null;
	// }}}
	// {{{ private properties
	private $tile = null;
	// }}}
	// {{{ public function __construct()
	public function __construct()
	{
	}
	// }}}
	// {{{ public function init()
	public function init()
	{
		$this->tile->init();
	}
	// }}}
	// {{{ public function getTile()
	public function getTile()
	{
		return $this->tile;
	}
	// }}}
	// {{{ public function setTile()
	public function setTile(SwatTile $tile)
	{
		$this->tile = $tile;
	}
	// }}}
	// {{{ public function addChild()
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
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ public function display()
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
	public function process()
	{
		parent::process();

		$this->tile->process();
	}
	// }}}
	// {{{ public function getMessages()
	public function getMessages()
	{
		$messages = parent::getMessages();
		$messages = array_merge($messages. $this->tile->messages);

		return $messages;
	}
	// }}}
	// {{{ public function hasMessage()
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
