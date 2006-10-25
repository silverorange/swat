<?php

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatFormField.php';

/**
 * A file upload widget
 *
 * @package   Swat
 * @copyright 2005-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFileEntry extends SwatInputControl
{
	// {{{ public properties

	/**
	 * The size in characters of the XHTML form input, or null if no width is
	 * specified
	 *
	 * @var integer
	 */
	public $size = 40;

	/**
	 * Array of mime-types to accept as uploads
	 *
	 * @var array
	 */
	public $accept_mime_types = null;

	/**
	 * Display acceptable mime-types as a note in this entry's parent
	 *
	 * @var boolean
	 */
	public $display_mime_types = true;

	/**
	 * Access key
	 *
	 * Access key for this file entry control, for keyboard nagivation.
	 *
	 * @var string
	 */
	public $access_key = null;

	// }}}
	// {{{ private properties

	/**
	 * Stores the relevant part of the $_FILES array for this widget after
	 * the widget's parent is processed
	 *
	 * @var array
	 */
	private $file = null;

	/**
	 * The mime type of the uploaded file
	 *
	 * If possible, this is the mime type detected by the server, not the mime
	 * type specified by the web-browser. This is only the mime type specified
	 * by the browser if the temporary uploaded file is deleted before the
	 * mime type is queried.
	 *
	 * @var string
	 */
	private $mime_type;

	// }}}
	// {{{ public function display()

	/**
	 * Displays this entry widget
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'file';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->class = $this->getCSSClassString();
		$input_tag->accesskey = $this->access_key;
		$input_tag->size = $this->size;

		if ($this->accept_mime_types !== null)
			$input_tag->accept = implode(',', $this->accept_mime_types);
			//note: the 'accept' attribute is part of the w3c
			//standard, but ignored by most browsers

		$input_tag->display();
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this file entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		parent::process();

		if (!isset($_FILES[$this->id]))
			return;
		else
			$this->file = $_FILES[$this->id];

		if ($this->file['name'] == null)
			$this->file = null;
			// note: an array is returned even if
			//       no file is uploaded, so check the filename

		if (!$this->required && $this->file === null) {
			return;

		} elseif ($this->file === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif ($this->accept_mime_types !== null &&
			!in_array($this->getMimeType(), $this->accept_mime_types)) {

			$msg = sprintf(
				Swat::_('The %%s field must be of the following type(s): %s.'),
				implode(', ', $this->accept_mime_types));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}

	// }}}
	// {{{ public function getNote()

	/**
	 * Gets a note specifying the mime types this file entry accepts
	 *
	 * The file types are only returned if
	 * {@link SwatFileEntry::$display_mimetypes} is set to true and
	 * {@link SwatFileEntry::$accept_mime_types} has entries.
	 *
	 * @return string an informative note of how to use this control.
	 */
	public function getNote()
	{
		$note = null;

		if ($this->accept_mime_types !== null && $this->display_mime_types) {
			$note = Swat::ngettext("File type must be '%s'",
				"Valid file types are: %s",
				count($this->accept_mime_types));

			$note = sprintf($note, implode(', ', $this->accept_mime_types));
		}

		return $note;
	}

	// }}}
	// {{{ public function isUploaded()

	/**
	 * Is file uploaded
	 *
	 * @return boolean whether or not a file was uploaded with this file entry.
	 */
	public function isUploaded()
	{
		return ($this->file !== null);
	}

	// }}}
	// {{{ public function getFileName()

	/**
	 * Gets the original file name of the uploaded file
	 *
	 * @return mixed the original filename of the uploaded file or null if no
	 *                file was uploaded.
	 *
	 * @see SwatFileEntry::getTempFileName()
	 */
	public function getFileName()
	{
		return ($this->isUploaded()) ? $this->file['name'] : null;
	}

	// }}}
	// {{{ public function getTempFileName()

	/**
	 * Gets the temporary name of the uploaded file
	 *
	 * @return mixed the temporary name of the uploaded file or null if no
	 *                file was uploaded.
	 *
	 * @see SwatFileEntry::getFileName()
	 */
	public function getTempFileName()
	{
		return ($this->isUploaded()) ? $this->file['tmp_name'] : null;
	}

	// }}}
	// {{{ public function getSize()

	/**
	 * Gets the size of the uploaded file in bytes
	 *
	 * @return mixed the size of the uploaded file in bytes or null if no file
	 *                was uploaded.
	 */
	public function getSize()
	{
		return ($this->isUploaded()) ? $this->file['size'] : null;
	}

	// }}}
	// {{{ public function getMimeType()

	/**
	 * Gets the mime type of the uploaded file
	 *
	 * @return mixed the mime type of the uploaded file or null if no file was
	 *                uploaded.
	 */
	public function getMimeType()
	{
		if ($this->isUploaded() && $this->mime_type === null) {
			if (file_exists($this->getTempFileName()))
				$this->mime_type = mime_content_type($this->getTempFileName());
			else
				$this->mime_type = $this->file['type'];
		}

		return $this->mime_type;
	}

	// }}}
	// {{{ public function saveFile()

	/**
	 * Saves the uploaded file to the server
	 *
	 * @param string $dst_dir the directory on the server to save the uploaded
	 *                        file in.
	 * @param string $dst_filename an optional filename to save the file under.
	 *                             If no filename is specified, the file is
	 *                             saved with the original filename.
	 *
	 * @return boolean true if the file was saved correctly and false if there
	 *                  was an error or no file was uploaded.
	 *
	 * @throws SwatException if the destination directory does not exist.
	 */
	public function saveFile($dst_dir, $dst_filename = null)
	{
		if (!$this->isUploaded())
			return false;

		if ($dst_filename === null)
			$dst_filename = $this->getFileName();

		if (is_dir($dst_dir))
			return move_uploaded_file($this->file['tmp_name'],
				$dst_dir.'/'.$dst_filename);
		else
			throw new SwatException("Destination of '{$dst_dir}' is not a ".
				'directory or does not exist.');
	}

	// }}}
	// {{{ public function getFocusableHtmlId()

	/**
	 * Gets the id attribute of the XHTML element displayed by this widget
	 * that should receive focus
	 *
	 * @return string the id attribute of the XHTML element displayed by this
	 *                 widget that should receive focus or null if there is
	 *                 no such element.
	 *
	 * @see SwatWidget::getFocusableHtmlId()
	 */
	public function getFocusableHtmlId()
	{
		return $this->id;
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this file entry widget
	 *
	 * @return array the array of CSS classes that are applied to this file
	 *                entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-file-entry');
		$classes = array_merge($classes, $this->classes);
		return $classes;
	}

	// }}}
}

?>
