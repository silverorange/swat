<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

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
 * @copyright 2004-2014 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFormField extends SwatDisplayableContainer implements SwatTitleable
{
	// {{{ constants

	const DISPLAY_REQUIRED = 1;
	const DISPLAY_OPTIONAL = 2;

	// }}}
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
	 * Display the required status of this field
	 *
	 * Bitwise display options:
	 * SwatFormField::DISPLAY_REQUIRED = display "required" if this field
	 *                                   is required
	 * SwatFormField::DISPLAY_OPTIONAL = display "optional" if this field
	 *                                   is not required
	 *
	 * @var integer
	 */
	public $required_status_display = self::DISPLAY_REQUIRED;

	/**
	 * Optional note of text to display with the field
	 *
	 * @var string
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

	/**
	 * Whether or not to show a colon after the title of this form field
	 *
	 * By default, a colon is shown.
	 *
	 * @var boolean
	 */
	public $show_colon = true;

	/**
	 * Display the title of the form field after the widget code
	 *
	 * This is automatically set for some widget types, but defaults to null
	 * (which we treat the same as false) to allow the value to be manually set
	 * for said widgets.
	 *
	 * @var boolean
	 */
	public $title_reversed = null;

	/**
	 * Whether or not to show notes on this field before showing content
	 *
	 * By default, content is displayed before notes.
	 *
	 * @var boolean
	 */
	public $show_notes_first = false;

	/**
	 * Whether or not to wrap the content and notes of the form field in a div
	 * for styling purposes.
	 *
	 * By default, content and notes are not wrapped.
	 *
	 * @var boolean
	 */
	public $wrap_content_and_notes = false;

	/**
	 * Whether or not to display validation messages in this form field
	 *
	 * Defaults to true. Set to false to prevent the displaying of messages in
	 * this form field.
	 *
	 * @var boolean
	 */
	public $display_messages = true;

	/**
	 * Whether or not to show the title for this form field
	 *
	 * Form fields can have a title and opt not to show it on display. This
	 * can be used to set a title for validation error messages but not
	 * add a visible title to the user-interface.
	 *
	 * @var boolean
	 */
	public $show_title = true;

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

		$this->addStyleSheet('packages/swat/styles/swat-message.css');
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
	// {{{ public function getTitleContentType()

	/**
	 * Gets the title content-type of this form field
	 *
	 * Implements the {@link SwatTitleable::getTitleContentType()} interface.
	 *
	 * @return string the title content-type of this form field.
	 */
	public function getTitleContentType()
	{
		return $this->title_content_type;
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

		SwatWidget::display();

		$container_tag = new SwatHtmlTag($this->container_tag);
		$container_tag->id = $this->id;
		$container_tag->class = $this->getCSSClassString();

		$container_tag->open();

		$content_wrapper = null;
		if ($this->wrap_content_and_notes) {
			$content_wrapper = new SwatHtmlTag('div');
			$content_wrapper->class = 'swat-form-field-content-wrapper';
		}

		if ($this->title_reversed) {
			if ($this->show_notes_first) {
				if ($this->wrap_content_and_notes) {
					$content_wrapper->open();
					$this->displayNotes();
					$this->displayContent();
					$content_wrapper->close();

					$this->displayTitle();
					$this->displayMessages();
				} else {
					$this->displayNotes();
					$this->displayContent();
					$this->displayTitle();
					$this->displayMessages();
				}
			} else {
				if ($this->wrap_content_and_notes) {
					$content_wrapper->open();
					$this->displayContent();
					$this->displayNotes();
					$content_wrapper->close();

					$this->displayTitle();
					$this->displayMessages();
				} else {
					$this->displayContent();
					$this->displayTitle();
					$this->displayMessages();
					$this->displayNotes();
				}
			}
		} else {
			if ($this->show_notes_first) {
				if ($this->wrap_content_and_notes) {
					$content_wrapper->open();
					$this->displayNotes();
					$this->displayContent();
					$content_wrapper->close();

					$this->displayTitle();
					$this->displayMessages();
				} else {
					$this->displayTitle();
					$this->displayNotes();
					$this->displayContent();
					$this->displayMessages();
				}
			} else {
				if ($this->wrap_content_and_notes) {
					$this->displayTitle();
					$content_wrapper->open();
					$this->displayContent();
					$this->displayNotes();
					$content_wrapper->close();

					$this->displayMessages();
				} else {
					$this->displayTitle();
					$this->displayContent();
					$this->displayMessages();
					$this->displayNotes();
				}
			}
		}

		$container_tag->close();
	}

	// }}}
	// {{{ protected function displayTitle()

	protected function displayTitle()
	{
		if (!$this->show_title ||
			($this->title === null && $this->access_key === null)) {
			return;
		}

		$title_tag = $this->getTitleTag();
		$title_tag->open();
		$title_tag->displayContent();
		$this->displayRequiredStatus();
		$title_tag->close();
	}

	// }}}
	// {{{ protected function displayRequiredStatus()

	protected function displayRequiredStatus()
	{
		if ($this->required &&
			$this->required_status_display & self::DISPLAY_REQUIRED) {

			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'swat-required';
			$span_tag->setContent(sprintf(' (%s)', Swat::_('required')));
			$span_tag->display();
		} elseif (!$this->required &&
			$this->required_status_display & self::DISPLAY_OPTIONAL) {

			$span_tag = new SwatHtmlTag('span');
			$span_tag->class = 'swat-optional';
			$span_tag->setContent(sprintf(' (%s)', Swat::_('optional')));
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
		if (!$this->display_messages || !$this->hasMessage())
			return;

		$messages = $this->getMessages();

		$message_ul = new SwatHtmlTag('ul');
		$message_ul->class = 'swat-form-field-messages';
		$message_li = new SwatHtmlTag('li');

		$message_ul->open();

		foreach ($messages as $message) {
			$message_li->class = $message->getCSSClassString();
			$message_li->setContent($message->primary_content,
				$message->content_type);

			if ($message->secondary_content !== null) {
				$secondary_span = new SwatHtmlTag('span');
				$secondary_span->setContent($message->secondary_content,
					$message->content_type);

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

		if ($this->note !== null) {
			$note = new SwatMessage($this->note);
			$note->content_type = $this->note_content_type;
			$notes[] = $note;
		}

		$control = $this->getFirstDescendant('SwatControl');
		if ($control !== null) {
			$note = $control->getNote();
			if ($note !== null)
				$notes[] = $note;
		}

		if (count($notes) == 1) {
			$note = reset($notes);
			$note_div = new SwatHtmlTag('div');
			$note_div->class = 'swat-note';
			$note_div->setContent($note->primary_content, $note->content_type);
			$note_div->display();
		} elseif (count($notes) > 1) {
			$note_list = new SwatHtmlTag('ul');
			$note_list->class = 'swat-note';
			$note_list->open();

			$li_tag = new SwatHtmlTag('li');
			foreach ($notes as $note) {
				$li_tag->setContent($note->primary_content,
					$note->content_type);

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

		if ($this->display_messages && $this->hasMessage())
			$classes[] = 'swat-form-field-with-messages';

		if ($this->required)
			$classes[] = 'swat-required';

		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function getTitleTag()

	/**
	 * Get a SwatHtmlTag to display the title
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @return SwatHtmlTag a tag object containing the title.
	 */
	protected function getTitleTag()
	{
		$label_tag = new SwatHtmlTag('label');

		if ($this->title !== null) {
			if ($this->show_colon)
				$label_tag->setContent(sprintf(Swat::_('%s: '), $this->title),
					$this->title_content_type);
			else
				$label_tag->setContent($this->title, $this->title_content_type);
		}

		$label_tag->for = $this->getFocusableHtmlId();
		$label_tag->accesskey = $this->access_key;

		return $label_tag;
	}

	// }}}
	// {{{ protected function notifyOfAdd()

	/**
	 * Notifies this widget that a widget was added
	 *
	 * This sets class propertes on this form field when certain classes of
	 * widgets are added.
	 *
	 * @param SwatWidget $widget the widget that has been added.
	 *
	 * @see SwatContainer::notifyOfAdd()
	 */
	protected function notifyOfAdd($widget)
	{
		if (class_exists('SwatCheckbox') && $widget instanceof SwatCheckbox) {
			$this->widget_class = 'swat-form-field-checkbox';

			// don't set these properties if title_reversed is explicitly set in
			// the xml
			if ($this->title_reversed === null) {
				$this->title_reversed = true;
				$this->show_colon = false;
			}
		} elseif (class_exists('SwatSearchEntry') &&
			$widget instanceof SwatSearchEntry) {
			$this->show_colon = false;
		}
	}

	// }}}

	// deprecated
	// {{{ protected function displayRequired()

	/**
	 * @deprecated use the displayRequiredStatus() method instead
	 */
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
}

?>
