<?php

require_once 'Swat/SwatPage.php';
require_once 'XML/RPC2/Server.php';

/**
 * Base class for an XML-RPC Server
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatXMLRPCServer extends SwatPage
{
	/**
	 * @xmlrpc.hidden
	 */
	public function __construct()
	{
		$this->layout = 'xmlrpcserver';
	}

	/**
	 * Display the page
	 *
	 * This method is called by the layout to output the XML-RPC response.
	 *
	 * @xmlrpc.hidden
	 */
	public function display()
	{
		$server = XML_RPC2_Server::create($this);
		$server->handleCall();
	}

}

?>
