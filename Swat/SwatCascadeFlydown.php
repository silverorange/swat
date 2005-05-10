<?php

require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * A cascading flydown (aka combo-box) selection widget
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatCascadeFlydown extends SwatFlydown {
	
	/**
	 * Flydown options
	 *
	 * An array of parents and options for the flydown in the form:
	 * parent = array(value1 => title2, value2 => title2).
	 * @var array
	 */
	public $options = null;

	/**
	 * Cascade From
	 *
	 * A reference to the {@link SwatWidget} that this item cascades from.
	 */
	public $cascade_from;

	public function display() {
		$this->show_blank = false;
		parent::display();
		$this->displayJavascript();
	}

	protected function &getOptions() {
		$parent_value = $this->cascade_from->value;
		if ($parent_value === null) {
			if ($this->cascade_from->show_blank)
				return array('' => _S("n/a"));
			else
				return $this->options[key($this->cascade_from->options)];
		}
		return $this->options[$parent_value];
	}

	private function displayJavascript() {
		echo '<script type="text/javascript">';
		include_once('Swat/javascript/swat-cascade.js');
		
		printf("\n {$this->id}_cascade = new SwatCascade('%s', '%s'); ",
				$this->cascade_from->id, $this->id);
		
		foreach($this->options as $parent => $options) {
			foreach ($options as $k => $v) {
				$selected = ($v == $this->value) ? 'true' : 'false';
				printf("\n {$this->id}_cascade.addChild('%s', '%s', '%s', %s);",
					$parent, $k, addslashes($v), $selected);
			}
		}
		echo '</script>';
	}
}

?>
