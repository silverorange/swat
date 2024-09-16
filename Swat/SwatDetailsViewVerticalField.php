<?php

/**
 * A visible field in a SwatDetailsView that has its label displayed above
 * its content
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatDetailsViewVerticalField extends SwatDetailsViewField
{


    /**
     * Displays this details view field using a data object
     *
     * @param mixed $data a data object used to display the cell renderers in
     *                      this field.
     * @param boolean $odd whether this is an odd or even field so alternating
     *                      style can be applied.
     *
     * @see SwatDetailsViewField::display()
     */
    public function display($data, $odd)
    {
        if (!$this->visible) {
            return;
        }

        $this->odd = $odd;

        $tr_tag = new SwatHtmlTag('tr');
        $tr_tag->id = $this->id;
        $tr_tag->class = $this->getCSSClassString();

        $td_tag = new SwatHtmlTag('td');
        $td_tag->colspan = 2;

        $tr_tag->open();
        $td_tag->open();
        $this->displayHeader();
        $this->displayValue($data);
        $td_tag->close();
        $tr_tag->close();
    }



    /**
     * Displays the header for this details view field
     *
     * @see SwatDetailsViewField::displayHeader()
     */
    public function displayHeader()
    {
        if ($this->title != '') {
            $div_tag = new SwatHtmlTag('div');
            $div_tag->class = 'swat-details-view-field-header';
            $div_tag->setContent(
                $this->getHeaderTitle(),
                $this->title_content_type,
            );

            $div_tag->display();
        }
    }



    /**
     * Renders each cell renderer in this details-view field
     *
     * @param mixed $data the data object being used to render the cell
     *                     renderers of this field.
     *
     * @see SwatDetailsViewField::displayRenderers()
     */
    protected function displayRenderers($data)
    {
        $div_tag = new SwatHtmlTag('div');
        $div_tag->open();

        foreach ($this->renderers as $renderer) {
            $renderer->render();
            echo ' ';
        }

        $div_tag->close();
    }



    /**
     * Gets the base CSS class names of this details-view field
     *
     * @return array the array of base CSS class names for this vertical
     *                details-view field.
     */
    protected function getBaseCSSClassNames()
    {
        return ['swat-details-view-vertical-field'];
    }

}
