<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatTableViewColumn.php');

/**
 * A widget to allow navigation between paginated data.
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatPagination extends SwatControl {

	/**
	 * Href
	 *
	 * The URL of the current page, used to build links.
	 * @var int
	 */
	public $href = null;

	/**
	 * Get vars to clobber
	 *
	 * An array of GET variable names to unset before rebuilding new link.
	 * @var int
	 */
	public $unset_get_vars = array();

	/**
	 * Current page
	 *
	 * The number of the current page. The value is zero based.
	 * @var int
	 */
	public $current_page = 0;

	/**
	 * Current start
	 *
	 * The first record that should be displayed on this page. The value is
	 * zero based.
	 * @var int
	 */
	public $current_start = 0;

	/**
	 * Page size
	 *
	 * The number of records that are displayed per page.
	 * @var int
	 */
	public $page_size = 20;

	/**
	 * Total records
	 *
	 * The total number of records that are available for display.
	 * @var int
	 */
	public $total_records = 0;

	protected $next_page;
	protected $prev_page;
	protected $total_pages;

	public function init() {
		$this->generateAutoName();
	}

	public function display() {
		$this->calcPages();

		$div = new SwatHtmlTag('div');
		$div->class = 'swat-pagination';

		$div->open();
		echo '<table width="100%"><tr><td>';
		$this->displayPrev();
		echo '</td><td>';
		$this->displayPosition();
		echo '</td><td>';
		$this->displayNext();
		echo '</td></tr></table>';
		$div->close();
	}

	/**
	 * Display previous page link
	 */
	protected function displayPrev() {
		if ($this->prev_page != -1) {
			$href = $this->getHref();
			$anchor = new SwatHtmlTag('a');

			$anchor->href = sprintf($href, 0);
			$anchor->content = 'Start';
			$anchor->display();

			$anchor->href = sprintf($href, $this->prev_page);
			$anchor->content = 'Previous';
			$anchor->display();

		} else {
			echo 'Start';
			echo 'Previous';
		}
	}

	/**
	 * Display current position of page
	 *
	 * i.e. "1 of 3"
	 */
	protected function displayPosition() {
		echo ($this->current_page + 1), ' of ', $this->total_pages;
	}

	/**
	 * Display next page link
	 */
	protected function displayNext() {
		if ($this->next_page != -1) {
			$href = $this->getHref();
			$anchor = new SwatHtmlTag('a');

			$anchor->href = sprintf($href, $this->next_page);
			$anchor->content = 'Next';
			$anchor->display();

			$anchor->href = sprintf($href, $this->total_pages - 1);
			$anchor->content = 'Last';
			$anchor->display();

		} else {
			echo 'Next';
			echo 'Last';
		}
	}

	public function process() {
		if (array_key_exists($this->name, $_GET))
			$this->current_page = $_GET[$this->name];

		$this->current_record = $this->current_page * $this->page_size;
	}

	public function gatherErrorMessages() {
		return array();
	}

	private function getHref() {
		//$vars = array_diff_key($_GET, array_flip($this->unset_get_vars));
		$vars = $_GET;

		foreach($vars as $name => $value)
 			if (in_array($name, $this->unset_get_vars))
				unset($vars[$name]);

		$vars[$this->name] = '%s';
		
		if ($this->href === null)
			$href = '?';
		else
			$href = $this->href.'?';

		foreach($vars as $name => $value)
			$href.= $name.'='.$value.'&';

		// remove trailing ampersand
		if (count($vars))
			$href = substr($href, 0, -1);

		return $href;
	}

	private function calcPages() {
		$this->total_pages = ceil($this->total_records / $this->page_size);

		if (($this->total_pages <= 1) || ($this->total_pages - 1 == $this->current_page))
			$this->next_page = -1;
		else
			$this->next_page = $this->current_page + 1;

		if ($this->current_page > 0)
			$this->prev_page = $this->current_page - 1;
		else
			$this->prev_page = -1;
	}
}
?>
