<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatButton.php');
require_once('Swat/SwatFlydown.php');
require_once('Swat/SwatActionItem.php');

/**
 * Actions widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatActions extends SwatControl {
	
	/**
	 * Selected action
	 * 
	 * The currently selected action item, or null.
	 * @var SwatActionItem
	 */
	public $selected = null;

	/**
	 * Show blank
	 *
	 * Whether to show an inital blank option in the flydown.
	 * @var boolean
	 */
	public $show_blank_option = true;

	/**
	 * Auto-reset
	 *
	 * Whether to auto reset the action flydown to the default action
	 * after processing.
	 * @var boolean
	 */
	public $auto_reset = true;

	private $actionfly;
	private $btn_apply;

	private $action_items;
	private $created = false;

	public function init() {
		$this->action_items = array();
	}
		
	public function display() {
		if (!$this->visible)
			return;

		$this->createWidgets();
		$this->displayJavascript();
		
		// set the flydown back to its initial state (no persistence)
		if ($this->auto_reset)
			$this->actionfly->reset();

		// select the current action item based upon the flydown value
		if (isset($this->action_items[$this->actionfly->value])) 
			$this->selected = $this->action_items[$this->actionfly->value];
		else
			$this->selected = null;

		echo '<div class="swat-actions">';
		echo _S('Action: ');
		$this->actionfly->display();
		echo ' ';
		$this->btn_apply->display();
		
		foreach ($this->action_items as $item) {
			if ($item->widget != null) {
				$div = new SwatHtmlTag('div');
				$div->class = ($item == $this->selected)? 'swat-visible': 'swat-hidden';
				$div->id = $this->name.'_'.$item->name;

				$div->open();
				$item->display();
				$div->close();
			}
		}

		echo '<br />', _S('Actions apply to checked items.');
		echo '</div>';
		
	}
	
	public function process() {
		$this->createWidgets();

		$this->actionfly->process();
		$selected_name = $this->actionfly->value;

		if (isset($this->action_items[$selected_name])) {
			$this->selected = $this->action_items[$selected_name];

			if ($this->selected->widget != null)
				$this->selected->widget->process();

		} else {
			$this->selected = null;
		}
	}

	/**
	 * Add an action item.
	 * Adds a SwatActionItem to this SwatActions widget.
	 * @param SwatActionItem $item A reference to the item to add.
	 */
	public function addActionItem(SwatActionItem $item) {
		$this->action_items[$item->name] = $item;
	}

	private function createWidgets() {	
		if ($this->created) return;
		
		$this->created = true;

		$this->actionfly = new SwatFlydown($this->name.'_actionfly');
		$this->actionfly->onchange = "swatActionsDisplay(this, '{$this->name}');";

		if ($this->show_blank_option)
			$this->actionfly->options = array('');

		foreach ($this->action_items as $item)
			$this->actionfly->options[$item->name] = $item->title;

		$this->btn_apply = new SwatButton($this->name.'_btn_apply');
		$this->btn_apply->setTitleFromStock('apply');
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-actions.js');
		echo '</script>';
	}
}
?>
