<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\ImageFormat;
use Stimulsoft\Export\Enums\StiWordRestrictEditing;

/**
 * Class describes settings for export to Microsoft Word formats.
 */
class StiWordExportSettings extends StiExportSettings
{

### Properties

    /** @var bool Gets or sets value which indicates that one (first) page header and page footer from report will be used in word file. */
    public $usePageHeadersAndFooters = false;

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var ImageFormat [enum] Gets or sets image format for exported images. 'null' corresponds to automatic mode. */
    public $imageFormat = null;

    /** @var bool Gets or sets a value indicating whether it is necessary to remove empty space at the bottom of the page. */
    public $removeEmptySpaceAtBottom = true;

    /** @var string Gets or sets information about the creator to be inserted into result Word file. */
    public $companyString = 'Stimulsoft';

    /** @var string */
    public $lastModifiedString = null;

    /** @var StiWordRestrictEditing|int [enum] */
    public $restrictEditing = StiWordRestrictEditing::No;

    /** @var string */
    public $protectionPassword = '*TestPassword*';

    /** @var string */
    public $encryptionPassword = null;


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Word;
    }
}