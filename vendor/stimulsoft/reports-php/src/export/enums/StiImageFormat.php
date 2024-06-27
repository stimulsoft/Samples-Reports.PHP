<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration for setting format of the exported images. */
class StiImageFormat
{
    /** Images are exported in the color mode. */
    const Color = 1;

    /** Images are exported in the grayscale mode. */
    const Grayscale = 2;

    /** Images are exported in the monochrome mode. */
    const Monochrome = 3;
}

class ImageFormat
{
    const Bmp = 0;
    //const Emf = 1;
    //const Exif = 2;
    const Gif = 3;
    //const Guid = 4;
    //const Icon = 5;
    const Jpeg = 6;
    //const MemoryBmp = 7;
    const Png = 8;
    const Tiff = 9;
    //const Wmf = 10
}