<?php

/**
 * A table-view row that allows the user to enter data
 *
 * This row object allows the user to enter data in a manner similar to how the
 * data is displayed. This makes data entry easier as the user can see examples
 * of the type of data they are entering above the fields in which they enter
 * data.
 *
 * Additionally, this row object makes data entry faster by allowing the user
 * to enter an arbitrary number of rows of data at the same time.
 *
 * TODO: work out ids. id is required
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTableViewInputRow extends SwatTableViewRow
{
    // {{{ public properties

    /**
     * The text to display in the link to enter a new row
     *
     * Defaults to 'enter another'.
     *
     * @var string
     */
    public $enter_text = '';

    /**
     * The number of rows to display
     *
     * This row can display an arbitrary number of copies of itself. This value
     * specifies how many copies to display by default. This number is set to
     * the number of entered rows in {@link SwatTableViewInputRow::process()}.
     *
     * @var integer
     */
    public $number = 1;

    /**
     * A unique identifier for this row
     *
     * @var string
     */
    public $id = '';

    /**
     * Whether or not to show sub-widget messages in displayed rows
     *
     * Messages are shown by default.
     *
     * @var boolean
     */
    public $show_row_messages = true;

    // {{{ private properties

    /**
     * The tool-link to create another row
     *
     * @var SwatToolLink
     */
    private $enter_another_link = null;

    /**
     * Whether or no the embedded widgets of this row have been created
     *
     * @var boolean
     */
    private $widgets_created = false;

    /**
     * An array of input cells for this row indexed by column id
     *
     * The array is of the form:
     * <code>
     * array('column_id' => $input_cell);
     * </code>
     *
     * @var array
     */
    private $input_cells = [];

    /**
     * An array of replicator ids for the individual rows displayed and entered
     * through this input row
     *
     * Replicator ids are integers by convention for input rows.
     *
     * @var array
     * @see SwatTableViewInputRow::getReplicators()
     */
    private $replicators = [];

    // {{{ public function __construct()

    /**
     * Creates a new input row
     */
    public function __construct()
    {
        parent::__construct();
        $this->enter_text = Swat::_('enter&nbsp;another');

        $yui = new SwatYUI(['animation']);
        $this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());
        $this->addJavaScript(
            'packages/swat/javascript/swat-table-view-input-row.js',
        );
    }

    // {{{ public function init()

    /**
     * Initializes this input row
     *
     * This initializes each input cell in this row.
     *
     * @see SwatTableViewRow::init()
     */
    public function init()
    {
        parent::init();

        $this->createEmbeddedWidgets();
        $this->enter_another_link->title = $this->enter_text;
        $this->enter_another_link->link = sprintf(
            'javascript:%s_obj.addRow();',
            $this->getId(),
        );

        $this->enter_another_link->init();

        // init input cells
        foreach ($this->input_cells as $cell) {
            $cell->init();
        }

        /*
         * Initialize replicators
         *
         * Don't use getHiddenField() here because the serialized field is not
         * updated by the controlling JavaScript and will not contain added
         * replicator ids.
         */
        $data = $this->getForm()->getFormData();
        $replicator_field = $data[$this->getId() . '_replicators'] ?? null;

        if ($replicator_field === null || $replicator_field == '') {
            // use generated ids
            for ($i = 0; $i < $this->number; $i++) {
                $this->replicators[] = $i;
            }
        } else {
            // retrieve ids from form
            $this->replicators = explode(',', $replicator_field);
        }
    }

    // {{{ public function process()

    /**
     * Processes this input row
     *
     * This gets the replicator ids of rows the user entered as well as
     * processing all cloned widgets in input cells that the user submitted.
     */
    public function process()
    {
        parent::process();

        // process input cells
        foreach ($this->replicators as $replicator_id) {
            foreach ($this->input_cells as $cell) {
                $cell->process($replicator_id);
            }
        }
    }

    // {{{ public function addInputCell()

    /**
     * Adds an input cell to this row from a column
     *
     * This method is called in {@link SwatTableViewColumn::init()} to move
     * input cells from the column to this row object. Attaching input cells
     * directly to their row makes row initilization and processing easier.
     *
     * This method may also be called manually to add an input cell directly
     * to an input row based on a table-view column.
     *
     * @param SwatInputCell $cell the input cell to add to this row.
     * @param string $column_id the unique identifier of the table column.
     *
     */
    public function addInputCell(SwatInputCell $cell, $column_id)
    {
        $this->input_cells[$column_id] = $cell;
    }

    // {{{ public function display()

    /**
     * Displays this row
     *
     * Uses widget cloning inside {@link SwatInputCell} to display rows and
     * also displays the 'enter-another-row' button. The number of rows
     * displayed is set either through {SwatTableViewInputRow::$number} or
     * by the number of rows the user submitted. When a user submits a
     * different number of rows than {SwatTableViewInputRow::$number} it
     * the user submitted number takes precedence.
     */
    public function display()
    {
        if (!$this->isVisible()) {
            return;
        }

        parent::display();

        if (count($this->replicators) < $this->number) {
            $diff = $this->number - count($this->replicators);
            $next_replicator =
                count($this->replicators) === 0
                    ? 0
                    : end($this->replicators) + 1;

            for ($i = $next_replicator; $i < $diff + $next_replicator; $i++) {
                $this->replicators[] = $i;
            }
        }

        // add replicator ids to the form as a hidden field
        $this->getForm()->addHiddenField(
            $this->getId() . '_replicators',
            implode(',', $this->replicators),
        );

        $this->displayInputRows();
        $this->displayEnterAnotherRow();
    }

    // {{{ public function addReplication()

    /**
     * Add a replicator id
     *
     * @param integer $id the id of the replicator to add.
     * @return integer index of the replicator.
     */
    public function addReplication($id)
    {
        $this->replicators[] = $id;
        return end($this->replicators);
    }

    // {{{ public function getReplicators()

    /**
     * Gets the replicator ids of this input row
     *
     * This is useful if you want to iterate through the results of user
     * submitted data. For example:
     *
     * <code>
     * foreach ($row->getReplicators() as $replicator_id)
     *     $my_widget = $row->getWidget('my_column', $replicator_id);
     * </code>
     *
     * @return array the replicator ids of this input row.
     */
    public function getReplicators()
    {
        return $this->replicators;
    }

    // {{{ public function getWidget()

    /**
     * Gets a particular widget in this row
     *
     * This method is used to get or set properties of specific cloned widgets
     * within this input row. The most common case is when you want to
     * iterate through the user submitted data in this input row.
     *
     * @param string $column_id the unique identifier of the table-view column
     *                           the widget resides in.
     * @param integer $row_identifier the numeric row identifier of the widget.
     * @param string $widget_id the unique identifier of the widget. If no id
     *                           is specified, the root widget of the column's
     *                           cell is returned for the given row.
     *
     * @return SwatWidget
     *
     * @see SwatInputCell::getWidget()
     *
     * @throws SwatException
     */
    public function getWidget($column_id, $row_identifier, $widget_id = null)
    {
        if (isset($this->input_cells[$column_id])) {
            return $this->input_cells[$column_id]->getWidget(
                $row_identifier,
                $widget_id,
            );
        }

        throw new SwatException(
            'No input cell for this row exists for the ' .
                'given column identifier.',
        );
    }

    // {{{ public function getPrototypeWidget()

    /**
     * Gets the prototype widget for a column attached to this row
     *
     * Note: The UI tree must be inited before this method works correctly.
     *       This is because the column identifiers are not finalized until
     *       init() has run and this method uses colum identifiers for lookup.
     *
     * This method is useful for setting properties on the prototype widget;
     * however, the {@link SwatTableViewColumn::getInputCell()} method is even
     * more useful because it may be safely used before init() is called on the
     * UI tree. You can then call {@link SwatInputCell::getPrototypeWidget()}
     * on the returned input cell.
     *
     * @param string $column_id the unique identifier of the column to get the
     *                           prototype widget from.
     *
     * @return SwatWidget the prototype widget from the given column.
     *
     * @see SwatTableViewColumn::getInputCell()
     * @throws SwatException
     */
    public function getPrototypeWidget($column_id)
    {
        if (isset($this->input_cells[$column_id])) {
            return $this->input_cells[$column_id]->getPrototypeWidget();
        }

        throw new SwatException(
            'The specified column does not have an input ' .
                'cell bound to this row or the column does not exist.',
        );
    }

    // {{{ public function removeReplicatedRow()

    /**
     * Removes a row from this input row by its replicator id
     *
     * This also unsets any cloned widgets from this row's input cells.
     *
     * @param integer $replicator_id the replicator id of the row to remove.
     */
    public function removeReplicatedRow($replicator_id)
    {
        $this->replicators = array_diff($this->replicators, [$replicator_id]);

        foreach ($this->input_cells as $cell) {
            $cell->unsetWidget($replicator_id);
        }
    }

    // {{{ public function getVisibleByCount()

    /**
     * Gets whether or not to show this row based on a count of rows
     *
     * Input rows are always shown even if there are no entries in the table-
     * view's model.
     *
     * @param integer $count the number of entries in this row's view's model.
     *
     * @return boolean $this->visible. Input rows are always shown if they are
     *                  visible even if there are no entries in the table-view's
     *                  model.
     */
    public function getVisibleByCount($count)
    {
        return $this->visible;
    }

    // {{{ public function rowHasMessage()

    /**
     * Gets whether or not a given replicated row has messages
     *
     * @param integer $replicator_id the replicator id of the row to check for
     *                                messages.
     *
     * @return boolean true if the replicated row has one or more messages and
     *                  false if it does not.
     */
    public function rowHasMessage($replicator_id)
    {
        $row_has_message = false;

        foreach ($this->input_cells as $cell) {
            if ($cell->getWidget($replicator_id)->hasMessage()) {
                $row_has_message = true;
                break;
            }
        }

        return $row_has_message;
    }

    // {{{ public function getMessages()

    /**
     * Gathers all messages from this table-view row
     *
     * @return array an array of {@link SwatMessage} objects.
     */
    public function getMessages()
    {
        $messages = [];

        foreach ($this->replicators as $replicator_id) {
            foreach ($this->input_cells as $cell) {
                $messages = array_merge(
                    $messages,
                    $cell->getWidget($replicator_id)->getMessages(),
                );
            }
        }

        return $messages;
    }

    // {{{ public function hasMessage()

    /**
     * Gets whether or not the widgets in this row have any messages
     *
     * @return boolean true if this table-view row has one or more messages
     *                 and false if it does not.
     */
    public function hasMessage()
    {
        $has_message = false;

        foreach ($this->replicators as $replicator_id) {
            foreach ($this->input_cells as $cell) {
                if ($cell->getWidget($replicator_id)->hasMessage()) {
                    $has_message = true;
                    break 2;
                }
            }
        }

        return $has_message;
    }

    // {{{ public function getInlineJavaScript()

    /**
     * Creates a JavaScript object to control the client behaviour of this
     * input row
     *
     * @return string the inline JavaScript required by this row.
     */
    public function getInlineJavaScript()
    {
        /*
         * Encode row string
         *
         * Mimize entities so that we do not have to specify a DTD when parsing
         * the final XML string. If we specify a DTD, Internet Explorer takes a
         * long time to strictly parse everything. If we do not specify a DTD
         * and try to parse the final XML string with XHTML entities in it we
         * get an undefined entity error.
         */
        $row_string = $this->getRowString();
        // these entities need to be double escaped
        $row_string = str_replace('&amp;', '&amp;amp;', $row_string);
        $row_string = str_replace('&quot;', '&amp;quot;', $row_string);
        $row_string = str_replace('&lt;', '&amp;lt;', $row_string);
        $row_string = SwatString::minimizeEntities($row_string);
        $row_string = str_replace("'", "\'", $row_string);

        // encode newlines for JavaScript string
        $row_string = str_replace("\n", '\n', $row_string);

        return sprintf(
            "var %s_obj = new SwatTableViewInputRow('%s', '%s');",
            $this->getId(),
            $this->getId(),
            trim($row_string),
        );
    }

    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects needed by this input row
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                               this input row.
     *
     * @see SwatUIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        $this->createEmbeddedWidgets();
        $set->addEntrySet($this->enter_another_link->getHtmlHeadEntrySet());

        return $set;
    }

    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the SwatHtmlHeadEntry objects that may be needed by this input row
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
     *                               needed by this input row.
     *
     * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        $this->createEmbeddedWidgets();
        $set->addEntrySet(
            $this->enter_another_link->getAvailableHtmlHeadEntrySet(),
        );

        return $set;
    }

    // {{{ private function getId()

    private function getId()
    {
        $id = $this->id;

        if ($this->parent->id !== null) {
            $id = $this->parent->id . '_' . $id;
        }

        return $id;
    }

    // {{{ private function displayInputRows()

    /**
     * Displays the actual XHTML input rows for this input row
     *
     * Displays a row for each replicator id in this input row. Each row is
     * displayed using cloned widgets inside {@link SwatInputCell} objects.
     *
     * @see SwatTableViewInputRow::display()
     */
    private function displayInputRows()
    {
        $columns = $this->parent->getVisibleColumns();

        foreach ($this->replicators as $replicator_id) {
            $messages = [];

            $row_has_messages = false;
            foreach ($this->input_cells as $cell) {
                if ($cell->getWidget($replicator_id)->hasMessage()) {
                    $row_has_messages = true;
                    break;
                }
            }

            $tr_tag = new SwatHtmlTag('tr');
            $tr_tag->class = 'swat-table-view-input-row';
            $tr_tag->id = $this->getId() . '_row_' . $replicator_id;

            if ($row_has_messages && $this->show_row_messages) {
                $tr_tag->class .= ' swat-error';
            }

            $tr_tag->open();

            foreach ($columns as $column) {
                // use the same style as table-view column
                $td_attributes = $column->getTdAttributes();
                $td_tag = new SwatHtmlTag('td', $td_attributes);

                if (isset($this->input_cells[$column->id])) {
                    $widget = $this->input_cells[$column->id]->getWidget(
                        $replicator_id,
                    );

                    if (
                        $this->show_row_messages &&
                        count($widget->getMessages()) > 0
                    ) {
                        $messages = array_merge(
                            $messages,
                            $widget->getMessages(),
                        );

                        $td_tag->class .= ' swat-error';
                    }
                }

                $td_tag->open();

                if (isset($this->input_cells[$column->id])) {
                    $this->input_cells[$column->id]->display($replicator_id);
                } else {
                    echo '&nbsp;';
                }

                $td_tag->close();
            }
            $tr_tag->close();

            if ($this->show_row_messages && count($messages) > 0) {
                $tr_tag = new SwatHtmlTag('tr');
                $tr_tag->class = 'swat-table-view-input-row-messages';
                $tr_tag->open();

                $td_tag = new SwatHtmlTag('td');
                $td_tag->colspan = count($columns);
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

                    $li_tag->class = $message->getCSSClassString();
                    $li_tag->display();
                }

                $ul_tag->close();

                $td_tag->close();
                $tr_tag->close();
            }
        }
    }

    // {{{ private function createEmbeddedWidgets()

    /**
     * Instantiates the tool-link for this input row
     */
    private function createEmbeddedWidgets()
    {
        if (!$this->widgets_created) {
            $this->enter_another_link = new SwatToolLink();
            $this->enter_another_link->parent = $this;
            $this->enter_another_link->stock_id = 'add';
            $this->enter_another_link->classes[] =
                'swat-table-view-input-row-add';

            $this->widgets_created = true;
        }
    }

    // {{{ private function displayEnterAnotherRow()

    /**
     * Displays the enter-another-row row
     */
    private function displayEnterAnotherRow()
    {
        $columns = $this->parent->getVisibleColumns();

        $this->createEmbeddedWidgets();

        /*
         * Get column position of enter-a-new-row text. The text is displayed
         * underneath the first input cell that is not blank. If all cells are
         * blank, text is displayed underneath the first cell.
         */
        $position = 0;
        $colspan = 0;
        foreach ($columns as $column) {
            if (
                array_key_exists($column->id, $this->input_cells) &&
                !(
                    $this->input_cells[$column->id] instanceof
                    SwatRemoveInputCell
                )
            ) {
                $position = $colspan;
                break;
            }
            $colspan += $column->getXhtmlColspan();
        }

        $close_length = $this->parent->getXhtmlColspan() - $position - 1;

        $tr_tag = new SwatHtmlTag('tr');
        $tr_tag->id = $this->getId() . '_enter_row';
        $tr_tag->class = 'swat-table-view-input-row-add-row';
        $tr_tag->open();

        if ($position > 0) {
            $td = new SwatHtmlTag('td');
            $td->colspan = $position;
            $td->open();
            echo '&nbsp;';
            $td->close();
        }

        // use the same style as table-view column
        $td = new SwatHtmlTag('td', $column->getTdAttributes());
        $td->open();
        $this->enter_another_link->display();
        $td->close();

        if ($close_length > 0) {
            $td = new SwatHtmlTag('td');
            $td->colspan = $close_length;
            $td->open();
            echo '&nbsp;';
            $td->close();
        }

        $tr_tag->close();
    }

    // {{{ private function getRowString()

    /**
     * Gets this input row as an XHTML table row with the row identifier as a
     * placeholder '%s'
     *
     * Returning the row identifier as a placeholder means we can use this
     * function to display multiple copies of this row just by substituting
     * a new identifier.
     *
     * @return string this input row as an XHTML table row with the row
     *                 identifier as a placeholder '%s'.
     */
    private function getRowString()
    {
        $columns = $this->parent->getVisibleColumns();

        ob_start();

        // properties of the dynamic tr's are set in javascript
        $tr_tag = new SwatHtmlTag('tr');
        $tr_tag->open();

        foreach ($columns as $column) {
            $td_attributes = $column->getTdAttributes();

            $td_tag = new SwatHtmlTag('td', $td_attributes);
            $td_tag->open();

            $suffix = '_' . $this->getId() . '_%s';

            if (isset($this->input_cells[$column->id])) {
                $widget = $this->input_cells[$column->id]->getPrototypeWidget();
                $widget = $widget->copy($suffix);
                $widget->parent = $this->getForm(); // so display will work.
                $widget->display();
                unset($widget);
            } else {
                echo '&nbsp;';
            }

            $td_tag->close();
        }
        $tr_tag->close();

        return ob_get_clean();
    }

    // {{{ private function getForm()

    /**
     * Gets the form this row's view is contained in
     *
     * @return SwatForm the form this row's view is contained in.
     *
     * @throws SwatException
     */
    private function getForm()
    {
        $form = $this->getFirstAncestor('SwatForm');

        if ($form === null) {
            throw new SwatException(
                'SwatTableView must be inside a SwatForm ' .
                    'for SwatTableViewInputRow to work.',
            );
        }

        return $form;
    }

}
