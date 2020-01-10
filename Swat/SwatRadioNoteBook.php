<?php

/**
 * Notebook widget for containing {@link SwatNoteBookPage} pages
 *
 * This notebook is controlled using radio buttons and is a
 * {@link SwatInputControl}.
 *
 * @package   Swat
 * @copyright 2012-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBookPage
 */
class SwatRadioNoteBook extends SwatInputControl implements SwatUIParent
{
    // {{{ public properties

    /**
     * Selected page
     *
     * The id of the {@link SwatNoteBookPage} to show as selected. By
     * default, the first page is selected.
     *
     * @var string
     */
    public $selected_page;

    // }}}
    // {{{ protected properties

    /**
     * Note book child objects initally added to this widget
     *
     * @var array
     */
    protected $children = array();

    /**
     * Pages affixed to this widget
     *
     * @var array
     */
    protected $pages = array();

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new notebook
     *
     * @param string $id a non-visable unique id for this widget.
     */
    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->requires_id = true;
    }

    // }}}
    // {{{ public function addChild()

    /**
     * Adds a {@link SwatNoteBookChild} to this notebook
     *
     * This method fulfills the {@link SwatUIParent} interface. It is used
     * by {@link SwatUI} when building a widget tree and should not need to be
     * called elsewhere. To add a notebook page to a notebook, use
     * {@link SwatRadioNoteBook::addPage()}.
     *
     * Note: This is the only way to add a SwatNoteBookChild that is not a
     *       SwatNoteBookPage.
     *
     * @param SwatNoteBookChild $child the notebook child to add.
     *
     * @throws SwatInvalidClassException if the given object is not an instance
     *                                    of SwatNoteBookChild.
     *
     * @see SwatUIParent
     */
    public function addChild(SwatObject $child)
    {
        if ($child instanceof SwatNoteBookChild) {
            $this->children[] = $child;
            $child->parent = $this;
        } else {
            throw new SwatInvalidClassException(
                'Only SwatNoteBookChild objects may be nested within a ' .
                    'SwatRadioNoteBook object.',
                0,
                $child
            );
        }
    }

    // }}}
    // {{{ public function addPage()

    /**
     * Adds a {@link SwatNoteBookPage} to this notebook
     *
     * @param SwatNoteBookPage $page the notebook page to add.
     */
    public function addPage(SwatNoteBookPage $page)
    {
        $this->pages[] = $page;
        $page->parent = $this;
    }

    // }}}
    // {{{ public function getPage()

    /**
     * Gets a page in this notebook
     *
     * Retrieves a page from the list of pages in this notebook based on
     * the unique identifier of the page.
     *
     * @param string $id the unique id of the page to look for.
     *
     * @return SwatNoteBookPage the found page or null if not found.
     */
    public function getPage($id)
    {
        $found_page = null;

        foreach ($this->pages as $page) {
            if ($page->id == $id) {
                $found_page = $page;
                break;
            }
        }

        return $found_page;
    }

    // }}}
    // {{{ public function init()

    /**
     * Initializes this notebook
     */
    public function init()
    {
        parent::init();

        foreach ($this->children as $child) {
            $child->init();
            foreach ($child->getPages() as $page) {
                $this->addPage($page);
            }
        }

        foreach ($this->pages as $page) {
            $page->init();
        }
    }

    // }}}
    // {{{ public function process()

    /**
     * Processes this notebook
     */
    public function process()
    {
        parent::process();

        if (!$this->processValue()) {
            return;
        }

        if (
            $this->required &&
            $this->isSensitive() &&
            $this->selected_page == ''
        ) {
            $this->addMessage($this->getValidationMessage('required'));
        }

        foreach ($this->pages as $page) {
            if ($page->id == $this->selected_page) {
                $page->process();
            }
        }
    }

    // }}}
    // {{{ public function display()

    /**
     * Displays this notebook
     */
    public function display()
    {
        if (!$this->visible) {
            return;
        }

        parent::display();

        // add a hidden field so we can check if this list was submitted on
        // the process step
        $this->getForm()->addHiddenField($this->id . '_submitted', 1);

        if (count($this->pages) === 1) {
            $this->displaySinglePage(reset($this->pages));
        } else {
            $table = new SwatHtmlTag('table');
            $table->id = $this->id;
            $table->class = 'swat-radio-note-book';
            $table->open();

            echo '<tbody>';

            $count = 0;
            foreach ($this->pages as $page) {
                if (!$page->visible) {
                    continue;
                }

                $count++;

                $this->displayPage($page, $count);
            }

            echo '</tbody>';

            $table->close();

            Swat::displayInlineJavaScript($this->getInlineJavaScript());
        }
    }

    // }}}
    // {{{ public function printWidgetTree()

    public function printWidgetTree()
    {
        echo get_class($this), ' ', $this->id;

        $children = $this->getChildren();
        if (count($children) > 0) {
            echo '<ul>';
            foreach ($children as $child) {
                echo '<li>';
                $child->printWidgetTree();
                echo '</li>';
            }
            echo '</ul>';
        }
    }

    // }}}
    // {{{ public function getMessages()

    /**
     * Gets all messaages
     *
     * Gathers all messages from pages of this notebook and from this notebook
     * itself.
     *
     * @return array an array of {@link SwatMessage} objects.
     *
     * @see SwatMessage
     */
    public function getMessages()
    {
        $messages = parent::getMessages();

        foreach ($this->pages as $page) {
            $messages = array_merge($messages, $page->getMessages());
        }

        return $messages;
    }

    // }}}
    // {{{ public function hasMessage()

    /**
     * Checks for the presence of messages
     *
     * @return boolean true if this notebook or the subtree below this notebook
     *                  has one or more messages.
     */
    public function hasMessage()
    {
        $has_message = parent::hasMessage();

        foreach ($this->pages as $page) {
            if ($page->hasMessage()) {
                $has_message = true;
                break;
            }
        }

        return $has_message;
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the {@link SwatHtmlHeadEntry} objects needed by this notebook
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
     *                               this notebook and any UI objects in this
     *                               notebook's widget subtree.
     *
     * @see SwatUIObject::getHtmlHeadEntrySet()
     */
    public function getHtmlHeadEntrySet()
    {
        $set = parent::getHtmlHeadEntrySet();

        foreach ($this->pages as $page) {
            $set->addEntrySet($page->getHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getAvailableHtmlHeadEntrySet()

    /**
     * Gets the {@link SwatHtmlHeadEntry} objects that may be needed by this
     * notebook
     *
     * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
     *                               needed by this notebook and any UI
     *                               objects in this notebook's widget subtree.
     *
     * @see SwatUIObject::getAvailableHtmlHeadEntrySet()
     */
    public function getAvailableHtmlHeadEntrySet()
    {
        $set = parent::getAvailableHtmlHeadEntrySet();

        foreach ($this->pages as $page) {
            $set->addEntrySet($page->getAvailableHtmlHeadEntrySet());
        }

        return $set;
    }

    // }}}
    // {{{ public function getDescendants()

    /**
     * Gets descendant UI-objects
     *
     * @param string $class_name optional class name. If set, only UI-objects
     *                            that are instances of <i>$class_name</i> are
     *                            returned.
     *
     * @return array the descendant UI-objects of this notebook. If descendant
     *                objects have identifiers, the identifier is used as the
     *                array key.
     *
     * @see SwatUIParent::getDescendants()
     */
    public function getDescendants($class_name = null)
    {
        if (
            !(
                $class_name === null ||
                class_exists($class_name) ||
                interface_exists($class_name)
            )
        ) {
            return array();
        }

        $out = array();

        foreach ($this->pages as $page) {
            if ($class_name === null || $page instanceof $class_name) {
                if ($page->id === null) {
                    $out[] = $page;
                } else {
                    $out[$page->id] = $page;
                }
            }

            if ($page instanceof SwatUIParent) {
                $out = array_merge($out, $page->getDescendants($class_name));
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getFirstDescendant()

    /**
     * Gets the first descendant UI-object of a specific class
     *
     * @param string $class_name class name to look for.
     *
     * @return SwatUIObject the first descendant UI-object or null if no
     *                       matching descendant is found.
     *
     * @see SwatUIParent::getFirstDescendant()
     */
    public function getFirstDescendant($class_name)
    {
        if (!class_exists($class_name) && !interface_exists($class_name)) {
            return null;
        }

        $out = null;

        foreach ($this->pages as $page) {
            if ($page instanceof $class_name) {
                $out = $page;
                break;
            }

            if ($page instanceof SwatUIParent) {
                $out = $page->getFirstDescendant($class_name);
                if ($out !== null) {
                    break;
                }
            }
        }

        return $out;
    }

    // }}}
    // {{{ public function getDescendantStates()

    /**
     * Gets descendant states
     *
     * Retrieves an array of states of all stateful UI-objects in the widget
     * subtree below this notebook.
     *
     * @return array an array of UI-object states with UI-object identifiers as
     *                array keys.
     */
    public function getDescendantStates()
    {
        $states = array();

        foreach ($this->getDescendants('SwatState') as $id => $object) {
            $states[$id] = $object->getState();
        }

        return $states;
    }

    // }}}
    // {{{ public function setDescendantStates()

    /**
     * Sets descendant states
     *
     * Sets states on all stateful UI-objects in the widget subtree below this
     * notebook.
     *
     * @param array $states an array of UI-object states with UI-object
     *                       identifiers as array keys.
     */
    public function setDescendantStates(array $states)
    {
        foreach ($this->getDescendants('SwatState') as $id => $object) {
            if (isset($states[$id])) {
                $object->setState($states[$id]);
            }
        }
    }

    // }}}
    // {{{ public function copy()

    /**
     * Performs a deep copy of the UI tree starting with this UI object
     *
     * @param string $id_suffix optional. A suffix to append to copied UI
     *                           objects in the UI tree.
     *
     * @return SwatUIObject a deep copy of the UI tree starting with this UI
     *                       object.
     *
     * @see SwatUIObject::copy()
     */
    public function copy($id_suffix = '')
    {
        $copy = parent::copy($id_suffix);

        foreach ($this->pages as $key => $page) {
            $copy_page = $page->copy($id_suffix);
            $copy_page->parent = $copy;
            $copy->pages[$key] = $copy_page;
        }

        return $copy;
    }

    // }}}
    // {{{ public function processValue()

    /**
     * Processes the value of this radio list from user-submitted form data
     *
     * This method can be used to process the list value without processing
     * the selected page widget sub-tree.
     *
     * @return boolean true if the value was processed from form data
     */
    public function processValue()
    {
        $form = $this->getForm();

        if ($form->getHiddenField($this->id . '_submitted') === null) {
            return false;
        }

        $data = &$form->getFormData();

        if (isset($data[$this->id])) {
            // get selected page id, strip off this id prefix
            $this->selected_page = mb_substr(
                $data[$this->id],
                mb_strlen($this->id) + 1
            );
        } else {
            $this->selected_page = null;
        }

        return true;
    }

    // }}}
    // {{{ protected function displayPage()

    /**
     * Displays an individual page in this radio notebook
     *
     * @param SwatNoteBookPage $page the page to display
     * @param integer $count the ordinal idnex of the page being displayed
     *                        starting at 1.
     */
    protected function displayPage(SwatNoteBookPage $page, $count = 0)
    {
        echo '<tr class="swat-radio-note-book-option-row">';
        echo '<td>';

        $radio = new SwatHtmlTag('input');
        $radio->type = 'radio';
        $radio->name = $this->id;
        $radio->id = $this->id . '_' . $page->id;
        $radio->value = $this->id . '_' . $page->id;

        if ($page->id == $this->selected_page) {
            $radio->checked = 'checked';
        }

        echo '<span class="swat-radio-wrapper">';
        $radio->display();
        echo '<span class="swat-radio-shim"></span>';
        echo '</span>';

        echo '</td>';
        echo '<td>';

        $label = new SwatHtmlTag('label');
        $label->for = $this->id . '_' . $page->id;
        $label->setContent($page->title, $page->title_content_type);
        $label->display();

        echo '</td>';
        echo '</tr>';

        echo '<tr class="swat-radio-note-book-page-row">';
        echo '<td></td>';

        $td = new SwatHtmlTag('td');
        $td->class = 'swat-radio-note-book-page';

        if ($page->id == $this->selected_page) {
            $td->class .= ' selected';
        }

        $td->open();
        echo '<div class="swat-radio-note-book-page-container">';
        $page->display();
        echo '</div>';
        $td->close();

        echo '</tr>';
    }

    // }}}
    // {{{ protected function displaySinglePage()

    /**
     * Displays the only page of this notebook if this notebook contains only
     * one page
     *
     * @param SwatNoteBookPage $page the page to display.
     */
    protected function displaySinglePage(SwatNoteBookPage $page)
    {
        $container = new SwatHtmlTag('div');
        $container->id = $this->id;
        $container->class = 'swat-radio-note-book';
        $container->open();

        $div = new SwatHtmlTag('div');
        $div->class = 'swat-radio-note-book-page';
        $div->open();
        $page->display();
        $div->close();

        $input = new SwatHtmlTag('input');
        $input->type = 'hidden';
        $input->name = $this->id;
        $input->id = $this->id . '_' . $page->id;
        $input->value = $this->id . '_' . $page->id;
        $input->display();

        $container->close();
    }

    // }}}
    // {{{ protected function getInlineJavaScript()

    /**
     * Gets the inline JavaScript used by this notebook
     *
     * @return string the inline JavaScript used by this notebook.
     */
    protected function getInlineJavaScript()
    {
        return sprintf(
            'var %s_obj = new SwatRadioNoteBook(%s);',
            $this->id,
            SwatString::quoteJavaScriptString($this->id)
        );
    }

    // }}}
}
