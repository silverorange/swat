<?php

setlocale(LC_ALL, 'fr_FR.utf8');

require_once __DIR__.'/../vendor/autoload.php';

SwatException::setupHandler();

$app = new DemoApplication('demo');

try {
	$app->run();
} catch (SwatException $exception) {
	$exception->process(false);
}

?>
