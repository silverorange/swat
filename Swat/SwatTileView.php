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
	// {{{ public function display()
	public function display()
	{
		$datas = $this->model->getRows();
		foreach ($datas as $data)
			$this->tile->display($data);
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
