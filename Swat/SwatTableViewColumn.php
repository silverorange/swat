<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * A visible column in a SwatTableView.
 */
class SwatTableViewColumn extends SwatObject {

	public $name = null;
	public $title = '';
	public $renderer = null;

	private $properties;

	function __construct($name = null) {
		$this->name = $name;
		$this->properties = array();
	}

	public function linkField($model_field, $renderer_property) {
		$this->properties[$model_field] = $renderer_property;
	}

	public function display($row) {
		if ($this->renderer == null)
			throw new SwatException(__CLASS__.': no renderer has been provided.');

		foreach ($this->properties as $field => $property)
			$this->renderer->$property = $row->$field;

		echo '<td>';
		$this->renderer->render();
		echo '</td>';	
	}
	
}
