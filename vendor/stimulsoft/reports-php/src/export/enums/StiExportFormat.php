<?php

namespace Stimulsoft\Export\Enums;

use Stimulsoft\Enums\StiReportType;
use Stimulsoft\Export\StiDataExportSettings;
use Stimulsoft\Export\StiExcelExportSettings;
use Stimulsoft\Export\StiExportSettings;
use Stimulsoft\Export\StiHtmlExportSettings;
use Stimulsoft\Export\StiImageExportSettings;
use Stimulsoft\Export\StiOdsExportSettings;
use Stimulsoft\Export\StiOdtExportSettings;
use Stimulsoft\Export\StiPdfExportSettings;
use Stimulsoft\Export\StiPowerPointExportSettings;
use Stimulsoft\Export\StiRtfExportSettings;
use Stimulsoft\Export\StiTxtExportSettings;
use Stimulsoft\Export\StiWordExportSettings;
use Stimulsoft\Export\StiXpsExportSettings;
use Stimulsoft\StiFunctions;

class StiExportFormat
{

### Constants

    /** @var int Export will not be done. */
    const None = 0;

    /** @var int Adobe PDF format. */
    const Pdf = 1;

    /** @var int XPS (XML Paper Specification) format. */
    const Xps = 2;

    /** @var int RTF (Rich Text) format */
    const Rtf = 6;

    /** @var int Text format. */
    const Text = 11;

    /** @var int Microsoft Excel format. */
    const Excel = 14;

    /** @deprecated Please use StiExportFormat::Excel constant. */
    const Excel2007 = 14;

    /** @var int Microsoft Word format. */
    const Word = 15;

    /** @deprecated Please use StiExportFormat::Word constant. */
    const Word2007 = 15;

    /** @var int XML (Extensible Markup Language) format. */
    const Xml = 16;

    /** @var int CSV (Comma Separated Value) format. */
    const Csv = 17;

    /** @var int DIF format. */
    const Dif = 18;

    /** @var int SYLK (Symbolic Link) format. */
    const Sylk = 19;

    /** @var int Image in GIF (Graphics Interchange) format. */
    const ImageGif = 21;

    /** @var int Image in BMP (Windows Bitmap) format. */
    const ImageBmp = 22;

    /** @var int Image in PNG (Portable Network Graphics) format. */
    const ImagePng = 23;

    /** @var int Image in TIFF (Tagged Image File Format) format. */
    const ImageTiff = 24;

    /** @var int Image in JPEG (Joint Photographic Experts Group) format. */
    const ImageJpeg = 25;

    /** @var int Image in PCX (Picture Exchange) format. */
    const ImagePcx = 26;

    /** @var int Image in SVG (Scalable Vector Graphics) format. */
    const ImageSvg = 28;

    /** @var int Image in SVGZ (Compressed SVG) format. */
    const ImageSvgz = 29;

    /** @var int DBF (dBase/FoxPro) format. */
    const Dbf = 31;

    /** @var int HTML format. */
    const Html = 32;

    /** @var int OpenDocument Calc format. */
    const Ods = 33;

    /** @var int OpenDocument Writer format. */
    const Odt = 34;

    /** @var int Microsoft PowerPoint format. */
    const PowerPoint = 35;

    /** @var int HTML5 format. */
    const Html5 = 36;

    /** @var int JSON (JavaScript Object Notation) format. */
    const Json = 38;

    /** @var int Document MDC file. */
    const Document = 1000;


### Helpers

    private static function getCorrectExportFormat(int $format): int
    {
        if ($format == 20) return StiExportFormat::ImageSvg;
        if ($format == 37) return StiExportFormat::Csv;
        return $format;
    }

    /**
     * Returns the file extension for the selected export format.
     */
    public static function getFileExtension(int $format, StiExportSettings $settings = null): string
    {
        $format = self::getCorrectExportFormat($format);
        $compressToArchive = $settings instanceof StiImageExportSettings && $settings->compressToArchive;

        switch ($format) {
            case StiExportFormat::Text:
                return 'txt';

            case StiExportFormat::Excel:
                return 'xlsx';

            case StiExportFormat::Word:
                return 'docx';

            case StiExportFormat::Html5:
                return 'html';

            case StiExportFormat::PowerPoint:
                return 'pptx';

            case StiExportFormat::ImageJpeg:
                return 'jpg';

            case StiExportFormat::Document:
                return 'mdc';
        }

        return $compressToArchive ? 'zip' : strtolower(str_replace('Image', '', StiExportFormat::getFormatName($format)));
    }

    /** Returns the mime type for the selected export format. */
    public static function getMimeType(int $format, StiExportSettings $settings = null): string
    {
        $format = self::getCorrectExportFormat($format);
        $compressToArchive = $settings instanceof StiImageExportSettings && $settings->compressToArchive;

        switch ($format) {
            case StiExportFormat::Pdf:
                return 'application/pdf';

            case StiExportFormat::Xps:
                return 'application/vnd.ms-xpsdocument';

            case StiExportFormat::Rtf:
                return 'application/rtf';

            case StiExportFormat::Text:
                return 'text/plain';

            case StiExportFormat::Excel:
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

            case StiExportFormat::Word:
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            case StiExportFormat::Xml:
                return 'application/xml';

            case StiExportFormat::Csv:
                return 'text/csv';

            case StiExportFormat::Dif:
                return 'text/x-diff';

            case StiExportFormat::Sylk:
                return 'application/x-sylk';

            case StiExportFormat::ImageGif:
                return $compressToArchive ? 'application/x-zip' : 'image/gif';

            case StiExportFormat::ImageBmp:
                return $compressToArchive ? 'application/x-zip' : 'image/x-ms-bmp';

            case StiExportFormat::ImagePng:
                return $compressToArchive ? 'application/x-zip' : 'image/x-png';

            case StiExportFormat::ImageTiff:
                return $compressToArchive ? 'application/x-zip' : 'image/tiff';

            case StiExportFormat::ImageJpeg:
                return $compressToArchive ? 'application/x-zip' : 'image/jpeg';

            case StiExportFormat::ImagePcx:
                return $compressToArchive ? 'application/x-zip' : 'image/x-pcx';

            case StiExportFormat::ImageSvg:
            case StiExportFormat::ImageSvgz:
                return $compressToArchive ? 'application/x-zip' : 'image/svg+xml';

            case StiExportFormat::Dbf:
                return 'application/dbf';

            case StiExportFormat::Html:
            case StiExportFormat::Html5:
                return 'text/html';

            case StiExportFormat::Ods:
                return 'application/vnd.oasis.opendocument.spreadsheet';

            case StiExportFormat::Odt:
                return 'application/vnd.oasis.opendocument.text';

            case StiExportFormat::PowerPoint:
                return 'application/vnd.ms-powerpoint';

            case StiExportFormat::Json:
                return 'application/json';

            case StiExportFormat::Document:
                return 'text/xml';
        }

        return 'text/plain';
    }

    /** Returns the name of the export format suitable for use in JavaScript code. */
    public static function getFormatName(int $format)
    {
        $format = self::getCorrectExportFormat($format);
        $constants = StiFunctions::getConstants('Stimulsoft\Export\Enums\StiExportFormat', true);
        return $constants[$format];
    }

    /** Returns the settings class for the specified export format. */
    public static function getExportSettings(int $format, int $reportType = StiReportType::Auto): StiExportSettings
    {
        $format = self::getCorrectExportFormat($format);

        switch ($format) {
            case StiExportFormat::Pdf:
                if ($reportType == StiReportType::Dashboard) return new \Stimulsoft\Export\StiPdfDashboardExportSettings();
                return new StiPdfExportSettings();

            case StiExportFormat::Xps:
                return new StiXpsExportSettings();

            case StiExportFormat::Rtf:
                return new StiRtfExportSettings();

            case StiExportFormat::Text:
                return new StiTxtExportSettings();

            case StiExportFormat::Excel:
                if ($reportType == StiReportType::Dashboard) return new \Stimulsoft\Export\StiExcelDashboardExportSettings();
                return new StiExcelExportSettings();

            case StiExportFormat::Word:
                return new StiWordExportSettings();

            case StiExportFormat::PowerPoint:
                return new StiPowerPointExportSettings();

            case StiExportFormat::Ods:
                return new StiOdsExportSettings();

            case StiExportFormat::Odt:
                return new StiOdtExportSettings();

            case StiExportFormat::Html:
                if ($reportType == StiReportType::Dashboard) return new \Stimulsoft\Export\StiHtmlDashboardExportSettings();
                return new StiHtmlExportSettings(StiHtmlType::Html);

            case StiExportFormat::Html5:
                return new StiHtmlExportSettings(StiHtmlType::Html5);

            case StiExportFormat::Xml:
                return new StiDataExportSettings(StiDataType::Xml);

            case StiExportFormat::Json:
                return new StiDataExportSettings(StiDataType::Json);

            case StiExportFormat::Csv:
                if ($reportType == StiReportType::Dashboard) return new \Stimulsoft\Export\StiDataDashboardExportSettings();
                return new StiDataExportSettings(StiDataType::Csv);

            case StiExportFormat::Dif:
                return new StiDataExportSettings(StiDataType::Dif);

            case StiExportFormat::Sylk:
                return new StiDataExportSettings(StiDataType::Sylk);

            case StiExportFormat::Dbf:
                return new StiDataExportSettings(StiDataType::Dbf);

            case StiExportFormat::ImageGif:
                return new StiImageExportSettings(StiImageType::Gif);

            case StiExportFormat::ImageBmp:
                return new StiImageExportSettings(StiImageType::Bmp);

            case StiExportFormat::ImagePng:
                return new StiImageExportSettings(StiImageType::Png);

            case StiExportFormat::ImageTiff:
                return new StiImageExportSettings(StiImageType::Tiff);

            case StiExportFormat::ImageJpeg:
                return new StiImageExportSettings(StiImageType::Jpeg);

            case StiExportFormat::ImagePcx:
                return new StiImageExportSettings(StiImageType::Pcx);

            case StiExportFormat::ImageSvg:
                if ($reportType == StiReportType::Dashboard) return new \Stimulsoft\Export\StiImageDashboardExportSettings();
                return new StiImageExportSettings(StiImageType::Svg);

            case StiExportFormat::ImageSvgz:
                return new StiImageExportSettings(StiImageType::Svgz);
        }

        return new StiExportSettings();
    }
}