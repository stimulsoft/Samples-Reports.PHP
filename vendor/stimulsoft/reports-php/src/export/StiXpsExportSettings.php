<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;

/**
 * Class describes settings for export to XPS format.
 */
class StiXpsExportSettings extends StiExportSettings
{

### Properties

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var bool Gets or sets value which indicates that RTF text will be exported as bitmap images or as vector images. */
    public $exportRtfTextAsImage = false;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Xps;
    }
}