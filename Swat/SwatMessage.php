<?php

/**
 * A data class to store a message  
 *
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
class SwatMessage {

	const INFO = 1;
	const WARNING = 2;
	const USER_ERROR = 3;
	const ERROR = 4;

	/**
	 * Type of message
	 * @var int
	 */
	public $type;

	/**
	 * Message text
	 * @var string
	 */
	public $content = null;

	/**
	 * @param string $content Message text 
	 * @param integer $type Type of message. Set from class constants.
	 */
	public function __construct($content, $type = self::INFO) {
		$this->content = $content;
		if ($type !== null)
			$this->type = $type;
	}

}

?>
