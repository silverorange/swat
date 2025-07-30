<?php

require_once 'Demo.php';

/**
 * A demo using flydowns.
 *
 * This demo sets up the various flydown widgets. All flydown widgets currently
 * must be set up manually as they contain SwatFlyDown options rather than
 * an array.
 *
 * @copyright 2005-2016 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class FlydownDemo extends Demo
{
    public function buildDemoUI(SwatUI $ui)
    {
        $flydown = $ui->getWidget('flydown');

        $flydown->addOption(
            new SwatOption('mackintosh', 'McIntosh'),
            ['classes' => 'apple']
        );

        $flydown->addOption(
            new SwatOption('courtland', 'Courtland'),
            ['classes' => 'apple']
        );

        $flydown->addOption(
            new SwatOption('golden', 'Golden Delicious'),
            ['classes' => 'apple']
        );

        $flydown->addOption(
            new SwatOption('fuji', 'Fuji'),
            ['classes' => 'apple']
        );

        $flydown->addOption(
            new SwatOption('smith', 'Granny Smith'),
            ['classes' => 'apple']
        );

        $flydown->addOption(
            new SwatOption('navel', 'Navel'),
            ['classes' => 'orange']
        );

        $flydown->addOption(
            new SwatOption('blood', 'Blood'),
            ['classes' => 'orange']
        );

        $flydown->addOption(
            new SwatOption('florida', 'Florida'),
            ['classes' => 'orange']
        );

        $flydown->addOption(
            new SwatOption('california', 'California'),
            ['classes' => 'orange']
        );

        $flydown->addOption(
            new SwatOption('mandarin', 'Mandarin'),
            ['classes' => 'orange']
        );

        // tree flydown
        $tree = new SwatTreeFlydownNode(null, 'Root');

        $apples = new SwatTreeFlydownNode('apple', 'Apple');
        $apples->addChild(new SwatTreeFlydownNode('mackintosh', 'McIntosh'));
        $apples->addChild(new SwatTreeFlydownNode('courtland', 'Courtland'));
        $apples->addChild(new SwatTreeFlydownNode('golden', 'Golden Delicious'));
        $apples->addChild(new SwatTreeFlydownNode('fuji', 'Fuji'));
        $apples->addChild(new SwatTreeFlydownNode('smith', 'Granny Smith'));

        $oranges = new SwatTreeFlydownNode('orange', 'Orange');
        $oranges->addChild(new SwatTreeFlydownNode('navel', 'Navel'));
        $oranges->addChild(new SwatTreeFlydownNode('blood', 'Blood'));
        $oranges->addChild(new SwatTreeFlydownNode('florida', 'Florida'));
        $oranges->addChild(new SwatTreeFlydownNode('california', 'California'));
        $oranges->addChild(new SwatTreeFlydownNode('mandarin', 'Mandarin'));

        $tree->addChild($apples);
        $tree->addChild($oranges);

        $tree_flydown = $ui->getWidget('tree_flydown');
        $tree_flydown->setTree($tree);

        // grouped flydown
        $grouped_tree = new SwatTreeFlydownNode(null, 'Root');

        $apples = new SwatTreeFlydownNode(null, 'Apple');
        $apples->addChild(new SwatTreeFlydownNode('mackintosh', 'McIntosh'));
        $apples->addChild(new SwatTreeFlydownNode('courtland', 'Courtland'));
        $apples->addChild(new SwatTreeFlydownNode('golden', 'Golden Delicious'));
        $apples->addChild(new SwatTreeFlydownNode('fuji', 'Fuji'));
        $apples->addChild(new SwatTreeFlydownNode('smith', 'Granny Smith'));

        $oranges = new SwatTreeFlydownNode(null, 'Orange');
        $oranges->addChild(new SwatTreeFlydownNode('navel', 'Navel'));
        $oranges->addChild(new SwatTreeFlydownNode('blood', 'Blood'));
        $oranges->addChild(new SwatTreeFlydownNode('florida', 'Florida'));
        $oranges->addChild(new SwatTreeFlydownNode('california', 'California'));
        $oranges->addChild(new SwatTreeFlydownNode('mandarin', 'Mandarin'));
        $grouped_flydown = $ui->getWidget('grouped_flydown');
        $grouped_flydown->setTree($grouped_tree);

        $grouped_tree->addChild($apples);
        $grouped_tree->addChild($oranges);

        // cascading flydown
        $cascade_from = $ui->getWidget('cascade_from');
        $cascade_from->options = [
            new SwatOption(0, 'Apple'),
            new SwatOption(1, 'Orange'),
        ];

        $cascade_to = $ui->getWidget('cascade_to');
        $cascade_to->cascade_from = $cascade_from;

        $cascade_to->options = [
            0 => [
                new SwatOption(0, 'McIntosh'),
                new SwatOption(1, 'Courtland'),
                new SwatOption(2, 'Golden Delicious'),
                new SwatOption(3, 'Fuji'),
                new SwatOption(4, 'Granny Smith'),
            ],
            1 => [
                new SwatOption(0, 'Navel'),
                new SwatOption(1, 'Blood'),
                new SwatOption(2, 'Florida'),
                new SwatOption(3, 'California'),
                new SwatOption(4, 'Mandarin'),
            ],
        ];
    }
}
