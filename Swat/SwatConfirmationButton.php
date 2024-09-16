<?php

/**
 * A button widget with a javascript confirmation dialog
 *
 * This widget displays as an XHTML form submit button, so it should be used
 * within {@link SwatForm}.
 *
 * @package   Swat
 * @copyright 2004-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @deprecated Confirmation functionality has been moved into SwatButton.
 * @see        SwatButton
 */
class SwatConfirmationButton extends SwatButton
{
    // {{{ public function __construct()

    /**
     * Creates a new confirmation button widget
     *
     * @see SwatWidget::__construct()
     */
    public function __construct($id = null)
    {
        parent::__construct($id);

        $this->confirmation_message = Swat::_(
            'Are you sure you wish to continue?',
        );
    }

}
