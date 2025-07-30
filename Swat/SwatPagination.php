<?php

/**
 * A widget to allow navigation between paged data.
 *
 * SwatPagination pages start at page 1, not page 0.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPagination extends SwatControl
{
    /**
     * Display part constant for the displaying 'next' link.
     */
    public const NEXT = 1;

    /**
     * Display part constant for displaying the 'previous' link.
     */
    public const PREV = 2;

    /**
     * Display part constant for displaying a textual description of the
     * current position.
     */
    public const POSITION = 4;

    /**
     * Display part constant for displaying a list of pages close to the
     * current page.
     */
    public const PAGES = 8;

    /**
     * The URI linked by this pagination widget.
     *
     * The first sprintf placeholder (%s) in the URI will be replaced with
     * the current page number. For example: "mydir/page%s".
     *
     * @var string
     */
    public $link;

    /**
     * The number of records displayed on a page.
     *
     * @var int
     */
    public $page_size = 20;

    /**
     * The total number of records that are available for display.
     *
     * @var int
     */
    public $total_records = 0;

    /**
     * The index of the first record being displayed on the current page.
     *
     * @var int
     */
    public $current_record = 0;

    /**
     * The text label displayed in the Next link.
     *
     * Defaults to "Next »".
     *
     * @var string
     */
    public $next_label;

    /**
     * The text label displayed in the Previous link.
     *
     * Defaults to "« Previous".
     *
     * @var string
     */
    public $previous_label;

    /**
     * Displayed pagination parts.
     *
     * The display parts property is a bitwise combination of the
     * {@link SwatPagination::PREV}, {@link SwatPagination::NEXT},
     * {@link SwatPagination::PAGES} and {@link SwatPagination::POSITION}
     * constants.
     *
     * For example, to show a pagination widget with just next and previous
     * links use the following:
     *
     * <code>
     * $pagination->display_parts = SwatPagination::PREV |
     *     SwatPagination::NEXT;
     * </code>
     *
     * Defaults to <code>SwatPagination::POSITION | SwatPagination::NEXT |
     * SwatPagination::PREV | SwatPagination::PAGES</code>.
     *
     * @var int
     */
    public $display_parts;

    /**
     * The next page to display.
     *
     * @var int
     */
    protected $next_page = 0;

    /**
     * The previous page to display.
     *
     * @var int
     */
    protected $prev_page = 0;

    /**
     * The current page number.
     *
     * @var int
     */
    protected $current_page = 1;

    /**
     * The total number of pages in the record set.
     *
     * This is not the number of <i>records</i> in the record set.
     *
     * @var int
     */
    protected $total_pages = 0;

    /**
     * Creates a new pagination widget.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->requires_id = true;

        $this->display_parts
            = self::POSITION | self::NEXT | self::PREV | self::PAGES;

        // These strings include a non-breaking space
        $this->previous_label = Swat::_('‹ Previous');
        $this->next_label = Swat::_('Next ›');

        $this->addStyleSheet('packages/swat/styles/swat-pagination.css');
    }

    /**
     * Gets a human readable summary of the current state of this pagination
     * widget.
     *
     * @param $unit        string optional. The type of unit being returned. By
     *                     default this is 'record'.
     * @param $unit_plural string optional. The plural version of the
     *                     <i>$unit</i> parameter. By default this is
     *                     'records'.
     *
     * @return string a human readable summary of the current state of this
     *                pagination widget
     */
    public function getResultsMessage($unit = null, $unit_plural = null)
    {
        if ($unit === null) {
            $unit = Swat::_('record');
        }

        if ($unit_plural === null) {
            $unit_plural = Swat::_('records');
        }

        $message = '';

        if ($this->total_records === 0) {
            $message = sprintf(Swat::_('No %s.'), $unit_plural);
        } elseif ($this->total_records === 1) {
            $message = sprintf(Swat::_('One %s.'), $unit);
        } else {
            $locale = SwatI18NLocale::get();
            $message = sprintf(
                Swat::_('%s %s, displaying %s to %s'),
                $locale->formatNumber($this->total_records),
                $unit_plural,
                $locale->formatNumber($this->current_record + 1),
                $locale->formatNumber(
                    min(
                        $this->current_record + $this->page_size,
                        $this->total_records,
                    ),
                ),
            );
        }

        return $message;
    }

    /**
     * Displays this pagination widget.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        $this->calculatePages();

        if ($this->total_pages > 1) {
            $div_tag = new SwatHtmlTag('div');
            $div_tag->id = $this->id;
            $div_tag->class = $this->getCSSClassString();
            $div_tag->open();

            if ($this->display_parts & self::POSITION) {
                $this->displayPosition();
            }

            if ($this->display_parts & self::PREV) {
                $this->displayPrev();
            }

            if ($this->display_parts & self::PAGES) {
                $this->displayPages();
            }

            if ($this->display_parts & self::NEXT) {
                $this->displayNext();
            }

            $div_tag->close();
        }
    }

    /**
     * Set the current page that is displayed.
     *
     * Calculates the current_record properties.
     *
     * @param int $page the current page being displayed
     */
    public function setCurrentPage($page)
    {
        $this->current_page = max(1, (int) $page);

        $this->current_record = ($this->current_page - 1) * $this->page_size;
    }

    /**
     * Get the current page that is displayed.
     *
     * @return int the current page being displayed
     */
    public function getCurrentPage()
    {
        return $this->current_page;
    }

    /**
     * Displays the previous page link.
     */
    protected function displayPrev()
    {
        if ($this->prev_page > 0) {
            $link = $this->getLink();

            $anchor = new SwatHtmlTag('a');
            $anchor->href = sprintf($link, (string) $this->prev_page);
            // this is a non-breaking space
            $anchor->setContent($this->previous_label);
            $anchor->class = 'swat-pagination-nextprev';
            $anchor->display();
        } else {
            $span = new SwatHtmlTag('span');
            $span->class = 'swat-pagination-nextprev';
            $span->setContent($this->previous_label);
            $span->display();
        }
    }

    /**
     * Displays the current page position.
     *
     * i.e. "1 of 3"
     */
    protected function displayPosition()
    {
        $div = new SwatHtmlTag('div');
        $div->class = 'swat-pagination-position';

        $div->setContent(
            sprintf(
                Swat::_('Page %d of %d'),
                $this->current_page,
                $this->total_pages,
            ),
        );

        $div->display();
    }

    /**
     * Displays the next page link.
     */
    protected function displayNext()
    {
        if ($this->next_page > 0) {
            $link = $this->getLink();

            $anchor = new SwatHtmlTag('a');
            $anchor->href = sprintf($link, (string) $this->next_page);
            // this is a non-breaking space
            $anchor->setContent($this->next_label);
            $anchor->class = 'swat-pagination-nextprev';
            $anchor->display();
        } else {
            $span = new SwatHtmlTag('span');
            $span->class = 'swat-pagination-nextprev';
            // this is a non-breaking space
            $span->setContent($this->next_label);
            $span->display();
        }
    }

    /**
     * Displays a smart list of pages.
     */
    protected function displayPages()
    {
        $j = 0;

        $link = $this->getLink();

        $anchor = new SwatHtmlTag('a');
        $span = new SwatHtmlTag('span');
        $current = new SwatHtmlTag('span');
        $current->class = 'swat-pagination-current';

        for ($i = 1; $i <= $this->total_pages; $i++) {
            $display = false;

            if ($this->current_page < 7 && $i <= 10) {
                // Current page is in the first 6, show the first 10 pages
                $display = true;
            } elseif (
                $this->current_page > $this->total_pages - 6
                && $i >= $this->total_pages - 10
            ) {
                // Current page is in the last 6, show the last 10 pages
                $display = true;
            } elseif (
                $i < 3
                || $i > $this->total_pages - 2
                || abs($this->current_page - $i) <= 3
            ) {
                // Always show the first 2, last 2, and middle 6 pages
                $display = true;
            }

            if ($display) {
                if ($j + 1 != $i) {
                    // ellipses
                    $span->setContent('…');
                    $span->display();
                }

                if ($i == $this->current_page) {
                    $current->setContent((string) $i);
                    $current->display();
                } else {
                    $anchor->href = sprintf($link, (string) $i);
                    $anchor->title = sprintf(Swat::_('Go to page %d'), $i);

                    $anchor->setContent((string) $i);
                    $anchor->display();
                }

                $j = $i;
            }
        }
    }

    /**
     * Gets the base link for all page links.
     *
     * @return string the base link for all pages
     */
    protected function getLink()
    {
        return $this->link === null ? '%s' : $this->link;
    }

    /**
     * Gets the array of CSS classes that are applied to this pagination
     * widget.
     *
     * @return array the array of CSS classes that are applied to this
     *               pagination widget
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-pagination'];

        return array_merge($classes, parent::getCSSClassNames());
    }

    /**
     * Calculates page totals.
     *
     * Sets the internal total_pages, next_page and prev_page properties.
     */
    protected function calculatePages()
    {
        $this->total_pages = ceil($this->total_records / $this->page_size);

        if (
            $this->total_pages <= 1
            || $this->total_pages == $this->current_page
        ) {
            $this->next_page = 0;
        } else {
            $this->next_page = $this->current_page + 1;
        }

        if ($this->current_page > 0) {
            $this->prev_page = $this->current_page - 1;
        } else {
            $this->prev_page = 0;
        }
    }
}
