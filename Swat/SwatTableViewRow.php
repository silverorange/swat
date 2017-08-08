<?php

/**
 * Base class for a extra row displayed at the bottom of a table view
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatTableViewRow extends SwatUIObject
{

	/**
	 * The {@link SwatTableView} associated with this row
	 *
	 * @var SwatTableView
	 */
	public $view = null;

	/**
	 * Unique identifier of this row
	 *
	 * @param string
	 */
	public $id = null;

	/**
	 * Whether or not this row has been processed
	 *
	 * @var boolean
	 *
	 * @see SwatTableViewRow::process()
	 */
	protected $processed = false;

	/**
	 * Whether or not this row has been displayed
	 *
	 * @var boolean
	 *
	 * @see SwatTableViewRow::display()
	 */
	protected $displayed = false;

	/**
	 * Initializes this row
	 *
	 * This method does nothing and is implemented here so subclasses do not
	 * need to implement it.
	 *
	 * Row initialization happens during table-view initialization. Rows are
	 * initialized after columns.
	 */
	public function init()
	{
	}

	/**
	 * Processes this row
	 *
	 * This method does nothing and is implemented here so subclasses do not
	 * need to implement it.
	 *
	 * Row processing happens during table-view processing. Rows are processed
	 * after columns.
	 */
	public function process()
	{
		$this->processed = true;
	}

	/**
	 * Displays this row
	 */
	public function display()
	{
		$this->displayed = true;
	}

	/**
	 * Whether or not this row is processed
	 *
	 * @return boolean whether or not this row is processed.
	 */
	public function isProcessed()
	{
		return $this->processed;
	}

	/**
	 * Whether or not this row is displayed
	 *
	 * @return boolean whether or not this row is displayed.
	 */
	public function isDisplayed()
	{
		return $this->displayed;
	}

	/**
	 * Gets the inline JavaScript required by this row
	 *
	 * All inline JavaScript is displayed after the table-view has been
	 * displayed.
	 *
	 * @return string the inline JavaScript required by this row.
	 */
	public function getInlineJavaScript()
	{
		return '';
	}

	/**
	 * Gets whether or not to show this row based on a count of rows
	 *
	 * By default if there are no entries in the table model, this row is not
	 * shown.
	 *
	 * @param integer $count the number of entries in this row's view's model.
	 *
	 * @return boolean true if this row should be shown and false if it should
	 *                  not.
	 */
	public function getVisibleByCount($count)
	{
		if ($count == 0)
			return false;

		return true;
	}

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this row
	 *
	 * If this row has not been displayed, an empty set is returned to reduce
	 * the number of required HTTP requests.
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 *                               this row.
	 */
	public function getHtmlHeadEntrySet()
	{
		if ($this->isDisplayed()) {
			$set = new SwatHtmlHeadEntrySet($this->html_head_entry_set);
		} else {
			$set = new SwatHtmlHeadEntrySet();
		}

		return $set;
	}

	/**
	 * Gets the SwatHtmlHeadEntry objects that may be needed by this row
	 *
	 * @return SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects that may be
	 *                               needed by this row.
	 */
	public function getAvailableHtmlHeadEntrySet()
	{
		return new SwatHtmlHeadEntrySet($this->html_head_entry_set);
	}

	/**
	 * Gathers all messages from this table-view row
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 */
	public function getMessages()
	{
		return array();
	}

	/**
	 * Gets whether or this row has any messages
	 *
	 * @return boolean true if this table-view row has one or more messages
	 *                 and false if it does not.
	 */
	public function hasMessage()
	{
		return false;
	}

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

		if ($id_suffix != '' && $copy->id !== null)
			$copy->id = $copy->id.$id_suffix;

		return $copy;
	}

}

?>
