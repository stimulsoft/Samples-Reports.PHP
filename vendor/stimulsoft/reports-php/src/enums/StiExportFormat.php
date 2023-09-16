<?php

namespace Stimulsoft;

class StiExportFormat
{
    const Pdf = 1;
    const Xps = 2;
    const Text = 11;
    const Excel2007 = 14;
    const Word2007 = 15;
    const Csv = 17;
    const ImageSvg = 28;
    const Html = 32;
    const Ods = 33;
    const Odt = 34;
    const Ppt2007 = 35;
    const Html5 = 36;
    const Document = 1000;

    public static function getFileExtension($format)
    {
        switch ($format) {
            case StiExportFormat::Pdf:
                return 'pdf';

            case StiExportFormat::Xps:
                return 'xps';

            case StiExportFormat::Text:
                return 'txt';

            case StiExportFormat::Excel2007:
                return 'xlsx';

            case StiExportFormat::Word2007:
                return 'docx';

            case StiExportFormat::Csv:
                return 'csv';

            case StiExportFormat::ImageSvg:
                return 'svg';

            case StiExportFormat::Html:
            case StiExportFormat::Html5:
                return 'html';

            case StiExportFormat::Ods:
                return 'ods';

            case StiExportFormat::Odt:
                return 'odt';

            case StiExportFormat::Ppt2007:
                return 'pptx';

            case StiExportFormat::Document:
                return 'mdc';
        }

        return strtolower($format);
    }

    public static function getMimeType($format)
    {
        switch ($format) {
            case StiExportFormat::Pdf:
                return 'application/pdf';

            case StiExportFormat::Xps:
                return 'application/vnd.ms-xpsdocument';

            case StiExportFormat::Text:
                return 'text/plain';

            case StiExportFormat::Excel2007:
                return 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

            case StiExportFormat::Word2007:
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

            case StiExportFormat::Csv:
                return 'text/csv';

            case StiExportFormat::ImageSvg:
                return 'image/svg+xml';

            case StiExportFormat::Html:
            case StiExportFormat::Html5:
                return 'text/html';

            case StiExportFormat::Ods:
                return 'application/vnd.oasis.opendocument.spreadsheet';

            case StiExportFormat::Odt:
                return 'application/vnd.oasis.opendocument.text';

            case StiExportFormat::Ppt2007:
                return 'application/vnd.ms-powerpoint';

            case StiExportFormat::Document:
                return 'text/xml';
        }

        return 'text/plain';
    }

    public static function getFormatName($format)
    {
        $class = new \ReflectionClass('\Stimulsoft\StiExportFormat');
        $constants = $class->getConstants();
        $names = array_flip($constants);

        return $names[$format];
    }
}