<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererContainer.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * A visible column in a SwatTableView
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewColumn extends SwatCellRendererContainer implements SwatUIParent
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
	 * Creates a new table-view column
	 *
	 * @param string $id an optional unique id identitying this column in the
	 *                    table view.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		parent::__construct();
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
	public function displayJavaScript()
	{
	}

	/**
	 * Add a child object to this object
	 * 
	 * @param SwatCellRenderer $child the reference to the child object to add.
	 *
	 * @throws SwatInvalidClassException
	 *
	 * @see SwatUIParent::addChild()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatCellRenderer)
			$this->addRenderer($child);
		else
			throw new SwatInvalidClassException(
				'Only SwatCellRender objects may be nested within '.
				'SwatTableViewColumn objects.', 0, $child);
	}

	/**
	 * Gathers the SwatHtmlHeadEntry objects needed by this column 
	 *
	 * @return array the SwatHtmlHeadEntry objects needed by this column.
	 *
	 * @see SwatUIBase::getHtmlHeadEntries()
	 */
	public function getHtmlHeadEntries()
	{
		$out = $this->html_head_entries;
		$renderers = $this->getRenderers();
		foreach ($renderers as $renderer)
			$out = array_merge($out, $renderer->getHtmlHeadEntries());

		return $out;
	}
}

?>
