<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';

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

	/**
	 * An array of renderers for this field of the form:
	 *    [$id] => array('renderer' => $renderer, 'property_map' => array())
	 *
	 * @var array
	 */
	protected $renderers = array();

	/**
	 * Creates a new details view field
	 *
	 * @param string $id an optional unique ideitifier for this details view
	 *                    field.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
	}

	/**
	 * Links a data field to a cell renderer property
	 *
	 * @param string $renderer_id the unique id of the cell renderer
	 *                             that the data field is mapped to.
	 * @param string $field the field of the data model to map to the
	 *                       renderer property.
	 * @param string $property the property of the cell renderer that
	 *                          data field is mapped to.
	 *
	 * @throws SwatException
	 */
	public function linkField($renderer, $field, $property)
	{
		if (isset($this->renderers[$renderer->id]))
			$this->renderers[$renderer->id]['property_map'][$property] =
				$field;
		else
			throw new SwatException("No renderer with an id of '{$renderer->id}'".
				" exists in this details view field.");
	}

	/**
	 * Adds a cell renderer to this field
	 *
	 * @param SwatCellRenderer $renderer a reference to the renderer to add.
	 *
	 * @throws SwatException
	 */
	public function addRenderer(SwatCellRenderer $renderer)
	{
		if ($renderer->id !== null)
			$this->renderers[$renderer->id] = array('renderer' => $renderer,
				'property_map' => array());
		else
			throw new SwatException('Cannot add a cell renderer without an id.');

		$renderer->parent = $this;
	}

	/**
	 * Get Renderer
	 * 
	 * Returns a reference to a {@link SwatCellRenderer}.
	 *
	 * @param $id The id of the {@link SwatCellRenderer}
	 * @return SwatCellRenderer The renderer with id = $id.
	 */
	public function getRenderer($id)
	{
		if (isset($this->renderers[$id])) {
			return $this->renderers[$id];
		} else
			throw new SwatException("No renderer with an id of '$id' found.");
	}

	/**
	 * Displays this details view field
	 *
	 * @param Object $data the data object to display in this field.
	 */
	public function display($data)
	{
		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->open();
		$this->displayHeader();
		$this->displayValue($data);
		$tr_tag->close();
	}

	/**
	 * Displays the header for this details view field
	 */
	public function displayHeader()
	{
		$th_tag = new SwatHtmlTag('th');
		$th_tag->align = 'right';
		$th_tag->content = $this->title;
		$th_tag->display();
	}

	/**
	 * Displays the value for this details view field
	 *
	 * @param Object $data the data object to display in this field.
	 */
	public function displayValue($data)
	{
		if (count($this->renderers) == 0)
			throw new SwatException('No renderer has been provided for this field.');

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer_id => $field_renderer)
			foreach ($field_renderer['property_map'] as $property => $field)
				$field_renderer['renderer']->$property = $data->$field;

		$this->displayRenderers($data);
	}

	/**
	 * Renders each cell renderer in this details view field
	 *
	 * @param Object $data the data object to render with the cell renderers
	 *                      in this field.
	 */
	protected function displayRenderers($data)
	{
		reset($this->renderers);
		$first_field_renderer = current($this->renderers);
		$first->renderer = $first_field_renderer['renderer'];
		$td_tag = new SwatHtmlTag('td', $first->renderer->getTdAttributes());
		$td_tag->open();

		foreach ($this->renderers as $id => $field_renderer) {
			$renderer = $field_renderer['renderer'];
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}

	/**
	 * Adds a child object to this object
	 * 
	 * @param SwatObject $child the reference to the child object to add.
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
			throw new SwatException('Only SwatCellRender objects '.
				'can be nested within SwatDetailsViewField objects.');
	}
}

?>
