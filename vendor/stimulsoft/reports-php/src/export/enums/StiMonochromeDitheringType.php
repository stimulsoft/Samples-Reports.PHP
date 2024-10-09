<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration describes a type of dithering for monochrome PCX file. */
class StiMonochromeDitheringType
{
    /** Without dithering. Low quality, small size of file. */
    const None = 1;

    /** Floyd-Steinberg dithering. Good quality, big size of file. */
    const FloydSteinberg = 2;

    /** Ordered dithering with Bayer matrix 4x4. Poor quality, medium size of file. */
    const Ordered = 3;
}