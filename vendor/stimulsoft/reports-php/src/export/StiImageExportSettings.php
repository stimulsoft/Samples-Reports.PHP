<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\StiImageFormat;
use Stimulsoft\Export\Enums\StiImageType;
use Stimulsoft\Export\Enums\StiMonochromeDitheringType;
use Stimulsoft\Export\Enums\StiTiffCompressionScheme;

/**
 * Class describes settings for export to image formats.
 */
class StiImageExportSettings extends StiExportSettings
{

### Properties

    /** @var StiImageType|int [enum] Gets or sets image type for exported images. */
    public $imageType = null;

    /** @var float Gets or sets image zoom factor for exported images. This property can't be used with EMF, SVG, SVGZ formats. */
    public $imageZoom = 1.0;

    /** @var int Gets or sets image resolution for exported images. This property can't be used with EMF, SVG, SVGZ formats. */
    public $imageResolution = 100;

    /** @var bool Gets or sets value which indicates that page margins will be cut or not. This property can't be used with EMF, SVG, SVGZ formats. */
    public $cutEdges = false;

    /** @var StiImageFormat|int [enum] Gets or sets image format for exported images. This property can't be used with EMF, SVG, SVGZ formats. */
    public $imageFormat = StiImageFormat::Color;

    /**
     * @var bool Gets or sets value which indicates that export engine will be create one solid file or multiple files (one file per page).
     * This property can't be used with EMF, SVG, SVGZ formats.
     */
    public $multipleFiles = false;

    /** @var StiMonochromeDitheringType|int [enum] Gets or sets type of dithering. This property can't be used with EMF, SVG, SVGZ formats. */
    public $ditheringType = StiMonochromeDitheringType::FloydSteinberg;

    /** @var StiTiffCompressionScheme|int [enum] Gets or sets compression scheme of TIFF format. This property can't be used with EMF, SVG, SVGZ formats. */
    public $tiffCompressionScheme = StiTiffCompressionScheme::Default;

    /** @var bool Gets or sets a value indicating whether it is necessary to save output files as zip-file. */
    public $compressToArchive = false;


### Helpers


    public function getExportFormat(): int
    {
        if ($this->imageType == StiImageType::Bmp)
            return StiExportFormat::ImageBmp;

        if ($this->imageType == StiImageType::Gif)
            return StiExportFormat::ImageGif;

        if ($this->imageType == StiImageType::Jpeg)
            return StiExportFormat::ImageJpeg;

        if ($this->imageType == StiImageType::Pcx)
            return StiExportFormat::ImagePcx;

        if ($this->imageType == StiImageType::Png)
            return StiExportFormat::ImagePng;

        if ($this->imageType == StiImageType::Svg)
            return StiExportFormat::ImageSvg;

        if ($this->imageType == StiImageType::Svgz)
            return StiExportFormat::ImageSvgz;

        return StiExportFormat::ImageTiff;
    }

    public function setImageType($format = null)
    {
        if ($format === null)
            $format = $this->getExportFormat();

        switch ($format) {
            case StiExportFormat::ImageBmp:
                $this->imageType = StiImageType::Bmp;
                break;

            case StiExportFormat::ImageGif:
                $this->imageType = StiImageType::Gif;
                break;

            case StiExportFormat::ImageJpeg:
                $this->imageType = StiImageType::Jpeg;
                break;

            case StiExportFormat::ImagePcx:
                $this->imageType = StiImageType::Pcx;
                break;

            case StiExportFormat::ImagePng:
                $this->imageType = StiImageType::Png;
                break;

            case StiExportFormat::ImageSvg:
                $this->imageType = StiImageType::Svg;
                break;

            case StiExportFormat::ImageSvgz:
                $this->imageType = StiImageType::Svgz;
                break;

            case StiExportFormat::ImageTiff:
                $this->imageType = StiImageType::Tiff;
                break;
        }
    }


### HTML

    public function getHtml(): string
    {
        $this->setImageType();

        return parent::getHtml();
    }


### Constructor

    /**
     * @param StiImageType|int $imageType Type of the exported image file.
     */
    public function __construct(int $imageType = null)
    {
        parent::__construct();
        $this->imageType = $imageType;
    }
}