<?php

require_once 'Swat/SwatContainer.php';
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
class SwatFormField extends SwatContainer implements SwatTitleable
{
	/**
	 * The visible name for this field, or null
	 *
	 * @var string
	 */
	public $title = null;

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
	 * CSS class to use on the container tag
	 *
	 * Subclasses can change this to change their appearance.
	 *
	 * @var string
	 */
	protected $class = 'swat-form-field';

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
	 * Creates a new form field
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->addStyleSheet('swat/styles/swat-message.css');
		$this->addStyleSheet('swat/styles/swat-form-field.css');
	}

	/**
	 * Gets the title of this form field
	 *
	 * Implements the {SwatTitleable::getTitle()} interface.
	 *
	 * @return the title of this form field.
	 */
	public function getTitle()
	{
		return $this->title;
	}

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

		$messages = &$this->getMessages();
		$container_tag = new SwatHtmlTag($this->container_tag);
		$container_tag->class = $this->class;

		if ($this->id !== null)
			$container_tag->id = $this->id;

		if (count($messages) > 0)
			$container_tag->class.= ' swat-form-field-with-messages';

		$container_tag->open();

		if ($this->title !== null) {
			$title_tag = $this->getTitleTag($this->title);
			$title_tag->open();
			$title_tag->displayContent();

			// TODO: widgets that are marked as required don't tell their field
			// parent
			if ($this->required) {
				$span_tag = new SwatHtmlTag('span');
				$span_tag->class = 'swat-required';
				$span_tag->setContent(sprintf(' (%s)', Swat::_('required')));
				$span_tag->display();
			}

			$title_tag->close();
		}

		$contents_tag = new SwatHtmlTag($this->contents_tag);
		$contents_tag->class = 'swat-form-field-contents';

		$contents_tag->open();

		foreach ($this->children as &$child)
			$child->display();

		$contents_tag->close();

		if (count($messages) > 0) {
			$message_ul = new SwatHtmlTag('ul');
			$message_ul->class = 'swat-form-field-messages';

			$message_ul->open();

			foreach ($messages as &$msg) {
				$message_li = new SwatHtmlTag('li');
				$message_li->setContent($msg->primary_content,
					$msg->content_type);

				$message_li->class = $msg->getCssClass();
				$message_li->display();
			}

			$message_ul->close();
		}

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

		$container_tag->close();
	}

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
		$label_tag->setContent(sprintf('%s: ', $title));
		$focus_id = $this->getFocusableHtmlId();
		if ($focus_id !== null)
			$label_tag->for = $focus_id;

		return $label_tag;
	}

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
			$this->class = 'swat-form-field-checkbox';
		}
	}
}

?>
