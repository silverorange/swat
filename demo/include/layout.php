<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<?php echo $html_head_entries ?? ''; ?>
	<link media="all" rel="stylesheet" href="/styles/demo.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/atom-one-light.min.css">
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/php.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/languages/xml.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.9.0/highlightjs-line-numbers.min.js"></script>
    <title><?php echo $title ?? 'Swat Demo'; ?></title>
    <link rel="shortcut icon" href="/favicon.ico" />
</head>
<body>
	<?php echo $ui ?? ''; ?>
    <script>
        hljs.highlightAll();
        hljs.initLineNumbersOnLoad();
    </script>
</body>
</html>
