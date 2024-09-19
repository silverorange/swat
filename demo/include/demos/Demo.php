<?php

/**
 * A demo in the Swat Demo Application.
 *
 * Each demo class is responsible for implementing the buildDemoUI() method
 * which does specialized initialization of the UI for the particular demo.
 *
 * @copyright 2007-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class Demo
{
    /**
     * Sets up the demo ui for this particular demo.
     *
     * @param SwatUI $ui the UI that is to be setup
     */
    abstract public function buildDemoUI(SwatUI $ui);
}
