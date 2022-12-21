<?php

namespace Stimulsoft\Viewer;

use Stimulsoft\StiComponentOptions;

/** A class which controls the export options. */
class StiExportsOptions extends StiComponentOptions
{
    /** Gets or sets a value which allows store the export settings in the cookies. */
    public $storeExportSettings = true;

    /** Gets or sets a value which allows to display the export dialog, or to export with the default settings. */
    public $showExportDialog = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the report document file. */
    public $showExportToDocument = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the PDF format. */
    public $showExportToPdf = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the HTML format. */
    public $showExportToHtml = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the HTML5 format. */
    public $showExportToHtml5 = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Word 2007-2010 format. */
    public $showExportToWord2007 = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Excel 2007-2010 format. */
    public $showExportToExcel2007 = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the CSV format. */
    public $showExportToCsv = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Json format. */
    public $showExportToJson = false;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Text format. */
    public $showExportToText = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Open Document Text format. */
    public $showExportToOpenDocumentWriter = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Open Document Calc format. */
    public $showExportToOpenDocumentCalc = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the HTML format. */
    public $showExportToPowerPoint = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the SVG format. */
    public $showExportToImageSvg = true;

    /** Gets or sets a value which indicates that the user can save the report from the viewer to the Xps format. */
    public $showExportToXps = true;
}