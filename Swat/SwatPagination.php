<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';

/**
 * A widget to allow navigation between paginated data
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPagination extends SwatControl
{
	// {{{ public properties

	/**
	 * Link
	 *
	 * The url to link to including a conversion string which is replaced
	 * by the page number (i.e. "mydir/page%s").
	 *
	 * @var string
	 */
	public $link = null;

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
	 * @var integer
	 */
	public $current_record = 0;

	// }}}
	// {{{ protected properties

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
	 * Current page
	 *
	 * The number of the current page. The value is zero based.
	 *
	 * @var integer
	 */
	protected $current_page = 0;

	/**
	 * The total number of pages in the database
	 *
	 * @var integer
	 */
	protected $total_pages = 0;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new pagination widget
	 *
	 * Enforces that a unique id is set.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$this->addStyleSheet('swat/styles/swat-pagination.css');
	}

	// }}}
	// {{{ public function getResultsMessage()

	/**
	 * Get Results Message
	 *
	 * Takes the current state of a {@link SwatPagination} widget and
	 * outputs a human readable summary of what is currently shown.
	 *
	 * @param $unit string Type of unit being returned (default 'record')
	 * @param $unit string Plural type of unit being returned (default
	 *        'records')
	 *
	 * @return string Results message
	 */
	public function getResultsMessage($unit = null, $unit_plural = null)
	{
		if ($unit === null)
			$unit = Swat::_('record');

		if ($unit_plural === null)
			$unit_plural = Swat::_('records');


		if ($this->total_records == 0)
			return sprintf(Swat::_('No %s.'), $unit_plural);

		elseif ($this->total_records == 1)
			return sprintf(Swat::_('One %s.'), $unit);

		else
			return sprintf(Swat::_('%s %s, displaying %s to %s'),
				SwatString::numberFormat($this->total_records),
				$unit_plural,
				SwatString::numberFormat($this->current_record + 1),
				SwatString::numberFormat(min($this->current_record +
					$this->page_size, $this->total_records)));
	}

	// }}}
	// {{{ public function display()

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

	// }}}
	// {{{ public function setCurrentPage()

	/**
	 * Set the current page that is displayed
	 *
	 * Calculates the current_record properties.
	 *
	 * @param integer $page The current page being displayed.
	 */
	public function setCurrentPage($page)
	{
		$this->current_page = $page;

		$this->current_record = $this->current_page * $this->page_size;
	}

	// }}}
	// {{{ public function getCurrentPage()

	/**
	 * Get the current page that is displayed
	 *
	 * @return integer The current page being displayed.
	 */
	public function getCurrentPage()
	{
		return $this->current_page;
	}

	// }}}
	// {{{ protected function displayPrev()

	/**
	 * Displays the previous page link
	 */
	protected function displayPrev()
	{
		if ($this->prev_page != -1) {
			$link = $this->getLink();

			$anchor = new SwatHtmlTag('a');
			$anchor->href = sprintf($link, (string) $this->prev_page);
			$anchor->setContent(sprintf(Swat::_('%sPrevious'), '&#171;&nbsp;'));
			$anchor->class = 'swat-pagination-nextprev';
			$anchor->display();
		} else {
			$span = new SwatHtmlTag('span');
			$span->class = 'swat-pagination-nextprev';
			$span->setContent(sprintf(Swat::_('%sPrevious'), '&#171;&nbsp;'));
			$span->display();
		}
	}

	// }}}
	// {{{ protected function displayPosition()

	/**
	 * Displays the current page position
	 *
	 * i.e. "1 of 3"
	 */
	protected function displayPosition()
	{
		$div = new SwatHtmlTag('div');
		$div->class = 'swat-pagination-position';

		$div->setContent(sprintf(Swat::_('Page %d of %d'),
			$this->current_page + 1, $this->total_pages));

		$div->display();
	}

	// }}}
	// {{{ protected function displayNext()

	/**
	 * Displays the next page link
	 */
	protected function displayNext()
	{
		if ($this->next_page != -1) {
			$link = $this->getLink();

			$anchor = new SwatHtmlTag('a');
			$anchor->href = sprintf($link, (string) $this->next_page);
			$anchor->setContent(sprintf(Swat::_('Next%s'), '&nbsp;&#187;'));
			$anchor->class = 'swat-pagination-nextprev';
			$anchor->display();
		} else {
			$span = new SwatHtmlTag('span');
			$span->class = 'swat-pagination-nextprev';
			$span->setContent(sprintf(Swat::_('Next%s'), '&nbsp;&#187;'));
			$span->display();
		}
	}

	// }}}
	// {{{ protected function displayPages()

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
		$current->class = 'swat-pagination-current';

		for ($i = 0; $i < $this->total_pages; $i++) {
			$display = false;

			if ($this->current_page < 7 && $i < 10) {
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
					$span->setContent('&#8230;');
					$span->display();
				}

				if ($i == $this->current_page) {
					$current->setContent((string)($i + 1));
					$current->display();
				} else {
					$anchor->href = sprintf($link, (string)$i);
					$anchor->title =
						sprintf(Swat::_('Go to page %d'), ($i + 1));

					$anchor->setContent((string)($i + 1));
					$anchor->display();
				}

				$j = $i;
			}
		}
	}

	// }}}
	// {{{ protected function getLink()

	/**
	 * Gets the base link for all page links
	 *
	 * @return string the base link for all pages.
	 */
	protected function getLink()
	{
		return ($this->link === null) ? '%s' : $this->link;
	}

	// }}}
	// {{{ private function calculatePages()

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

	// }}}
}

?>
