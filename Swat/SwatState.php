<?php

/**
 * Interface for controls that can store and restore their state.
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
interface SwatState
{


    /**
     * Set the state of the control
     *
     * Used to set the current state of the control back to a stored state.
     * This implementation of this method should correspond to the
     * implementation of getState(). Sub-classes should override and implement
     * this method to store their state.
     *
     * @param mixed $state The state to load into the control.
     */
    public function setState($state);



    /**
     * Get the state of the control
     *
     * Used to remember the current state of the control. For example,
     * {@link SwatEntry} implements this method to return its $value property,
     * but can return any variable type, including array, that represents the
     * control's current state. Sub-classes should override and implement this
     * method to store their state.
     *
     * @return mixed The current state of the control.
     */
    public function getState();

}
