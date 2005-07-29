<?php

require_once 'Swat/SwatPage.php';
require_once 'XML/RPC2/Server.php';

/**
 * Base class for an XML-RPC Server
 *
 * The XML-RPC server acts as a regular page in an application . This means
 * all the regular page security features work for XML-RPC servers.
 *
 * Swat XML-RPC server pages use the PEAR::XML_RPC2 package to service
 * requests.
 *
 * @package   Swat
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatXMLRPCServer extends SwatPage
{
	/**
	 * Creates a new XML-RPC server
	 *
	 * @xmlrpc.hidden
	 */
	public function __construct()
	{
		$this->layout = 'xmlrpcserver';
	}

	/**
	 * Displays this page
	 *
	 * This method is called by the application's layout and creates an
	 * XML-RPC server and handles a request. The XML-RPC response from the
	 * server is output here as well.
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
