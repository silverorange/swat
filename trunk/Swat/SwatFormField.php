<?php

require_once 'Swat/SwatDisplayableContainer.php';
require_once 'Swat/SwatTitleable.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/SwatMessage.php';

/**
 * A container to use around control widgets in a form
 *
 * Adds a label and space to output messages.
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFormField extends SwatDisplayableContainer implements SwatTitleable
{
	// {{{ public properties

	/**
	 * The visible name for this field, or null
	 *
	 * @var string
	 */
	public $title = null;

	/**
	 * Optional content type for the title 
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $title_content_type = 'text/plain';

	/*
	 * Display a visible indication that this field is required
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * Optional note of text to display with the field
	 *
	 * @var boolean
	 */
	public $note = null;

	/**
	 * Optional content type for the note
	 *
	 * Default text/plain, use text/xml for XHTML fragments.
	 *
	 * @var string
	 */
	public $note_content_type = 'text/plain';

	/**
	 * Access key
	 *
	 * Sets an access key for the label of this form field, if one exists.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ protected properties

	/**
	 * Container tag to use
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $container_tag = 'div';

	/**
	 * Contents tag to use
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $contents_tag = 'div';

	/**
	 * A CSS class name set by the subwidgets in this form field
	 *
	 * @var string
	 *
	 * @see SwatFormField::notifyOfAdd()
	 */
	protected $widget_class;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new form field
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('packages/swat/styles/swat-message.css',
			Swat::PACKAGE_ID);

		$this->addStyleSheet('packages/swat/styles/swat-form-field.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function getTitle()

	/**
	 * Gets the title of this form field
	 *
	 * Satisfies the {SwatTitleable::getTitle()} interface.
	 *
	 * @return string the title of this form field.
	 */
	public function getTitle()
	{
		return $this->title;
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this form field
	 *
	 * Associates a label with the first widget of this container.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		if ($this->getFirst() === null)
			return;

		$container_tag = new SwatHtmlTag($this->container_tag);
		$container_tag->id = $this->id;
		$container_tag->class = $this->getCSSClassString();

		$container_tag->open();
		$this->displayTitle();
		$this->displayContent();
		$this->displayMessages();
		$this->displayNotes();
		$container_tag->close();
	}

	// }}}
	// {{{ protected function displayTitle()

	protected function displayTitle()
	{
		if ($this->title === null)
			return;

		$title_tag = $this->getTitleTag($this->title);
		$title_tag->open();
		$title_tag->displayContent();
		$this->displayRequired();
		$title_tag->close();
	}

	// }}}
	// {{{ protected function displayRequired()

	protected function displayRequired()
	{
		if ($this->required) {
			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'swat-required';
			$span_tag->setContent(sprintf(' (%s)', Swat::_('required')));
			$span_tag->display();
		}
	}

	// }}}
	// {{{ protected function displayContent()

	protected function displayContent()
	{
		$contents_tag = new SwatHtmlTag($this->contents_tag);
		$contents_tag->class = 'swat-form-field-contents';

		$contents_tag->open();
		$this->displayChildren();
		$contents_tag->close();
	}

	// }}}
	// {{{ protected function displayMessages()

	protected function displayMessages()
	{
		if (!$this->hasMessage())
			return;

		$messages = &$this->getMessages();

		$message_ul = new SwatHtmlTag('ul');
		$message_ul->class = 'swat-form-field-messages';
		$message_li = new SwatHtmlTag('li');

		$message_ul->open();

		foreach ($messages as &$msg) {
			$message_li->class = $msg->getCssClass();
			$message_li->setContent($msg->primary_content, $msg->content_type);

			if ($msg->secondary_content !== null) {
				$secondary_span = new SwatHtmlTag('span');
				$secondary_span->setContent($msg->secondary_content,
					$msg->content_type);

				$message_li->open();
				$message_li->displayContent();
				echo ' ';
				$secondary_span->display();
				$message_li->close();
			} else {
				$message_li->display();
			}
		}

		$message_ul->close();
	}

	// }}}
	// {{{ protected function displayNotes()

	protected function displayNotes()
	{
		$notes = array();
		if ($this->note !== null)
			$notes[] = $this->note;

		$control = $this->getFirstDescendant('SwatControl');
		if ($control !== null) {
			$note = $control->getNote();
			if ($note !== null)
				$notes[] = $note;
		}

		if (count($notes) == 1) {
			$note_div = new SwatHtmlTag('div');
			$note_div->class = 'swat-note';
			$note_div->setContent(reset($notes), $this->note_content_type);
			$note_div->display();
		} elseif (count($notes) > 1) {
			$note_list = new SwatHtmlTag('ul');
			$note_list->class = 'swat-note';
			$note_list->open();

			$li_tag = new SwatHtmlTag('li');
			foreach ($notes as $note) {
				// TODO: get content type of control note.
				$li_tag->setContent($note, $this->note_content_type);
				$li_tag->display();
			}

			$note_list->close();
		}
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this form field
	 *
	 * @return array the array of CSS classes that are applied to this form
	 *                field.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-form-field');

		if ($this->widget_class !== null)
			$classes[] = $this->widget_class;

		if ($this->hasMessage())
			$classes[] = 'swat-form-field-with-messages';

		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
	// {{{ protected function getTitleTag()

	/**
	 * Get a SwatHtmlTag to display the title
	 *
	 * Subclasses can change this to change their appearance.
	 * 
	 * @param string $title title of the form field.
	 * @return SwatHtmlTag a tag object containing the title.
	 */
	protected function getTitleTag($title)
	{
		$label_tag = new SwatHtmlTag('label');
		$label_tag->setContent(sprintf('%s: ', $title),
			$this->title_content_type);

		$label_tag->for = $this->getFocusableHtmlId();
		$label_tag->accesskey = $this->access_key;

		return $label_tag;
	}

	// }}}
	// {{{ protected function notifyOfAdd()

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This sets a special class on this form field if a checkbox is added.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatCheckbox') && $widget instanceof SwatCheckbox) {
			$this->widget_class = 'swat-form-field-checkbox';
		}
	}

	// }}}
}

?>
