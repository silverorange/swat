<?php
require_once('Swat/SwatTableViewColumn.php');
require_once('Swat/SwatHtmlTag.php');

/**
 * An orderable column.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatTableViewOrderableColumn extends SwatTableViewColumn {

	/**
	 * Href
	 *
	 * The initial HREF used when building links.  If null, link HREF's will
	 * begin with '?'.
	 *
	 * @var string
	 */
	public $href = null;

	/**
	 * HTTP GET vars to clobber
	 *
	 * An array of GET variable names to unset before rebuilding new link.
	 * @var array
	 */
	public $unset_get_vars = array();

	/**
	 * Direction of ordering
	 *
	 * The current ordering of this column. Valid values are ORDER_BY_DIR_* constants.
	 * @var int
	 */
	public $direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE;

	const ORDER_BY_DIR_NONE = 0;
	const ORDER_BY_DIR_DESCENDING = 1;
	const ORDER_BY_DIR_ASCENDING = 2;

	public function init() {
		$key_orderby = $this->view->name.'_orderby';
		$key_orderbydir = $this->view->name.'_orderbydir';

		if (isset($_GET[$key_orderby]) && $_GET[$key_orderby] == $this->name) {
			$this->view->orderby_column = $this;

			if (isset($_GET[$key_orderbydir])) {
				$this->setDirectionByString($_GET[$key_orderbydir]);
			}
		}
	}

	public function displayHeader() {
		$anchor = new SwatHtmlTag('a');
		$anchor->href = $this->getHref();

		$img = new SwatHtmlTag('img');
	
		if ($this->direction == SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING) {
			$img->src = 'swat/images/table-view-column-desc.png';
			$img->alt = 'Descending';
		} elseif ($this->direction == SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING) {
			$img->src = 'swat/images/table-view-column-asc.png';
			$img->alt = 'Ascending';
		}

		$img->width = 16;
		$img->height = 16;

		$anchor->open();
		echo $this->title, '&nbsp;';

		if ($this->view->orderby_column === $this)
			if ($this->direction != SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE)
				$img->display();

		$anchor->close();
	}

	private function getHref() {
		//$vars = array_diff_key($_GET, array_flip($this->unset_get_vars));
		$vars = $_GET;

		foreach($vars as $name => $value)
 			if (in_array($name, $this->unset_get_vars))
				unset($vars[$name]);

		$key_orderby = $this->view->name.'_orderby';
		$key_orderbydir = $this->view->name.'_orderbydir';

		unset($vars[$key_orderby]);
		unset($vars[$key_orderbydir]);

		$next_dir = $this->getNextDirection();

		if ($next_dir != SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE) {
			$vars[$key_orderby] = $this->name;
			$vars[$key_orderbydir] = $this->getDirectionString($next_dir);
		}

		if ($this->href === null)
			$href = '?';
		else
			$href = $this->href.'?';

		foreach($vars as $name => $value)
			$href.= $name.'='.$value.'&';

		// remove trailing ampersand
		$href = substr($href, 0, -1);

		return $href;
	}

	private function getNextDirection() {
		switch ($this->direction) {
			case SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE:
				return SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING;

			case SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING:
				return SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING;

			default:
				return SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE;
		}
	}

	/**
	 * Get direction of ordering
	 *
	 * Retrieve the current ordering direction of this column as a string 
	 * ('asc' or 'desc').
	 *
	 * @param int $id Optional value to convert rather than 
	 *        {@link SwatTableViewOrderableColumn::$direction}.
	 *
	 * @return string Ordering direction as a string.
	 */
	public function getDirectionString($id = null) {
		if ($id === null)
			$id = $this->direction;

		switch ($id) {
			case SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE:
				return '';

			case SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING:
				return 'asc';

			case SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING:
				return 'desc';

			default:
				throw new Exception('SwatTableViewOrderableColumn: unknown orderby');
		}
	}

	/**
	 * Set direction of ordering
	 *
	 * Set the current ordering direction of this column.
	 *
	 * @param string $name Ordering direction as a string ('asc' or 'desc').
	 */
	public function setDirectionByString($name) {
		switch ($name) {
			case 'asc':
				$this->direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING;
				break;

			case 'desc':
				$this->direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING;
				break;

			default:
				$this->direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE;
		}
	}
}
