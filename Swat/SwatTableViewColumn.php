<?php

require_once 'Swat/SwatObject.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererSet.php';

/**
 * A visible column in a SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewColumn extends SwatObject implements SwatUIParent
{
	/**
	 * Unique identifier of this column
	 *
	 * @var string
	 */
	public $id = null;

	/**
	 * Title of this column
	 *
	 * @var string
	 */
	public $title = '';

	/**
	 * The {@link SwatTableView} associated with this column
	 *
	 * The table view is the parent of this object.
	 *
	 * @var SwatTableView
	 */
	public $view = null;

	/**
	 * Visible
	 *
	 * Whether the column is displayed.
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
	 * Creates a new table-view column
	 *
	 * @param string $id an optional unique id identitying this column in the
	 *                    table view.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		$this->renderers = new SwatCellRendererSet();
	}

	/**
	 * Links a datafield to a renderer property
	 *
	 * @param SwatCellRenderer $renderer the cell renderer in this column that
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
	 * Adds a cell renderer to this column's set of renderers
	 *
	 * @param SwatCellRenderer $renderer the renderer to add.
	 */
	public function addRenderer(SwatCellRenderer $renderer)
	{
		$this->renderers->addRenderer($renderer);
	}

	/**
	 * Gets the cell renderers of this column
	 * 
	 * Returns an the array of {@link SwatCellRenderer} objects contained
	 * by this column.
	 *
	 * @return array the cell renderers contained by this column.
	 */
	public function getRenderers()
	{
		$out = array();
		foreach ($this->renderers as $renderer)
			$out[] = $renderer;

		return $out;
	}

	/**
	 * Gets a cell renderers of this column by its unique identifier
	 *
	 * @param string the unique identifier of the cell renderer to get.
	 * 
	 * @return SwatCellRenderer the cell renderer of this column with the
	 *                           provided unique identifier.
	 */
	public function getRenderer($renderer_id)
	{
		return $this->renderers->getRenderer($renderer_id);
	}

	/**
	 * Gets a cell renderer in this column based on its ordinal position
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

	public function init()
	{
	}

	public function process()
	{
	}

	/**
	 * Displays the table-view header cell for this column
	 */
	public function displayHeaderCell()
	{
		$first_renderer = $this->renderers->getFirst();
		$th_tag = new SwatHtmlTag('th', $first_renderer->getThAttributes());
		$th_tag->open();
		$this->displayHeader();
		$th_tag->close();
	}

	/**
	 * Displays the contents of the header cell for this column
	 */
	public function displayHeader()
	{
		echo $this->title;
	}

	/**
	 * Displays this column using a data object
	 *
	 * @param mixed $row a data object used to display the cell renderers in
	 *                    this column.
	 */
	public function display($row)
	{
		if (!$this->visible)
			return;

		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'column.');

		$sensitive = $this->view->isSensitive();

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $row);
			$renderer->sensitive = $sensitive;
		}

		$this->displayRenderers($row);
	}

	/**
	 * Displays JavaScript required by this column
	 * 
	 * Optionally output a JavaScript object representing the
	 * {@link SwatTableViewColumn}. JavaScript is displayed after the table
	 * has been displayed.
	 */
	public function displayJavascript()
	{
	}

	/**
	 * Add a child object to this object
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
				'can be nested within SwatTableViewColumn objects.');
	}

	/**
	 * Renders each cell renderer in this table-view column
	 *
	 * The properties of the cell renderers are set the the fields of the
	 * data object through the datafield property mappings.
	 *
	 * @param mixed $row the data object to render with the cell renderers
	 *                    of this field.
	 */
	protected function displayRenderers($row)
	{
		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->open();

		$prefix = ($this->view->id === null)? '': $this->view->id.'_';

		foreach ($this->renderers as $renderer) {
			$renderer->render($prefix);
			echo ' ';
		}

		$td_tag->close();
	}
}

?>
