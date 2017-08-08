<?php

/**
 * A time zone selection widget
 *
 * @package   Swat
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatTimeZoneEntry extends SwatInputControl implements SwatState
{

	/**
	 * Time zone identifier
	 *
	 * The id of the selected time zone.
	 *
	 * @var string
	 */
	public $value = null;

	/**
	 * Time zone areas available for this time zone entry widget
	 *
	 * This is an array of flydown options. Areas are usually continents.
	 *
	 * @var array
	 */
	private $areas = array();

	/**
	 * Time zone regions available for this time zone entry widget
	 *
	 * This is an array of flydown options. Regions are usually cities.
	 *
	 * @var array
	 */
	private $regions = array();

	/**
	 * Creates a new time zone selector widget
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		static $area_whitelist = array(
			DateTimeZone::AFRICA,
			DateTimeZone::AMERICA,
			DateTimeZone::ANTARCTICA,
			DateTimeZone::ARCTIC,
			DateTimeZone::ASIA,
			DateTimeZone::ATLANTIC,
			DateTimeZone::AUSTRALIA,
			DateTimeZone::EUROPE,
			DateTimeZone::INDIAN,
			DateTimeZone::PACIFIC,
			DateTimeZone::UTC,
		);

		$time_zone_list = $this->parseAreaWhitelist($area_whitelist);
		$this->setAreas($time_zone_list);
	}

	/**
	 * Displays this time zone entry widget
	 *
	 * Outputs a cascading list of time zones.
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		parent::display();

		$areas_flydown = $this->getCompositeWidget('areas_flydown');
		$regions_flydown = $this->getCompositeWidget('regions_flydown');

		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open();

		$areas_flydown->value = $this->getArea($this->value);
		$areas_flydown->display();

		$regions_flydown->options = $this->regions;
		$regions_flydown->value = $this->getRegion($this->value);
		$regions_flydown->display();

		$div_tag->close();
	}

	/**
	 * Processes this time zone entry widget
	 *
	 * If any validation type errors occur, an error message is attached to
	 * this entry widget.
	 */
	public function process()
	{
		parent::process();

		$areas_flydown   = $this->getCompositeWidget('areas_flydown');
		$regions_flydown = $this->getCompositeWidget('regions_flydown');

		if ($areas_flydown->value === 'UTC') {
			$this->value = 'UTC';
		} elseif ($areas_flydown->value === null ||
			$regions_flydown->value === null) {
			$this->value = null;
		} else {
			$this->value = $areas_flydown->value.'/'.$regions_flydown->value;
		}

		if (!$this->required && $this->value === null && $this->isSensitive()) {
			return;
		} elseif ($this->value === null) {
			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, 'error'));
		} else {
			try {
				$time_zone = new DateTimeZone($this->value);
			} catch (Exception $e) {
				$message = Swat::_('The %s field is an invalid time zone.');
				$this->addMessage(new SwatMessage($message, 'error'));
			}
		}
	}

	/**
	 * Gets the current state of this time zone entry widget
	 *
	 * @return string the current state of this time zone entry widget.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->value;
	}

	/**
	 * Sets the current state of this time zone entry widget
	 *
	 * @param string $state the new state of this time zone entry widget.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->value = $state;
	}

	/**
	 * Gets the array of CSS classes that are applied to this time zone entry
	 * widget
	 *
	 * @return array the array of CSS classes that are applied to this time
	 *                zone entry widget.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-time zone-entry');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	/**
	 * Creates all internal widgets required for this time zone entry
	 */
	protected function createCompositeWidgets()
	{
		$areas_flydown = new SwatFlydown($this->id.'_areas');
		$areas_flydown->addOptionsByArray($this->areas);
		$areas_flydown->show_blank = true;
		$areas_flydown->blank_title = Swat::_('choose region …');
		$this->addCompositeWidget($areas_flydown, 'areas_flydown');

		$regions_flydown = new SwatCascadeFlydown($this->id.'_regions');
		$regions_flydown->show_blank = true;
		$regions_flydown->blank_value = null;
		$regions_flydown->cascade_from = $areas_flydown;
		$regions_flydown->width = '15em';
		$this->addCompositeWidget($regions_flydown, 'regions_flydown');
	}

	/**
	 * Parses a whitelist of valid areas
	 *
	 * Filters the full list of areas down to a select list and returns a
	 * tree-structured array of areas, regions, and subregions.
	 *
	 * @param array area_whitelist an array of valid area names.
	 *
	 * @return array a tree-structured array of areas regions and subregions
	 *                inside the specified whitelist of areas.
	 */
	private function parseAreaWhitelist($area_whitelist)
	{
		$areas = array();

		$whitelist = 0;
		foreach ($area_whitelist as $area) {
			$whitelist = $whitelist | $area;
		}

		$tz_data = DateTimeZone::listIdentifiers($whitelist);

		foreach ($tz_data as $id) {
			$area = $this->getArea($id);
			$region = $this->getRegion($id);

			if (!array_key_exists($area, $areas)) {
				$areas[$area] = array();
			}
			$areas[$area][] = $region;
		}

		return $areas;
	}

	/**
	 * Sets areas
	 *
	 * Builds the class variable array $areas.
	 *
	 * @param array $time_zone_list a tree structured array of areas and
	 *                               regions.
	 */
	private function setAreas($time_zone_list)
	{
		ksort($time_zone_list);

		foreach ($time_zone_list as $area => $regions) {
			$this->areas[$area]   = $area;
			$this->regions[$area] = array();

			// special case for UTC area
			if ($area === 'UTC') {
				$regions = array($this->getRegion('UTC'));
			}

			$this->setRegions($regions, $area);
		}
	}

	/**
	 * Builds the internal array of {@link SwatOption} objects for the
	 * specified regions
	 *
	 * @param array $regions an array of regions.
	 * @param string $area the region's area.
	 */
	private function setRegions($regions, $area)
	{
		sort($regions);

		$abbreviations = SwatDate::getTimeZoneAbbreviations();

		foreach ($regions as $region) {
			$title = $this->getRegionTitle($region);

			if (isset($abbreviations[$area.'/'.$region])) {
				$data = $abbreviations[$area.'/'.$region];

				if (!empty($data['dt']) && !empty($data['st'])) {
					$title.= sprintf(' (%s/%s)', $data['st'], $data['dt']);
				} elseif (!empty($data['st'])) {
					$title.= sprintf(' (%s)', $data['st']);
				}
			}

			$this->regions[$area][] = new SwatOption($region, $title);
		}
	}

	/**
	 * Gets an area from a time zone identifier
	 *
	 * Returns the area part of a full time zone.
	 *
	 * @param string $time_zone the time zone identifier to get the area from.
	 *
	 * @return string an area name.
	 */
	private function getArea($time_zone)
	{
		$area = null;

		if ($time_zone === 'UTC') {
			$area = 'UTC';
		} elseif ($time_zone !== null) {
			$parts = explode('/', $time_zone, 2);
			$area = reset($parts);
		}

		return $area;
	}

	/**
	 * Gets a region from a time zone identifier
	 *
	 * @param string $time_zone the time zone identifier from which to get the
	 *                           region.
	 *
	 * @return string the region part of a full time zone indentifier.
	 */
	private function getRegion($time_zone)
	{
		$region = null;

		if ($time_zone === 'UTC') {
			$region = 'Coordinated_Universal_Time'; // fake region for UTC
		} elseif ($time_zone !== null) {
			$parts = explode('/', $time_zone, 2);
			$region = end($parts);
		}

		return $region;
	}

	/**
	 * Gets a formatted region title from the region part of a time zone
	 * identifier
	 *
	 * @param string $region the region part of the time zone identifier.
	 *
	 * @return string the formatted region title.
	 */
	private function getRegionTitle($region)
	{
		$region = str_replace('_', ' ', $region);

		$region = explode('/', $region);
		$title = array_shift($region);
		foreach ($region as $part) {
			$title.= ' ('.$part.')';
		}

		return $title;
	}

}

?>
