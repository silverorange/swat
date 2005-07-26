<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A widget to allow navigation between paginated data
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPagination extends SwatControl
{
	/**
	 * Link
	 *
	 * The initial link used when building links. If null, links will
	 * begin with '?'.
	 *
	 * @var string
	 */
	public $link = null;

	/**
	 * HTTP GET vars to clobber
	 *
	 * An array of GET variable names to unset before rebuilding a new link.
	 *
	 * @var integer
	 */
	public $unset_get_vars = array();

	/**
	 * Current page
	 *
	 * The number of the current page. The value is zero based.
	 *
	 * @var integer
	 */
	public $current_page = 0;

	/**
	 * Current start
	 *
	 * The first record that should be displayed on this page. The value is
	 * zero based.
	 *
	 * @var integer
	 */
	public $current_start = 0;

	/**
	 * Page size
	 *
	 * The number of records that are displayed per page.
	 *
	 * @var integer
	 */
	public $page_size = 20;

	/**
	 * Total records
	 *
	 * The total number of records that are available for display.
	 *
	 * @var integer
	 */
	public $total_records = 0;

	/**
	 * Current record
	 *
	 * The record that is currently being displayed first on the page.
	 *
	 * TODO: I'm not sure what the scope of this should be.
	 *
	 * @var integer
	 */
	public $current_record = 0;
	
	/**
	 * The next page to display
	 *
	 * The value is zero based.
	 *
	 * @var integer
	 */
	protected $next_page = 0;

	/**
	 * The previous page to display
	 *
	 * The value is zero based.
	 *
	 * @var integer
	 */
	protected $prev_page = 0;
	
	/**
	 * The total number of pages in the database
	 *
	 * @var integer
	 */
	protected $total_pages = 0;

	/**
	 * Initializes this pagination widget
	 *
	 * Enforces that a unique id is set.
	 */
	public function init()
	{
		if ($this->id === null)
			$this->id = $this->getUniqueId();
	}

	/**
	 * Displays this pagination widget
	 */
	public function display()
	{
		$this->calculatePages();

		if ($this->total_pages > 1) {

			$div = new SwatHtmlTag('div');
			$div->class = 'swat-pagination';
			$div->open();
			
			$this->displayPosition();
			$this->displayPrev();
			$this->displayPages();
			$this->displayNext();

			$div->close();
			
		}
	}

	/**
	 * Processes this pagination widget
	 *
	 * Sets the current_page and current_record properties.
	 */
	public function process()
	{
		if (array_key_exists($this->id, $_GET))
			$this->current_page = $_GET[$this->id];

		$this->current_record = $this->current_page * $this->page_size;
	}

	/**
	 * Displays the previous page link
	 */
	protected function displayPrev()
	{
		if ($this->prev_page != -1) {
			$link = $this->getLink();
			
			$anchor = new SwatHtmlTag('a');
			$anchor->href = sprintf($link, (string) $this->prev_page);
			$anchor->content = sprintf(Swat::_('%s Previous'), '&#171;');
			$anchor->class = 'nextprev';
			$anchor->display();
		} else {
			$span = new SwatHtmlTag('span');
			$span->class = 'nextprev';
			$span->content = sprintf(Swat::_('%s Previous'), '&#171;');
			$span->display();
		}
	}

	/**
	 * Displays the current page position
	 *
	 * i.e. "1 of 3"
	 */
	protected function displayPosition()
	{
		$div = new SwatHtmlTag('div');
		$div->class = 'position';

		$div->content = sprintf(Swat::_('Page %d of %d'),
			$this->current_page + 1, $this->total_pages);

		$div->display();
	}

	/**
	 * Displays the next page link
	 */
	protected function displayNext()
	{
		if ($this->next_page != -1) {
			$link = $this->getLink();
			
			$anchor = new SwatHtmlTag('a');
			$anchor->href = sprintf($link, (string) $this->next_page);
			$anchor->content = sprintf(Swat::_('Next %s'), '&#187;');
			$anchor->class = 'nextprev';
			$anchor->display();
		} else {
			$span = new SwatHtmlTag('span');
			$span->class = 'nextprev';
			$span->content = sprintf(Swat::_('Next %s'), '&#187;');
			$span->display();
		}
	}

	/**
	 * Displays a smart list of pages
	 */
	protected function displayPages()
	{
		$j = -1;

		$link = $this->getLink();

		$anchor = new SwatHtmlTag('a');
		$span = new SwatHtmlTag('span');
		$current = new SwatHtmlTag('span');
		$current->class = 'current';

		for ($i = 0; $i < $this->total_pages; $i++) {
			$display = false;

			if ($this->current_page <= 6 && $i <= 9) {
				// Current page is in the first 6, show the first 10 pages
				$display = true;
				
			} elseif ($this->current_page >= $this->total_pages - 7 &&
				$i >= $this->total_pages - 10) {

				// Current page is in the last 6, show the last 10 pages
				$display = true;

			} elseif ($i <= 1 || $i >= $this->total_pages -2 ||
				abs($this->current_page - $i) <= 3) {
				
				// Always show the first 2, last 2, and middle 6 pages
				$display = true;
			}

			if ($display) {
				if ($j + 1 != $i) {
					// ellipses
					$span->content = '&#8230;';
					$span->display();
				}

				if ($i == $this->current_page) {
					$cuttent->content = ($i + 1);
					$current->display();
				} else {
					$anchor->href = sprintf($link, (string) $i);
					$anchor->title =
						sprintf(Swat::_('Go to page %d'), ($i + 1));

					$anchor->content = ($i + 1);
					$anchor->display();
				}

				$j = $i;
			}
		}
	}

	/**
	 * Gets the base link for all page links
	 *
	 * This removes all unwanted elements from the get variables and adds
	 * all the wanted ones back into an acceptable url string.
	 *
	 * @return string the base link for all pages with cleaned get variables.
	 */
	private function getLink()
	{
		//$vars = array_diff_key($_GET, array_flip($this->unset_get_vars));
		$vars = $_GET;

		foreach($vars as $name => $value)
 			if (in_array($name, $this->unset_get_vars))
				unset($vars[$name]);

		if ($this->link === null)
			$link = '?';
		else
			$link = $this->link.'?';

		foreach($vars as $name => $value)
			$link .= $name.'='.urlencode($value).'&amp;';

		$link.= urlencode($this->id).'=%s';

		return $link;
	}

	/**
	 * Calculates page totals
	 *
	 * Sets the internal total_pages, next_page and prev_page properties.
	 */
	private function calculatePages()
	{
		$this->total_pages = ceil($this->total_records / $this->page_size);

		if (($this->total_pages <= 1) ||
			($this->total_pages - 1 == $this->current_page))
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
