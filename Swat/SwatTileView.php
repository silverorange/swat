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
	 * Whether to show a "check all" widget
	 *
	 * For this option to have an effect, this tile view's tile must contain a
	 * {@link SwatCheckboxCellRenderer}.
	 *
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * Optional label title for the check-all widget
	 *
	 * Defaults to "Check All".
	 *
	 * @var string
	 */
	public $check_all_title;

	/**
	 * Optional content type for check-all widget title
	 *
	 * Defaults to text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $check_all_content_type = 'text/plain';

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
	 *
	 * @see SwatTileView::$show_check_all
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
	public function __construct($id = null)
	{
		parent::__construct($id);

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
	 * @see SwatView::init()
	 */
	public function init()
	{
		parent::init();

		$this->createEmbeddedWidgets();
		$this->check_all->init();
		if ($this->tile !== null)
			$this->tile->init();
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
		if ($this->tile !== null)
			$this->tile->process();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this tile view
	 */
	public function display()
	{
		$tile_view_tag = new SwatHtmlTag('div');
		$tile_view_tag->id = $this->id;
		$tile_view_tag->class = $this->getCSSClassString();
		$tile_view_tag->open();

		if ($this->tile !== null)
			foreach ($this->model as $data)
				$this->tile->display($data);

		if ($this->showCheckAll()) {
			if ($this->check_all_title !== null) {
				$this->check_all->title = $this->check_all_title;
				$this->check_all->content_type = $this->check_all_content_type;
			}
			$this->check_all->display();
		}

		$clear_div_tag = new SwatHtmlTag('div');
		$clear_div_tag->style = 'clear: left;';
		$clear_div_tag->setContent('');
		$clear_div_tag->display();

		$tile_view_tag->close();

		Swat::displayInlineJavaScript($this->getInlineJavaScript());
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
		// if we're overwriting an existing tile, remove it's parent link
		if ($this->tile !== null)
			$this->tile->parent = null;

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
	 * @param SwatTile $child a reference to a child object to add.
	 *
	 * @throws SwatInvalidClassException if the added object is not a tile.
	 * @throws SwatException if more than one tile is added to this tile view.
	 *
	 * @see SwatUIParent
	 * @see SwatTileView::setTile()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatTile) {
			if ($this->tile !== null)
				throw new SwatException(
					'Only one tile may be added to a tile view.');

			$this->setTile($child);
		} else {
			throw new SwatInvalidClassException(
				'Only SwatTile objects can be added to a SwatTileView.',
				0, $child);
		}
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
		if ($this->tile !== null)
			$messages = array_merge($messages, $this->tile->messages);

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
		$has_message = parent::hasMessage();
		if (!$has_message && $this->tile !== null)
			$has_message = $this->tile->hasMessage();

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

		if ($this->tile !== null)
			$set->addEntrySet($this->tile->getHtmlHeadEntrySet());

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
		$javascript = sprintf("var %s = new SwatTileView('%s');",
			$this->id, $this->id);

		if ($this->tile !== null) {
			$tile_javascript = $this->tile->getRendererInlineJavaScript();
			if (strlen($tile_javascript) > 0)
				$javascript.= $tile_javascript;

			$tile_javascript = $this->tile->getInlineJavaScript();
			if (strlen($tile_javascript) > 0)
				$javascript.= "\n".$tile_javascript;
		}

		if ($this->showCheckAll()) {
			$renderer = $this->getCheckboxCellRenderer();
			$javascript.= "\n".$this->check_all->getInlineJavascript();

			// set the controller of the check-all widget
			$javascript.= sprintf("\n%s_obj.setController(%s);",
				$this->check_all->id, $renderer->id);
		}

		return $javascript;
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
	 * Whether or not a check-all widget is to be displayed for the tiles
	 * of this tile view
	 *
	 * This depends on the {@link SwatTileView::$show_check_all} property as
	 * well as whether or not this tile view contains a
	 * {@link SwatCheckboxCellRenderer} and whether or not this tile view
	 * contains enough tiles to warrent having a check-all widget
	 *
	 * @return boolean true if a check-all widget is to be displayed for this
	 *                  tile view and false if it is not.
	 */
	protected function showCheckAll()
	{
		return ($this->show_check_all && count($this->model) > 2
			&& $this->getCheckboxCellRenderer() !== null);
	}

	// }}}
	// {{{ protected function getCheckboxCellRenderer()

	/**
	 * Gets the first checkbox cell renderer in this tile view's tile
	 *
	 * @return SwatCheckboxCellRenderer the first checkbox cell renderer in
	 *                                   this tile view's tile or null if no
	 *                                   such cell renderer exists.
	 */
	protected function getCheckboxCellRenderer()
	{
		$checkbox_cell_renderer = null;

		foreach ($this->tile->getRenderers() as $renderer) {
			if ($renderer instanceof SwatCheckboxCellRenderer) {
				$checkbox_cell_renderer = $renderer;
				break;
			}
		}

		return $checkbox_cell_renderer;
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
