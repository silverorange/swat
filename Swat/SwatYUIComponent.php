<?php

/**
 * A component in the Yahoo UI Library
 *
 * This class is used internally by the {@link SwatYUI} class and is not meant
 * to be used by itself.
 *
 * @package   Swat
 * @copyright 2006-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatYUI
 */
class SwatYUIComponent extends SwatObject
{
    // {{{ private properties

    private $id;
    private $dependencies = [];
    private $html_head_entries = [];
    private $beta = false;

    // }}}
    // {{{ public function __construct()

    /**
     * Creates a new YUI component
     *
     * @param string $id the identifier of this YUI component. This corresponds
     *                    to a directory name under the <i>build</i> directory
     *                    in the YUI distribution.
     * @param boolean $beta optional. Whether or not this component is in beta.
     *                       Beta components have a slightly different naming
     *                       convention.
     */
    public function __construct($id, $beta = false)
    {
        $this->id = $id;
        $this->beta = $beta;

        $this->html_head_entry_set['normal'] = new SwatHtmlHeadEntrySet();

        $this->html_head_entry_set['debug'] = new SwatHtmlHeadEntrySet();

        $this->html_head_entry_set['min'] = new SwatHtmlHeadEntrySet();
    }

    // }}}
    // {{{ public function addDependency()

    /**
     * Adds a YUI component dependency to this YUI component
     *
     * @param SwatYUIComponent the YUI component this component depends on.
     */
    public function addDependency(SwatYUIComponent $component)
    {
        $this->dependencies[] = $component;
    }

    // }}}
    // {{{ public function addJavaScript()

    /**
     * Adds a {@link SwatJavaScriptHtmlHeadEntry} to this YUI component
     *
     * YUI component JavaScript is distributed in three modes:
     * - debug
     * - min
     * - normal
     *
     * Adding JavaScript using this method creates HTML head entries for each
     * of these three modes.
     *
     * @param string $component_directory optional. The YUI component directory
     *                                     the JavaScript exists in. If the
     *                                     directory is not specified, this
     *                                     component's id is used.
     * @param string $filename optional. The filename of the YUI JavaScript for
     *                          this component. If not specified, the name of
     *                          the component is used. Do not specify the file
     *                          extension or the -min/-debug suffix here.
     */
    public function addJavaScript($component_directory = '', $filename = '')
    {
        if ($component_directory == '') {
            $component_directory = $this->id;
        }

        if ($filename == '') {
            $filename = $this->id;
        }

        $modes = [
            'min' => '-min',
            'debug' => '-debug',
            'normal' => '',
        ];

        if ($this->beta) {
            $filename_template =
                'packages/yui/' .
                $component_directory .
                '/' .
                $filename .
                '-beta%s.js';
        } else {
            $filename_template =
                'packages/yui/' .
                $component_directory .
                '/' .
                $filename .
                '%s.js';
        }

        foreach ($modes as $mode => $suffix) {
            $filename = sprintf($filename_template, $suffix);
            $this->html_head_entry_set[$mode]->addEntry($filename);
        }
    }

    // }}}
    // {{{ public function addStyleSheet()

    /**
     * Adds a {@link SwatStyleSheetHtmlHeadEntry} to this YUI component
     *
     * YUI component style sheets are distributed in three modes:
     * - min
     * - normal
     *
     * Adding style sheets using this method creates HTML head entries for
     * these two modes.
     *
     * @param string $component_directory optional. The YUI component directory
     *                                     the style sheet exists in. If the
     *                                     directory is not specified, this
     *                                     component's id is used.
     * @param string $filename optional. The filename of the YUI style-sheet for
     *                          this component. If not specified, the name of
     *                          the component is used. Do not specify the file
     *                          extension or the -min  suffix here.
     * @param boolean $has_min_version optional. Whether or not the style-sheet
     *                                  for this component has a minimized
     *                                  version in the YUI distribution.
     *                                  Defaults to true.
     */
    public function addStyleSheet(
        $component_directory = '',
        $filename = '',
        $has_min_version = true,
    ) {
        if ($component_directory == '') {
            $component_directory = $this->id;
        }

        if ($filename == '') {
            $filename = $this->id;
        }

        $modes = [
            'min' => '-min',
            'debug' => '',
            'normal' => '',
        ];

        if (!$has_min_version) {
            $modes['min'] = '';
        }

        $filename_template =
            'packages/yui/' . $component_directory . '/' . $filename . '%s.css';

        foreach ($modes as $mode => $suffix) {
            $filename = sprintf($filename_template, $suffix);
            $this->html_head_entry_set[$mode]->addEntry($filename);
        }
    }

    // }}}
    // {{{ public function getHtmlHeadEntrySet()

    /**
     * Gets the set of {@link SwatHtmlHeadEntry} objects required for this
     * YUI component
     *
     * @return SwatHtmlHeadEntrySet the set of {@link SwatHtmlHeadEntry}
     *                               objects required for this YUI component.
     */
    public function getHtmlHeadEntrySet($mode = 'min')
    {
        $set = new SwatHtmlHeadEntrySet();
        if (isset($this->html_head_entry_set[$mode])) {
            foreach ($this->dependencies as $component) {
                $set->addEntrySet($component->getHtmlHeadEntrySet($mode));
            }
            $set->addEntrySet($this->html_head_entry_set[$mode]);
        }

        return $set;
    }

    // }}}
}
