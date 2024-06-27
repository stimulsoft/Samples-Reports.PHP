<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration describes a type of the border. */
class StiTxtBorderType
{
    /** A border which consists of "+","-","|" symbols. */
    const Simple = 1;

    /** A border which consists of character graphics symbols. A Single type of the border. */
    const UnicodeSingle = 2;

    /** A border consists of character graphics symbols. A Double type of the border. */
    const UnicodeDouble = 3;
}