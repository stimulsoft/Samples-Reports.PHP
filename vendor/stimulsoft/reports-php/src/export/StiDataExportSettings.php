<?php

namespace Stimulsoft\Export;

use Stimulsoft\Enums\Encoding;
use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\StiDataExportMode;
use Stimulsoft\Export\Enums\StiDataType;
use Stimulsoft\Export\Enums\StiDbfCodePages;

/**
 * Class describes settings for export to data formats.
 */
class StiDataExportSettings extends StiExportSettings
{

### Properties

    /** @var StiDataType|int [enum] Gets or sets type of the exported data file. */
    public $dataType = null;

    /** @var StiDataExportMode|int [enum] Gets or sets data export mode. SYLK and DIF formats does not support this property. */
    public $dataExportMode = StiDataExportMode::Data;

    /** @var Encoding|string [enum] Gets or sets encoding of DIF file format. XML, JSON and DBF formats does not support this property. */
    public $encoding = Encoding::UTF8;

    /** @var bool Gets or sets value which indicates that all formatting of exported report will be removed. XML, JSON, DBF and CSV formats does not support this property. */
    public $exportDataOnly = false;

    /** @var StiDbfCodePages|int [enum] Gets or sets code page of DBF file format. DBF format only! */
    public $codePage = StiDbfCodePages::Default;

    /** @var string Gets or sets string which represents column separator in CSV file. CSV format only! */
    public $separator = ';';

    /** @var string Gets or sets name of the table. XML and JSON formats only! */
    public $tableName = null;

    /** @var bool Gets or sets value which indicates that export engine will be write column headers as column headers in table or as simple column values. CSV format only! */
    public $skipColumnHeaders = false;

    /** @var bool Gets or sets value which indicates that default system encoding will be used for DIF and SYLK formats. DIF and SYLK format only! */
    public $useDefaultSystemEncoding = true;


### Helpers

    public function getExportFormat(): int
    {
        if ($this->dataType == StiDataType::Dbf)
            return StiExportFormat::Dbf;

        if ($this->dataType == StiDataType::Dif)
            return StiExportFormat::Dif;

        if ($this->dataType == StiDataType::Sylk)
            return StiExportFormat::Sylk;

        if ($this->dataType == StiDataType::Xml)
            return StiExportFormat::Xml;

        if ($this->dataType == StiDataType::Json)
            return StiExportFormat::Json;

        return StiExportFormat::Csv;
    }

    public function setDataType($format = null)
    {
        if ($format === null)
            $format = $this->getExportFormat();

        switch ($format) {
            case StiExportFormat::Dbf:
                $this->dataType = StiDataType::Dbf;
                break;

            case StiExportFormat::Dif:
                $this->dataType = StiDataType::Dif;
                break;

            case StiExportFormat::Sylk:
                $this->dataType = StiDataType::Sylk;
                break;

            case StiExportFormat::Xml:
                $this->dataType = StiDataType::Xml;
                break;

            case StiExportFormat::Json:
                $this->dataType = StiDataType::Json;
                break;

            case StiExportFormat::Csv:
                $this->dataType = StiDataType::Csv;
                break;
        }
    }


### HTML

    public function getHtml(): string
    {
        $this->setDataType();

        return parent::getHtml();
    }


### Constructor

    /**
     * @param StiDataType|int $dataType [enum] Type of the exported data file.
     */
    public function __construct(int $dataType = null)
    {
        parent::__construct();
        $this->dataType = $dataType;
    }
}