<?php

require_once 'Swat/SwatControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatFormField.php';

/**
 * A file upload widget
 *
 * Note: you must set the form's enctype to "multipart/form-data"
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFileEntry extends SwatControl
{
	/**
	 * Whether this entry widget is required or not
	 *
	 * Must have a non-empty value when processed.
	 *
	 * @var boolean
	 */
	public $required = false;

	/**
	 * input size
	 *
	 * size in characters of the html text form input, or null.
	 *
	 * @var integer
	 */
	public $size = 40;

	/**
	 * Accept Mime-Types
	 *
	 * Array of mime-types to accept
	 *
	 * @var array
	 */
	public $accept_mime_types = null;

	/**
	 * Display acceptable mime-types
	 *
	 * @var boolean
	 */
	public $display_mime_types = true;

	private $file = null;

	/**
	 * Displays this entry widget
	 *
	 * Outputs an appropriate XHTML tag.
	 */
	public function display()
	{
		if (!$this->visible)
			return

		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'file';
		$input_tag->name = $this->id;
		$input_tag->id = $this->id;

		if ($this->size !== null)
			$input_tag->size = $this->size;

		if ($this->accept_mime_types !== null)
			$input_tag->accept = implode(',', $this->accept_mime_types);
			//note: the 'accept' attribute is part of the w3c
			//standard, but ignored by most browsers

		$input_tag->display();

		if ($this->accept_mime_types !== null && $this->display_mime_types
			&& $this->parent instanceof SwatFormField) {

			$note = Swat::ngettext("File type must be '%s'",
				"Valid file types are: %s",
				count($this->accept_mime_types));

			$this->parent->note = sprintf($note, implode(', ', $this->accept_mime_types));
		}
	}

	/**
	 * Processes this entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		$this->file = SwatApplication::initVar($this->id, null, SwatApplication::VAR_FILES);

		if ($this->file['name'] == null)
			$this->file = null; 	//note: an array is returned even if
						//no file is uploaded, so check the filename

		if (!$this->required && $this->file === null) {
			return;

		} elseif ($this->file === null) {
			$msg = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));

		} elseif (!in_array($this->getMimeType(), $this->accept_mime_types)) {
			$msg = sprintf(Swat::_('The %s field must be of the following type(s): %s.'),
				'%s',
				implode(', ', $this->accept_mime_types));

			$this->addMessage(new SwatMessage($msg, SwatMessage::ERROR));
		}
	}


	/**
	 * Is file uploaded
	 *
	 * @return boolean Whether or not a file was uploaded
	 */
	public function isUploaded()
	{
		return ($this->file !== null);
	}

	/**
	 * Get file name
	 *
	 * Get the original filename of the uploaded file
	 *
	 * @return string Original filename
	 */
	public function getFileName()
	{
		return ($this->isUploaded()) ? $this->file['name'] : null;
	}

	/**
	 * Get file mime type
	 *
	 * @return string Mime type of the uploaded file
	 */
	public function getMimeType()
	{
		return ($this->isUploaded()) ? $this->file['type'] : null;
	}

	/**
	 * Save file
	 *
	 * Save the uploaed file to the server
	 *
	 * param string $dst_dir path to save file in
	 * param string $dst_filename optional filename to save as. If not
	 * 	 given, file is saved with the original filename.
	 *
	 * @return boolean Whether or not the file was saved correctly
	 */
	public function saveFile($dst_dir, $dst_filename = null)
	{
		if (!$this->isUploaded())
			return false;

		if ($dst_filename === null)
			$dst_filename = $this->getFileName();

		return move_uploaded_file($this->file['tmp_name'], $dst_dir.'/'.$dst_filename);
	}
}

?>
