<?php

/**
 * A basic menu control
 *
 * @package   Swat
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 *
 * @see SwatMenuItem
 */
class SwatMenu extends SwatAbstractMenu implements SwatUIParent
{

	/**
	 * The set of SwatMenuItem objects contained in this menu
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Adds a menu item to this menu
	 *
	 * @param SwatMenuItem $item the item to add.
	 */
	public function addItem(SwatMenuItem $item)
	{
		$this->items[] = $item;
		$item->parent = $this;
	}

	/**
	 * Adds a child object
	 *
	 * This method fulfills the {@link SwatUIParent} interface. It is used
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a menu item to a menu, use
	 * {@link SwatMenu::addItem()}.
	 *
	 * @param SwatMenuItem $child the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent, SwatUI, SwatMenu::addItem()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatMenuItem)
			$this->addItem($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatMenuItem objects may be nested within a '.
				'SwatMenu object.', 0, $child);
	}

	/**
	 * Initializes this menu
	 */
	public function init()
	{
		parent::init();
		foreach ($this->items as $item)
			$item->init();
	}

	/**
	 * Displays this menu
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$displayed_classes = array();

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		echo '<div class="bd">';

		$ul_tag = new SwatHtmlTag('ul');
		$ul_tag->class = 'first-of-type';
		$ul_tag->open();

		$li_tag = new SwatHtmlTag('li');
		$li_tag->class = $this->getMenuItemCSSClassName().' first-of-type';
		$first = true;
		foreach ($this->items as $item) {
			ob_start();
			$item->display();
			$content = ob_get_clean();
			if ($content != '') {
				$li_tag->setContent($content, 'text/xml');
				$li_tag->display();

				if ($first) {
					$li_tag->class = $this->getMenuItemCSSClassName();
					$first = false;
				}
			}
		}

		$ul_tag->close();

		echo '</div>';

		$div_tag->close();

		if ($this->parent === null || !($this->parent instanceof SwatMenuItem))
			Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this menu. If descendant
	 *                objects have identifiers, the identifier is used as the
	 *                array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		foreach ($this->items as $item) {
			if ($class_name === null || $item instanceof $class_name) {
				if ($item->id === null)
					$out[] = $item;
				else
					$out[$item->id] = $item;
			}

			if ($item instanceof SwatUIParent)
				$out = array_merge($out, $item->getDescendants($class_name));
		}

		return $out;
	}

	/**
	 * Gets the first descendant UI-object of a specific class
	 *
	 * @param string $class_name class name to look for.
	 *
	 * @return SwatUIObject the first descendant UI-object or null if no
	 *                       matching descendant is found.
	 *
	 * @see SwatUIParent::getFirstDescendant()
	 */
	public function getFirstDescendant($class_name)
	{
		if (!class_exists($class_name) && !interface_exists($class_name))
			return null;

		$out = null;

		foreach ($this->items as $item) {
			if ($item instanceof $class_name) {
				$out = $item;
				break;
			}

			if ($item instanceof SwatUIParent) {
				$out = $item->getFirstDescendant($class_name);
				if ($out !== null)
					break;
			}
		}

		return $out;
	}

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this menu.
	 *
	 * @return array an array of UI-object states with UI-object identifiers as
	 *                array keys.
	 */
	public function getDescendantStates()
	{
		$states = array();

		foreach ($this->getDescendants('SwatState') as $id => $object)
			$states[$id] = $object->getState();

		return $states;
	}

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * menu.
	 *
	 * @param array $states an array of UI-object states with UI-object
	 *                       identifiers as array keys.
	 */
	public function setDescendantStates(array $states)
	{
		foreach ($this->getDescendants('SwatState') as $id => $object)
			if (isset($states[$id]))
				$object->setState($states[$id]);
	}

	/**
	 * Performs a deep copy of the UI tree starting with this UI object
	 *
	 * @param string $id_suffix optional. A suffix to append to copied UI
	 *                           objects in the UI tree.
	 *
	 * @return SwatUIObject a deep copy of the UI tree starting with this UI
	 *                       object.
	 *
	 * @see SwatUIObject::copy()
	 */
	public function copy($id_suffix = '')
	{
		$copy = parent::copy($id_suffix);

		foreach ($this->items as $key => $item) {
			$copy_item = $item->copy($id_suffix);
			$copy_item->parent = $copy;
			$copy->items[$key] = $copy_item;
		}

		return $copy;
	}

	/**
	 * Gets the CSS class name to use for menu items in this menu
	 *
	 * @return string the CSS class name to use for menu items in this menu.
	 */
	protected function getMenuItemCSSClassName()
	{
		return 'yuimenuitem';
	}

	/**
	 * Gets the array of CSS classes that are applied to this menu
	 *
	 * @return array the array of CSS classes that are applied to this menu.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('yuimenu');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

}

?>
