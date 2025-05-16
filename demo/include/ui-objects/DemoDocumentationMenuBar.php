<?php

require_once 'DemoMenuBar.php';

/**
 * The menu for the Swat Demo Application.
 *
 * This is a simple menu that takes a flat array of titles and links and
 * displays them in an unordered list.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoDocumentationMenuBar extends DemoMenuBar
{
    // {{{ public function display()

    public function display()
    {
        if (count($this->entries) > 0) {
            $p_tag = new SwatHtmlTag('p');
            $p_tag->id = $this->id;
            $p_tag->class = 'demo-documentation-menu-bar';
            $p_tag->open();

            echo '<span class="menutitle">';
            echo Swat::ngettext(
                'Documentation Link',
                'Documentation Links',
                count($this->entries)
            );

            echo '</span>: ';

            $first = true;
            foreach ($this->entries as $class) {
                if (!$first) {
                    echo ', ';
                }
                $first = false;
                echo '<a href="http://docs.silverorange.com/swat/'
                    . strtolower($class) . '.html">' . $class . '</a>';
            }

            $p_tag->close();
        }
    }

    // }}}
}
