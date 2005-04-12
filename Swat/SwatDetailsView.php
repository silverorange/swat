<?php
require_once('Swat/SwatControl.php');
require_once('Swat/SwatHtmlTag.php');
require_once('Swat/SwatDetailsViewField.php');
require_once('Swat/SwatUIParent.php');

//TODO: finish documentation for public methods

/**
 * A widget to display field-value pairs
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatDetailsView extends SwatControl implements SwatUIParent {
	
	/**
	 * Object containing values to display
	 * @var array
	 */
	public $data = null;

	private $fields = array();

	/**
	 * Append Field
	 * @param SwatDetailViewField $field
	 */
	public function appendField(SwatDetailsViewField $field) {
		$this->fields[] = $field;

		$field->view = $this;
	}

	/**
	 * Count fields
	 * @return int Number of fields in the view.
	 */
	public function getFieldCount() {
		return count($this->fields);
	}

	/**
	 * Get fields
	 * @return array Array of fields in the view.
	 */
	public function &getFields() {
		return $this->fields;
	}

	public function display() {
		if (!$this->visible)
			return;

		$table_tag = new SwatHtmlTag('table');
		$table_tag->class = 'swat-detail-view';
		//$table_tag->border = 1;

		$table_tag->open();
		$this->displayContent();
		$table_tag->close();
	}

	private function displayContent() {
		$count = 0;
		$tr_tag = new SwatHtmlTag('tr');

		foreach ($this->fields as $field) {

			$count++;
			$tr_tag->class = ($count % 2 == 1)? 'odd': null;
			$tr_tag->open();

			$field->display($this->data);

			$tr_tag->close();
		}
	}

	/**
	 * Add a child object
	 * 
	 * This method fulfills the {@link SwatUIParent} interface.  It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere.  To add a field to a field view, use 
	 * {@link SwatFieldView::appendField()}.
	 *
	 * @param $child A reference to a child object to add.
	 */
	public function addChild($child) {

		if ($child instanceof SwatDetailsViewField)
			$this->appendField($child);
		else
			throw new SwatException('SwatDetailsView: Only '.
				'SwatDetailsViewFields can be nested within SwatDetailsViews');
	}

}
?>
