<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration for setting Code Pages. */
class StiDbfCodePages
{
    /** A parameter indicating that the code page of the exported document will not be specified. */
    const Default = 0;

    /** A code page of the exported document is U.S. MS-DOS. Code page number 437. */
    const USDOS = 437;

    /** A code page of the exported document is Mazovia (Polish) MS-DOS. Code page number 620. */
    const MazoviaDOS = 620;

    /** A code page of the exported document is Greek MS-DOS (437G). Code page number 737. */
    const GreekDOS = 737;

    /** A code page of the exported document is International MS-DOS. Code page number 850. */
    const InternationalDOS = 850;

    /** A code page of the exported document is Eastern European MS-DOS. Code page number 852. */
    const EasternEuropeanDOS = 852;

    /** A code page of the exported document is Icelandic MS-DOS. Code page number 861. */
    const IcelandicDOS = 861;

    /** A code page of the exported document is Nordic MS-DOS. Code page number 865. */
    const NordicDOS = 865;

    /** A code page of the exported document is Russian MS-DOS. Code page number 866. */
    const RussianDOS = 866;

    /** A code page of the exported document is Kamenicky (Czech) MS-DOS. Code page number 895. */
    const KamenickyDOS = 895;

    /** A code page of the exported document is Turkish MS-DOS. Code page number 857. */

    const TurkishDOS = 857;

    /** A code page of the exported document is EasternEuropean MS-DOS. Code page number 1250. */
    const EasternEuropeanWindows = 1250;

    /** A code page of the exported document is Russian Windows. Code page number 1251. */

    const RussianWindows = 1251;

    /** A code page of the exported document is Windows ANSI. Code page number 1252. */

    const WindowsANSI = 1252;

    /** A code page of the exported document is Greek Windows. Code page number 1253. */

    const GreekWindows = 1253;

    /** A code page of the exported document is Turkish Windows. Code page number 1254. */

    const TurkishWindows = 1254;

    /** A code page of the exported document is Standard Macintosh. Code page number 10000. */
    const StandardMacintosh = 10000;

    /** A code page of the exported document is Greek Macintosh. Code page number 10006. */

    const GreekMacintosh = 10006;

    /** A code page of the exported document is Russian Macintosh. Code page number 10007. */

    const RussianMacintosh = 10007;

    /** A code page of the exported document is Eastern European Macintosh. Code page number 10029. */
    const EasternEuropeanMacintosh = 10029;
}