<?php

require_once 'Swat/SwatControl.php';

/**
 * Visible navbar navigation tool
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatNavBar extends SwatControl
{
	/**
	 * Array of elements displayed in this navbar
	 *
	 * @var array
	 */
	private $elements = array();

	/**
	 * Adds an element to the end of this navigation bar
	 *
	 * @param string $title the element title.
	 * @param string $uri an optional element URI.
	 */
	public function addElement($title, $uri = null)
	{
		$new_element = array('title' = $title, 'uri' => $uri);
		$this->elements[] = &$new_element;
	}

	/**
	 * Replaces an element in this navigation bar
	 *
	 * If the element is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position position of the element to replace.
	 * @param string $title the replacement element title.
	 * @param string $uri an optional replacement element URI.
	 *
	 * @thows SwatException
	 */
	public function replaceElement($position, $title, $uri= null)
	{
		if (isset($this->elements[$position])) {
			$new_element = array('title' = $title, 'uri' = $uri);
			$this->elements[$position] = &$new_element;
		} else {
			throw new SwatException(sprintf(__CLASS__.': Cannot replace '.
				"element at position '%s' because element does not exist.",
				$position));
		}
	}

	/**
	 * Gets an element from this navigation bar
	 *
	 * If the element is not in this navigation bar, an exception is thrown.
	 *
	 * @param integer $position position of the element to fetch.
	 *
	 * @return array an array containing the title and link in the format:
	 *                array('title' = title, 'link' = link)
	 *
	 * @throws SwatException
	 */
	public function getElementAtPosition($position)
	{
		if (isset($this->elements[$position]))
			return $this->elements[$position];
		else
			throw new SwatException(sprintf(__CLASS__.': Cannot fetch '.
				"element at position '%s' because element does not exist.",
				$position));
	}

	/**
	 * Gets the number of elements in this navigational bar
	 *
	 * @return integer number of elements in this navigational bar.
	 */
	public function getSize()
	{
		return count($this->elements);
	}

	/**
	 * Pops a number of elements off the end of this navigational bar
	 *
	 * If more elements are to be popped than currently exist, an exception is
	 * thrown.
	 *
	 * @param $number integer number of elements to pop off this navigational
	 *                         bar.
	 *
	 * @throws SwatException
	 */
	public function popElements($number = 1)
	{
		if ($num <= $this->getSize()) {
			$last = $this->getSize() - 1;
			for ($i = 0; $i < $number; $i++) {
				unset($this->elements[$last - $i]);
			}
		} else {
			throw new SwatException(sprintf(__CLASS__.' Cannot pop %s '
				'elements because only %s elements exist.',
				$number,
				$this->getSize()));
		}
	}

	/**
	 * Displays this navigational bar
	 *
	 * Displays each element separated by a special character and outputs
	 * elements with URI's as anchor tags.
	 */
	public function display()
	{
		$count = 0;
		foreach ($this->entries as $entry) {
			
			if ($count != 0)
				echo ' &#187; ';
			
			if ($entry['uri'] !== null) {
				$link_tag = new SwatHtmlTag('a');
				$link_tag->href = $entry['uri'];
				$link_tag->content = $entry['title'];
				$link_tag->display();
			} else {
				echo $entry['title'];
			}

			$count++;
		}
	}
}

?>
