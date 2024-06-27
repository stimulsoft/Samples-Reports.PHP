<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration which sets an AutoPrint mode for pdf files. */
class StiPdfAutoPrintMode
{
    /** Do not use AutoPrint feature. */
    const None = 1;

    /** Use printing with print dialog. */
    const Dialog = 2;

    /** Use silent printing. */
    const Silent = 3;
}