<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatUIParent.php');

/**
 * A single entry in a SwatActions widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatActionItem extends SwatControl implements SwatUIParent {

	public $name;
	public $title = '';
	public $widget = null;

	function __construct($name = '') {
		$this->name = $name;
	}

	public function display() {
		$this->widget->display();
	}
	
	public function setWidget(SwatWidget $widget) {
		if ($this->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an SwatActionItem');

		$this->widget = $widget;
	}

	/**
	 * Add a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface.  It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.  To set the a widget in an action item, use 
	 * {@link SwatActionItem::setWidget()}.
	 *
	 * @param $child A reference to a child object to add.
	 */
	public function addChild($child) {

		if ($child instanceof SwatWidget)
			$this->setWidget($child);
		else
			throw new SwatException('SwatActionItem: Only '.
				'SwatWidgets can be nested within SwatActionItem');
	}

}
?>

