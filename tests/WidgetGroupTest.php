<?php

namespace Arrilot\Widgets\Test;

use Arrilot\Widgets\WidgetGroup;
use PHPUnit_Framework_TestCase;

class WidgetGroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WidgetGroup
     */
    protected $widgetGroup;

    public function setUp()
    {
        $this->widgetGroup = new WidgetGroup('key1', new TestApplicationWrapper());
    }

    public function testItCanDisplayWidgets()
    {
        $this->widgetGroup->addWidget('Slider', ['slides' => 5]);
        $this->widgetGroup->addAsyncWidget('Slider');

        $output = $this->widgetGroup->display();

        $this->assertEquals('Slider was executed with $slides = 5 foo: bar'.
            '<div id="arrilot-widget-container-2" style="display:inline" class="arrilot-widget-container">Placeholder here!'.
            "<script type=\"text/javascript\">$('#arrilot-widget-container-2').load('/arrilot/load-widget', ".javascript_data_stub('Slider', [], 2).')</script>'.
            '</div>', $output);
    }

    public function testItCanSetAndResetPosition()
    {
        $this->widgetGroup->addWidget('Slider', ['slides' => 5, 'foo' => 'Taylor']);
        $this->widgetGroup->position(50)->addWidget('Slider');

        $output = $this->widgetGroup->display();

        $this->assertEquals('Slider was executed with $slides = 6 foo: bar'.
            'Slider was executed with $slides = 5 foo: Taylor', $output);
        $this->assertEquals(100, $this->widgetGroup->getPosition());
    }

    public function testMultipleWidgetGroupsCanExistTogether()
    {
        $this->widgetGroup->addWidget('Slider', ['slides' => 5, 'foo' => 'Taylor']);
        $this->widgetGroup->position(50)->addWidget('Slider');

        $widgetGroup2 = new WidgetGroup('key2', new TestApplicationWrapper());
        $widgetGroup2->position(40)->addWidget('Slider', ['slides' => 10]);
        $widgetGroup2->position(40)->addWidget('Slider', ['slides' => 15]);

        $output = $this->widgetGroup->display();
        $output2 = $widgetGroup2->display();

        $this->assertEquals('Slider was executed with $slides = 6 foo: bar'.
            'Slider was executed with $slides = 5 foo: Taylor', $output);
        $this->assertEquals(100, $this->widgetGroup->getPosition());

        $this->assertEquals('Slider was executed with $slides = 10 foo: bar'.
            'Slider was executed with $slides = 15 foo: bar', $output2);
        $this->assertEquals(100, $widgetGroup2->getPosition());
    }
}