<?php

require_once 'Swat/SwatTableViewColumn.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';

/**
 * An orderable table view column.
 *
 * This column has a clickable header that allows the user to change the
 * ordering of the column. This behaviour is commonly used for databound table
 * columns.
 *
 * TODO: Implement this functionality with AJAX.
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewOrderableColumn extends SwatTableViewColumn
{
	// {{{ constants

	/**
	 * Indicates no ordering is done
	 */
	const ORDER_BY_DIR_NONE = 0;

	/**
	 * Indicates descending ordering is done
	 */
	const ORDER_BY_DIR_DESCENDING = 1;

	/**
	 * Indicates ascending ordering is done
	 */
	const ORDER_BY_DIR_ASCENDING = 2;

	// }}}
	// {{{ public properties

	/**
	 * The base of the link used when building column header links
	 *
	 * Additional GET variables are appended to this link in the getLink()
	 * method.
	 *
	 * @var string
	 *
	 * @see SwatTableViewOrderableColumn::getLink()
	 */
	public $link = '';

	/**
	 * HTTP GET variables to remove from the column header link
	 *
	 * An array of GET variable names to unset before building new links.
	 *
	 * @var array
	 */
	public $unset_get_vars = array();

	/**
	 * The direction of ordering
	 *
	 * The current direction of ordering for this column. Valid values are
	 * ORDER_BY_DIR_* constants.
	 *
	 * @var integer
	 */
	private $direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE;

	/**
	 * The default direction of ordering
	 *
	 * The default direction of ordering before the GET variables are processed.
	 * When the GET variables are processed, they change $direction and 
	 * $default_direction remains unchanged.  Valid values are
	 * ORDER_BY_DIR_* constants.
	 *
	 * @var integer
	 */
	private $default_direction =
		SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE;

	/**
	 * The mode of ordering
	 *
	 * The mode of switching between ordering states.
	 * Valid values are ORDER_MODE_TRISTATE, and ORDER_MODE_BISTATE constants.
	 *
	 * @var int
	 */
	//private $mode = SwatTableViewOrderableColumn::ORDER_MODE_TRISTATE;

	// }}}
	// {{{ public function init()
	
	/**
	 * Initializes this column
	 *
	 * The current direction of ordering is grabbed from GET variables.
	 */
	public function init()
	{
		parent::init();
		$this->initFromGetVariables();
	}

	// }}}
	// {{{ public function setDirection()

	/**
	 * Sets the direction of ordering
	 * 
	 * This method sets the direction of ordering of the column, either asc,
	 * desc, or none.
	 *
	 * @param $direction integer One of the ORDER_BY_DIR_* class contants
	 * @param $set_as_orderby_column boolean Whether to set this column as the 
	 *                                    column currently used for ordering 
	 *                                    of the view
	 */
	public function setDirection($direction)
	{
		$this->direction = $direction;
		$this->default_direction = $direction;

		if ($this->view->orderby_column === null)
			$this->view->orderby_column = $this;

		$this->view->default_orderby_column = $this;

		$this->initFromGetVariables();
	}

	// }}}
	// {{{ public function displayHeader()

	/**
	 * Displays the column header for this table view column
	 *
	 * This method displays an appropriate header based on the current
	 * direction of ordering of this table view column. If the column has been
	 * ordered, an image indicating the ordering is also displayed in the
	 * header.
	 */
	public function displayHeader()
	{
		$anchor = new SwatHtmlTag('a');
		$anchor->href = $this->getLink();

		$anchor->open();

		// display image
		if ($this->view->orderby_column === $this &&
			$this->direction != self::ORDER_BY_DIR_NONE) {

			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'swat-nowrap';

			$title_exp = explode(' ', $this->title);
			$last_word = array_pop($title_exp);

			if (count($title_exp))
				$title = implode(' ', $title_exp).' ';
			else
				$title = '';

			echo SwatString::minimizeEntities($title);

			$span_tag->open();

			echo SwatString::minimizeEntities($last_word);

			$img = new SwatHtmlTag('img');

			if ($this->direction == self::ORDER_BY_DIR_DESCENDING) {

				$img->src = 'packages/swat/images/swat-table-view-column-desc.png';
				$img->alt = Swat::_('Descending');

			} elseif ($this->direction == self::ORDER_BY_DIR_ASCENDING) {

				$img->src = 'packages/swat/images/swat-table-view-column-asc.png';
				$img->alt = Swat::_('Ascending');

			}

			$img->width = 11;
			$img->height = 11;

			$img->display();

			$span_tag->close();

		} else {
			parent::displayHeader();
		}

		$anchor->close();
	}

	// }}}
	// {{{ public function getDirectionAsString()

	/**
	 * Gets the direction of ordering as a string
	 *
	 * Retrieves the current ordering direction of this column or an arbitrary
	 * direction constant as a string. The string is returned the lowercase
	 * abbreviated form 'asc' or 'desc'.
	 *
	 * @param integer $direction_id an optional direction constant to convert
	 *                               to a string rather than using this
	 *                               column's current direction.
	 *
	 * @return string the direction of ordering.
	 */
	public function getDirectionAsString($direction_id = null)
	{
		if ($direction_id === null)
			$direction_id = $this->direction;

		switch ($direction_id) {
		case self::ORDER_BY_DIR_NONE:
			return '';

		case self::ORDER_BY_DIR_ASCENDING:
			return 'asc';

		case self::ORDER_BY_DIR_DESCENDING:
			return 'desc';

		default:
			throw new SwatException("Ordering direction '$direction_id' not found.");
		}
	}

	// }}}
	// {{{ protected function displayRenderers()

	/**
	 * Renders each cell renderer in this orderable table-view column
	 *
	 * @param mixed $data the data object being used to render the cell
	 *                     renderers of this field.
	 *
	 * @see SwatTableViewColumn::displayRenderers()
	 */
	protected function displayRenderers($row)
	{
		$td_tag = new SwatHtmlTag('td', $this->getTdAttributes());
		$td_tag->open();

		$prefix = ($this->view->id === null)? '': $this->view->id.'_';

		foreach ($this->renderers as $renderer) {
			$renderer->render($prefix);
			echo ' ';
		}

		$td_tag->close();
	}

	// }}}
	// {{{ protected function getBaseCSSClassNames()

	/** 
	 * Gets the base CSS class names of this orderable table-view column
	 *
	 * @return array the array of base CSS class names for this orderable
	 *                table-view column.
	 */
	protected function getBaseCSSClassNames()
	{
		$classes = array();

		if ($this->view->orderby_column === $this)
			$classes[] = 'swat-table-view-orderable-column-selected';

		return $classes;
	}

	// }}}
	// {{{ private function setDirectionByString()

	/**
	 * Sets direction of ordering by a string
	 *
	 * Sets the current ordering direction of this column.
	 *
	 * @param string $direction ordering direction as a string. The direction
	 *                           is case insensitive and may be the short form
	 *                           'asc' or 'desc' or the long form 'ascending'
	 *                           or 'descending'.
	 */
	private function setDirectionByString($direction)
	{
		$direction = strtolower($direction);

		switch ($direction) {
		case 'ascending':
		case 'asc':
			$this->direction = self::ORDER_BY_DIR_ASCENDING;
			break;

		case 'descending':
		case 'desc':
			$this->direction = self::ORDER_BY_DIR_DESCENDING;
				
			break;

		default:
			$this->direction = self::ORDER_BY_DIR_NONE;
		}
	}

	// }}}
	// {{{ private function getLink()

	/**
	 * Gets the link for this column's header
	 *
	 * This method builds the link by appending special GET variables and
	 * unsetting other ones.
	 *
	 * @return string the link for this column's header.
	 */
	private function getLink()
	{
		// unset GET vars that we want to ignore
		$vars = $_GET;

		foreach($vars as $name => $value)
			if (in_array($name, $this->unset_get_vars))
				unset($vars[$name]);

		// TODO:: is id a required field for table views?
		$key_orderby = $this->view->id.'_orderby';
		$key_orderbydir = $this->view->id.'_orderbydir';

		unset($vars[$key_orderby]);
		unset($vars[$key_orderbydir]);

		$next_dir = $this->getNextDirection();

		if ($next_dir != $this->default_direction) {
			$vars[$key_orderby] = $this->id;
			$vars[$key_orderbydir] = $this->getDirectionAsString($next_dir);
		}

		// build the new link
		$link = $this->link.'?';
		$first = true;

		foreach($vars as $name => $value) { 
			if ($first)
				$first = false;
			else
				$link .= '&amp;';

			$link .= $name.'='.$value;
		}

		return $link;
	}

	// }}}
	// {{{ private function getNextDirection()

	/**
	 * Gets the next direction or ordering in the rotation
	 *
	 * As a user clicks on the comun headers the direction of ordering changes
	 * from NONE => ASCSENDING => DESCENDING => NONE in a loop.
	 *
	 * @return integer the next direction of ordering for this column.
	 */
	private function getNextDirection()
	{
		switch ($this->direction) {
		case self::ORDER_BY_DIR_NONE:
			return self::ORDER_BY_DIR_ASCENDING;

		case self::ORDER_BY_DIR_ASCENDING:
			return self::ORDER_BY_DIR_DESCENDING;

		case self::ORDER_BY_DIR_DESCENDING:
		default:
			if ($this->view->default_orderby_column === null)
				// tri-state
				return self::ORDER_BY_DIR_NONE;
			else
				// bi-state
				return self::ORDER_BY_DIR_ASCENDING;
		}
	}

	// }}}
	// {{{ private function initFromGetVariables()

	/**
	 * Process GET variables and set class variables
	 */
	private function initFromGetVariables()
	{
		// TODO: is id a required field of table views?
		$key_orderby = $this->view->id.'_orderby';
		$key_orderbydir = $this->view->id.'_orderbydir';

		if (isset($_GET[$key_orderby]) && $_GET[$key_orderby] == $this->id) {
			$this->view->orderby_column = $this;

			if (isset($_GET[$key_orderbydir]))
				$this->setDirectionByString($_GET[$key_orderbydir]);
		}
	}

	// }}}
}

?>
