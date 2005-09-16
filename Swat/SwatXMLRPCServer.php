<?php

require_once 'Swat/SwatPage.php';
require_once 'XML/RPC2/Server.php';

/**
 * Base class for an XML-RPC Server
 *
 * The XML-RPC server acts as a regular page in an application. This means
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
	 * Process the request
	 *
	 * This method is called by site code to process the page request. It creates 
	 * an XML-RPC server and handles a request. The XML-RPC response from the
	 * server is output here as well.
	 *
	 * @xmlrpc.hidden
	 */
	public function process()
	{
		$server = XML_RPC2_Server::create($this);

		ob_start();
		$server->handleCall();
		$this->layout->response = ob_get_clean();
	}

	/**
	 * @xmlrpc.hidden
	 */
	public function build()
	{
	}

	protected function createLayout()
	{
		return new SwatLayout('Swat/layouts/xmlrpcserver.php');
	}
}

?>
