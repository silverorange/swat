<?php
require_once('Swat/SwatObject.php');
require_once('Swat/SwatHtmlTag.php');

//TODO: finish documentation for public functions

/**
 * A visible column in a SwatTableView
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableViewColumn extends SwatObject {
	/**
	 * Name of the column
	 * @var string
	 */
	public $name = null;

	/**
	 * Title of the column
	 * @var string
	 */
	public $title = '';

	/**
	 * The {@link SwatTableView} associated with this column
	 * @var SwatTableView
	 */
	public $view = null;

	protected $renderers = array();

	public function __construct($name = null) {
		$this->name = $name;
	}

	public function linkField($renderer, $model_field, $renderer_property) {
		if (!isset($renderer->_property_map) || !is_array($renderer->_property_map))
			$renderer->_property_map = array();

		$renderer->_property_map[$renderer_property] = $model_field;
	}

	public function addRenderer(SwatCellRenderer $renderer) {
		$this->renderers[] = $renderer;

		if (!isset($renderer->_property_map) || !is_array($renderer->_property_map))
			$renderer->_property_map = array();
	}

	public function init() {

	}

	public function displayHeader() {
		echo $this->title;
	}

	public function display($row) {
		if (count($this->renderers) == 0)
			throw new SwatException(__CLASS__.': no renderer has been provided.');

		// set the properties of the renderers
		foreach ($this->renderers as $renderer)
			foreach ($renderer->_property_map as $property => $field)
				$renderer->$property = $row->$field;

		$this->displayRenderers($row);
	}

	protected function displayRenderers($row) {
		reset($this->renderers);
		$first_renderer = current($this->renderers);
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttribs());
		$td_tag->open();

		$prefix = ($this->view->name == null)? '': $this->view->name.'_';

		foreach ($this->renderers as $renderer) {
			$renderer->render($prefix);
			echo ' ';
		}

		$td_tag->close();
	}
}
