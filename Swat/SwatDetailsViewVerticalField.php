<?php

require_once 'Swat/SwatDetailsViewField.php';

/**
 * A visible field in a SwatDetailsView that has its label displayed above
 * its content
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsViewverticalField extends SwatDetailsViewField
{
	/**
	 * Displays this details view field using a data object
	 *
	 * @param mixedt $data a data object used to display the cell renderers in
	 *                      this field.
	 *
	 * @see SwatDetailsViewField::display()
	 */
	public function display($data)
	{
		if (!$this->visible)
			return;

		$tr_tag = new SwatHtmlTag('tr');

		$tr_tag->open();
		$this->displayHeader();
		$tr_tag->close();

		$tr_tag->open();
		$this->displayValue($data);
		$tr_tag->close();
	}

	/**
	 * Displays the header for this details view field
	 *
	 * @see SwatDetailsViewField::displayHeader()
	 */
	public function displayHeader()
	{
		$th_tag = new SwatHtmlTag('th');
		$th_tag->colspan = '2';
		$th_tag->align = 'left';
		$th_tag->content = $this->title.':';
		$th_tag->display();
	}

	/**
	 * Renders each cell renderer in this details-view field
	 *
	 * The properties of the cell renderers are set the the fields of the
	 * data object through the datafield property mappings.
	 *
	 * @param mixed $data the data object to render with the cell renderers
	 *                     of this field.
	 *
	 * @see SwatDetailsViewField::displayRenderers()
	 */
	protected function displayRenderers($data)
	{
		$first_renderer = $this->renderers->getFirst();
		$td_tag = new SwatHtmlTag('td', $first_renderer->getTdAttributes());
		$td_tag->colspan = '2';
		$td_tag->open();

		foreach ($this->renderers as $renderer) {
			$renderer->render();
			echo ' ';
		}

		$td_tag->close();
	}
}

?>
