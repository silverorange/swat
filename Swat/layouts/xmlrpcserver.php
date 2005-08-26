<?php
// Set content type to XML
header('Content-type: text/xml');

// Disable any caching with HTTP headers
// Any date in the past will do here
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// Set always modified
// for HTTP/1.1
header('Cache-Control: no-cache, must-revalidate max-age=0');
// for HTTP/1.0
header('Pragma: no-cache');

echo $this->response;

?>
