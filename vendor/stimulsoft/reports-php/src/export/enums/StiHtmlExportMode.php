<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration which sets an exported mode for the Html export. */
class StiHtmlExportMode
{
    /** A span tag of the HTML will be used for the exporting of the rendered document. */
    const Span = 1;

    /** A div tag of the HTML will be used for the exporting of the rendered document. */
    const Div = 2;

    /** A table tag of the HTML will be used for the exporting of the rendered document. */
    const Table = 3;

    /** A tag of the HTML will be taken from the report preview settings. */
    const FromReport = 4;
}