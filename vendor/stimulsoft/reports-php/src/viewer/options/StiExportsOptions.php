<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponent;
use Stimulsoft\StiComponentOptions;

/** A class which controls the export options. */
class StiExportsOptions extends StiComponentOptions
{

### Options

    /** @var bool Gets or sets a value which allows store the export settings in the cookies. */
    public $storeExportSettings = true;

    /** @var bool Gets or sets a value which allows to display the export dialog, or to export with the default settings. */
    public $showExportDialog = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the report document file. */
    public $showExportToDocument = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the PDF format. */
    public $showExportToPdf = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the HTML format. */
    public $showExportToHtml = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the HTML5 format. */
    public $showExportToHtml5 = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Word 2007-2024 format. */
    public $showExportToWord = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Excel 2007-2024 format. */
    public $showExportToExcel = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the CSV format. */
    public $showExportToCsv = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the JSON format. */
    public $showExportToJson = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the DBF format. */
    public $showExportToDbf = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the XML format. */
    public $showExportToXml = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the DIF format. */
    public $showExportToDif = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the SYLK format. */
    public $showExportToSylk = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Text format. */
    public $showExportToText = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Open Document Text format. */
    public $showExportToOpenDocumentWriter = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Open Document Calc format. */
    public $showExportToOpenDocumentCalc = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the HTML format. */
    public $showExportToPowerPoint = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the SVG format. */
    public $showExportToImageSvg = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the SVG format. */
    public $showExportToImagePng = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the JPEG format. */
    public $showExportToImageJpeg = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the SVGZ format. */
    public $showExportToImageSvgz = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the PCX format. */
    public $showExportToImagePcx = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the BMP format. */
    public $showExportToImageBmp = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the GIF format. */
    public $showExportToImageGif = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the TIFF format. */
    public $showExportToImageTiff = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the XPS format. */
    public $showExportToXps = false;

    /** @var bool Gets or sets a value which allows to display the option 'Export Data Only'. */
    public $showExportDataOnly = true;

    /** @var bool Gets or sets a value which indicates that the user can save the report from the viewer to the Rich Text format. */
    public $showExportToRtf = true;


### Helpers

    public function setComponent(StiComponent $component)
    {
        parent::setComponent($component);

        $this->id .= '.exports';
    }
}