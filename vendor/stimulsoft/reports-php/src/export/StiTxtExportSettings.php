<?php

namespace Stimulsoft\Export;

use Stimulsoft\Enums\Encoding;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\StiTxtBorderType;

/**
 * Class describes settings for export to Text format.
 */
class StiTxtExportSettings extends StiExportSettings
{

### Properties

    /** @var Encoding|string [enum] Gets or sets encoding of result text file. */
    public $encoding = Encoding::UTF8;

    /** @var bool Gets or sets value which indicates that borders will be drawn or not. */
    public $drawBorder = true;

    /** @var StiTxtBorderType|int [enum] Gets or sets type of drawing border. */
    public $borderType = StiTxtBorderType::UnicodeSingle;

    /** @var bool Gets or sets value which indicates that empty space lines will be removed. */
    public $killSpaceLines = true;

    /** @var bool Gets or sets value which indicates that empty graph space lines will be removed. */
    public $killSpaceGraphLines = true;

    /** @var bool Gets or sets value which indicates that special FeedPageCode marker will be placed in result file. */
    public $putFeedPageCode = true;

    /** @var bool Gets or sets value which indicates that long text lines will be cut. */
    public $cutLongLines = true;

    /** @var int Gets or sets horizontal zoom factor by X axis. By default a value is 1.0f what is equal 100% in export settings window. */
    public $zoomX = 1;

    /** @var int Gets or sets vertical zoom factor by Y axis. By default a value is 1.0f what is equal 100% in export settings window. */
    public $zoomY = 1;

    /** @var bool Gets or sets value which indicates that Escape codes will be used. */
    public $useEscapeCodes = false;

    /** @var string Gets or sets value which indicates a EscapeCodesCollection name. */
    public $escapeCodesCollectionName = '';


### Helpers

    public function getExportFormat(): int
    {
        return StiExportFormat::Text;
    }
}