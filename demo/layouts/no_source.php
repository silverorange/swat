<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<style type="text/css" media="all">@import "swat/swat.css";</style>
	<style type="text/css" media="all">@import "example.css";</style>
	<title><?=$this->title?> | <?=$this->app_title?></title>
	<link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>

<body>
<div id="header">
	<h1><a href="<?=$this->base_href?>"><?=$this->app_title?></a>: <?=$this->title?></h1>
</div>
<div id="content">
	<?=$this->ui?>
</div>
<div id="footer">
PHP Execution Time: <?=$this->execution_time?> seconds
</div>
<div id="menu">
	<?=$this->menu?>
</div>
</body>
</html>
