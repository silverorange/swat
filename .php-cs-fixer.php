<?php

use PhpCsFixer\Finder;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Silverorange\PhpCodingTools\Standards\PhpCsFixer\Php82;

// Choose the appropriate base configuration for your project
$config = new Php82();

// If you have a set of custom rules you'd like to add to the
// base configuration, or specific rules you'd like to override
// for this project, you can define them here. e.g.:
//
// $config->setCustomRules(['yoda_style' => true]);
$config->setCustomRules([
    // don't simplify returns automatically; sometimes it makes it less readable
    'return_assignment' => false,
    // don't mess with backslashes in any strings
    'string_implicit_backslashes' => false,
    // don't try and fit classes with several interfaces onto one line
    'class_definition' => ['single_line' => false],
]);

// Set up the directories you want to process
$finder = (new Finder())
    ->in(__DIR__)
    ->exclude([
        'node_modules',
    ]);

return $config
    // comment the following if you don't want to use parallelism to speed up processing
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setIndent('    ')
    ->setLineEnding("\n")
    ->setFinder($finder);
