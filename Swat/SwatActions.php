<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatButton.php');
require_once('Swat/SwatFlydown.php');
require_once('Swat/SwatActionItem.php');
require_once('Swat/SwatUIParent.php');

/**
 * Actions widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatActions extends SwatControl implements SwatUIParent {
	
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
	public $show_blank = true;

	/**
	 * Auto-reset
	 *
	 * Whether to auto reset the action flydown to the default action
	 * after processing.
	 * @var boolean
	 */
	public $auto_reset = true;

	private $action_flydown;
	private $apply_button;

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
			$this->action_flydown->reset();

		// select the current action item based upon the flydown value
		if (isset($this->action_items[$this->action_flydown->value])) 
			$this->selected = $this->action_items[$this->action_flydown->value];
		else
			$this->selected = null;

		echo '<div class="swat-actions">';
		
		$label = new SwatHtmlTag('label');
		$label->for = $this->id.'_action_flydown';
		$label->open();
		echo sprintf('%s: ', _S('Action'));
		$label->close();
		
		$this->action_flydown->display();
		echo ' ';
		$this->apply_button->display();
		
		foreach ($this->action_items as $item) {
			if ($item->widget !== null) {
				$div = new SwatHtmlTag('div');
				
				$div->class = ($item == $this->selected) ?
					'swat-visible' : 'swat-hidden';
					
				$div->id = $this->id.'_'.$item->id;

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

		$this->action_flydown->process();
		$selected_id = $this->action_flydown->value;

		if (isset($this->action_items[$selected_id])) {
			$this->selected = $this->action_items[$selected_id];

			if ($this->selected->widget != null)
				$this->selected->widget->process();

		} else {
			$this->selected = null;
		}
	}

	/**
	 * Add an action item.
	 *
	 * Adds a SwatActionItem to this SwatActions widget.
	 *
	 * @param SwatActionItem $item A reference to the item to add.
	 */
	public function addActionItem(SwatActionItem $item) {
		$this->action_items[$item->id] = $item;
	}

	private function createWidgets() {	
		if ($this->created)
			return;
		
		$this->created = true;

		$this->action_flydown = new SwatFlydown($this->id.'_action_flydown');
		$this->action_flydown->onchange =
			"swatActionsDisplay(this, '{$this->id}');";
			
		$this->action_flydown->show_blank = $this->show_blank;

		foreach ($this->action_items as $item)
			$this->action_flydown->options[$item->id] = $item->title;

		$this->apply_button = new SwatButton($this->id.'_apply_button');
		$this->apply_button->setTitleFromStock('apply');
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-actions.js');
		echo '</script>';
	}

	/**
	 * Add a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface.  It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.  To add an action item to an actions object use 
	 * {@link SwatActions::addActionItem()}.
	 *
	 * @param $child A reference to a child object to add.
	 */
	public function addChild($child) {
		if ($child instanceof SwatActionItem)
			$this->addActionItem($child);
		else
			throw new SwatException('SwatActions: Only '.
				'SwatActionItems can be nested within SwatActions');
	}

}

?>
