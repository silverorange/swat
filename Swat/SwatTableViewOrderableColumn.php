<?php

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
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewOrderableColumn extends SwatTableViewColumn
{


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

    /**
     * Indicates ascending ordering is done
     */
    const NULLS_FIRST = 1;

    /**
     * Indicates ascending ordering is done
     */
    const NULLS_LAST = 2;



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
     * Optional setting of the ordering of null values.
     *
     * Either {@link SwatTableViewOrderableColumn::NULLS_FIRST} or
     * {@link SwatTableViewOrderableColumn::NULLS_LAST}. If not set, defaults to
     * the databases default behaviour.
     *
     * For example, to order by nulls last when ordering use the following:
     *
     * <code>
     * $column->nulls_ordering = SwatTableViewOrderableColumn::NULLS_LAST;
     * </code>
     *
     * @var integer
     */
    public $nulls_ordering;

    /**
     * HTTP GET variables to remove from the column header link
     *
     * An array of GET variable names to unset before building new links.
     *
     * @var array
     */
    public $unset_get_vars = [];



    /**
     * The direction of ordering
     *
     * The current direction of ordering for this column. Valid values are:
     *
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE},
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING}, and
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING}.
     *
     * @var integer
     */
    protected $direction = self::ORDER_BY_DIR_NONE;

    /**
     * The default direction of ordering
     *
     * The default direction of ordering before the GET variables are processed.
     * When the GET variables are processed, they change
     * {@link SwatTableViewOrderableColumn::$direction} and
     * <kbd>$default_direction</kbd> remains unchanged. Valid values are:
     *
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE},
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING}, and
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING}.
     *
     * @var integer
     */
    protected $default_direction = self::ORDER_BY_DIR_NONE;



    /**
     * The mode of ordering
     *
     * The mode of switching between ordering states.
     * Valid values are ORDER_MODE_TRISTATE, and ORDER_MODE_BISTATE constants.
     *
     * @var integer
     */
    //private $mode = SwatTableViewOrderableColumn::ORDER_MODE_TRISTATE;



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



    /**
     * Sets the direction of ordering
     *
     * This method sets the direction of ordering of the column, either asc,
     * desc, or none. Valid directions are:
     *
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_NONE},
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING}, and
     * - {@link SwatTableViewOrderableColumn::ORDER_BY_DIR_ASCENDING}.
     *
     * @param $direction integer One of the ORDER_BY_DIR_* class contants
     */
    public function setDirection($direction)
    {
        $this->direction = $direction;
        $this->default_direction = $direction;

        if ($this->view->orderby_column === null) {
            $this->view->orderby_column = $this;
        }

        $this->view->default_orderby_column = $this;

        $this->initFromGetVariables();
    }



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
        $anchor->class = 'swat-table-view-orderable-column';

        // Set direction-specific CSS class on the anchor.
        if ($this->direction == self::ORDER_BY_DIR_DESCENDING) {
            $anchor->class .= ' swat-table-view-orderable-column-descending';
        } elseif ($this->direction == self::ORDER_BY_DIR_ASCENDING) {
            $anchor->class .= ' swat-table-view-orderable-column-ascending';
        }

        $anchor->open();

        if ($this->abbreviated_title === null) {
            $this->displayTitle($this->title, $this->title_content_type);
        } else {
            $abbr_tag = new SwatHtmlTag('abbr');
            $abbr_tag->title = $this->title;
            $abbr_tag->open();

            $this->displayTitle(
                $this->abbreviated_title,
                $this->abbreviated_title_content_type,
            );

            $abbr_tag->close();
        }

        $anchor->close();
    }



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
     * @param boolean $include_nulls_ordering optional. If specified, an extra
     *                                         string indicating the nulls
     *                                         ordering behaviour is appended.
     *
     * @return string the direction of ordering.
     */
    public function getDirectionAsString($direction_id = null, $include_nulls_ordering = true)
    {
        if ($direction_id === null) {
            $direction_id = $this->direction;
        }

        $direction = match ($direction_id) {
            self::ORDER_BY_DIR_NONE => '',
            self::ORDER_BY_DIR_ASCENDING => 'asc',
            self::ORDER_BY_DIR_DESCENDING => 'desc',
            default => throw new SwatException(
                sprintf(
                    "Ordering direction '%s' not found.",
                    $direction_id,
                ),
            ),
        };

        if ($include_nulls_ordering && $this->nulls_ordering !== null) {
            $direction .= match ($this->nulls_ordering) {
                self::NULLS_FIRST => ' nulls first',
                self::NULLS_LAST => ' nulls last',
                default => throw new SwatException(
                    sprintf(
                        "Nulls ordering '%s' not found.",
                        $this->nulls_ordering,
                    ),
                ),
            };
        }

        return $direction;
    }



    protected function displayTitle($title, $content_type)
    {
        // Display last word of the title in its own span so it can be styled
        // with an image.
        $title_exp = explode(' ', $title);
        $last_word = array_pop($title_exp);

        if (count($title_exp)) {
            $title = implode(' ', $title_exp) . ' ';
        } else {
            $title = '';
        }

        if ($content_type === 'text/plain') {
            echo SwatString::minimizeEntities($title);
        } else {
            echo $title;
        }

        $span_tag = new SwatHtmlTag('span');
        $span_tag->class = 'swat-table-view-orderable-column-title-last';
        $span_tag->setContent($last_word, $content_type);
        $span_tag->display();
    }



    /**
     * Gets the base CSS class names of this orderable table-view column
     *
     * @return array the array of base CSS class names for this orderable
     *                table-view column.
     */
    protected function getBaseCSSClassNames()
    {
        $classes = [];

        if ($this->view->orderby_column === $this) {
            $classes[] = 'swat-table-view-orderable-column-selected';
        }

        return $classes;
    }



    /**
     * Gets the prefix for GET var links
     *
     * @return string The prefix for GET var links
     */
    protected function getLinkPrefix()
    {
        // TODO: is id a required field of table views?
        return $this->view->id . '_';
    }



    /**
     * Gets the next direction or ordering in the rotation
     *
     * As a user clicks on the comun headers the direction of ordering changes
     * from NONE => ASCSENDING => DESCENDING => NONE in a loop.
     *
     * @return integer the next direction of ordering for this column.
     */
    protected function getNextDirection()
    {
        switch ($this->direction) {
            case self::ORDER_BY_DIR_NONE:
                return self::ORDER_BY_DIR_ASCENDING;

            case self::ORDER_BY_DIR_ASCENDING:
                return self::ORDER_BY_DIR_DESCENDING;

            case self::ORDER_BY_DIR_DESCENDING:
            default:
                if ($this->view->default_orderby_column === null) {
                    // tri-state
                    return self::ORDER_BY_DIR_NONE;
                }

                // bi-state
                return self::ORDER_BY_DIR_ASCENDING;
        }
    }



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
        $direction = mb_strtolower($direction);

        $this->direction = match ($direction) {
            'ascending', 'asc' => self::ORDER_BY_DIR_ASCENDING,
            'descending', 'desc' => self::ORDER_BY_DIR_DESCENDING,
            default => self::ORDER_BY_DIR_NONE,
        };
    }



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

        foreach ($vars as $name => $value) {
            if (in_array($name, $this->unset_get_vars)) {
                unset($vars[$name]);
            }
        }

        $key_orderby = $this->getLinkPrefix() . 'orderby';
        $key_orderbydir = $this->getLinkPrefix() . 'orderbydir';

        unset($vars[$key_orderby]);
        unset($vars[$key_orderbydir]);

        $next_dir = $this->getNextDirection();

        if ($next_dir != $this->default_direction) {
            $vars[$key_orderby] = $this->id;
            $vars[$key_orderbydir] = $this->getDirectionAsString(
                $next_dir,
                false,
            );
        }

        // build the new link
        $link = $this->link . '?';
        $first = true;

        foreach ($vars as $name => $value) {
            if ($first) {
                $first = false;
            } else {
                $link .= '&amp;';
            }

            $link .= $name . '=' . $value;
        }

        return $link;
    }



    /**
     * Process GET variables and set class variables
     */
    private function initFromGetVariables()
    {
        $key_orderby = $this->getLinkPrefix() . 'orderby';
        $key_orderbydir = $this->getLinkPrefix() . 'orderbydir';

        if (isset($_GET[$key_orderby]) && $_GET[$key_orderby] == $this->id) {
            $this->view->orderby_column = $this;

            if (isset($_GET[$key_orderbydir])) {
                $this->setDirectionByString($_GET[$key_orderbydir]);
            }
        }
    }

}
