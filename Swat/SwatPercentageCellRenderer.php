<?php

/**
 * A percentage cell renderer.
 *
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatPercentageCellRenderer extends SwatNumericCellRenderer
{
    /**
     * Renders the contents of this cell.
     *
     * @see SwatCellRenderer::render()
     */
    public function render()
    {
        if (!$this->visible) {
            return;
        }

        SwatCellRenderer::render();

        if ($this->value === null && $this->null_display_value !== null) {
            $this->renderNullValue();
        } else {
            $old_value = $this->value;
            $this->value = $this->value * 100;
            printf('%s%%', $this->getDisplayValue());
            $this->value = $old_value;
        }
    }
}
