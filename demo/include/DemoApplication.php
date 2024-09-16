<?php

/**
 * A demo application
 *
 * This is an application to demonstrate various Swat widgets.
 *
 * @package   SwatDemo
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class DemoApplication
{


	private $ui;
	private $demo;

	private $available_demos = [
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



	/**
	 * test
	 */
	public function run()
	{
		SwatError::setupHandler();
		$this->layout_ui = new SwatUI();
		$this->layout_ui->mapClassPrefixToPath('Demo', '../include/ui-objects');
		$this->layout_ui->loadFromXML('../include/layout.xml');

		$this->demo = $this->getDemo();

		if ($this->demo === null) {
			$this->buildFrontPage();
			$title = Swat::_('Swat Demo');
		} else {
			$this->buildTitle();
			$this->buildDemo();
			$this->buildDemoDocumentationMenuBar();
			$this->buildXmlSourceView();
			$this->buildPhpSourceView();
			$title = sprintf(Swat::_('%s - Swat Demo'),
				$this->available_demos[$this->demo]);
		}

		$this->buildDemoMenuBar();
		$this->buildDemoNavBar();

		$this->layout_ui->process();

		$this->buildLayout();
	}



	private function buildTitle()
	{
		$this->layout_ui->getWidget('main_frame')->title =
			sprintf(Swat::_('%s Demo'), $this->available_demos[$this->demo]);
	}



	private function buildDemo()
	{
		$this->demo_ui = new SwatUI($this->layout_ui->getWidget('main_frame'));
		$this->demo_ui->loadFromXML(
			'../include/demos/'.mb_strtolower($this->demo).'.xml');

		if (file_exists(__DIR__.'/../include/demos/'.$this->demo.'Demo.php')) {
			require_once __DIR__.'/../include/demos/'.$this->demo.'Demo.php';

			$class_name = $this->demo.'Demo';
			if (class_exists($class_name)) {
				$demo = new $class_name();
				$demo->buildDemoUI($this->demo_ui);
			}
		}
	}



	private function buildXmlSourceView()
	{
		$filename = '../include/demos/'.mb_strtolower($this->demo).'.xml';
		if (file_exists($filename)) {
			$this->layout_ui->getWidget('xml_source_container')->visible = true;

			$code = file_get_contents($filename);
			$code = str_replace("\t", '  ', $code);
			$code = highlight_string($code, true);

			$pre_tag = new SwatHtmlTag('pre');
			$pre_tag->setContent($code, 'text/xml');

			$this->layout_ui->getWidget('xml_source_view')->content =
				$pre_tag->toString();
		}
	}



	private function buildPhpSourceView()
	{
		$filename = '../include/demos/'.$this->demo.'Demo.php';
		if (file_exists($filename)) {
			$this->layout_ui->getWidget('php_source_container')->visible = true;

			$code = file_get_contents($filename);
			$code = str_replace("\t", '  ', $code);
			$code = highlight_string($code, true);
			$pre_tag = new SwatHtmlTag('pre');
			$pre_tag->setContent($code, 'text/xml');

			$this->layout_ui->getWidget('php_source_view')->content =
				$pre_tag->toString();
		}
	}



	private function buildDemoMenuBar()
	{
		$this->layout_ui->getWidget('menu')->setEntries($this->available_demos);
		$this->layout_ui->getWidget('menu')->setSelectedEntry($this->demo);
	}



	private function buildDemoNavBar()
	{
		$navbar = $this->layout_ui->getWidget('navbar');
		if ($this->demo) {
			$title = $this->available_demos[$this->demo];
			$navbar->addEntry(new SwatNavBarEntry('Swat Demos', '.'));
			$navbar->addEntry(new SwatNavBarEntry($title));
		} else {
			$navbar->addEntry(new SwatNavBarEntry('Swat Demos'));
		}
	}



	private function buildDemoDocumentationMenuBar()
	{
		switch ($this->demo) {
		case 'Accordion':
			$entries = [
                'SwatAccordion',
                'SwatNoteBookPage'
            ];
			break;

		case 'Button':
			$entries = [
                'SwatButton',
                'SwatConfirmationButton'
            ];
			break;

		case 'Calendar':
			$entries = ['SwatCalendar'];
			break;

		case 'ChangeOrder':
			$entries = ['SwatChangeOrder'];
			break;

		case 'Checkbox':
			$entries = [
                'SwatCheckbox',
                'SwatCheckboxList',
                'SwatCheckboxEntryList',
                'SwatCheckboxTree'
            ];
			break;

		case 'DateEntry':
			$entries = ['SwatDateEntry'];
			break;

		case 'DetailsView':
			$entries = [
                'SwatDetailsView',
                'SwatDetailsViewField',
                'SwatDetailsViewVerticalField',
                'SwatBooleanCellRenderer',
                'SwatDateCellRenderer',
                'SwatImageCellRenderer',
                'SwatMoneyCellRenderer',
                'SwatTextCellRenderer'
            ];
			break;

		case 'Disclosure':
			$entries = [
                'SwatDisclosure',
                'SwatFrameDisclosure'
            ];
			break;

		case 'Entry':
			$entries = [
                'SwatEntry',
                'SwatListEntry',
                'SwatEmailEntry',
                'SwatIntegerEntry',
                'SwatFloatEntry',
                'SwatMoneyEntry'
            ];
			break;

		case 'Fieldset':
			$entries = ['SwatFieldset'];
			break;

		case 'FileEntry':
			$entries = ['SwatFileEntry'];
			break;

		case 'Flydown':
			$entries = [
                'SwatFlydown',
                'SwatFlydownTree',
                'SwatGroupedFlydown',
                'SwatCascadeFlydown',
                'SwatFlydownDivider',
                'SwatTreeFlydownNode',
                'SwatOption'
            ];
			break;

		case 'Frame':
			$entries = ['SwatFrame'];
			break;

		case 'ImageCropper':
			$entries = ['SwatImageCropper'];
			break;

		case 'ImageDisplay':
			$entries = [
                'SwatImageDisplay',
                'SwatImagePreviewDisplay'
            ];
			break;

		case 'MessageDisplay':
			$entries = ['SwatMessageDisplay'];
			break;

		case 'NavBar':
			$entries = [
                'SwatNavBar',
                'SwatNavBarEntry'
            ];
			break;

		case 'NoteBook':
			$entries = [
                'SwatNoteBook',
                'SwatNoteBookPage'
            ];
			break;

		case 'Pagination':
			$entries = ['SwatPagination'];
			break;

		case 'PasswordEntry':
			$entries = [
                'SwatPasswordEntry',
                'SwatConfirmPasswordEntry'
            ];
			break;

		case 'ProgressBar':
			$entries = ['SwatProgressBar'];
			break;

		case 'RadioList':
			$entries = [
                'SwatRadioList',
                'SwatRadioTable'
            ];
			break;

		case 'Replicable':
			$entries = [
                'SwatReplicable',
                'SwatReplicableContainer',
                'SwatReplicableFieldset',
                'SwatReplicableFormField'
            ];
			break;

		case 'SelectListDemo':
			$entries = ['SwatSelectList'];
			break;

		case 'SimpleColorEntry':
			$entries = ['SwatSimpleColorEntry'];
			break;

		case 'StringDemo':
			$entries = ['SwatString'];
			break;

		case 'TableView':
			$entries = [
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
                'SwatActionItem'
            ];
			break;

		case 'TableViewInputRow':
			$entries = [
                'SwatTableView',
                'SwatTableViewInputRow',
                'SwatInputCell',
                'SwatRemoveInputCell'
            ];
			break;

		case 'Textarea':
			$entries = [
                'SwatTextarea',
                'SwatXHTMLTextarea'
            ];
			break;

		case 'TextareaEditor':
			$entries = ['SwatTextareaEditor'];
			break;

		case 'TileView':
			$entries = [
                'SwatTileView',
                'SwatTile',
                'SwatTableStore',
                'SwatCheckboxCellRenderer',
                'SwatDateCellRenderer',
                'SwatTextCellRenderer'
            ];
			break;

		case 'TimeEntry':
			$entries = ['SwatTimeEntry'];
			break;

		case 'TimeZoneEntry':
			$entries = ['SwatTimeZoneEntry'];
			break;

		case 'ToolLink':
			$entries = ['SwatToolLink'];
			break;

		case 'ViewSelector':
			$entries = [
                'SwatCheckboxCellRenderer',
                'SwatRadioButtonCellRenderer',
                'SwatView',
                'SwatViewSelection',
                'SwatViewSelector'
            ];
			break;

		case 'YesNoFlydown':
			$entries = ['SwatYesNoFlydown'];
			break;

		default:
			$entries = [];
			break;
		}

		$documentation_links =
			$this->layout_ui->getWidget('documentation_links');

		$documentation_links->setEntries($entries);
	}



	private function buildFrontPage()
	{
		$content_block = new SwatContentBlock();
		$content_block->content =
			'This Swat demo site includes examples of Swat widgets and '.
			'classes. Each demo includes the SwatML source and links to '.
			'the related documentation for the classes used.';

		$main_frame = $this->layout_ui->getWidget('main_frame');
		$main_frame->title = 'Swat Demos';
		$main_frame->add($content_block);
	}



	private function buildLayout()
	{
		if ($this->demo === null) {
			$title = Swat::_('Swat Demo');
		} else {
			$title = sprintf(Swat::_('%s - Swat Demo'),
				$this->available_demos[$this->demo]);
		}

		$concentrator = new Concentrate_Concentrator();
		$displayer = new SwatHtmlHeadEntrySetDisplayer($concentrator);

		ob_start();
		$this->layout_ui->display();
		$ui = ob_get_clean();

		ob_start();
		$displayer->display($this->layout_ui->getRoot()->getHtmlHeadEntrySet());
		$html_head_entries = ob_get_clean();

		require '../include/layout.php';
	}



	/**
	 * Gets the demo page
	 */
	private function getDemo()
	{
		$demo = $_GET['demo'] ?? null;

		// simple security
		if (!array_key_exists($demo, $this->available_demos))
			$demo = null;

		return $demo;
	}

}

?>
