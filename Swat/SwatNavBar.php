<?php

require_once('Swat/SwatControl.php');

/**
 * Visible navbar navigation tool
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatNavBar extends SwatControl {
	private $titles = array();
	private $links = array();

	/**
	 * Add a item to the end of the navbar
	 *
	 * @param string $title Element title
	 * @param string $link Optional link
	 */
	public function add($title, $link = null) {
		$insertpos = $this->size();
		$this->titles[$insertpos] = $title;
		$this->links[$insertpos]  = $link;
	}

	/**
	 * Replace an element
	 *
	 * @param integer $pos Element position to replace at
	 * @param string $title Element title
	 * @param string $link Optional link
	 */
	public function replace($pos, $title, $link = null) {
		$this->titles[$pos] = $title;
		$this->links[$pos]  = $link;
	}

	/**
	 * Fetch an element of the navbar
	 *
	 * @param integer $level Level of navbar to return
	 *
	 * @return array An array containing the title and link in the format:
	 *               array('title' = title, 'link' = link)
	 */
	public function fetch($level) {
		$out=array();
		$out['title'] = $this->titles[$level];
		$out['link']  = $this->links[$level];
		return $out;
	}

	/**
	 * Get number of elements
	 *
	 * @return integer Number of elements
	 */
	public function size() {
		return count($this->titles);
	}

	/**
	 * Pop the last 'num' elements off the end of the navbar
	 *
	 * @param $num integer Number of elements to pop
	 */
	public function pop($num = 1) {
		for ($i = 0; $i < $num; $i++) {
			$last = $this->size() - 1;
			unset($this->titles[$last]);
			unset($this->links[$last]);
		}
	}

	public function display() {
		for ($i = 0; $i < $this->size(); $i++) {
			$entry = $this->fetch($i);
			
			if ($i != 0)
				echo ' &#187; ';
			
			if ($entry['link'] !== null) {
				$link = new SwatHtmlTag('a');
				$link->href = $entry['link'];
				$link->open();
				echo $entry['title'];
				$link->close();
			} else
				echo $entry['title'];
		}
	}
}

?>
