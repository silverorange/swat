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
	// {{{ private properties

	private $ui;
	private $demo;

	private $available_demos = array(
		'Accordion'         => 'SwatAccordion',
		'Button'            => 'SwatButton',
		'Calendar'          => 'SwatCalendar',
		'ChangeOrder'       => 'SwatChangeOrder',
		'Checkbox'          => 'SwatCheckbox',
		'ColorEntry'        => 'SwatColorEntry',
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
		'Menu'              => 'SwatMenu',
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
	);

	// }}}
	// {{{ public function run()

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

	// }}}
	// {{{ private function buildTitle()

	private function buildTitle()
	{
		$this->layout_ui->getWidget('main_frame')->title =
			sprintf(Swat::_('%s Demo'), $this->available_demos[$this->demo]);
	}

	// }}}
	// {{{ private function buildDemo()

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

	// }}}
	// {{{ private function buildXmlSourceView()

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

	// }}}
	// {{{ private function buildPhpSourceView()

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

	// }}}
	// {{{ private function buildDemoMenuBar()

	private function buildDemoMenuBar()
	{
		$this->layout_ui->getWidget('menu')->setEntries($this->available_demos);
		$this->layout_ui->getWidget('menu')->setSelectedEntry($this->demo);
	}

	// }}}
	// {{{ private function buildDemoNavBar()

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

	// }}}
	// {{{ private function buildDemoDocumentationMenuBar()

	private function buildDemoDocumentationMenuBar()
	{
		switch ($this->demo) {
		case 'Accordion':
			$entries = array(
				'SwatAccordion',
				'SwatNoteBookPage'
				);
			break;

		case 'Button':
			$entries = array(
				'SwatButton',
				'SwatConfirmationButton'
				);
			break;

		case 'Calendar':
			$entries = array('SwatCalendar');
			break;

		case 'ChangeOrder':
			$entries = array('SwatChangeOrder');
			break;

		case 'Checkbox':
			$entries = array(
				'SwatCheckbox',
				'SwatCheckboxList',
				'SwatCheckboxEntryList',
				'SwatCheckboxTree',
				'SwatExpandableCheckboxTree',
				);
			break;

		case 'ColorEntry':
			$entries = array(
				'SwatColorEntry',
				'SwatSimpleColorEntry'
				);
			break;

		case 'DateEntry':
			$entries = array('SwatDateEntry');
			break;

		case 'DetailsView':
			$entries = array(
				'SwatDetailsView',
				'SwatDetailsViewField',
				'SwatDetailsViewVerticalField',
				'SwatBooleanCellRenderer',
				'SwatDateCellRenderer',
				'SwatImageCellRenderer',
				'SwatMoneyCellRenderer',
				'SwatTextCellRenderer'
				);
			break;

		case 'Disclosure':
			$entries = array(
				'SwatDisclosure',
				'SwatFrameDisclosure',
				);
			break;

		case 'Entry':
			$entries = array(
				'SwatEntry',
				'SwatListEntry',
				'SwatEmailEntry',
				'SwatIntegerEntry',
				'SwatFloatEntry',
				'SwatMoneyEntry'
				);
			break;

		case 'Fieldset':
			$entries = array('SwatFieldset');
			break;

		case 'FileEntry':
			$entries = array('SwatFileEntry');
			break;

		case 'Flydown':
			$entries = array(
				'SwatFlydown',
				'SwatFlydownTree',
				'SwatGroupedFlydown',
				'SwatCascadeFlydown',
				'SwatFlydownDivider',
				'SwatTreeFlydownNode',
				'SwatOption',
				);
			break;

		case 'Frame':
			$entries = array('SwatFrame');
			break;

		case 'ImageCropper':
			$entries = array('SwatImageCropper');
			break;

		case 'ImageDisplay':
			$entries = array(
				'SwatImageDisplay',
				'SwatImagePreviewDisplay',
				);
			break;

		case 'MessageDisplay':
			$entries = array('SwatMessageDisplay');
			break;

		case 'NavBar':
			$entries = array(
				'SwatNavBar',
				'SwatNavBarEntry',
				);
			break;

		case 'NoteBook':
			$entries = array(
				'SwatNoteBook',
				'SwatNoteBookPage',
				);
			break;

		case 'Pagination':
			$entries = array('SwatPagination');
			break;

		case 'PasswordEntry':
			$entries = array(
				'SwatPasswordEntry',
				'SwatConfirmPasswordEntry'
				);
			break;

		case 'ProgressBar':
			$entries = array(
				'SwatProgressBar',
				);
			break;

		case 'RadioList':
			$entries = array(
				'SwatRadioList',
				'SwatRadioTable',
				);
			break;

		case 'Replicable':
			$entries = array(
				'SwatReplicable',
				'SwatReplicableContainer',
				'SwatReplicableFieldset',
				'SwatReplicableFormField',
				);
			break;

		case 'SelectListDemo':
			$entries = array('SwatSelectList');
			break;

		case 'StringDemo':
			$entries = array('SwatString');
			break;

		case 'TableView':
			$entries = array(
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
				);
			break;

		case 'TableViewInputRow':
			$entries = array(
				'SwatTableView',
				'SwatTableViewInputRow',
				'SwatInputCell',
				'SwatRemoveInputCell',
				);
			break;

		case 'Textarea':
			$entries = array(
				'SwatTextarea',
				'SwatXHTMLTextarea',
				);
			break;

		case 'TextareaEditor':
			$entries = array('SwatTextareaEditor');
			break;

		case 'TileView':
			$entries = array(
				'SwatTileView',
				'SwatTile',
				'SwatTableStore',
				'SwatCheckboxCellRenderer',
				'SwatDateCellRenderer',
				'SwatTextCellRenderer',
				);
			break;

		case 'TimeEntry':
			$entries = array('SwatTimeEntry');
			break;

		case 'TimeZoneEntry':
			$entries = array('SwatTimeZoneEntry');
			break;

		case 'ToolLink':
			$entries = array('SwatToolLink');
			break;

		case 'ViewSelector':
			$entries = array(
				'SwatCheckboxCellRenderer',
				'SwatRadioButtonCellRenderer',
				'SwatView',
				'SwatViewSelection',
				'SwatViewSelector',
				);
			break;

		case 'YesNoFlydown':
			$entries = array('SwatYesNoFlydown');
			break;

		default:
			$entries = array();
			break;
		}

		$documentation_links =
			$this->layout_ui->getWidget('documentation_links');

		$documentation_links->setEntries($entries);
	}

	// }}}
	// {{{ private function buildFrontPage()

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

	// }}}
	// {{{ private function buildLayout()

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

	// }}}
	// {{{ private function getDemo()

	/**
	 * Gets the demo page
	 */
	private function getDemo()
	{
		$demo = isset($_GET['demo']) ? $_GET['demo'] : null;

		// simple security
		if (!array_key_exists($demo, $this->available_demos))
			$demo = null;

		return $demo;
	}

	// }}}
}

?>
