<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\ImageFormat;

/**
 * Class describes settings for export to Adobe PowerPoint format.
 */
class StiPowerPointExportSettings extends StiExportSettings
{

### Properties

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var string */
    public $encryptionPassword = null;

    /** @var ImageFormat Gets or sets image format for exported images. 'null' corresponds to automatic mode. */
    public $imageFormat = null;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::PowerPoint;
    }
}