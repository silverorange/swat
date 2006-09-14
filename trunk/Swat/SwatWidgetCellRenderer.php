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
	 * A reference to the prototype widget for this cell renderer
	 *
	 * @var SwatWidget
	 */
	private $prototype_widget = null;

	private $mappings = array();
	private $clones = array();
	private $property_values = array();

	// }}}
	// {{{ public function addChild()

	/**
	 * Fufills addChild
	 *
	 * @throws SwatException
	 */
	public function addChild(SwatObject $child)
	{
		if ($this->prototype_widget === null)
			$this->setPrototypeWidget($child);
		else
			throw new SwatException('Can only add one widget to a widget '.
				'cell renderer');
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
			$this->property_values[$name] = $value;
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
		$replicators = null;

		$form = $this->getForm();
		if ($form !== null && $form->isSubmitted()) {
			$replicators = $form->getHiddenField(
				$this->prototype_widget->id.'_replicators');

			if ($replicators !== null)
				foreach ($replicators as $replicator)
					$this->createClonedWidget($replicator);
		}

		if ($replicators === null)
			$this->prototype_widget->init();
	}

	// }}}
	// {{{ public function process()

	/**
	 *
	 */
	public function process()
	{
		$form = $this->getForm();
		if ($form === null)
			$replicators = null;
		else
			$replicators = $form->getHiddenField(
				$this->prototype_widget->id.'_replicators');

		if ($replicators === null) {
			if ($this->prototype_widget !== null)
				$this->prototype_widget->process();
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
	 *
	 * @throws SwatException
	 */
	public function render()
	{
		if (!$this->visible)
			return;

		if ($this->replicator_id === null) {
			if ($this->prototype_widget !== null) {
				$this->applyPropertyValuesToPrototypeWidget();
				$this->prototype_widget->display();
			}
		} else {
			$form = $this->getForm();
			$widget = $this->getClonedWidget($this->replicator_id);
			$this->applyPropertyValuesToClonedWidget($widget);

			if ($form === null) {
				throw new SwatException('Cell renderer container must be inside '.
					'a SwatForm for SwatWidgetCellRenderer to work.');
			} else {
				if ($this->prototype_widget->id === null)
					throw new SwatException(
						'Prototype widget must have a non-null id.');
				else
					$form->addHiddenField($this->prototype_widget->id.'_replicators',
						array_keys($this->clones));
			}

			if ($widget !== null)
				$widget->display();
		}
	}

	// }}}
	// {{{ public function setPrototypeWidget()

	/**
	 *
	 * @param SwatWidget $widget
	 */
	public function setPrototypeWidget(SwatWidget $widget)
	{
		$this->prototype_widget = $widget;
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
		return $this->prototype_widget;
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
	// {{{ public function getClonedWidgets()

	/** 
	 * Gets an array of cloned widgets indexed by the replicator_id
	 *
	 * If this cell renderer's form is submitted, only cloned widgets that were
	 * displayed and processed are returned.
	 *
	 * @return array an array of widgets indexed by replicator_id
	 */
	public function &getClonedWidgets()
	{
		$form = $this->getForm();
		if ($form !== null && $form->isSubmitted()) {
			$replicators = $form->getHiddenField(
				$this->prototype_widget->id.'_replicators');

			$clones = array();
			foreach ($this->clones as $replicator_id => $clone) {
				if (in_array($replicator_id, $replicators))
					$clones[$replicator_id] = $clone;
			}
		} else {
			$clones = $this->clones;
		}

		return $clones;
	}

	// }}}
	// {{{ public function getDataSpecificCSSClassNames()

	/** 
	 * Gets the data specific CSS class names for this widget cell renderer
	 *
	 * If the widget within this cell renderer has messages, a CSS class of
	 * 'swat-error' is added to the base CSS classes of this cell renderer.
	 *
	 * @return array the array of data specific CSS class names for this widget
	 *                cell-renderer.
	 */
	public function getDataSpecificCSSClassNames()
	{
		$classes = array();

		if ($this->replicator_id !== null &&
			$this->hasMessage($this->replicator_id))
			$classes[] = 'swat-error';

		return $classes;
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
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this widget cell renderer
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this widget cell renderer.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$set->addEntrySet($this->getPrototypeWidget()->getHtmlHeadEntrySet());
		return $set;
	}

	// }}}
	// {{{ private function getForm()

	/**
	 * Gets the form
	 *
	 * @return SwatForm the form this cell renderer's view is contained in.
	 */
	private function getForm()
	{
		$form = $this->getFirstAncestor('SwatForm');
		return $form;
	}

	// }}}
	// {{{ private function getClonedWidget()

	private function getClonedWidget($replicator)
	{
		if (!isset($this->clones[$replicator]))
			$this->createClonedWidget($replicator);

		return $this->clones[$replicator];
	}

	// }}}
	// {{{ private function applyPropertyValuesToPrototypeWidget()

	private function applyPropertyValuesToPrototypeWidget()
	{
		foreach ($this->property_values as $name => $value) {
			$object = $this->mappings[$name]['object'];
			$property = $this->mappings[$name]['property'];
			$object->$property = $value;
		}
	}

	// }}}
	// {{{ private function applyPropertyValuesToClonedWidget()

	private function applyPropertyValuesToClonedWidget($cloned_widget)
{
		foreach ($this->property_values as $name => $value) {
			$object = $this->mappings[$name]['object'];
			$property = $this->mappings[$name]['property'];

			$prototype_descendendents = array($this->prototype_widget);
			$cloned_descendendents = array($cloned_widget);

			if ($this->prototype_widget instanceof SwatContainer) {
				$prototype_descendendents = array_merge($prototype_descendendents, 
					$this->prototype_widget->getDescendants());

				$cloned_descendendents = array_merge($cloned_descendendents,
					$this->cloned_widget->getDescendants());
			}

			$cloned_object = null;
			foreach ($prototype_descendendents as $index => $prototype_object) {
				if ($object === $prototype_object) {
					$cloned_object = $cloned_descendendents[$index];
					break;
				}
			}
			
			if ($cloned_object === null)
				throw new SwatException('Cloned widget tree does not match '.
					'prototype widget tree.');
			else
				$cloned_object->$property = $value;
		}
	}

	// }}}
	// {{{ private function createClonedWidget()

	private function createClonedWidget($replicator)
	{
		if ($this->prototype_widget === null)
			return;

		$suffix = '_'.$replicator;
		$new_widget = clone $this->prototype_widget;

		if ($new_widget->id !== null)
			$new_widget->id.= $suffix;

		if ($new_widget instanceof SwatContainer)
			foreach ($new_widget->getDescendants() as $descendant)
				if ($descendant->id !== null)
					$descendant->id.= $suffix;

		$new_widget->init();

		$this->clones[$replicator] = $new_widget;
	}

	// }}}
}

?>
