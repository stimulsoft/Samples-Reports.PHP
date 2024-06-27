<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration describes a type of the excel exports. */
class StiExcelType
{
    /** Excel format from Office 97 to Office 2003. */
    const ExcelBinary = 1;

    /** Xml Excel format starts from Office 2003. */
    const ExcelXml = 2;

    /** Excel format starts from Office 2007. */
    const Excel2007 = 3;
}