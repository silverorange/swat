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

		// note: the 'accept' attribute is part of the w3c standard, but
		//       is ignored by most browsers
		if ($this->accept_mime_types !== null)
			$input_tag->accept = implode(',', $this->accept_mime_types);

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

		// note: an array is returned even if no file is uploaded, so check
		//       the filename
		if ($this->file['name'] == null)
			$this->file = null;

		if (!$this->required && $this->file === null) {
			return;

		} elseif ($this->file === null) {
			$message = $this->getValidationMessage('required');
			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));

		} elseif ($this->accept_mime_types !== null &&
			!in_array($this->getMimeType(), $this->accept_mime_types)) {

			$message = sprintf(
				$this->getValidationMessage('mime-type'),
				implode(', ', $this->accept_mime_types));

			$this->addMessage(new SwatMessage($message, SwatMessage::ERROR));
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
	 * @return SwatMessage a note listing the accepted mime-types for this
	 *                      file entry widget or null if any mime-type is
	 *                      accepted.
	 *
	 * @see SwatControl::getNote()
	 */
	public function getNote()
	{
		$message = null;

		if ($this->accept_mime_types !== null && $this->display_mime_types) {
			$message = new SwatMessage(sprintf(Swat::ngettext(
				'File type must be %s.',
				'Valid file types are: %s.',
				count($this->accept_mime_types)),
				implode(', ', $this->accept_mime_types)));
		}

		return $message;
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
			$temp_file_name = $this->getTempFileName();
			if (file_exists($temp_file_name)) {
				// use fileinfo extension if available otherwise fallback to
				// mime_content_type()
				if (class_exists('finfo')) {
					$finfo = new finfo(FILEINFO_MIME);
					$this->mime_type = $finfo->file($temp_file_name);
				} else {
					$this->mime_type = mime_content_type($temp_file_name);
				}
			} else {
				$this->mime_type = $this->file['type'];
			}
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
		return ($this->visible) ? $this->id : null;
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Gets a validation message for this file entry
	 *
	 * Can be used by sub-classes to change the validation messages.
	 *
	 * @param string $id the string identifier of the validation message.
	 *
	 * @return string the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'required':
			return Swat::_('The %s field is required.');
		case 'mime-type':
			return
				Swat::_('The %%s field must be of the following type(s): %s.');
		default:
			return null;
		}
	}

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
