<?php

require_once 'Swat/SwatUIObject.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 *
 *
 */
class SwatInputCell extends SwatUIObject implements SwatUIParent
{
	/**
	 * The id of the input row for this input cell
	 */
	public $row = null;

	public $replicator;

	/**
	 * A reference to the widget for this cell
	 */
	private $widget = null;

	/**
	 *
	 * @var SwatForm
	 */
	private $form = null;

	/**
	 * A cache of cloned widgets
	 *
	 * @var array
	 */
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
			throw new SwatException('Can only add one widget to an input cell');
	}

	/**
	 * Initializes this input cell
	 *
	 * This calls {@link SwatWidget::init()} on the cell's widget.
	 */
	public function init()
	{
		$this->form = $this->getFirstAncestor('SwatForm');

		if ($this->widget !== null)
			$this->widget->init();

		// ensure the widget has an id
		if ($this->widget->id === null)
			$this->widget->id = $this->widget->getUniqueId();
	}

	/**
	 *
	 */
	public function process($row_number)
	{
		$widget = $this->getClonedWidget($row_number);
		$widget->process();
		Swat::printObject($widget->id.' :: '.$widget->getState());
	}

	public function display($row_number)
	{
		$widget = $this->getClonedWidget($row_number);
		$widget->display();
	}

	/**
	 *
	 * @param SwatWidget $widget
	 *
	 * @throws SwatInvaidClassException
	 */
	public function setWidget($widget)
	{
		$this->widget = $widget;
	}

	/** 
	 * Gets the widget of this input cell
	 *
	 * @return SwatWidget the widget of this input cell.
	 */
	public function getWidget()
	{
		return $this->widget;
	}

	public function getHtmlHeadEntries()
	{
		return $this->html_head_entries;
	}

	/**
	 *
	 *
	 * @param string $replicator_id
	 *
	 * @return SwatWidget
	 */
	private function getClonedWidget($replicator_id)
	{
		if (isset($this->clones[$replicator_id]))
			return $this->clones[$replicator_id];

		if ($this->widget === null)
			return null;

		$suffix = '_'.$this->row.'_'.$replicator_id;
		$new_widget = clone $this->widget;

		if ($new_widget->id !== null)
			$new_widget->id.= $suffix;

		// TODO: this doesn't work for embedded table views, etc
		if ($new_widget instanceof SwatContainer) {
			$descendants = $new_widget->getDescendants();
			foreach ($descendants as $descendant)
				if ($descendant->id !== null)
					$descendant->id.= $suffix;
		}

		$this->clones[$replicator_id] = $new_widget;

		return $new_widget;
	}
}

?>
