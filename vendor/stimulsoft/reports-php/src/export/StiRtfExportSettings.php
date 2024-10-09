<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\StiRtfExportMode;

/**
 * Class describes settings for export to RTF format.
 */
class StiRtfExportSettings extends StiExportSettings
{

### Properties

    /** @var int Gets or sets code page of RTF file. */
    public $codePage = 0;

    /** @var StiRtfExportMode|int [enum] Gets or sets mode of RTF file creation. */
    public $exportMode = StiRtfExportMode::Table;

    /**
     * @var bool
     * Gets or sets value which enables special mode of exporting page headers and page footers into result file.
     * In this mode export engine try to insert all page headers and footers as RTF headers and footers.
     */
    public $usePageHeadersAndFooters = false;

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var bool Gets or sets a value indicating whether it is necessary to remove empty space at the bottom of the page. */
    public $removeEmptySpaceAtBottom = true;

    /** @var bool Gets or sets a value indicating whether it is necessary to store images in PNG format. */
    public $storeImagesAsPng = false;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Rtf;
    }
}