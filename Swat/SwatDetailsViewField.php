<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererSet.php';

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
	 * Visible
	 *
	 * Whether the field is displayed.
	 *
	 * @var boolean
	 */
	public $visible = true;

	/**
	 * A set of SwatCellRenderer objects
	 *
	 * This object contains all the cell renderers for this column.
	 *
	 * @var SwatCellRendererSet
	 */
	protected $renderers = null;

	/**
	 * Creates a new details view field
	 *
	 * @param string $id an optional unique ideitifier for this details view
	 *                    field.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;

		$this->renderers = new SwatCellRendererSet();
	}

	/**
	 * Links a data field to a cell renderer property
	 *
	 * @param SwatCellRenderer $renderer the cell renderer in this field that
	 *                                    the datafield is mapped onto.
	 * @param string $datafield the field of the data model to map to the
	 *                           renderer property.
	 * @param string $property the property of the cell renderer that the
	 *                          datafield is mapped to.
	 */
	public function addMappingToRenderer($renderer, $datafield, $property)
	{
		$this->renderers->addMappingToRenderer($renderer,
			$datafield, $property);
	}

	/**
	 * Adds a cell renderer to this field's set of renderers
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 */
	public function addRenderer(SwatCellRenderer $renderer)
	{
		$this->renderers->addRenderer($renderer);
	}

	/**
	 * Gets a cell renderers of this field by its unique identifier
	 *
	 * @param string the unique identifier of the cell renderer to get.
	 * 
	 * @return SwatCellRenderer the cell renderer of this field with the
	 *                           provided unique identifier.
	 */
	public function getRenderer($renderer_id)
	{
		return $this->renderers->getRenderer($renderer_id);
	}

	/**
	 * Gets the cell renderers of this field
	 * 
	 * Returns an the array of {@link SwatCellRenderer} objects contained
	 * by this field.
	 *
	 * @return array the cell renderers contained by this field.
	 */
	public function getRenderers()
	{
		$out = array();
		foreach ($this->renderers as $renderer)
			$out[] = $renderer;

		return $out;
	}

	/**
	 * Gets a cell renderer in this field based on its ordinal position
	 *
	 * @param $position the ordinal position of the cell renderer to get. The
	 *                   position is zero-based.
	 *
	 * @return SwatCellRenderer the renderer at the specified ordinal position.
	 */
	public function getRendererByPosition($position = 0)
	{
		return $this->renderers->getRendererByPosition($position);
	}

	/**
	 * Displays this details view field using a data object
	 *
	 * @param mixedt $data a data object used to display the cell renderers in
	 *                      this field.
	 */
	public function display($data)
	{
		if (!$this->visible)
			return;

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
		$th_tag->content = $this->title.':';
		$th_tag->display();
	}

	/**
	 * Displays the value of this details view field
	 *
	 * @param mixed $data the data object to display in this field.
	 */
	public function displayValue($data)
	{
		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'field.');

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer)
			$this->renderers->applyMappingsToRenderer($renderer, $data);

		$this->displayRenderers($data);
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

	/**
	 * Renders each cell renderer in this details-view field
	 *
	 * The properties of the cell renderers are set the the fields of the
	 * data object through the datafield property mappings.
	 *
	 * @param mixed $data the data object to render with the cell renderers
	 *                     of this field.
	 */
	protected function displayRenderers($data)
	{
		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->open();

		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}
}

?>
