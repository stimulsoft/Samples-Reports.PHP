<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\ImageFormat;
use Stimulsoft\Export\Enums\StiDataExportMode;
use Stimulsoft\Export\Enums\StiExcelRestrictEditing;
use Stimulsoft\Export\Enums\StiExcelType;

/**
 * Class describes settings for export to Microsoft Excel formats.
 */
class StiExcelExportSettings extends StiExportSettings
{

### Properties

    /** @var StiExcelType|int [enum] */
    public $excelType = StiExcelType::Excel2007;

    /** @var bool Gets or sets value which indicates that one (first) page header and page footer from report will be used in excel file. */
    public $useOnePageHeaderAndFooter = false;

    /** @var bool Gets or sets value which indicates that only data information will be created in excel file. */
    public $exportDataOnly = false;

    /** @var StiDataExportMode|int [enum] Gets or sets data export mode. */
    public $dataExportMode = StiDataExportMode::AllBands;

    /** @var bool Gets or sets value which indicates that special page break markers will be created in excel file. */
    public $exportPageBreaks = false;

    /** @var bool Gets or sets value which indicates that formatting of components will be exported to excel file or not. */
    public $exportObjectFormatting = true;

    /** @var bool Gets or sets value which indicates that each page from report will be exported to excel file as separate excel sheet. */
    public $exportEachPageToSheet = false;

    /** @var float Gets or sets image quality of images which will be exported to excel file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to excel file. */
    public $imageResolution = 100;

    /** @var ImageFormat Gets or sets image format for exported images. 'None' corresponds to automatic mode. */
    public $imageFormat = null;

    /** @var string Gets or sets information about the creator to be inserted into result Excel file. ExcelXml is not supported! */
    public $companyString = 'Stimulsoft';

    /** @var string */
    public $lastModifiedString = null;

    /** @var StiExcelRestrictEditing|int [enum] */
    public $restrictEditing = StiExcelRestrictEditing::No;

    /** @var string */
    public $protectionPassword = '*TestPassword*';

    /** @var string */
    public $encryptionPassword = null;


### Helpers

    private function setExportDataOnly()
    {
        if ($this->dataExportMode != StiDataExportMode::AllBands)
            $this->exportDataOnly = true;

        if ($this->exportDataOnly && $this->dataExportMode == StiDataExportMode::AllBands)
            $this->dataExportMode = StiDataExportMode::Data | StiDataExportMode::Headers;
    }

    public function getExportFormat(): int
    {
        return StiExportFormat::Excel;
    }


### HTML

    public function getHtml(): string
    {
        $this->setExportDataOnly();

        return parent::getHtml();
    }
}