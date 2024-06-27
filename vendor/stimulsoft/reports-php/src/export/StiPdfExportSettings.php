<?php

namespace Stimulsoft\Export;

use Stimulsoft\Export\Enums\StiExportFormat;
use Stimulsoft\Export\Enums\StiImageFormat;
use Stimulsoft\Export\Enums\StiImageResolutionMode;
use Stimulsoft\Export\Enums\StiMonochromeDitheringType;
use Stimulsoft\Export\Enums\StiPdfAllowEditable;
use Stimulsoft\Export\Enums\StiPdfAutoPrintMode;
use Stimulsoft\Export\Enums\StiPdfComplianceMode;
use Stimulsoft\Export\Enums\StiPdfEncryptionKeyLength;
use Stimulsoft\Export\Enums\StiPdfImageCompressionMethod;
use Stimulsoft\Export\Enums\StiPdfZUGFeRDComplianceMode;
use Stimulsoft\Export\Enums\StiUserAccessPrivileges;

/**
 * Class describes settings for export to Adobe PDF format.
 */
class StiPdfExportSettings extends StiExportSettings
{

### Properties

    /** @var float Gets or sets image quality of images which will be exported to result file. */
    public $imageQuality = 0.75;

    /** @var int Gets or sets image resolution of images which will be exported to result file. */
    public $imageResolution = 100;

    /** @var StiImageResolutionMode|int [enum] Gets or sets image resolution mode. */
    public $imageResolutionMode = StiImageResolutionMode::Auto;

    /** @var bool Gets or sets value which indicates that fonts which used in report will be included in PDF file. */
    public $embeddedFonts = true;

    /** @var bool Gets or sets value which indicates that only standard PDF fonts will be used in result PDF file. */
    public $standardPdfFonts = false;

    /** @var bool Gets or sets value which indicates that result file will be used compression. */
    public $compressed = true;

    /** @var bool Gets or sets value which indicates that unicode symbols must be used in result PDF file. */
    public $useUnicode = true;

    /** @var bool Gets or sets value which indicates that digital signature is used for creating PDF file. */
    public $useDigitalSignature = false;

    /** @var bool Gets or sets value which indicates that certificate for PDF file digital signing must be get with help of special GUI. */
    public $getCertificateFromCryptoUI = true;

    /** @var bool Gets or sets value which indicates that rtf text will be exported as bitmap images or as vector images. */
    public $exportRtfTextAsImage = false;

    /** @var string Gets or sets user password for created PDF file. */
    public $passwordInputUser = '';

    /** @var string Gets or sets owner password for created PDF file. */
    public $passwordInputOwner = '';

    /** @var StiUserAccessPrivileges|int [enum] Gets or sets user access privileges when Adobe PDF file is viewing. */
    public $userAccessPrivileges = StiUserAccessPrivileges::All;

    /** @var StiPdfEncryptionKeyLength|int [enum] Gets or sets length of encryption key. */
    public $keyLength = StiPdfEncryptionKeyLength::Bit40;

    /** @var string Gets or sets information about the creator to be inserted into result PDF file. */
    public $creatorString = '';

    /** @var string Gets or sets keywords information to be inserted into result PDF file. */
    public $keywordsString = '';

    /** @var StiPdfImageCompressionMethod|int [enum] Gets or sets mode of image compression in PDF file. */
    public $imageCompressionMethod = StiPdfImageCompressionMethod::Jpeg;

    /** @var int Gets or sets a Palette size for the Indexed color mode of image compression. */
    public $imageIndexedColorPaletteSize = 96;

    /** @var StiImageFormat|int [enum] Gets or sets image format for exported images. */
    public $imageFormat = StiImageFormat::Color;

    /** @var StiMonochromeDitheringType|int [enum] Gets or sets type of dithering. */
    public $ditheringType = StiMonochromeDitheringType::FloydSteinberg;

    /** @var bool Gets or sets value which indicates that resulting PDF file is PDF/A compliance. */
    public $pdfACompliance = false;

    /** @var StiPdfComplianceMode|int [enum] Gets or sets value which indicates the PDF file compliance mode. */
    public $pdfComplianceMode = StiPdfComplianceMode::None;

    /** @var StiPdfAutoPrintMode|int [enum] Gets or sets a value indicating AutoPrint mode. */
    public $autoPrintMode = StiPdfAutoPrintMode::None;

    /** @var StiPdfAllowEditable|int [enum] */
    public $allowEditable = StiPdfAllowEditable::No;

    // public $embeddedFiles = [];

    /** @var StiPdfZUGFeRDComplianceMode|int [enum] Gets or sets value which indicates the ZUGFeRD compliance mode. */
    public $ZUGFeRDComplianceMode = StiPdfZUGFeRDComplianceMode::None;

    /** @var string Gets or sets value which indicates the ZUGFeRD Conformance Level. */
    public $ZUGFeRDConformanceLevel = 'BASIC';

    /** @var array Gets or sets value of the ZUGFeRD Invoice data. */
    public $ZUGFeRDInvoiceData = null;


### Helpers

    private function setPdfACompliance()
    {
        if ($this->pdfComplianceMode != StiPdfComplianceMode::None)
            $this->pdfACompliance = true;

        if ($this->pdfACompliance && $this->pdfComplianceMode == StiPdfComplianceMode::None)
            $this->pdfComplianceMode = StiPdfComplianceMode::A1;
    }

    public function getExportFormat(): int
    {
        return StiExportFormat::Pdf;
    }


### HTML

    public function getHtml(): string
    {
        $this->setPdfACompliance();

        return parent::getHtml();
    }
}