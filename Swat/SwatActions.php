<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatButton.php';
require_once 'Swat/SwatFlydown.php';
require_once 'Swat/SwatActionItem.php';
require_once 'Swat/SwatUIParent.php';

/**
 * Actions widget
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatActions extends SwatControl implements SwatUIParent
{
	/**
	 * Selected action
	 * 
	 * The currently selected action item, or null.
	 *
	 * @var SwatActionItem
	 */
	public $selected = null;

	/**
	 * Show blank
	 *
	 * Whether to show an inital blank option in the flydown.
	 *
	 * @var boolean
	 */
	public $show_blank = true;

	/**
	 * Auto-reset
	 *
	 * Whether to auto reset the action flydown to the default action
	 * after processing.
	 *
	 * @var boolean
	 */
	public $auto_reset = true;

	/**
	 * A reference to an internal flydown widget that displays available
	 * actions.
	 *
	 * @var SwatFlydown
	 */
	private $action_flydown = null;

	/**
	 * A reference to an internal button that is clicked to carry out the
	 * currently selected action.
	 *
	 * @var SwatButton
	 */
	private $apply_button;

	/**
	 * The available actions for this actions selector.
	 *
	 * @var array
	 */
	private $action_items = array();

	/**
	 * An internal flag that is set to true when internal widgets have been
	 * created.
	 *
	 * @var boolean
	 */
	private $widgets_created = false;
	
	/**
	 * Displays this list of actions
	 *
	 * Internal widgets are automatically created if they do not exist.
	 * Javascript is displayed, then the display methods of the internal
	 * widgets are called.
	 */
	public function display()
	{
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
		$label->content = sprintf('%s: ', Swat::_('Action'));
		$label->display();
		
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

		echo '<div class="swat-actions-note">';
		echo Swat::_('Actions apply to checked items.');
		echo '</div>';
		echo '</div>';
		
	}
	
	/**
	 * Figures out what action item is selected
	 *
	 * This method creates internal widgets if they do not exist, and then
	 * determines what SwatActionItem was selected by the user by calling
	 * the process methods of the internal widgets.
	 */
	public function process()
	{
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
	 * Adds an action item
	 *
	 * Adds a SwatActionItem to this SwatActions widget.
	 *
	 * @param SwatActionItem $item a reference to the item to add.
	 *
	 * @see SwatActionItem
	 */
	public function addActionItem(SwatActionItem $item)
	{
		$this->action_items[$item->id] = $item;
	}

	/**
	 * Adds a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add an action item to an actions object use 
	 * {@link SwatActions::addActionItem()}.
	 *
	 * @param $child A reference to a child object to add.
	 *
	 * @see SwatUIParent, SwatUI, SwatActions::addActionItem()
	 */
	public function addChild($child)
	{
		if ($child instanceof SwatActionItem)
			$this->addActionItem($child);
		else
			throw new SwatException('SwatActions: Only '.
				'SwatActionItems can be nested within SwatActions');
	}

	/**
	 * Creates internal widgets
	 *
	 * Widgets references are assigned to private class properties. Widgets are
	 * only created once even if this method is called multiple times.
	 */
	private function createWidgets()
	{
		if ($this->widgets_created)
			return;
		
		$this->widgets_created = true;

		$this->action_flydown = new SwatFlydown($this->id.'_action_flydown');
		$this->action_flydown->onchange =
			"swatActionsDisplay(this, '{$this->id}');";
			
		$this->action_flydown->show_blank = $this->show_blank;

		foreach ($this->action_items as $item)
			$this->action_flydown->addOption($item->id, $item->title);

		$this->apply_button = new SwatButton($this->id.'_apply_button');
		$this->apply_button->setTitleFromStock('apply');
	}

	/**
	 * Loads the Javascript required for this control
	 *
	 * Javascript is only loaded once to make downloads faster and so that
	 * variable names do not collide in the javascript.
	 */
	private function displayJavascript()
	{
		echo '<script type="text/javascript">';
		echo '//<![CDATA[';
		include_once 'Swat/javascript/swat-actions.js';
		echo '//]]>';
		echo '</script>';
	}
}

?>
