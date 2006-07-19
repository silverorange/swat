<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 *
 *
 */
class SwatWidgetCellRenderer extends SwatCellRenderer implements SwatUIParent,
	SwatTitleable
{
	// {{{ public properties

	/**
	 * Unique value used to uniquely identify the replicated widget.
	 * If null, no replicating is done and the prototype widget is used.
	 */
	public $replicator_id = null;

	// }}}
	// {{{ private properties

	/**
	 * A reference to the widget for this cell
	 */
	private $widget = null;

	private $mappings = array();
	private $clones = array();

	// }}}
	// {{{ public function addChild()

	/**
	 * fufills addChild
	 *
	 * @throws SwatException
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->widget === null)
			$this->setWidget($child);
		else
			throw new SwatException('Can only add one widget to a widget cell '.
				'renderer');
	}

	// }}}
	// {{{ public function getPropertyNameToMap()

	public function getPropertyNameToMap(SwatUIObject $object, $name)
	{
		$mangled_name = $name;
		$suffix = 0;

		while (array_key_exists($mangled_name, $this->mappings)) {
			$mangled_name = $name.$suffix;
			$suffix++;
		}

		$this->mappings[$mangled_name] = 
			array('object' => $object, 'property' => $name);

		return $mangled_name;
	}

	// }}}
	// {{{ public function __set()

	/**
	 * Maps a data field to a property of a widget in the widget tree
	 *
	 * TODO: document me better
	 */
	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->mappings)) {
			$object = $this->mappings[$name]['object'];
			$property = $this->mappings[$name]['property'];
			$object->$property = $value;
		} else {
			// TODO: throw something meaningful
			throw new SwatException();
		}
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this cell renderer
	 *
	 * This calls {@link SwatWidget::init()} on this renderer's widget.
	 */
	public function init()
	{
		if ($this->widget !== null)
			$this->widget->init();
	}

	// }}}
	// {{{ public function process()

	/**
	 *
	 */
	public function process()
	{
		$form = $this->getForm();
		$replicators = $form->getHiddenField($this->widget->id.'_replicators');

		if ($replicators === null) {
			if ($this->widget !== null)
				$this->widget->process();
		} else {
			foreach ($replicators as $replicator) {
				$widget = $this->getClonedWidget($replicator);
				$widget->process();
			}
		}
	}

	// }}}
	// {{{ public function render()

	/**
	 *
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		if ($this->replicator_id === null) {
			if ($this->widget !== null)
				$this->widget->display();
		} else {
			$form = $this->getForm();
			$widget = $this->getClonedWidget($this->replicator_id);

			// TODO: make sure there actually is an id
			$form->addHiddenField(
				$this->widget->id.'_replicators', array_keys($this->clones));

			if ($widget !== null)
				$widget->display();
		}
	}

	// }}}
	// {{{ public function setWidget()

	/**
	 *
	 * @param SwatWidget $widget
	 */
	public function setWidget(SwatWidget $widget)
	{
		$this->widget = $widget;
		$widget->parent = $this;
	}

	// }}}
	// {{{ public function getPrototypeWidget()

	/** 
	 * Gets the prototype widget of this widget cell renderer
	 *
	 * @return SwatWidget the prototype widget of this widget cell renderer.
	 */
	public function getPrototypeWidget()
	{
		return $this->widget;
	}

	// }}}
	// {{{ public function getWidget()

	/** 
	 * Gets a cloned widget from this widget cell renderer
	 *
	 * @param integer $replicator the replicator id of the cloned widget.
	 * @return SwatWidget the cloned widget identified by $replicator.
	 */
	public function getWidget($replicator)
	{
		if (isset($this->clones[$replicator]))
			return $this->clones[$replicator];

		return null;
	}

	// }}}
	// {{{ public function getCloneWidgets()

	/** 
	 * Gets an array of cloned widgets indexed by the replicator_id
	 *
	 * @return array an array of  widgets indexed by replicator_id
	 */
	public function getClonedWidgets()
	{
		return $this->clones;
	}

	// }}}
	// {{{ public function getTdAttributes()

	/**
	 * Gets TD-tag attributes for htis widget cell renderer
	 *
	 * If the widget within this cell renderer has messages, a 'swat-error' CSS
	 * class is prepended to the CSS classes of the TD-tag.
	 *
	 * @return array an array of attributes to apply to the TD tag of this
	 *                widget cell renderer.
	 */
	public function getTdAttributes()
	{
		$classes = implode(' ', $this->getCSSClassNames());

		if ($this->replicator_id !== null &&
			$this->hasMessage($this->replicator_id))
			$classes = 'swat-error '.$classes;

		return array('class' => $classes);
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gathers all messages from the widget of this cell renderer for the given
	 * replicator id
	 *
	 * @param mixed $replicator_id an optional replicator id of the row to
	 *                              gather messages from. If no replicator id
	 *                              is specified, the current replicator_id is
	 *                              used.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages($replicator_id = null)
	{
		$messages = array();

		if ($replicator_id !== null)
			$messages = $this->getClonedWidget($replicator_id)->getMessages();
		elseif ($this->replicator_id !== null)
			$messages =
				$this->getClonedWidget($this->replicator_id)->getMessages();

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Gets whether or not this widget cell renderer has messages
	 *
	 * @param mixed $replicator_id an optional replicator id of the row to
	 *                              check for messages. If no replicator id is
	 *                              specified, the current replicator_id is
	 *                              used.
	 *
	 * @return boolean true if this widget cell renderer has one or more
	 *                  messages for the given replicator id and false if it
	 *                  does not.
	 */
	public function hasMessage($replicator_id = null)
	{
		$has_message = false;

		if ($replicator_id !== null)
			$has_message =
				$this->getClonedWidget($replicator_id)->hasMessage();
		elseif ($this->replicator_id !== null)
			$has_message =
				$this->getClonedWidget($this->replicator_id)->hasMessage();

		return $has_message;
	}

	// }}}
	// {{{ public function getTitle()

	/**
	 * Gets the title of this widget cell renderer
	 *
	 * The title is taken from this cell renderer's parent.
	 * Satisfies the {SwatTitleable::getTitle()} interface.
	 *
	 * @return string the title of this widget cell renderer.
	 */
	public function getTitle()
	{
		$title = null;

		if (isset($this->parent->title))
			$title = $this->parent->title;

		return $title;
	}

	// }}}
	// {{{ private function getForm()

	/**
	 * Gets the form
	 *
	 * @return SwatForm the form this cell renderer's view is contained in.
	 *
	 * @throws SwatException
	 */
	private function getForm()
	{
		$form = $this->getFirstAncestor('SwatForm');

		if ($form === null)
			throw new SwatException('SwatTableView must be inside a SwatForm for '.
				'SwatWidgetCellRenderer to work.');

		return $form;
	}

	// }}}
	// {{{ private function getCloneWidget()

	private function getClonedWidget($replicator)
	{
		if (isset($this->clones[$replicator]))
			return $this->clones[$replicator];

		if ($this->widget === null)
			return null;

		$suffix = '_'.$replicator;
		$new_widget = clone $this->widget;

		if ($new_widget->id !== null) {
			//$this->widgets[$id][$new_widget->id] = $new_widget;
			$new_widget->id.= $suffix;
		}

		if ($new_widget instanceof SwatContainer) {
			foreach ($new_widget->getDescendants() as $descendant) {
				if ($descendant->id !== null) {
					//$this->widgets[$id][$descendant->id] = $descendant;
					$descendant->id.= $suffix;
				}
			}
		}

		$this->clones[$replicator] = $new_widget;
		return $new_widget;
	}

	// }}}
}

?>
