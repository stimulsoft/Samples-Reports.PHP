<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;

/**
 * Class describes settings for export to OpenDocument Writer format.
 */
class StiOdtExportSettings extends StiExportSettings
{

### Properties

    /** @var bool Gets or sets value which indicates that one (first) page header and page footer from report will be used in ODT file. */
    public $usePageHeadersAndFooters = false;

    /** @var float Gets or sets image quality of images which will be exported to ODT file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to ODT file. */
    public $imageResolution = 100;

    /** @var bool Gets or sets a value indicating whether it is necessary to remove empty space at the bottom of the page. */
    public $removeEmptySpaceAtBottom = true;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Odt;
    }
}