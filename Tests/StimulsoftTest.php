<?php

namespace Tests;

class StimulsoftTest extends \PHPUnit\Framework\TestCase
	{
	public function testBadField()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$this->expectException(\Exception::class);
		$options->badField = 'test';
		}

	public function testBadType()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$this->expectException(\Exception::class);
		$options->height = 1000;
		}

	public function testBadEnum()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$this->expectException(\Exception::class);
		$options->viewerOptions->toolbar->alignment = 'fred';
		}

	public function testEnum()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$this->assertEquals(3, $options->viewerOptions->toolbar->alignment);
		$options->viewerOptions->toolbar->alignment = 'Right';
		$this->assertEquals(2, $options->viewerOptions->toolbar->alignment);
		}

	public function testDefaults()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$this->assertEquals('800px', $options->height, 'Default failure');
		$this->assertEquals(100, $options->appearance->_zoom, 'Default sub fetch failure');
		$this->assertEquals('Empty', $options->viewerOptions->toolbar->backgroundColor->name, 'options.viewerOptions.toolbar.backgroundColor.name fetch failure');
		}

	public function testSetAndGet()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$pixels = '1000px';
		$options->height = $pixels;
		$this->assertEquals($pixels, $options->height, \get_class($options) . '::__get failure');
		$color = 'Blue';
		$options->viewerOptions->toolbar->backgroundColor->name = $color;
		$this->assertEquals($color, $options->viewerOptions->toolbar->backgroundColor->name, 'options.viewerOptions.toolbar.backgroundColor.name set and fetch failure');
		}

	public function testGenerate()
		{
		$options = new \Stimulsoft\Designer\StiDesignerOptions('options');
		$options->appearance->fullScreenMode = false;
		$options->appearance->showTooltips = false;
		$options->height = '1000px';
		$options->viewerOptions->toolbar->backgroundColor->name = 'B"lue';
		$javaScript = "var options = new Stimulsoft.Designer.StiDesignerOptions();\noptions.appearance.fullScreenMode = false;\noptions.appearance.showTooltips = false;\noptions.viewerOptions.toolbar.backgroundColor.name = \"B\\\"lue\";\noptions.height = \"1000px\";\n";
		$this->assertEquals($javaScript, "{$options}", 'JavaScript generation failure');
		}
	}
