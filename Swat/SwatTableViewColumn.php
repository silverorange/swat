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
	protected $properties = array();

	function __construct($name = null) {
		$this->name = $name;
	}

	public function linkField($model_field, $renderer_property) {
		$this->properties[$model_field] = $renderer_property;
	}

	public function addRenderer(SwatCellRenderer $renderer) {
		$this->renderers[] = $renderer;
	}

	public function display($row) {
		if (count($this->renderers) == 0)
			throw new SwatException(__CLASS__.': no renderer has been provided.');

		// set the properties of the renderers
		foreach ($this->properties as $field => $property)
			foreach ($this->renderers as $renderer)
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
