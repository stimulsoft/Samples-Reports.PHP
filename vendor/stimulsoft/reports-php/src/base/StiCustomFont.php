<?php

namespace Stimulsoft;

class StiCustomFont extends StiElement
{

### Fields

    private $filePath;
    private $fontName;
    private $fontStyle;


### HTML

    public function getHtml(): string
    {
        $result = '';

        $filePath = StiFunctions::getJavaScriptValue($this->filePath);
        $fontName = StiFunctions::getJavaScriptValue($this->fontName);
        $fontStyle = StiFunctions::getJavaScriptValue($this->fontStyle);

        $result .= "Stimulsoft.Base.StiFontCollection.addFontFile($filePath, $fontName, $fontStyle);\n";

        return $result . parent::getHtml();
    }


### Constructor

    public function __construct(string $filePath, $fontName, $fontStyle)
    {
        $this->filePath = $filePath;
        $this->fontName = $fontName;
        $this->fontStyle = $fontStyle;
    }
}