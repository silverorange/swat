<?php

require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatCellRendererContainer.php';

/**
 * A visible field in a SwatDetailsView
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsViewField extends SwatCellRendererContainer
	implements SwatUIParent
{
	// {{{ public properties

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
	 * Whether or not this field is displayed
	 *
	 * @var boolean
	 */
	public $visible = true;

	// }}}
	// {{{ protected properties

	/**
	 * Whether or not this field is odd or even in its parent details view
	 *
	 * @var boolean
	 */
	protected $odd = false;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new details view field
	 *
	 * @param string $id an optional unique ideitifier for this details view
	 *                    field.
	 */
	public function __construct($id = null)
	{
		$this->id = $id;
		parent::__construct();
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this field
	 *
	 * This calls init on all renderers in this field.
	 */
	public function init()
	{
		foreach ($this->renderers as $renderer)
			$renderer->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this details view field using a data object
	 *
	 * @param mixed $data a data object used to display the cell renderers in
	 *                      this field.
	 * @param boolean $odd whether this is an odd or even field so alternating 
	 *                      style can be applied.
	 */
	public function display($data, $odd)
	{
		if (!$this->visible)
			return;

		$this->odd = $odd;

		$tr_tag = new SwatHtmlTag('tr');
		$tr_tag->id = $this->id;
		$tr_tag->class = $this->getCSSClassString();

		$tr_tag->open();
		$this->displayHeader();
		$this->displayValue($data);
		$tr_tag->close();
	}

	// }}}
	// {{{ public function displayHeader()

	/**
	 * Displays the header for this details view field
	 */
	public function displayHeader()
	{
		$th_tag = new SwatHtmlTag('th');
		$th_tag->scope = 'row';
		$th_tag->setContent($this->title.':');
		$th_tag->display();
	}

	// }}}
	// {{{ public function displayValue()

	/**
	 * Displays the value of this details view field
	 *
	 * The properties of the cell renderers are set from the data object
	 * through the datafield property mappings.
	 *
	 * @param mixed $data the data object to display in this field.
	 */
	public function displayValue($data)
	{
		if ($this->renderers->getCount() == 0)
			throw new SwatException('No renderer has been provided for this '.
				'field.');

		$sensitive = $this->view->isSensitive();

		// Set the properties of the renderers to the value of the data field.
		foreach ($this->renderers as $renderer) {
			$this->renderers->applyMappingsToRenderer($renderer, $data);
			$renderer->sensitive = $sensitive;
		}

		$this->displayRenderers($data);
	}

	// }}}
	// {{{ protected function displayRenderers()

	/**
	 * Renders each cell renderer in this details-view field
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
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

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this field 
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this details-view field.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();
		$renderers = $this->getRenderers();
		foreach ($renderers as $renderer)
			$set->addEntrySet($renderer->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this entry widget
	 *
	 * @return array the array of CSS classes that are applied to this entry
	 *                widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-details-view-field');

		if ($this->odd)
			$classes[] = 'odd';

		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
