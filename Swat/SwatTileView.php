<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatView.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A tile view widget for containing a {@link SwatTile} tile
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @lisence   http://www.gnu.org/copyleft/lesser.html LGPL Lisence 2.1
 * @see       SwatTile
 */
class SwatTileView extends SwatView implements SwatUIParent
{
	// {{{ public properties

	/**
	 * Show check all
	 *
	 * Whether to show a "check all" widget.  For this option to work, the
	 * table view must contain a column with an id of "checkbox".
	 * @var boolean
	 */
	public $show_check_all = true;

	// }}}
	// {{{ private properties

	/**
	 * The tile of this tile view
	 *
	 * @var SwatTile
	 */

	private $tile = null;

	/**
	 * The check-all widget for this tile view 
	 *
	 * @var SwatCheckAll
	 */
	private $check_all;

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
		parent::__construct();

		$this->addStyleSheet('packages/swat/styles/swat-tile-view.css',
			Swat::PACKAGE_ID);

		$this->addJavaScript('packages/swat/javascript/swat-tile-view.js',
			Swat::PACKAGE_ID);

		$this->addJavaScript(
			'packages/swat/javascript/swat-table-view-checkbox-column.js',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this tile view
	 *
	 * This initializes the tile view and the tile contained in the view.
	 *
	 * @see SwatWidget::init()
	 */
	public function init()
	{
		$this->createEmbeddedWidgets();
		$this->check_all->init();
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
		$tile->parent = $this;
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
			throw new SwatInvalidClassException(
				'Only SwatTile objects can be added to a SwatTileView.',
				0, $child);
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this tile view
	 *
	 * This tile view is display as a section of div tags.
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

		if ($this->showCheckAll())
			$this->check_all->display();

		$tile_view_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this tile view
	 *
	 * Process the tile contained by this tile view.
	 */
	public function process()
	{
		parent::process();

		$this->check_all->process();
		$this->tile->process();

		if ($this->hasCheckboxCellRenderer('items'))
			if (isset($_POST['items']) && is_array($_POST['items']))
				$this->checked_items = $_POST['items'];

	}

	// }}}
	// {{{ public function getDescendants()

	/**
	 * Gets descendant UI-objects
	 *
	 * @param string $class_name optional class name. If set, only UI-objects
	 *                            that are instances of <i>$class_name</i> are
	 *                            returned.
	 *
	 * @return array the descendant UI-objects of this tile view. If
	 *                descendant objects have identifiers, the identifier is
	 *                used as the array key.
	 *
	 * @see SwatUIParent::getDescendants()
	 */
	public function getDescendants($class_name = null)
	{
		if (!($class_name === null ||
			class_exists($class_name) || interface_exists($class_name)))
			return array();

		$out = array();

		if ($this->tile !== null) {
			if ($class_name === null || $this->tile instanceof $class_name) {
				if ($this->tile->id === null)
					$out[] = $this->tile;
				else
					$out[$this->tile->id] = $this->id;
			}

			if ($this->tile instanceof SwatUIParent)
				$out = array_merge($out,
					$this->tile->getDescendants($class_name));
		}

		return $out;
	}

	// }}}
	// {{{ public function getFirstDescendant()

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

		if ($this->tile instanceof SwatUIParent)
			$out = $this->tile->getFirstDescendant($class_name);

		if ($out === null && $this->tile instanceof $class_name)
			$out = $this->tile;

		return $out;
	}

	// }}}
	// {{{ public function getDescendantStates()

	/**
	 * Gets descendant states
	 *
	 * Retrieves an array of states of all stateful UI-objects in the widget
	 * subtree below this tile view.
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

	// }}}
	// {{{ public function setDescendantStates()

	/**
	 * Sets descendant states
	 *
	 * Sets states on all stateful UI-objects in the widget subtree below this
	 * tile view.
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
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this tile view
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this tile view.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

		if ($this->showCheckAll())
			$set->addEntrySet($this->check_all->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript required for this tile view 
	 *
	 * @return string the inline JavaScript required for this tile view.
	 *
	 * @see SwatTableViewRow::getInlineJavaScript()
	 */
	protected function getInlineJavaScript()
	{
		if (!$this->showCheckAll())
			return '';

		ob_start();

		printf("var %s = new SwatTileView('%s');",
			$this->id, $this->id);

		// TODO: SwatTableViewCheckboxColumn has all the functionality we need,
		// but it needs to somehow be renamed to be be shared between a
		// TableView and TileView (SwatViewCheckbox maybe?)
		printf("var %s = new SwatTableViewCheckboxColumn(%s, %s);",
			$this->id, "'items'", $this->id);

		echo $this->check_all->getInlineJavascript();

		// set the controller of the check-all widget
		printf("%s_obj.setController(%s);",
			$this->check_all->id, $this->id);

		return ob_get_clean();
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
	// {{{ protected function showCheckAll()

	/**
	 * @todo document me.
	 */
	protected function showCheckAll()
	{
		return ($this->show_check_all && $this->model->getRowCount() > 2
			&& $this->hasCheckboxCellRenderer('items'));
	}

	// }}}
	// {{{ protected function hasCheckboxCellRenderer()

	/**
	 * @todo document me.
	 */
	protected function hasCheckboxCellRenderer($renderer_id)
	{
		foreach ($this->tile->getRenderers() as $renderer)
			if ($renderer->id == $renderer_id)
				return true;

		return false;
	}

	// }}}
	// {{{ private function createEmbeddedWidgets()

	/**
	 * Creates internal widgets required for this tile view
	 */
	private function createEmbeddedWidgets()
	{
		$this->check_all = new SwatCheckAll();
		$this->check_all->parent = $this;
	}

	// }}}
}

?>
