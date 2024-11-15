<?php

namespace Stimulsoft;

use Stimulsoft\Enums\FontStyle;

class StiFontCollection extends StiElement
{

### Fields

    private static $fontsCollection = [];
    private static $fontsFolder = null;


### Methods

    /**
     * Adds the specified font file to the collection for use in the report generator.
     * @param string $filePath Path or URL to the font file.
     * @param string|null $fontName Uses the specified name for this font.
     * @param int|FontStyle|null $fontStyle [enum] Uses the specified style for this font.
     */
    public static function addFontFile(string $filePath, string $fontName = null, int $fontStyle = null)
    {
        if (!StiFunctions::isNullOrEmpty($filePath)) {
            $filePath = preg_replace('/\\\\/', '/', $filePath);
            self::$fontsCollection[] = new StiCustomFont($filePath, $fontName, $fontStyle);
        }
    }

    public static function setFontsFolder(string $folderPath)
    {
        self::$fontsFolder = $folderPath;
    }


### HTML

    public function getHtml(): string
    {
        $result = '';

        if (!StiFunctions::isNullOrEmpty(self::$fontsFolder)) {
            $folderPath = StiFunctions::getJavaScriptValue(self::$fontsFolder);
            $result .= "Stimulsoft.Base.StiFontCollection.setFontsFolder($folderPath);\n";
        }

        /** @var StiCustomFont $font */
        foreach (self::$fontsCollection as $font) {
            $result .= $font->getHtml();
        }

        return $result . parent::getHtml();
    }
}