<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration which sets an image compression method for PDF export. */
class StiPdfImageCompressionMethod
{
    /** A Jpeg method (DCTDecode) will be used for the exporting of the rendered document. */
    const Jpeg = 1;

    /** A Flate method (FlateDecode) will be used for the exporting of the rendered document. */
    const Flate = 2;

    /** A Indexed method (IndexedColors + FlateDecode) will be used for the exporting of the rendered document. */
    const Indexed = 3;
}