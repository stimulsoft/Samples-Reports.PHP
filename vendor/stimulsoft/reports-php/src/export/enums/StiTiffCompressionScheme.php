<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration for setting compression scheme of the exported Tiff image. */
class StiTiffCompressionScheme
{
    /** Specifies that a multiple-frame file or stream should be closed. Can be passed to the TIFF encoder as a parameter that belongs to the save flag category. */
    const Default = 20;

    /** Specifies the LZW compression scheme. Can be passed to the TIFF encoder as a parameter that belongs to the Compression category. */
    const LZW = 2;

    /** Specifies the CCITT3 compression scheme. Can be passed to the TIFF encoder as a parameter that belongs to the compression category. */
    const CCITT3 = 3;

    /** Specifies the CCITT4 compression scheme. Can be passed to the TIFF encoder as a parameter that belongs to the compression category. */
    const CCITT4 = 4;

    /** Specifies the RLE compression scheme. Can be passed to the TIFF encoder as a parameter that belongs to the compression category. */
    const Rle = 5;

    /** Specifies no compression. Can be passed to the TIFF encoder as a parameter that belongs to the compression category. */
    const None = 6;
}