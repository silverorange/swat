<?php

/**
 *
 */
class SwatDBDataObject
{
	/**
	 *
	 */
	private $property_hashes = array();

	/**
	 *
	 */
	public function __construct($rs = null);
	{
		if ($rs !== null)
			$this->initWrapper();
	}

	/**
	 *
	 */
	public function initWrapper($rs);
	{

	}

	/**
	 *
	 */
	private function generatePropertyHashes();
	{
	}
}

?>
