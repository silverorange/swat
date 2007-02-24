<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatYUI.php';

class SwatMenuItem extends SwatControl
{
	public $uri;
	public $title;

	/**
	 * @var SwatMenu
	 */
	protected $sub_menu;

	public function setSubMenu(SwatAbstractMenu $menu)
	{
		$this->sub_menu = $menu;
	}

	public function init()
	{
		if ($this->sub_menu !== null)
			$this->sub_menu->init();
	}

	public function display()
	{
		if ($this->uri === null) {
			echo SwatString::minimizeEntities($this->title);
		} else {
			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = $this->uri;
			$anchor_tag->setContent($this->title);
			$anchor_tag->display();
		}

		if ($this->sub_menu !== null) {
			$this->sub_menu->display(false);
		}
	}
}

class SwatMenuGroup extends SwatControl
{
	public $title;

	protected $items = array();

	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
	}

	public function display($first = false)
	{
		$header_tag = new SwatHtmlTag('h6');
		if ($first)
			$header_tag->class = 'first-of-type';

		$header_tag->setContent($this->title);
		$header_tag->display();

		$ul_tag = new SwatHtmlTag('ul');
		if ($first)
			$ul_tag->class = 'first-of-type';

		$ul_tag->open();

		foreach ($this->items as $item) {
			echo '<li class="yuimenuitem">';
			$item->display();
			echo '</li>';
		}

		$ul_tag->close();
	}
}

abstract class SwatAbstractMenu extends SwatControl
{
	public $click_to_hide = true;
	public $auto_sub_menu_display = true;

	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;

		$yui = new SwatYUI(array('menu'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
		$this->addStyleSheet('packages/swat/styles/swat-menu.css',
			Swat::PACKAGE_ID);
	}

	protected function getJavaScriptClass()
	{
		return 'YAHOO.widget.Menu';
	}

	protected function getInlineJavaScript()
	{
		$properties = sprintf('{ clicktohide: %s, autosubmenudisplay: %s, width: \'100px\', position: \'dynamic\' }',
			$this->click_to_hide ? 'true' : 'false',
			$this->auto_sub_menu_display ? 'true' : 'false');

		$javascript = sprintf("var %s_obj = new %s('%s', %s);",
			$this->id,
			$this->getJavaScriptClass(),
			$this->id,
			$properties);

		$javascript.= sprintf("\n%s_obj.render('new-menu');\n%s_obj.show();",
			$this->id,
			$this->id);

		return $javascript;
	}
}

class SwatMenu extends SwatAbstractMenu
{
	protected $items = array();

	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
	}

	public function init()
	{
		parent::init();
		foreach ($this->items as $item)
			$item->init();
	}

	public function display($top_level = true)
	{
		$displayed_classes = array();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'yuimenu';
		$div_tag->open();

		echo '<div class="bd">';

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->class = 'first-of-type';
		$ul_tag->open();

		foreach ($this->items as $item) {
			echo '<li class="yuimenuitem">';
			$item->display();
			echo '</li>';
		}

		$ul_tag->close();

		echo '</div>';

		$div_tag->close();

		if ($top_level)
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}
}

class SwatMenuBar extends SwatMenu
{
	protected function getJavaScriptClass()
	{
		return 'YAHOO.widget.MenuBar';
	}
}

class SwatGroupedMenu extends SwatAbstractMenu
{
	protected $groups = array();

	public function init()
	{
		parent::init();
		foreach ($this->groups as $group)
			$group->init();
	}

	public function addGroup(SwatMenuGroup $group)
	{
		$this->groups[] = $group;
	}

	public function display($top_level = true)
	{
		$displayed_classes = array();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'yuimenu';
		$div_tag->open();

		echo '<div class="bd">';

		$first = true;
		foreach ($this->groups as $group) {
			if ($first) {
				$group->display(true);
				$first = false;
			} else {
				$group->display();
			}
		}

		echo '</div>';

		$div_tag->close();

		if ($top_level)
			$this->displayInlineJavaScript($this->getInlineJavaScript());
	}
}

?>
