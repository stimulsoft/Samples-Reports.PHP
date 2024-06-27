<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;

/**
 * Class describes settings for export to OpenDocument Calc format.
 */
class StiOdsExportSettings extends StiExportSettings
{

### Properties

    /** @var float Gets or sets image quality of images which will be exported to ODS file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to ODS file. */
    public $imageResolution = 100;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Ods;
    }
}