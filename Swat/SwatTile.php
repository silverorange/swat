<?php

require_once 'Swat/SwatCellRendererContainer.php';

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

class SwatTile extends SwatCellRendererContainer
	implements SwatUIParent
{
	// {{{ public properties
	public $visible = true;
	
	// }}}
	// {{{ private properties
	private  $messages = array();

	// }}}
	// {{{ public function __construct()
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->require_id = true;
	}
	// }}}
	// {{{ public function display()
	public function display($data)
	{
		if (!$this->visible)
			return;
		foreach ($this->renderers as $renderer){
			$this->renderers->applyMappingsToRenderer($renderer, $data);
			echo '<div>';
			$renderer->render();
			echo '</div>';
		}
	}
	// }}}
	// {{{ public function init()
	public function init()
	{
		foreach ($this->renderers as $renderer)
			$renderer->init();
	}
	// }}}
	// {{{ public function process()
	public function process()
	{
		foreach ($this->renderers as $renderer)
			$renderer->process();
	}
	// }}}
	// {{{ public function getMessages()
	public function getMessages()
	{
		$messages = $this->messages;

		foreach ($this->renderers->renderers as $renderer)
			$messages = array_merge($messages, $renderer->getMessages());

		return $messages;
	}
	// }}}
	// {{{ public function addMessages()
	public function addMessage(SwatMessage $message)
	{
		$this->messages[] = $message;
	}
	// }}}
	// {{{ public function hasMessage()
	public function hasMessage()
	{
		$has_message = false;

		foreach ($this->renderers->renderers as $renderer){
			if ($renderer->hasMessage()){
				$has_message = true;
				break;
			}
		}
		
		return $has_message;
	}
	// }}}
}
?>
