<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration for setting modes of the data export. */
class StiDataExportMode
{
    const Data = 1;
    const Headers = 2;
    const DataAndHeaders = 3;
    const Footers = 4;
    const HeadersFooters = 6;
    const DataAndHeadersFooters = 7;
    const AllBands = 15;
}