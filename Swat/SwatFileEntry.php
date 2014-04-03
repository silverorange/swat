<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatInputControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatFormField.php';

/**
 * A file upload widget
 *
 * Note: Mime-type detection is done with the
 * {@link http://pecl.php.net/package/Fileinfo Fileinfo} extension if avaiable.
 * Mime-type detection falls back to the mime_content_type() function if
 * Fileinfo is not available but Fileinfo is highly recommended. If no
 * mime-type detection is supported by the server, mime-types are returned as
 * 'application/octet-stream'.
 *
 * @package   Swat
 * @copyright 2005-2014 silverorange
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
	 * Array of mime types to accept as uploads
	 *
	 * @var array
	 */
	public $accept_mime_types = null;

	/**
	 * Associative array of human-readable file types indexed by mime type.
	 *
	 * Human-readable file types are not a one-to-one replacement of mime types,
	 * as certain file types can have multiple acceptable mime types. An example
	 * would be MP3 files, which can have 'audio/mpeg', 'audio/mp3' as mime
	 * types, but the human-readbable file type would just be 'MP3'.
	 *
	 * If set, when displaying acceptable mime types, we display the human-
	 * readable file types in place of the mime types.
	 *
	 * @var array
	 */
	public $human_file_types = array();

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

	/**
	 * Tab index
	 *
	 * The ordinal tab index position of the XHTML input tag, or null.
	 * Values 1 or greater will affect the tab index of this widget. A value
	 * of 0 or null will use the position of the input tag in the XHTML
	 * character stream to determine tab order.
	 *
	 * @var integer
	 */
	public $tab_index = null;

	/**
	 * Display maximum file upload size
	 *
	 * @var boolean
	 */
	public $display_maximum_upload_size = false;

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

		parent::display();

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'file';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;
		$input_tag->class = $this->getCSSClassString();
		$input_tag->accesskey = $this->access_key;
		$input_tag->tabindex = $this->tab_index;
		$input_tag->size = $this->size;

		// note: the 'accept' attribute is part of the w3c standard, but
		//       is ignored by most browsers
		if ($this->accept_mime_types !== null)
			$input_tag->accept = implode(',', $this->accept_mime_types);

		if (!$this->isSensitive())
			$input_tag->disabled = 'disabled';

		$input_tag->display();

		if ($this->display_maximum_upload_size) {
			$div_tag = new SwatHtmlTag('div');
			$div_tag->class = 'swat-note';
			$div_tag->setContent($this->getMaximumUploadSizeText());
			$div_tag->display();
		}
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

		// The $_FILES[$this->id] array is always set unless the POST data
		// was greater than the PHP's post_max_size ini setting.
		if (!isset($_FILES[$this->id]) || !$this->isSensitive()) {
			return;
		}

		if ($_FILES[$this->id]['error'] === UPLOAD_ERR_OK) {
			$this->file = $_FILES[$this->id];

			if (!$this->hasValidMimeType()) {
				$this->addMessage($this->getValidationMessage('mime-type'));
			}
		} else if ($_FILES[$this->id]['error'] === UPLOAD_ERR_NO_FILE) {
			if ($this->required) {
				$this->addMessage($this->getValidationMessage('required'));
			}
		} else if ($_FILES[$this->id]['error'] === UPLOAD_ERR_INI_SIZE ||
			       $_FILES[$this->id]['error'] === UPLOAD_ERR_FORM_SIZE) {

			$this->addMessage($this->getValidationMessage('too-large'));
		} else {
			// There are other status codes we may want to check for in the
			// future. Upload status codes can be found here:
			// http://php.net/manual/en/features.file-upload.errors.php
			$this->addMessage($this->getValidationMessage('upload-error'));
		}
	}

	// }}}
	// {{{ public function getNote()

	/**
	 * Gets a note specifying the mime types this file entry accepts
	 *
	 * The file types are only returned if
	 * {@link SwatFileEntry::$display_mimetypes} is set to true and
	 * {@link SwatFileEntry::$accept_mime_types} has entries. If
	 * {@link SwatFileEntry::$human_file_types} is set, the note displays the
	 * human-readable file types where possible.
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
			$displayable_types = $this->getDisplayableTypes();
			$message = new SwatMessage(
				sprintf(
					Swat::ngettext(
						'Valid files are the following type: %s.',
						'Valid files are the following type(s): %s.',
						count($displayable_types)
					),
					implode(', ', $displayable_types)
				)
			);
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
	// {{{ public function getUniqueFileName()

	/**
	 * Gets a unique file name for the uploaded file for the given path.
	 *
	 * If the original file name is already unqiue, it will be used, otherwise
	 * a number will be appended to the end of the file name to make it unique.
	 *
	 * @param string path where the file is to be saved
	 *
	 * @return string the unique file name
	 *
	 * @see SwatFileEntry::getFileName()
	 */
	public function getUniqueFileName($path)
	{
		if (is_dir($path))
			return $this->generateUniqueFileName($path);
		else
			throw new SwatException("Path '{$path}' is not a ".
				'directory or does not exist.');
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

				if (extension_loaded('fileinfo')) {
					// Use the fileinfo extension if available.
					$finfo = $this->getFinfo();
					$this->mime_type = reset(explode(';',
						$finfo->file($temp_file_name)));

				} elseif (function_exists('mime_content_type')) {
					// Fall back to mime_content_type() if available.
					$this->mime_type = mime_content_type($temp_file_name);
				}

				// No mime-detection functions, or mime-detection function
				// failed to detect the type. Default to
				// 'application/octet-stream'. Relying on HTTP headers could
				// be a security problem so we never fall back to that option.
				if ($this->mime_type == '') {
					$this->mime_type = 'application/octet-stream';
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
			$dst_filename = $this->getUniqueFileName($dst_dir);

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
	// {{{ public static function getMaximumFileUploadSize()

	/**
	 * Returns the size (in bytes) of the upload size limit of the PHP
	 * configuration
	 *
	 * The maximum upload size is calculated based on the php ini values for
	 * <code>upload_max_filesize</code> and <code>post_max_size</code>. Be
	 * aware that web server and POST data settings can also affect the
	 * maximum upload size limit.
	 *
	 * @return integer the maximum upload size in bytes.
	 */
	public static function getMaximumFileUploadSize()
	{
		return min(self::parseFileUploadSize(ini_get('post_max_size')),
			self::parseFileUploadSize(ini_get('upload_max_filesize')));
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
	 * @return SwatMessage the validation message.
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'mime-type':
			$displayable_types = $this->getDisplayableTypes();

			if ($this->show_field_title_in_messages) {
				$text = sprintf(
					Swat::ngettext(
						'The %%s field must be of the following type: %s.',
						'The %%s field must be of the following type(s): %s.',
						count($displayable_types)
					),
					implode(', ', $displayable_types)
				);
			} else {
				$text = sprintf(
					Swat::ngettext(
						'This field must be of the following type: %s.',
						'This field must be of the following type(s): %s.',
						count($displayable_types)
					),
					implode(', ', $displayable_types)
				);
			}

			$message = new SwatMessage($text, 'error');
			break;

		case 'too-large':
			if ($this->show_field_title_in_messages) {
				$text = Swat::_(
					'The %s field exceeds the maximum allowable file size.'
				);
			} else {
				$text = Swat::_(
					'This field exceeds the maximum allowable file size.'
				);
			}

			$message = new SwatMessage($text, 'error');
			break;

		case 'upload-error':
			if ($this->show_field_title_in_messages) {
				$text = Swat::_(
					'The %s field encounted an error when trying to upload '.
					'the file. Please try again.'
				);
			} else {
				$text = Swat::_(
					'This field encounted an error when trying to upload the '.
					'file. Please try again.'
				);
			}

			$message = new SwatMessage($text, 'error');
			break;

		default:
			$message = parent::getValidationMessage($id);
			break;
		}

		return $message;
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
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function hasValidMimeType()

	/**
	 * Whether or not the uploaded file's mime type is valid
	 *
	 * Gets whether or not the upload file's mime type matches the accepted
	 * mime types of this widget. Valid mime types for this widget are stored in
	 * the {@link SwatFileEntry::$accept_mime_types} array. If the
	 * <kbd>$accept_mime_types</kbd> array is empty, the uploaded file's mime
	 * type is always valid.
	 *
	 * Some container formats may have multiple mime-types. In this case, if
	 * any of the contained types are valid, we consider the file valid.
	 *
	 * @return boolean whether or not this file's mime type is valid.
	 */
	protected function hasValidMimeType()
	{
		$valid = false;

		if ($this->isUploaded()) {
			// Some container formats can contain return multiple mime-types.
			// If any of the contained types are valid, we consider the file
			// valid.
			$mime_types = explode(' ', $this->getMimeType());
			if (is_array($this->accept_mime_types) &&
				count($this->accept_mime_types) > 0) {

				$types = array_intersect($mime_types, $this->accept_mime_types);
				$valid = (count($types) > 0);

			} else {
				$valid = true;
			}
		}

		return $valid;
	}

	// }}}
	// {{{ protected function getMaximumUploadSizeText()

	protected function getMaximumUploadSizeText()
	{
		return sprintf(Swat::_('Maximum file size %s.'),
			SwatString::byteFormat(self::getMaximumFileUploadSize()));
	}

	// }}}
	// {{{ protected function getFinfo()

	/**
	 * Gets a new finfo resource
	 *
	 * @return mixed the magic database resource or FALSE on failure.
	 */
	protected function getFinfo()
	{
		// PHP >= 5.3.0 supports returning only the mimetype
		// without returning the encoding. See
		// http://us3.php.net/manual/en/fileinfo.constants.php for
		// details.
		$mime_constant = (defined('FILEINFO_MIME_TYPE')) ?
			FILEINFO_MIME_TYPE : FILEINFO_MIME;

		return new finfo($mime_constant);
	}

	// }}}
	// {{{ protected function getDisplayableTypes()

	protected function getDisplayableTypes()
	{
		$displayable_types = array();

		foreach ($this->accept_mime_types as $mime_type) {
			$displayable_type = (isset($this->human_file_types[$mime_type]))
				? $this->human_file_types[$mime_type]
				: $mime_type;

			// Use the value as the key to de-dupe.
			$displayable_types[$displayable_type] = $displayable_type;
		}

		return $displayable_types;
	}

	// }}}
	// {{{ private function generateUniqueFileName()

	private function generateUniqueFileName($path, $count = 0) {
		if (strpos($this->getFileName(), '.') === false) {
			$extension = '';
			$base_name = $this->getFileName();
		} else {
			$extension = '.'.array_pop(explode('.', $this->getFileName()));
			$base_name = basename($this->getFileName(), $extension);
		}

		if ($count > 0)
			$file_name = $base_name.$count.$extension;
		else
			$file_name = $base_name.$extension;

		if (file_exists($path.'/'.$file_name))
			return $this->generateUniqueFileName($path, $count + 1);
		else
			return $file_name;
	}

	// }}}
	// {{{ private static function parseFileUploadSize()

	private static function parseFileUploadSize($ini_value)
	{
		if (is_numeric($ini_value)) {
			return $value = $ini_value;
		} else {
			$size  = strtoupper(substr($ini_value, -1));
			$value = (integer)substr($ini_value, 0, -1);

			switch($size) {
			case 'P':
				$value *= 1024;
			case 'T':
				$value *= 1024;
			case 'G':
				$value *= 1024;
			case 'M':
				$value *= 1024;
			case 'K':
				$value *= 1024;
			}
		}

		return $value;
	}

	// }}}
}

?>
