<?php

/**
 * A widget to display data in a tabular form.
 *
 * Records in this table-view's model may be selected by the user by adding a
 * view-selector to this table-view. See {@link SwatView} for details on how to
 * use {@link SwatViewSelector} objects.
 *
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableView extends SwatView
{
    /**
     * The column of this table-view that data in the model is currently being
     * sorted by.
     *
     * If no sorting is currently happening, this can be null. Alternatively,
     * this can be set to a SwatTableViewOrderableColumn and the column itself
     * may be set to no sorting.
     *
     * @var SwatTableViewOrderableColumn
     *
     * @todo Clean up this API. Making this a public property is prone to
     *       misuse.
     */
    public $orderby_column;

    /**
     * The column of this table-view that the data in the model is sorted by
     * by default if no sorting is happening.
     *
     * Setting this directly usually won't do what you want. Use the
     * {@link SwatTableView::setDefaultOrderbyColumn()} method instead.
     *
     * If this is null then the default order of data in the model is some
     * implicit order that the user cannot see. This results in tri-state
     * column headers.
     *
     * If this is set then the data ordering is always explicit and visible to
     * the user. This results in bi-state column headers.
     *
     * @var SwatTableViewOrderableColumn
     *
     * @see SwatTableViewOrderableColumn
     * @see SwatTableView::setDefaultOrderbyColumn()
     */
    public $default_orderby_column;

    /**
     * No records message text.
     *
     * A message to show if the table view has no records to display. If
     * set to an empty string, no message is displayed.
     *
     * @var string
     */
    public $no_records_message = '<none>';

    /**
     * Optional content type for the no records message.
     *
     * Default text/plain, use text/xml for XHTML fragments.
     *
     * @var string
     */
    public $no_records_message_type = 'text/plain';

    /**
     * Whether of not to display the tfoot element after the tbody element.
     *
     * If this flag is set to true, the tfoot element will be displayed after
     * the tbody element. This is invalid XHTML but fixes a number of rendering
     * bugs in various browsers. This flag defaults to false.
     *
     * When browser support for tfoot is better, this property will be
     * deprecated. This property is not recommended for use unless you are
     * experiencing browser bugs in your table views.
     *
     * @var bool
     */
    public $use_invalid_tfoot_ordering = false;

    /**
     * The columns of this table-view indexed by their unique identifier.
     *
     * A unique identifier is not required so this array does not necessarily
     * contain all columns in the view. It serves as an efficient data
     * structure to lookup columns by their id.
     *
     * The array is structured as id => column reference.
     *
     * @var array
     */
    protected $columns_by_id = [];

    /**
     * The row columns of this table-view indexed by their unique identifier.
     *
     * A unique identifier is not required so this array does not necessarily
     * contain all row columns in the view. It serves as an efficient data structure
     * to lookup row columns by their id.
     *
     * The array is structured as id => column reference.
     *
     * @var array
     */
    protected $spanning_columns_by_id = [];

    /**
     * The groups of this table-view indexed by their unique identifier.
     *
     * A unique identifier is not required so this array does not necessarily
     * contain all groups in the view. It serves as an efficient data structure
     * to lookup groups by their id.
     *
     * The array is structured as id => group reference.
     *
     * @var array
     */
    protected $groups_by_id = [];

    /**
     * The extra rows of this table-view indexed by their unique identifier.
     *
     * A unique identifier is not required so this array does not necessarily
     * contain all extra rows in the view. It serves as an efficient data
     * structure to lookup extra rows by their id.
     *
     * The array is structured as id => row reference.
     *
     * @var array
     */
    protected $rows_by_id = [];

    /**
     * The columns of this table-view.
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Row column objects for this table view.
     *
     * @var array
     *
     * @see SwatTableView::addSpanningColumn()
     */
    protected $spanning_columns = [];

    /**
     * Grouping objects for this table view.
     *
     * @var array
     *
     * @see SwatTableView::addGroup()
     */
    protected $groups = [];

    /**
     * Any extra rows that were appended to this view.
     *
     * This array does not include rows that are displayed based on this
     * table-view's model.
     *
     * @var array
     */
    protected $extra_rows = [];

    /**
     * Whether or not this table view has an input row.
     *
     * Only one input row is allowed for each table-view.
     *
     * @var bool
     *
     * @see SwatTableViewInputRow
     */
    protected $has_input_row = false;

    // general methods

    /**
     * Creates a new table view.
     *
     * @param string $id a non-visible unique id for this widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $yui = new SwatYUI(['dom']);
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

        $this->addJavaScript('packages/swat/javascript/swat-table-view.js');
        $this->addStyleSheet('packages/swat/styles/swat-table-view.css');
    }

    /**
     * Initializes this table-view.
     *
     * This initializes all columns, extra rows and groupsin this table-view.
     *
     * @see SwatWidget::init()
     */
    public function init()
    {
        parent::init();

        foreach ($this->columns as $column) {
            $column->init();
            // index the column by id if it is not already indexed
            if (!array_key_exists($column->id, $this->columns_by_id)) {
                $this->columns_by_id[$column->id] = $column;
            }
        }

        foreach ($this->extra_rows as $row) {
            $row->init();
            // index the row by id if it is not already indexed
            if (!array_key_exists($row->id, $this->rows_by_id)) {
                $this->rows_by_id[$row->id] = $row;
            }
        }

        foreach ($this->groups as $group) {
            $group->init();
            // index the group by id if it is not already indexed
            if (!array_key_exists($group->id, $this->groups_by_id)) {
                $this->groups_by_id[$group->id] = $group;
            }
        }

        foreach ($this->spanning_columns as $column) {
            $column->init();
            // index the row column by id if it is not already indexed
            if (!array_key_exists($column->id, $this->spanning_columns_by_id)) {
                $this->spanning_columns_by_id[$column->id] = $column;
            }
        }
    }

    /**
     * Displays this table-view.
     *
     * The table view is displayed as an XHTML table.
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        if ($this->model === null) {
            return;
        }

        parent::display();

        $show_no_records = true;
        $row_count = count($this->model);
        foreach ($this->extra_rows as $row) {
            if ($row->getVisibleByCount($row_count)) {
                $show_no_records = false;
                break;
            }
        }

        if ($row_count === 0 && $show_no_records) {
            if ($this->no_records_message != '') {
                $div = new SwatHtmlTag('div');
                $div->class = 'swat-none';
                $div->setContent(
                    $this->no_records_message,
                    $this->no_records_message_type,
                );

                $div->display();
            }

            return;
        }

        $table_tag = new SwatHtmlTag('table');
        $table_tag->id = $this->id;
        $table_tag->class = $this->getCSSClassString();
        $table_tag->cellspacing = '0';

        $table_tag->open();

        if ($this->hasHeader()) {
            $this->displayHeader();
        }

        if ($this->use_invalid_tfoot_ordering) {
            $this->displayBody();
            $this->displayFooter();
        } else {
            $this->displayFooter();
            $this->displayBody();
        }

        $table_tag->close();

        Swat::displayInlineJavaScript($this->getInlineJavaScript());
    }

    /**
     * Processes this table-view.
     */
    public function process()
    {
        parent::process();

        foreach ($this->columns as $column) {
            $column->process();
        }

        foreach ($this->spanning_columns as $column) {
            $column->process();
        }

        foreach ($this->extra_rows as $row) {
            $row->process();
        }

        // this is part of the old selection API
        if ($this->hasColumn('checkbox')) {
            $items = $this->getColumn('checkbox');
            $this->checked_items = $items->getItems();
        }
    }

    /**
     * Adds a child object.
     *
     * This method fulfills the {@link SwatUIParent} interface. It is used
     * by {@link SwatUI} when building a widget tree and should not need to be
     * called elsewhere.
     *
     * To add columns, rows, or a grouping to a table-view, use
     * {@link SwatTableView::appendColumn()},
     * {@link SwatTableView::appendRow()},
     * or {@link SwatTableView::appendGroup()}.
     *
     * @param mixed $child a reference to a child object to add
     *
     * @throws SwatInvalidClassException
     *
     * @see SwatUIParent
     * @see SwatTableView::appendColumn()
     * @see SwatTableView::appendGroup()
     * @see SwatTableView::appendRow()
     */
    public function addChild(SwatObject $child)
    {
        if ($child instanceof SwatTableViewGroup) {
            $this->appendGroup($child);
        } elseif ($child instanceof SwatTableViewSpanningColumn) {
            $this->appendSpanningColumn($child);
        } elseif ($child instanceof SwatTableViewRow) {
            $this->appendRow($child);
        } elseif ($child instanceof SwatTableViewColumn) {
            $this->appendColumn($child);
        } else {
            throw new SwatInvalidClassException(
                'Only SwatTableViewColumn, SwatTableViewGroup, or '
                    . 'SwatTableViewRow objects may be nested within SwatTableView '
                    . 'objects.',
                0,
                $child,
            );
        }
    }

    /**
     * Gathers all messages from this table-view.
     *
     * @return array an array of {@link SwatMessage} objects
     */
    public function getMessages()
    {
        $messages = parent::getMessages();

        if ($this->model !== null) {
            foreach ($this->model as $row) {
                foreach ($this->columns as $column) {
                    $messages = array_merge(
                        $messages,
                        $column->getMessages($row),
                    );
                }

                foreach ($this->spanning_columns as $column) {
                    $messages = array_merge(
                        $messages,
                        $column->getMessages($row),
                    );
                }
            }

            foreach ($this->extra_rows as $row) {
                if ($row instanceof SwatTableViewWidgetRow) {
                    $messages = array_merge($messages, $row->getMessages());
                }
            }
        }

        return $messages;
    }

    /**
     * Gets whether or not this table-view has any messages.
     *
     * @return bool true if this table-view has one or more messages and
     *              false if it does not
     */
    public function hasMessage()
    {
        $has_message = parent::hasMessage();

        if (!$has_message && $this->model !== null) {
            foreach ($this->model as $row) {
                foreach ($this->columns as $column) {
                    if ($column->hasMessage($row)) {
                        $has_message = true;
                        break 2;
                    }
                }

                foreach ($this->spanning_columns as $column) {
                    if ($column->hasMessage($row)) {
                        $has_message = true;
                        break 2;
                    }
                }
            }
        }

        if (!$has_message) {
            foreach ($this->extra_rows as $row) {
                if ($row->hasMessage()) {
                    $has_message = true;
                    break;
                }
            }
        }

        return $has_message;
    }

    /**
     * Gets how many XHTML table columns the visible column objects of this
     * table-view object span on display.
     *
     * @return int the number of XHTML table columns the visible column
     *             objects of this table-view object span on display
     */
    public function getXhtmlColspan()
    {
        $colspan = 0;

        // Add all colspans of visible columns to get table colspan.
        foreach ($this->getVisibleColumns() as $column) {
            $colspan += $column->getXhtmlColspan();
        }

        // Check each spanning column. If it has more colspan than the colspan
        // defined by the columns, set the table colspan to the spanning column
        // colspan.
        foreach ($this->getVisibleSpanningColumns() as $column) {
            $spanning_colspan = $column->getXhtmlColspan();
            if ($spanning_colspan > $colspan) {
                $colspan = $spanning_colspan;
            }
        }

        return $colspan;
    }

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this table.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                              this table-view
     *
     * @see SwatUIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        foreach ($this->columns as $column) {
            $set->addEntrySet($column->getHtmlHeadEntrySet());
        }

        foreach ($this->spanning_columns as $column) {
            $set->addEntrySet($column->getHtmlHeadEntrySet());
        }

        foreach ($this->extra_rows as $row) {
            $set->addEntrySet($row->getHtmlHeadEntrySet());
        }

        foreach ($this->groups as $group) {
            $set->addEntrySet($group->getHtmlHeadEntrySet());
        }

        return $set;
    }

    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this table.
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
     *                              needed by this table-view
     *
     * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        foreach ($this->columns as $column) {
            $set->addEntrySet($column->getAvailableHtmlHeadEntrySet());
        }

        foreach ($this->spanning_columns as $column) {
            $set->addEntrySet($column->getAvailableHtmlHeadEntrySet());
        }

        foreach ($this->extra_rows as $row) {
            $set->addEntrySet($row->getAvailableHtmlHeadEntrySet());
        }

        foreach ($this->groups as $group) {
            $set->addEntrySet($group->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    /**
     * Gets descendant UI-objects.
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                           that are instances of <i>$class_name</i> are
     *                           returned.
     *
     * @return array the descendant UI-objects of this table-view. If
     *               descendant objects have identifiers, the identifier is
     *               used as the array key.
     *
     * @see SwatUIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (
            !(
                $class_name === null
                || class_exists($class_name)
                || interface_exists($class_name)
            )
        ) {
            return [];
        }

        $out = [];

        foreach ($this->columns as $column) {
            if ($class_name === null || $column instanceof $class_name) {
                if ($column->id === null) {
                    $out[] = $column;
                } else {
                    $out[$column->id] = $column;
                }
            }

            if ($column instanceof SwatUIParent) {
                $out = array_merge($out, $column->getDescendants($class_name));
            }
        }

        foreach ($this->spanning_columns as $column) {
            if ($class_name === null || $column instanceof $class_name) {
                if ($column->id === null) {
                    $out[] = $column;
                } else {
                    $out[$column->id] = $column;
                }
            }

            if ($column instanceof SwatUIParent) {
                $out = array_merge($out, $column->getDescendants($class_name));
            }
        }

        foreach ($this->groups as $group) {
            if ($class_name === null || $group instanceof $class_name) {
                if ($group->id === null) {
                    $out[] = $group;
                } else {
                    $out[$group->id] = $group;
                }
            }

            if ($group instanceof SwatUIParent) {
                $out = array_merge($out, $group->getDescendants($class_name));
            }
        }

        foreach ($this->extra_rows as $row) {
            if ($class_name === null || $row instanceof $class_name) {
                if ($row->id === null) {
                    $out[] = $row;
                } else {
                    $out[$row->id] = $row;
                }
            }

            if ($row instanceof SwatUIParent) {
                $out = array_merge($out, $row->getDescendants($class_name));
            }
        }

        return $out;
    }

    /**
     * Gets the first descendant UI-object of a specific class.
     *
     * @param string $class_name class name to look for
     *
     * @return SwatUIObject the first descendant UI-object or null if no
     *                      matching descendant is found
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name)) {
            return null;
        }

        $out = null;

        foreach ($this->columns as $column) {
            if ($column instanceof $class_name) {
                $out = $column;
                break;
            }

            if ($column instanceof SwatUIParent) {
                $out = $column->getFirstDescendant($class_name);
                if ($out !== null) {
                    break;
                }
            }
        }

        if ($out === null) {
            foreach ($this->spanning_columns as $column) {
                if ($column instanceof $class_name) {
                    $out = $column;
                    break;
                }

                if ($column instanceof SwatUIParent) {
                    $out = $column->getFirstDescendant($class_name);
                    if ($out !== null) {
                        break;
                    }
                }
            }
        }

        if ($out === null) {
            foreach ($this->groups as $group) {
                if ($group instanceof $class_name) {
                    $out = $group;
                    break;
                }

                if ($group instanceof SwatUIParent) {
                    $out = $group->getFirstDescendant($class_name);
                    if ($out !== null) {
                        break;
                    }
                }
            }
        }

        if ($out === null) {
            foreach ($this->extra_rows as $row) {
                if ($row instanceof $class_name) {
                    $out = $row;
                    break;
                }

                if ($row instanceof SwatUIParent) {
                    $out = $row->getFirstDescendant($class_name);
                    if ($out !== null) {
                        break;
                    }
                }
            }
        }

        return $out;
    }

    /**
     * Gets descendant states.
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this table-view.
     *
     * @return array an array of UI-object states with UI-object identifiers as
     *               array keys
     */
    public function getDescendantStates()
    {
        $states = [];

        foreach ($this->getDescendants('SwatState') as $id => $object) {
            $states[$id] = $object->getState();
        }

        return $states;
    }

    /**
     * Sets descendant states.
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * table-view.
     *
     * @param array $states an array of UI-object states with UI-object
     *                      identifiers as array keys
     */
    public function setDescendantStates(array $states)
    {
        foreach ($this->getDescendants('SwatState') as $id => $object) {
            if (isset($states[$id])) {
                $object->setState($states[$id]);
            }
        }
    }

    /**
     * Performs a deep copy of the UI tree starting with this UI object.
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                          objects in the UI tree.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                      object
     *
     * @see SwatUIObject::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);

        $copy->columns_by_id = [];
        foreach ($this->columns as $key => $column) {
            $copy_column = $column->copy($id_suffix);
            $copy_column->parent = $copy;
            $copy->columns[$key] = $copy_column;
            if ($copy_column->id !== null) {
                $copy->columns_by_id[$copy_column->id] = $copy_column;
            }
        }

        $copy->spanning_columns_by_id = [];
        foreach ($this->spanning_columns as $key => $column) {
            $copy_column = $column->copy($id_suffix);
            $copy_column->parent = $copy;
            $copy->spanning_columns[$key] = $copy_column;
            if ($copy_column->id !== null) {
                $copy->spanning_columns_by_id[$copy_column->id] = $copy_column;
            }
        }

        $copy->groups_by_id = [];
        foreach ($this->groups as $key => $group) {
            $copy_group = $group->copy($id_suffix);
            $copy_group->parent = $copy;
            $copy->groups[$key] = $copy_group;
            if ($copy_group->id !== null) {
                $copy->groups_by_id[$copy_group->id] = $copy_group;
            }
        }

        $copy->rows_by_id = [];
        foreach ($this->extra_rows as $key => $row) {
            $copy_row = $row->copy($id_suffix);
            $copy_row->parent = $copy;
            $copy->extra_rows[$key] = $copy_row;
            if ($copy_row->id !== null) {
                $copy->rows_by_id[$copy_row->id] = $copy_row;
            }
        }

        // TODO: what to do with view selectors?

        return $copy;
    }

    /**
     * Whether this table has a header to display.
     *
     * Each column is asked whether is has a header to display.
     */
    protected function hasHeader()
    {
        $has_header = false;

        foreach ($this->columns as $column) {
            if ($column->hasHeader()) {
                $has_header = true;
                break;
            }
        }

        return $has_header;
    }

    /**
     * Displays the column headers for this table-view.
     *
     * Each column is asked to display its own header.
     * Rows in the header are outputted inside a <thead> HTML tag.
     */
    protected function displayHeader()
    {
        echo '<thead>';
        echo '<tr>';

        foreach ($this->columns as $column) {
            $column->displayHeaderCell();
        }

        echo '</tr>';
        echo '</thead>';
    }

    /**
     * Displays the contents of this view.
     *
     * The contents reflect the data stored in the model of this table-view.
     * Things like row highlighting are done here.
     *
     * Table rows are displayed inside a <tbody> XHTML tag.
     */
    protected function displayBody()
    {
        $count = 0;

        // this uses read-ahead iteration

        $this->model->rewind();
        $row = $this->model->valid() ? $this->model->current() : null;

        $this->model->next();
        $next_row = $this->model->valid() ? $this->model->current() : null;

        if (count($this->model) > 0) {
            echo '<tbody>';

            while ($row !== null) {
                $count++;

                $this->displayRow($row, $next_row, $count);

                $row = $next_row;
                $this->model->next();
                $next_row = $this->model->valid()
                    ? $this->model->current()
                    : null;
            }

            echo '</tbody>';
        } else {
            // to validate in the case where the form has a tfoot but no rows
            echo '<tbody class="swat-hidden"><tr><td></td></tr></tbody>';
        }
    }

    /**
     * Displays a single row.
     *
     * The contents reflect the data stored in the model of this table-view.
     * Things like row highlighting are done here.
     *
     * @param mixed $row      the row to display
     * @param mixed $next_row the next row that will be displayed. If there is
     *                        no next row, this is null.
     * @param int   $count    the ordinal position of the current row. Starts
     *                        at one.
     */
    protected function displayRow($row, $next_row, $count)
    {
        $this->displayRowGroupHeaders($row, $next_row, $count);
        $this->displayRowColumns($row, $next_row, $count);
        $this->displayRowSpanningColumns($row, $next_row, $count);
        $this->displayRowMessages($row);
        $this->displayRowGroupFooters($row, $next_row, $count);
    }

    /**
     * Displays row group headers.
     *
     * @param mixed $row      the row to display
     * @param mixed $next_row the next row that will be displayed. If there is
     *                        no next row, this is null.
     * @param int   $count    the ordinal position of the current row. Starts
     *                        at one.
     */
    protected function displayRowGroupHeaders($row, $next_row, $count)
    {
        foreach ($this->groups as $group) {
            $group->display($row);
        }
    }

    /**
     * Displays row group headers.
     *
     * @param mixed $row      the row to display
     * @param mixed $next_row the next row that will be displayed. If there is
     *                        no next row, this is null.
     * @param int   $count    the ordinal position of the current row. Starts
     *                        at one.
     */
    protected function displayRowGroupFooters($row, $next_row, $count)
    {
        foreach ($this->groups as $group) {
            $group->displayFooter($row, $next_row);
        }
    }

    /**
     * Displays the columns for a row.
     *
     * @param mixed $row      the row to display
     * @param mixed $next_row the next row that will be displayed. If there is
     *                        no next row, this is null.
     * @param int   $count    the ordinal position of the current row. Starts
     *                        at one.
     */
    protected function displayRowColumns($row, $next_row, $count)
    {
        // Make sure we should display a row for the current data. If there
        // are no visible columns for the row data, just skip displaying the
        // entire row.
        $should_show_row = false;
        foreach ($this->getVisibleColumns() as $column) {
            if ($column->hasVisibleRenderer($row)) {
                $should_show_row = true;
                break;
            }
        }

        // Display a row of data.
        if ($should_show_row) {
            $tr_tag = new SwatHtmlTag('tr');
            $tr_tag->class = $this->getRowClassString($row, $count);

            foreach ($this->columns as $column) {
                $tr_tag->addAttributes($column->getTrAttributes($row));
            }

            if ($this->rowHasMessage($row)) {
                $tr_tag->class .= ' swat-error';
            }

            $tr_tag->open();

            foreach ($this->columns as $column) {
                $column->display($row);
            }

            $tr_tag->close();
        }
    }

    /**
     * Displays row spanning columns.
     *
     * @param mixed $row      the row to display
     * @param mixed $next_row the next row that will be displayed. If there is
     *                        no next row, this is null.
     * @param int   $count    the ordinal position of the current row. Starts
     *                        at one.
     */
    protected function displayRowSpanningColumns($row, $next_row, $count)
    {
        $tr_tag = new SwatHtmlTag('tr');
        $tr_tag->class = $this->getRowClassString($row, $count);

        if ($this->rowHasMessage($row)) {
            $tr_tag->class = $tr_tag->class . ' swat-error';
        }

        $tr_tag->class .= ' swat-table-view-spanning-column';

        foreach ($this->spanning_columns as $column) {
            if ($column->visible && $column->hasVisibleRenderer($row)) {
                $tr_tag->open();
                $column->display($row);
                $tr_tag->close();
            }
        }
    }

    /**
     * Displays a list of {@link SwatMessage} object for the given row.
     *
     * @param mixed $row the row for which to display messages
     */
    protected function displayRowMessages($row)
    {
        $messages = [];

        foreach ($this->columns as $column) {
            $messages = array_merge($messages, $column->getMessages($row));
        }

        foreach ($this->spanning_columns as $column) {
            $messages = array_merge($messages, $column->getMessages($row));
        }

        if (count($messages) > 0) {
            $tr_tag = new SwatHtmlTag('tr');
            $tr_tag->class = 'swat-table-view-input-row-messages';
            $tr_tag->open();

            $td_tag = new SwatHtmlTag('td');
            $td_tag->colspan = $this->getXhtmlColspan();
            $td_tag->open();

            $ul_tag = new SwatHtmlTag('ul');
            $ul_tag->class = 'swat-table-view-input-row-messages';
            $ul_tag->open();

            $li_tag = new SwatHtmlTag('li');
            foreach ($messages as &$message) {
                $li_tag->setContent(
                    $message->primary_content,
                    $message->content_type,
                );

                $li_tag->class = $message->getCssClass();
                $li_tag->display();
            }

            $ul_tag->close();

            $td_tag->close();
            $tr_tag->close();
        }
    }

    /**
     * Displays any footer content for this table-view.
     *
     * Rows in the footer are outputted inside a <tfoot> HTML tag.
     */
    protected function displayFooter()
    {
        ob_start();

        foreach ($this->extra_rows as $row) {
            $row->display();
        }

        $footer_content = ob_get_clean();

        if ($footer_content != '') {
            $tfoot_tag = new SwatHtmlTag('tfoot');
            if ($this->use_invalid_tfoot_ordering) {
                $tfoot_tag->class = 'swat-table-view-invalid-tfoot-ordering';
            }

            $tfoot_tag->setContent($footer_content, 'text/xml');
            $tfoot_tag->display();
        }
    }

    /**
     * Whether any of the columns in the row has a message.
     *
     * @param mixed $row the data object to use to check the column for
     *                   messages
     *
     * @return bool true if any of the columns in the row has a message,
     *              otherwise false
     */
    protected function rowHasMessage($row)
    {
        $has_message = false;

        foreach ($this->columns as $column) {
            if ($column->hasMessage($row)) {
                $has_message = true;
                break;
            }
        }

        if (!$has_message) {
            foreach ($this->spanning_columns as $column) {
                if ($column->hasMessage($row)) {
                    $has_message = true;
                    break;
                }
            }
        }

        return $has_message;
    }

    /**
     * Gets the array of CSS classes that are applied to this table view.
     *
     * @return array the array of CSS classes that are applied to this table
     *               view
     */
    protected function getCSSClassNames()
    {
        $classes = ['swat-table-view'];

        return array_merge($classes, parent::getCSSClassNames());
    }

    /**
     * Gets CSS classes for the XHTML tr tag.
     *
     * @param mixed $row   a data object containing the data to be displayed in
     *                     this row
     * @param int   $count the ordinal position of this row in the table
     *
     * @return array CSS class names
     */
    protected function getRowClasses($row, $count)
    {
        $classes = [];

        if ($count % 2 === 1) {
            $classes[] = 'odd';
        }

        if ($count === 1) {
            $classes[] = 'first';
        }

        if ($count === count($this->model)) {
            $classes[] = 'last';
        }

        return $classes;
    }

    /**
     * Gets CSS class string for the XHTML tr tag.
     *
     * @param mixed $row   a data object containing the data to be displayed in
     *                     this row
     * @param int   $count the ordinal position of this row in the table
     *
     * @return string CSS class string
     */
    protected function getRowClassString($row, $count)
    {
        $class_string = null;

        $classes = $this->getRowClasses($row, $count);

        if (count($classes) > 0) {
            $class_string = implode(' ', $classes);
        }

        return $class_string;
    }

    /**
     * Gets inline JavaScript required by this table-view as well as any
     * JavaScript required by columns and/or rows.
     *
     * Column JavaSscript is placed before extra row JavaScript.
     *
     * @return string inline JavaScript needed by this table-view
     */
    protected function getInlineJavaScript()
    {
        $javascript = sprintf(
            "var %s = new SwatTableView('%s');",
            $this->id,
            $this->id,
        );

        $has_rows
            = $this->model instanceof SwatTableModel && count($this->model) > 0;

        if ($has_rows) {
            foreach ($this->columns as $column) {
                if ($column->visible) {
                    $column_javascript = $column->getRendererInlineJavaScript();
                    if ($column_javascript != '') {
                        $javascript .= "\n" . $column_javascript;
                    }
                }
            }

            foreach ($this->spanning_columns as $column) {
                if ($column->visible) {
                    $column_javascript = $column->getRendererInlineJavaScript();
                    if ($column_javascript != '') {
                        $javascript .= "\n" . $column_javascript;
                    }
                }
            }
        }

        foreach ($this->columns as $column) {
            if ($column->visible) {
                $column_javascript = $column->getInlineJavaScript();
                if ($column_javascript != '') {
                    $javascript .= "\n" . $column_javascript;
                }
            }
        }

        foreach ($this->spanning_columns as $column) {
            if ($column->visible) {
                $column_javascript = $column->getInlineJavaScript();
                if ($column_javascript != '') {
                    $javascript .= "\n" . $column_javascript;
                }
            }
        }

        foreach ($this->extra_rows as $row) {
            if ($row->visible) {
                $row_javascript = $row->getInlineJavaScript();
                if ($row_javascript != '') {
                    $javascript .= "\n" . $row_javascript;
                }
            }
        }

        return $javascript;
    }

    // column methods

    /**
     * Appends a column to this table-view.
     *
     * @param SwatTableViewColumn $column the column to append
     *
     * @throws SwatDuplicateIdException if the column has the same id as a
     *                                  column already in this table-view
     */
    public function appendColumn(SwatTableViewColumn $column)
    {
        $this->insertColumn($column);
    }

    /**
     * Inserts a column before an existing column in this table-view.
     *
     * @param SwatTableViewColumn $column           the column to insert
     * @param SwatTableViewColumn $reference_column the column before which the
     *                                              column will be inserted
     *
     * @throws SwatWidgetNotFoundException if the reference column does not
     *                                     exist in this table-view
     * @throws SwatDuplicateIdException    if the column has the same id as a
     *                                     column already in this table-view
     */
    public function insertColumnBefore(
        SwatTableViewColumn $column,
        SwatTableViewColumn $reference_column,
    ) {
        $this->insertColumn($column, $reference_column, false);
    }

    /**
     * Inserts a column after an existing column in this table-view.
     *
     * @param SwatTableViewColumn $column           the column to insert
     * @param SwatTableViewColumn $reference_column the column after which the
     *                                              column will be inserted
     *
     * @throws SwatWidgetNotFoundException if the reference column does not
     *                                     exist in this table-view
     * @throws SwatDuplicateIdException    if the column has the same id as a
     *                                     column already in this table-view
     */
    public function insertColumnAfter(
        SwatTableViewColumn $column,
        SwatTableViewColumn $reference_column,
    ) {
        $this->insertColumn($column, $reference_column, true);
    }

    /**
     * Returns true if a column with the given id exists within this
     * table-view.
     *
     * @param string $id the unique identifier of the column within this
     *                   table view to check the existance of
     *
     * @return bool true if the column exists in this table view and
     *              false if it does not
     */
    public function hasColumn($id)
    {
        return array_key_exists($id, $this->columns_by_id);
    }

    /**
     * Gets a column in this table-view by the column's id.
     *
     * @param string $id the id of the column to get
     *
     * @return SwatTableViewColumn the requested column
     *
     * @throws SwatWidgetNotFoundException if no column with the specified id
     *                                     exists in this table-view
     */
    public function getColumn($id)
    {
        if (!array_key_exists($id, $this->columns_by_id)) {
            throw new SwatWidgetNotFoundException(
                "Column with an id of '{$id}' not found.",
            );
        }

        return $this->columns_by_id[$id];
    }

    /**
     * Gets all columns of this table-view as an array.
     *
     * @return array the columns of this table-view
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Gets the number of columns in this table-view.
     *
     * @return int the number of columns of this table-view
     */
    public function getColumnCount()
    {
        return count($this->columns);
    }

    /**
     * Gets all visible columns of this table-view as an array.
     *
     * @return array the visible columns of this table-view
     */
    public function getVisibleColumns()
    {
        $columns = [];
        foreach ($this->columns as $column) {
            if ($column->visible) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    /**
     * Gets the number of visible columns in this table-view.
     *
     * @return int the number of visible columns of this table-view
     */
    public function getVisibleColumnCount()
    {
        return count($this->getVisibleColumns());
    }

    /**
     * Sets a default column to use for ordering the data of this table-view.
     *
     * @param SwatTableViewOrderableColumn the column in this view to use
     *                                      for default ordering
     * @param int $direction the default direction of the ordered column
     *
     * @throws SwatException
     *
     * @see SwatTableView::$default_orderby_column
     */
    public function setDefaultOrderbyColumn(
        SwatTableViewOrderableColumn $column,
        $direction = SwatTableViewOrderableColumn::ORDER_BY_DIR_DESCENDING,
    ) {
        if ($column->view !== $this) {
            throw new SwatException(
                'Can only set the default orderby on '
                    . 'orderable columns in this view.',
            );
        }

        // this method sets properties on the table-view
        $column->setDirection($direction);
    }

    /**
     * Ensures a column added to this table-view is valid for this table-view.
     *
     * @param SwatTableViewColumn $column the column to check
     *
     * @throws SwatDuplicateIdException if the column has the same id as a
     *                                  column already in this table-view
     */
    protected function validateColumn(SwatTableViewColumn $column)
    {
        // note: This works because the id property is set before children are
        // added to parents in SwatUI.
        if ($column->id !== null) {
            if (array_key_exists($column->id, $this->columns_by_id)) {
                throw new SwatDuplicateIdException(
                    "A column with the id '{$column->id}' already exists "
                        . 'in this table view.',
                    0,
                    $column->id,
                );
            }
        }
    }

    /**
     * Helper method to insert columns into this table-view.
     *
     * @param SwatTableViewColumn $column           the column to insert
     * @param SwatTableViewColumn $reference_column optional. An existing column
     *                                              within this table-view to
     *                                              which the inserted column
     *                                              is relatively positioned.
     *                                              If not specified, the
     *                                              column is inserted at the
     *                                              beginning or the end of
     *                                              this table-view's list of
     *                                              columns.
     * @param bool                $after            optional. If true and a reference column is
     *                                              specified, the column is inserted immediately
     *                                              before the reference column. If true and no
     *                                              reference column is specified, the column is
     *                                              inserted at the beginning of the column list. If
     *                                              false and a reference column is specified, the
     *                                              column is inserted immediately after the reference
     *                                              column. If false and no reference column is
     *                                              specified, the column is inserted at the end of
     *                                              the column list. Defaults to false.
     *
     * @throws SwatWidgetNotFoundException if the reference column does not
     *                                     exist in this table-view
     * @throws SwatDuplicateIdException    if the column to be inserted has the
     *                                     same id as a column already in this
     *                                     table-view
     *
     * @see SwatTableView::appendColumn()
     * @see SwatTableView::insertColumnBefore()
     * @see SwatTableView::insertColumnAfter()
     */
    protected function insertColumn(
        SwatTableViewColumn $column,
        ?SwatTableViewColumn $reference_column = null,
        $after = true,
    ) {
        $this->validateColumn($column);

        if ($reference_column !== null) {
            $key = array_search($reference_column, $this->columns, true);

            if ($key === false) {
                throw new SwatWidgetNotFoundException(
                    'The reference column '
                        . 'could not be found in this table-view.',
                );
            }

            if ($after) {
                // insert after reference column
                array_splice($this->columns, $key, 1, [
                    $reference_column,
                    $column,
                ]);
            } else {
                // insert before reference column
                array_splice($this->columns, $key, 1, [
                    $column,
                    $reference_column,
                ]);
            }
        } else {
            if ($after) {
                // append to array
                $this->columns[] = $column;
            } else {
                // prepend to array
                array_unshift($this->columns, $column);
            }
        }

        if ($column->id !== null) {
            $this->columns_by_id[$column->id] = $column;
        }

        $column->view = $this; // deprecated reference
        $column->parent = $this;
    }

    // spanning column methods

    /**
     * Appends a spanning column object to this table-view.
     *
     * @param SwatTableViewSpanningColumn $column the table-view spanning column to use for this
     *                                            table-view
     *
     * @see SwatTableViewSpanningColumn
     */
    public function appendSpanningColumn(SwatTableViewSpanningColumn $column)
    {
        $this->spanning_columns[] = $column;
        $column->view = $this;
        $column->parent = $this;
    }

    /**
     * Returns true if a spanning column with the given id exists within this table-view.
     *
     * @param mixed $id
     *
     * @return SwatTableViewSpanningColumn the requested spanning column
     */
    public function hasSpanningColumn($id)
    {
        return array_key_exists($id, $this->spanning_columns_by_id);
    }

    /**
     * Gets a spanning column in this table-view by the spanning column's id.
     *
     * @param string $id the id of the row to get
     *
     * @return SwatTableViewSpanningColumn the requested spanning column
     *
     * @throws SwatWidgetNotFoundException if no spanning column with the
     *                                     specified id exists in this
     *                                     table-view
     */
    public function getSpanningColumn($id)
    {
        if (!array_key_exists($id, $this->spanning_columns_by_id)) {
            throw new SwatWidgetNotFoundException(
                "Spanning column with an id of '{$id}' not found.",
            );
        }

        return $this->spanning_columns_by_id[$id];
    }

    /**
     * Gets all spanning columns of this table-view as an array.
     *
     * @return array the spanning columns of this table-view
     */
    public function getSpanningColumns()
    {
        return $this->spanning_columns;
    }

    /**
     * Gets all visible spanning columns of this table-view as an array.
     *
     * @return array the visible spanning columns of this table-view
     */
    public function getVisibleSpanningColumns()
    {
        $columns = [];

        foreach ($this->spanning_columns as $column) {
            if ($column->visible) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    // grouping methods

    /**
     * Appends a grouping object to this table-view.
     *
     * A grouping object affects how the data in the table model is displayed
     * in this table-view. With a grouping, rows are split into groups with
     * special group headers above each group.
     *
     * Multiple groupings may be added to table-views.
     *
     * @param SwatTableViewGroup $group the table-view grouping to append to
     *                                  this table-view
     *
     * @see SwatTableViewGroup
     *
     * @throws SwatDuplicateIdException if the group has the same id as a
     *                                  group already in this table-view
     */
    public function appendGroup(SwatTableViewGroup $group)
    {
        $this->validateGroup($group);

        $this->groups[] = $group;

        if ($group->id !== null) {
            $this->groups_by_id[$group->id] = $group;
        }

        $group->view = $this;
        $group->parent = $this;
    }

    /**
     * Returns true if a group with the given id exists within this table-view.
     *
     * @param string $id the unique identifier of the group within this table-
     *                   view to check the existance of
     *
     * @return bool true if the group exists in this table-view and false if
     *              it does not
     */
    public function hasGroup($id)
    {
        return array_key_exists($id, $this->groups_by_id);
    }

    /**
     * Gets a group in this table-view by the group's id.
     *
     * @param string $id the id of the group to get
     *
     * @return SwatTableViewGroup the requested group
     *
     * @throws SwatWidgetNotFoundException if no group with the specified id
     *                                     exists in this table-view
     */
    public function getGroup($id)
    {
        if (!array_key_exists($id, $this->groups_by_id)) {
            throw new SwatWidgetNotFoundException(
                "Group with an id of '{$id}' not found.",
            );
        }

        return $this->groups_by_id[$id];
    }

    /**
     * Gets all groups of this table-view as an array.
     *
     * @return array the the groups of this table-view
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Ensures a group added to this table-view is valid for this table-view.
     *
     * @param SwatTableViewGroup $group the group to check
     *
     * @throws SwatDuplicateIdException if the group has the same id as a
     *                                  group already in this table-view
     */
    protected function validateGroup(SwatTableViewGroup $group)
    {
        // note: This works because the id property is set before children are
        // added to parents in SwatUI.
        if ($group->id !== null) {
            if (array_key_exists($group->id, $this->groups_by_id)) {
                throw new SwatDuplicateIdException(
                    "A group with the id '{$group->id}' already exists "
                        . 'in this table view.',
                    0,
                    $group->id,
                );
            }
        }
    }

    // extra row methods

    /**
     * Appends a single row to this table-view.
     *
     * Rows appended to table-views are displayed after all the data from the
     * table-view model is displayed.
     *
     * @param SwatTableViewRow $row the row to append
     *
     * @throws SwatDuplicateIdException if the row has the same id as a row
     *                                  already in this table-view
     */
    public function appendRow(SwatTableViewRow $row)
    {
        $this->insertRow($row);
    }

    /**
     * Inserts a row before an existing row in this table-view.
     *
     * @param SwatTableViewRow $row           the row to insert
     * @param SwatTableViewRow $reference_row the row before which the row will
     *                                        be inserted
     *
     * @throws SwatWidgetNotFoundException if the reference row does not exist
     *                                     in this table-view
     * @throws SwatDuplicateIdException    if the row has the same id as a row
     *                                     already in this table-view
     * @throws SwatException               if the row is an input row and this table-view
     *                                     already contains an input-row
     */
    public function insertRowBefore(
        SwatTableViewRow $row,
        SwatTableViewRow $reference_row,
    ) {
        $this->insertRow($row, $reference_row, false);
    }

    /**
     * Inserts a row after an existing row in this table-view.
     *
     * @param SwatTableViewRow $row           the row to insert
     * @param SwatTableViewRow $reference_row the row after which the row will
     *                                        be inserted
     *
     * @throws SwatWidgetNotFoundException if the reference row does not exist
     *                                     in this table-view
     * @throws SwatDuplicateIdException    if the row has the same id as a row
     *                                     already in this table-view
     * @throws SwatException               if the row is an input row and this table-view
     *                                     already contains an input-row
     */
    public function insertRowAfter(
        SwatTableViewRow $row,
        SwatTableViewRow $reference_row,
    ) {
        $this->insertRow($row, $reference_row, true);
    }

    /**
     * Returns true if a row with the given id exists within this table-view.
     *
     * @param string $id the unique identifier of the row within this
     *                   table-view to check the existance of
     *
     * @return bool true if the row exists in this table-view and false if
     *              it does not
     */
    public function hasRow($id)
    {
        return array_key_exists($id, $this->rows_by_id);
    }

    /**
     * Gets a row in this table-view by the row's id.
     *
     * @param string $id the id of the row to get
     *
     * @return SwatTableViewRow the requested row
     *
     * @throws SwatWidgetNotFoundException if no row with the specified id
     *                                     exists in this table-view
     */
    public function getRow($id)
    {
        if (!array_key_exists($id, $this->rows_by_id)) {
            throw new SwatWidgetNotFoundException(
                "Row with an id of '{$id}' not found.",
            );
        }

        return $this->rows_by_id[$id];
    }

    /**
     * Gets all the extra rows of the specified class from this table-view.
     *
     * @param string $class_name the class name to filter by
     *
     * @return array all the extra rows of the specified class
     */
    public function getRowsByClass($class_name)
    {
        $rows = [];
        foreach ($this->extra_rows as $row) {
            if ($row instanceof $class_name) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Gets the first extra row of the specified class from this table-view.
     *
     * Unlike the {@link SwatUIParent::getFirstDescendant()} method, this
     * method only checks this table-view and does not check the child objects
     * of this table-view.
     *
     * @param string $class_name the class name to filter by
     *
     * @return SwatTableViewRow the first extra row of the specified class or
     *                          null if no such row object exists in this
     *                          table-view
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstRowByClass($class_name)
    {
        $my_row = null;
        foreach ($this->extra_rows as $row) {
            if ($row instanceof $class_name) {
                $my_row = $row;
                break;
            }
        }

        return $my_row;
    }

    /**
     * Ensures a row added to this table-view is valid for this table-view.
     *
     * @param SwatTableViewRow $row the row to check
     *
     * @throws SwatDuplicateIdException if the row has the same id as a row
     *                                  already in this table-view
     * @throws SwatException            if the row is an input row and this table-view
     *                                  already contains an input-row
     */
    protected function validateRow(SwatTableViewRow $row)
    {
        if ($row instanceof SwatTableViewInputRow) {
            if ($this->has_input_row) {
                throw new SwatException(
                    'Only one input row may be added to a table-view.',
                );
            }
            $this->has_input_row = true;
        }

        if ($row->id !== null) {
            if (array_key_exists($row->id, $this->rows_by_id)) {
                throw new SwatDuplicateIdException(
                    "A row with the id '{$row->id}' already exists "
                        . 'in this table-view.',
                    0,
                    $row->id,
                );
            }
        }
    }

    /**
     * Helper method to insert rows into this table-view.
     *
     * @param SwatTableViewRow $row           the row to insert
     * @param SwatTableViewRow $reference_row optional. An existing row within
     *                                        this table-view to which the
     *                                        inserted row is relatively
     *                                        positioned. If not specified,
     *                                        the row is inserted at the
     *                                        beginning or the end of this
     *                                        table-view's list of extra rows.
     * @param bool             $after         optional. If true and a reference row is specified,
     *                                        the row is inserted immediately before the
     *                                        reference row. If true and no reference row is
     *                                        specified, the row is inserted at the beginning
     *                                        of the extra row list. If false and a reference
     *                                        row is specified, the row is inserted immediately
     *                                        after the reference row. If false and no
     *                                        reference row is specified, the row is inserted
     *                                        at the end of the extra row list. Defaults to
     *                                        false.
     *
     * @throws SwatWidgetNotFoundException if the reference row does not exist
     *                                     in this table-view
     * @throws SwatDuplicateIdException    if the row to be inserted has the same
     *                                     id as a row already in this table-view
     *
     * @see SwatTableView::appendRow()
     * @see SwatTableView::insertRowBefore()
     * @see SwatTableView::insertRowAfter()
     */
    protected function insertRow(
        SwatTableViewRow $row,
        ?SwatTableViewRow $reference_row = null,
        $after = true,
    ) {
        $this->validateRow($row);

        if ($reference_row !== null) {
            $key = array_search($reference_row, $this->extra_rows, true);

            if ($key === false) {
                throw new SwatWidgetNotFoundException(
                    'The reference row '
                        . 'could not be found in this table-view.',
                );
            }

            if ($after) {
                // insert after reference row
                array_splice($this->extra_rows, $key, 1, [
                    $reference_row,
                    $row,
                ]);
            } else {
                // insert before reference row
                array_splice($this->extra_rows, $key, 1, [
                    $row,
                    $reference_row,
                ]);
            }
        } else {
            if ($after) {
                // append to array
                $this->extra_rows[] = $row;
            } else {
                // prepend to array
                array_unshift($this->extra_rows, $row);
            }
        }

        if ($row->id !== null) {
            $this->rows_by_id[$row->id] = $row;
        }

        $row->view = $this; // deprecated reference
        $row->parent = $this;
    }
}
