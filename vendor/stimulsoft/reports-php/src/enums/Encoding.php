<?php

namespace Stimulsoft\Enums;

class Encoding
{
    const ASCII = 'Stimulsoft.System.Text.Encoding.ASCII';
    const BigEndianUnicode = 'Stimulsoft.System.Text.Encoding.BigEndianUnicode';
    const Default = 'Stimulsoft.System.Text.Encoding.Default';
    const Unicode = 'Stimulsoft.System.Text.Encoding.Unicode';
    const UTF32 = 'Stimulsoft.System.Text.Encoding.UTF32';
    const UTF7 = 'Stimulsoft.System.Text.Encoding.UTF7';
    const UTF8 = 'Stimulsoft.System.Text.Encoding.UTF8';
    const Windows1250 = 'Stimulsoft.System.Text.Encoding.Windows1250';
    const Windows1251 = 'Stimulsoft.System.Text.Encoding.Windows1251';
    const Windows1252 = 'Stimulsoft.System.Text.Encoding.Windows1252';
    const Windows1256 = 'Stimulsoft.System.Text.Encoding.Windows1256';
    const ISO_8859_1 = 'Stimulsoft.System.Text.Encoding.ISO_8859_1';


### Helpers

    public static function getByName($name)
    {
        if (defined("Stimulsoft\Enums\Encoding::$name"))
            return constant("Stimulsoft\Enums\Encoding::$name");

        return $name;
    }
}