<?php

/**
 * A demo application.
 *
 * This is an application to demonstrate various Swat widgets.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoApplication
{
    private SwatUI $layout_ui;
    private ?string $demo_page = null;

    private array $available_demos = [
        'Accordion'         => 'SwatAccordion',
        'Button'            => 'SwatButton',
        'Calendar'          => 'SwatCalendar',
        'ChangeOrder'       => 'SwatChangeOrder',
        'Checkbox'          => 'SwatCheckbox',
        'DateEntry'         => 'SwatDateEntry',
        'DetailsView'       => 'SwatDetailsView',
        'Disclosure'        => 'SwatDisclosure',
        'Entry'             => 'SwatEntry',
        'Fieldset'          => 'SwatFieldset',
        'FileEntry'         => 'SwatFileEntry',
        'Flydown'           => 'SwatFlydown',
        'Frame'             => 'SwatFrame',
        'ImageCropper'      => 'SwatImageCropper',
        'ImageDisplay'      => 'SwatImageDisplay',
        'MessageDisplay'    => 'SwatMessageDisplay',
        'NavBar'            => 'SwatNavBar',
        'NoteBook'          => 'SwatNoteBook',
        'Pagination'        => 'SwatPagination',
        'PasswordEntry'     => 'SwatPasswordEntry',
        'ProgressBar'       => 'SwatProgressBar',
        'RadioList'         => 'SwatRadioList',
        'Rating'            => 'SwatRating',
        'Replicable'        => 'SwatReplicable',
        'SelectList'        => 'SwatSelectList',
        'SimpleColorEntry'  => 'SwatSimpleColorEntry',
        'String'            => 'SwatString',
        'TableView'         => 'SwatTableView',
        'TableViewInputRow' => 'SwatTableViewInputRow',
        'Textarea'          => 'SwatTextarea',
        'TextareaEditor'    => 'SwatTextareaEditor',
        'TileView'          => 'SwatTileView',
        'TimeEntry'         => 'SwatTimeEntry',
        'TimeZoneEntry'     => 'SwatTimeZoneEntry',
        'ToolLink'          => 'SwatToolLink',
        'ViewSelector'      => 'SwatViewSelector',
        'YesNoFlydown'      => 'SwatYesNoFlydown',
    ];

    public function run(): void
    {
        SwatError::setupHandler();

        require_once __DIR__ . '/ui-objects/DemoDocumentationMenuBar.php';
        require_once __DIR__ . '/ui-objects/DemoMenuBar.php';

        $this->layout_ui = new SwatUI();
        $this->layout_ui->loadFromXML(__DIR__ . '/layout.xml');

        $this->demo_page = $this->getDemo();

        if ($this->demo_page === null) {
            $this->buildFrontPage();
            $title = Swat::_('Swat Demo');
        } else {
            $this->buildTitle();
            $this->buildDemo();
            $this->buildDemoDocumentationMenuBar();
            $this->buildSourceView(
                __DIR__ . '/demos/' . mb_strtolower($this->demo_page) . '.xml',
                'xml'
            );
            $this->buildSourceView(
                __DIR__ . '/demos/' . $this->demo_page . 'Demo.php',
                'php'
            );

            $title = sprintf(
                Swat::_('%s - Swat Demo'),
                $this->available_demos[$this->demo_page]
            );
        }

        $this->buildDemoMenuBar();
        $this->buildDemoNavBar();

        $this->layout_ui->process();

        $this->buildLayout();
    }

    private function buildTitle(): void
    {
        $this->layout_ui->getWidget('main_frame')->title
            = sprintf(Swat::_('%s Demo'), $this->available_demos[$this->demo_page]);
    }

    private function buildDemo(): void
    {
        $demo_ui = new SwatUI($this->layout_ui->getWidget('main_frame'));
        $demo_ui->loadFromXML(
            __DIR__ . '/demos/' . mb_strtolower($this->demo_page) . '.xml'
        );

        $class_name = $this->demo_page . 'Demo';

        if (file_exists(__DIR__ . '/demos/' . $class_name . '.php')) {
            require_once __DIR__ . '/demos/' . $class_name . '.php';

            if (class_exists($class_name)) {
                $demo = new $class_name();
                $demo->buildDemoUI($demo_ui);
            }
        }
    }

    private function buildSourceView(string $filename, string $language): void
    {
        if (!file_exists($filename)) {
            return;
        }

        $this->layout_ui->getWidget("{$language}_source_container")->visible = true;

        $code = file_get_contents($filename);

        $code_tag = new SwatHtmlTag('code');
        $code_tag->addAttributes([
            'class' => "language-{$language}",
        ]);
        $code_tag->setContent(SwatString::minimizeEntities($code), 'text/xml');

        $pre_tag = new SwatHtmlTag('pre');
        $pre_tag->setContent($code_tag->toString(), 'text/xml');

        $this->layout_ui->getWidget("{$language}_source_view")->content
            = $pre_tag->toString();
    }

    private function buildDemoMenuBar(): void
    {
        $this->layout_ui->getWidget('menu')->setEntries($this->available_demos);
        $this->layout_ui->getWidget('menu')->setSelectedEntry($this->demo_page);
    }

    private function buildDemoNavBar(): void
    {
        $navbar = $this->layout_ui->getWidget('navbar');
        if ($this->demo_page) {
            $title = $this->available_demos[$this->demo_page];
            $navbar->addEntry(new SwatNavBarEntry('Swat Demos', '.'));
            $navbar->addEntry(new SwatNavBarEntry($title));
        } else {
            $navbar->addEntry(new SwatNavBarEntry('Swat Demos'));
        }
    }

    private function buildDemoDocumentationMenuBar(): void
    {
        $entries = match ($this->demo_page) {
            'Accordion' => [
                'SwatAccordion',
                'SwatNoteBookPage',
            ],
            'Button' => [
                'SwatButton',
                'SwatConfirmationButton',
            ],
            'Calendar' => [
                'SwatCalendar',
            ],
            'ChangeOrder' => [
                'SwatChangeOrder',
            ],
            'Checkbox' => [
                'SwatCheckbox',
                'SwatCheckboxList',
                'SwatCheckboxEntryList',
                'SwatCheckboxTree',
            ],
            'DateEntry' => [
                'SwatDateEntry',
            ],
            'DetailsView' => [
                'SwatDetailsView',
                'SwatDetailsViewField',
                'SwatDetailsViewVerticalField',
                'SwatBooleanCellRenderer',
                'SwatDateCellRenderer',
                'SwatImageCellRenderer',
                'SwatMoneyCellRenderer',
                'SwatTextCellRenderer',
            ],
            'Disclosure' => [
                'SwatDisclosure',
                'SwatFrameDisclosure',
            ],
            'Entry' => [
                'SwatEntry',
                'SwatListEntry',
                'SwatEmailEntry',
                'SwatIntegerEntry',
                'SwatFloatEntry',
                'SwatMoneyEntry',
            ],
            'Fieldset' => [
                'SwatFieldset',
            ],
            'FileEntry' => [
                'SwatFileEntry',
            ],
            'Flydown' => [
                'SwatFlydown',
                'SwatFlydownTree',
                'SwatGroupedFlydown',
                'SwatCascadeFlydown',
                'SwatFlydownDivider',
                'SwatTreeFlydownNode',
                'SwatOption',
            ],
            'Frame' => [
                'SwatFrame'],
            'ImageCropper' => [
                'SwatImageCropper',
            ],
            'ImageDisplay' => [
                'SwatImageDisplay',
                'SwatImagePreviewDisplay',
            ],
            'MessageDisplay' => [
                'SwatMessageDisplay',
            ],
            'NavBar' => [
                'SwatNavBar',
                'SwatNavBarEntry',
            ],
            'NoteBook' => [
                'SwatNoteBook',
                'SwatNoteBookPage',
            ],
            'Pagination' => [
                'SwatPagination',
            ],
            'PasswordEntry' => [
                'SwatPasswordEntry',
                'SwatConfirmPasswordEntry',
            ],
            'ProgressBar' => [
                'SwatProgressBar',
            ],
            'RadioList' => [
                'SwatRadioList',
                'SwatRadioTable',
            ],
            'Replicable' => [
                'SwatReplicable',
                'SwatReplicableContainer',
                'SwatReplicableFieldset',
                'SwatReplicableFormField',
            ],
            'SelectListDemo' => [
                'SwatSelectList',
            ],
            'SimpleColorEntry' => [
                'SwatSimpleColorEntry',
            ],
            'StringDemo' => [
                'SwatString',
            ],
            'TableView' => [
                'SwatTableView',
                'SwatTableStore',
                'SwatTableViewColumn',
                'SwatTableViewCheckboxColumn',
                'SwatCheckboxCellRenderer',
                'SwatBooleanCellRenderer',
                'SwatDateCellRenderer',
                'SwatImageCellRenderer',
                'SwatMoneyCellRenderer',
                'SwatTextCellRenderer',
                'SwatActions',
                'SwatActionItem',
            ],
            'TableViewInputRow' => [
                'SwatTableView',
                'SwatTableViewInputRow',
                'SwatInputCell',
                'SwatRemoveInputCell',
            ],
            'Textarea' => [
                'SwatTextarea',
                'SwatXHTMLTextarea',
            ],
            'TextareaEditor' => [
                'SwatTextareaEditor',
            ],
            'TileView' => [
                'SwatTileView',
                'SwatTile',
                'SwatTableStore',
                'SwatCheckboxCellRenderer',
                'SwatDateCellRenderer',
                'SwatTextCellRenderer',
            ],
            'TimeEntry' => [
                'SwatTimeEntry',
            ],
            'TimeZoneEntry' => [
                'SwatTimeZoneEntry',
            ],
            'ToolLink' => [
                'SwatToolLink',
            ],
            'ViewSelector' => [
                'SwatCheckboxCellRenderer',
                'SwatRadioButtonCellRenderer',
                'SwatView',
                'SwatViewSelection',
                'SwatViewSelector',
            ],
            'YesNoFlydown' => [
                'SwatYesNoFlydown',
            ],
            default => [],
        };

        $documentation_links = $this->layout_ui->getWidget('documentation_links');

        $documentation_links->setEntries($entries);
    }

    private function buildFrontPage(): void
    {
        $content_block = new SwatContentBlock();
        $content_block->content
            = 'This Swat demo site includes examples of Swat widgets and '
            . 'classes. Each demo includes the SwatML source and links to '
            . 'the related documentation for the classes used.';

        $main_frame = $this->layout_ui->getWidget('main_frame');
        $main_frame->title = 'Swat Demos';
        $main_frame->add($content_block);
    }

    private function buildLayout(): void
    {
        if ($this->demo_page === null) {
            $title = Swat::_('Swat Demo');
        } else {
            $title = sprintf(
                Swat::_('%s - Swat Demo'),
                $this->available_demos[$this->demo_page]
            );
        }

        $concentrator = new Concentrate_Concentrator();
        $displayer = new SwatHtmlHeadEntrySetDisplayer($concentrator);

        ob_start();
        $this->layout_ui->display();
        $ui = ob_get_clean();

        ob_start();
        $displayer->display($this->layout_ui->getRoot()->getHtmlHeadEntrySet());
        $html_head_entries = ob_get_clean();

        require __DIR__ . '/layout.php';
    }

    /**
     * Gets the demo page.
     */
    private function getDemo(): ?string
    {
        $demo = $_GET['demo'] ?? null;

        // simple security
        if (!array_key_exists($demo, $this->available_demos)) {
            $demo = null;
        }

        return $demo;
    }
}
