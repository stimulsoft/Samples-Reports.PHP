<?php

namespace Stimulsoft\Export;

use Stimulsoft\Enums\Encoding;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\ImageFormat;
use Stimulsoft\Export\Enums\StiHorAlignment;
use Stimulsoft\Export\Enums\StiHtmlChartType;
use Stimulsoft\Export\Enums\StiHtmlExportBookmarksMode;
use Stimulsoft\Export\Enums\StiHtmlExportMode;
use Stimulsoft\Export\Enums\StiHtmlExportQuality;
use Stimulsoft\Export\Enums\StiHtmlType;

/**
 * Class describes settings for export to HTML formats.
 */
class StiHtmlExportSettings extends StiExportSettings
{

### Properties

    /** @var StiHtmlType|int [enum] Gets or sets type of the exported html file. */
    public $htmlType = null;

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var ImageFormat|int [enum] Gets or sets image format for exported images. */
    public $imageFormat = ImageFormat::Png;

    /** @var Encoding|string [enum] Gets or sets encoding of html file. */
    public $encoding = Encoding::UTF8;

    /** @var float Gets or sets zoom factor of exported file. HTML5 export mode is not supported. */
    public $zoom = 1.0;

    /** @var StiHtmlExportMode|int [enum] Gets or sets mode of html file creation. HTML5 export mode is not supported. */
    public $exportMode = StiHtmlExportMode::Div;

    /** @var StiHtmlExportQuality|int [enum] Gets or sets quality of html file. HTML5 export mode is not supported. */
    public $exportQuality = StiHtmlExportQuality::High;

    /** @var bool Gets or sets value which indicates that special page breaks marker will be added to result html file. HTML5 export mode is not supported. */
    public $addPageBreaks = true;

    /** @var int Gets or sets default width of bookmarks tree. HTML5 export mode is not supported. */
    public $bookmarksTreeWidth = 150;

    /** @var StiHtmlExportBookmarksMode|int [enum] Gets or sets export mode of bookmarks tree. HTML5 export mode is not supported. */
    public $exportBookmarksMode = StiHtmlExportBookmarksMode::All;

    /** @var bool Gets or sets value which indicates that table styles will be used in result html file. HTML5 and MHT export mode is not supported. */
    public $useStylesTable = true;

    /** @var bool Gets or sets a value indicating whether it is necessary to remove empty space at the bottom of the page. HTML5 and MHT export mode is not supported. */
    public $removeEmptySpaceAtBottom = true;

    /** @var StiHorAlignment|int [enum] Gets or sets the horizontal alignment of pages. HTML5 and MHT export mode is not supported. */
    public $pageHorAlignment = StiHorAlignment::Left;

    /** @var bool Gets or sets a value indicating whether it is necessary to save output file as zip-file. HTML5 and MHT export mode is not supported. */
    public $compressToArchive = false;

    /** @var bool Gets or sets a value indicating whether it is necessary to save images as embedded data in html file. HTML5 and MHT export mode is not supported. */
    public $useEmbeddedImages = true;

    /** @var bool Gets or sets value which indicates that all report pages will be shown as vertical ribbon. HTML and MHT export mode is not supported. */
    public $continuousPages = true;

    /** @var StiHtmlChartType|int [enum] */
    public $chartType = StiHtmlChartType::AnimatedVector;

    /** @var string Gets or sets a target to open links from the exported report. */
    public $openLinksTarget = '';

    /** @var bool */
    public $useWatermarkMargins = false;


### Helpers

    public function getExportFormat(): int
    {
        if ($this->htmlType == StiHtmlType::Html5)
            return StiExportFormat::Html5;

        return StiExportFormat::Html;
    }

    public function setHtmlType($format = null)
    {
        if ($format === null)
            $format = $this->getExportFormat();

        switch ($format) {
            case StiExportFormat::Html5:
                $this->htmlType = StiHtmlType::Html5;
                break;

            case StiExportFormat::Html:
                $this->htmlType = StiHtmlType::Html;
                break;
        }
    }


### HTML

    public function getHtml(): string
    {
        $this->setHtmlType();

        return parent::getHtml();
    }


### Constructor

    /**
     * @param StiHtmlType|int $htmlType Type of the exported HTML file.
     */
    public function __construct(int $htmlType = null)
    {
        parent::__construct();
        $this->htmlType = $htmlType;
    }
}