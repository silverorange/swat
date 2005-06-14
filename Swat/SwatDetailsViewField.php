<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';

// TODO: Document this class

/**
 * A visible field in a SwatDetailsView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsViewField extends SwatObject implements SwatUIParent
{
	/**
	 * The unique identifier of this field
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * The title of this field
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The {@link SwatDetailsView} associated with this field
	 *
	 * @var SwatDetailsView
	 */
	public $view = null;

	protected $renderers = array();

	public function __construct($id = null)
	{
		$this->id = $id;
	}

	/**
	 * Links a data field to a cell renderer property
	 *
	 * @param SwatCellRenderer $renderer a reference to the cell renderer
	 *                                    that the data field is mapped to.
	 * @param string $model_field the field of the data model to map to the
	 *                             renderer property.
	 * @param string $render_property the property of the cell renderer that
	 *                                 data field is mapped to.
	 */
	public function linkField($renderer, $model_field, $renderer_property)
	{
		if (!isset($renderer->_property_map) ||
			!is_array($renderer->_property_map))
				$renderer->_property_map = array();

		$renderer->_property_map[$renderer_property] = $model_field;
	}

	public function addRenderer(SwatCellRenderer $renderer)
	{
		$this->renderers[] = $renderer;
		$renderer->parent = $this;

		if (!isset($renderer->_property_map) ||
			!is_array($renderer->_property_map))
				$renderer->_property_map = array();
	}

	public function display($data)
	{
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();
		$this->displayHeader();
		$this->displayValue($data);
		$tr_tag->close();
	}

	public function displayHeader()
	{
		$th_tag = new SwatHtmlTag('th');
		$th_tag->align = 'right';
		$th_tag->content = $this->title;
		$th_tag->display();
	}

	public function displayValue($data)
	{
		if (count($this->renderers) == 0)
			throw new SwatException(__CLASS__.
				': no renderer has been provided.');

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer)
			foreach ($renderer->_property_map as $property => $field)
				$renderer->$property = $data->$field;

		$this->displayRenderers($data);
	}

	protected function displayRenderers($data)
	{
		reset($this->renderers);
		$first_renderer = current($this->renderers);
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->open();

		$prefix = ($this->view->id === null) ? '' : $this->view->id.'_';

		foreach ($this->renderers as $renderer) {
			$renderer->render($prefix);
			echo ' ';
		}

		$td_tag->close();
	}

	/**
	 * Adds a child object to this object
	 * 
	 * @param $child A reference to a child object to add.
	 *
	 * @throws SwatException
	 *
	 * @see SwatUIParent::addChild()
	 */
	public function addChild($child)
	{
		if ($child instanceof SwatCellRenderer)
			$this->addRenderer($child);
		else
			throw new SwatException(__CLASS__.': Only SwatCellRender objects '.
				'can be nested within SwatDetailsViewField objects.');
	}
}

?>
