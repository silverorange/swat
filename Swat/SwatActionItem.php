<?php

require_once('Swat/SwatControl.php');

/**
 * A single entry in a SwatActions widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatActionItem extends SwatControl {
	public $name;
	public $title = '';
	public $widget = null;

	function __construct($name = '') {
		$this->name = $name;
	}

	public function display() {
		$this->widget->display();
	}
	
	public function add($widget) {
		if ($this->widget != null)
			throw new SwatException('SwatUI: Only one widget can be nested '.
				'within an SwatActionItem');

		$this->widget = $widget;
	}
}
?>
