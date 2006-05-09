<?php

require_once 'Swat/SwatCellRenderer.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 *
 *
 */
class SwatWidgetCellRenderer extends SwatCellRenderer implements SwatUIParent
{
	/**
	 * Unique value used to uniquely identify the replicated widget.
	 * If null, no replicating is done and the prototype widget is used.
	 */
	public $replicator_id = null;

	/**
	 * A reference to the widget for this cell
	 */
	private $widget = null;

	private $mappings = array();
	private $clones = array();

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

	/**
	 *
	 */
	public function render()
	{
		if ($this->replicator_id === null) {
			if ($this->widget !== null)
				$this->widget->display();
		} else {
			$form = $this->getForm();
			$widget = $this->getClonedWidget($this->replicator_id);

			// TODO: make sure there actually is an id
			$form->addHiddenField($this->widget->id.'_replicators', array_keys($this->clones));

			if ($widget !== null)
				$widget->display();
		}
	}

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

	/**
	 *
	 * @param SwatWidget $widget
	 */
	public function setWidget(SwatWidget $widget)
	{
		$this->widget = $widget;
		$widget->parent = $this;
	}

	/** 
	 * Gets the prototype widget of this widget cell renderer
	 *
	 * @return SwatWidget the prototype widget of this widget cell renderer.
	 */
	public function getPrototypeWidget()
	{
		return $this->widget;
	}

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

	/** 
	 * Gets an array of cloned widgets indexed by the replicator_id
	 *
	 * @return array an array of  widgets indexed by replicator_id
	 */
	public function getClonedWidgets()
	{
		return $this->clones;
	}

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
}

?>
