<?php

require_once 'Swat/SwatApplication.php';
require_once 'pages/ExamplePage.php';

class ExampleApplication extends SwatApplication
{
	protected function resolvePage()
	{
		$demo = SwatApplication::initVar('demo', 'FrontPage',
			SwatApplication::VAR_GET);

		// simple security
		$demo = basename($demo);

		if (file_exists('../include/pages/'.$demo.'.php')) {
			require_once '../include/pages/'.$demo.'.php';
			return new $demo($this);
		} else {
			return new ExamplePage($this);
		}
	}
}

?>
