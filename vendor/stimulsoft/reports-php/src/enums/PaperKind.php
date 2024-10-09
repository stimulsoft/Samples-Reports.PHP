<?php

namespace Stimulsoft\Enums;

class PaperKind
{
    /** The paper size is defined by the user. */
    const Custom = 0;

    /** Letter paper (8.5 in. by 11 in.). */
    const Letter = 1;

    /** Legal paper (8.5 in. by 14 in.). */
    const Legal = 5;

    /** A4 paper (210 mm by 297 mm). */
    const A4 = 9;

    /** C paper (17 in. by 22 in.). */
    const CSheet = 24;

    /** D paper (22 in. by 34 in.). */
    const DSheet = 25;

    /** E paper (34 in. by 44 in.). */
    const ESheet = 26;

    /** Letter small paper (8.5 in. by 11 in.). */
    const LetterSmall = 2;

    /** Tabloid paper (11 in. by 17 in.). */
    const Tabloid = 3;

    /** Ledger paper (17 in. by 11 in.). */
    const Ledger = 4;

    /** Statement paper (5.5 in. by 8.5 in.). */
    const Statement = 6;

    /** Executive paper (7.25 in. by 10.5 in.). */
    const Executive = 7;

    /** A3 paper (297 mm by 420 mm). */
    const A3 = 8;

    /** A4 small paper (210 mm by 297 mm). */
    const A4Small = 10;

    /** A5 paper (148 mm by 210 mm). */
    const A5 = 11;

    /** B4 paper (250 mm by 353 mm). */
    const B4 = 12;

    /** B5 paper (176 mm by 250 mm). */
    const B5 = 13;

    /** Folio paper (8.5 in. by 13 in.). */
    const Folio = 14;

    /** Quarto paper (215 mm by 275 mm). */
    const Quarto = 15;

    /** Standard paper (10 in. by 14 in.). */
    const Standard10x14 = 16;

    /** Standard paper (11 in. by 17 in.). */
    const Standard11x17 = 17;

    /** Note paper (8.5 in. by 11 in.). */
    const Note = 18;

    /** #9 envelope (3.875 in. by 8.875 in.). */
    const Number9Envelope = 19;

    /** #10 envelope (4.125 in. by 9.5 in.). */
    const Number10Envelope = 20;

    /** #11 envelope (4.5 in. by 10.375 in.). */
    const Number11Envelope = 21;

    /** #12 envelope (4.75 in. by 11 in.). */
    const Number12Envelope = 22;

    /** #14 envelope (5 in. by 11.5 in.). */
    const Number14Envelope = 23;

    /** DL envelope (110 mm by 220 mm). */
    const DLEnvelope = 27;

    /** C5 envelope (162 mm by 229 mm). */
    const C5Envelope = 28;

    /** C3 envelope (324 mm by 458 mm). */
    const C3Envelope = 29;

    /** C4 envelope (229 mm by 324 mm). */
    const C4Envelope = 30;

    /** C6 envelope (114 mm by 162 mm). */
    const C6Envelope = 31;

    /** C65 envelope (114 mm by 229 mm). */
    const C65Envelope = 32;

    /** B4 envelope (250 mm by 353 mm). */
    const B4Envelope = 33;

    /** B5 envelope (176 mm by 250 mm). */
    const B5Envelope = 34;

    /** B6 envelope (176 mm by 125 mm). */
    const B6Envelope = 35;

    /** Italy envelope (110 mm by 230 mm). */
    const ItalyEnvelope = 36;

    /** Monarch envelope (3.875 in. by 7.5 in.). */
    const MonarchEnvelope = 37;

    /** 6 3/4 envelope (3.625 in. by 6.5 in.). */
    const PersonalEnvelope = 38;

    /** US standard fanfold (14.875 in. by 11 in.). */
    const USStandardFanfold = 39;

    /** German standard fanfold (8.5 in. by 12 in.). */
    const GermanStandardFanfold = 40;

    /** German legal fanfold (8.5 in. by 13 in.). */
    const GermanLegalFanfold = 41;

    /** ISO B4 (250 mm by 353 mm). */
    const IsoB4 = 42;

    /** Japanese postcard (100 mm by 148 mm). */
    const JapanesePostcard = 43;

    /** Standard paper (9 in. by 11 in.). */
    const Standard9x11 = 44;

    /** Standard paper (10 in. by 11 in.). */
    const Standard10x11 = 45;

    /** Standard paper (15 in. by 11 in.). */
    const Standard15x11 = 46;

    /** Invitation envelope (220 mm by 220 mm). */
    const InviteEnvelope = 47;

    /**
     * Letter extra paper (9.275 in. by 12 in.). This value is specific to the PostScript
     * driver and is used only by Linotronic printers in order to conserve paper.
     */
    const LetterExtra = 50;

    /**
     * Legal extra paper (9.275 in. by 15 in.). This value is specific to the PostScript
     * driver and is used only by Linotronic printers in order to conserve paper.
     */
    const LegalExtra = 51;

    /**
     * Tabloid extra paper (11.69 in. by 18 in.). This value is specific to the PostScript
     * driver and is used only by Linotronic printers in order to conserve paper.
     */
    const TabloidExtra = 52;

    /**
     * A4 extra paper (236 mm by 322 mm). This value is specific to the PostScript driver
     * and is used only by Linotronic printers to help save paper.
     */
    const A4Extra = 53;

    /** Letter transverse paper (8.275 in. by 11 in.). */
    const LetterTransverse = 54;

    /** A4 transverse paper (210 mm by 297 mm). */
    const A4Transverse = 55;

    /** Letter extra transverse paper (9.275 in. by 12 in.). */
    const LetterExtraTransverse = 56;

    /** SuperA/SuperA/A4 paper (227 mm by 356 mm). */
    const APlus = 57;

    /** SuperB/SuperB/A3 paper (305 mm by 487 mm). */
    const BPlus = 58;

    /** Letter plus paper (8.5 in. by 12.69 in.). */
    const LetterPlus = 59;

    /** A4 plus paper (210 mm by 330 mm). */
    const A4Plus = 60;

    /** A5 transverse paper (148 mm by 210 mm). */
    const A5Transverse = 61;

    /** JIS B5 transverse paper (182 mm by 257 mm). */
    const B5Transverse = 62;

    /** A3 extra paper (322 mm by 445 mm). */
    const A3Extra = 63;

    /** A5 extra paper (174 mm by 235 mm). */
    const A5Extra = 64;

    /** ISO B5 extra paper (201 mm by 276 mm). */
    const B5Extra = 65;

    /** A2 paper (420 mm by 594 mm). */
    const A2 = 66;

    /** A3 transverse paper (297 mm by 420 mm). */
    const A3Transverse = 67;

    /** A3 extra transverse paper (322 mm by 445 mm). */
    const A3ExtraTransverse = 68;

    /** Japanese double postcard (200 mm by 148 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseDoublePostcard = 69;

    /** A6 paper (105 mm by 148 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const A6 = 70;

    /** Japanese Kaku #2 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeKakuNumber2 = 71;

    /** Japanese Kaku #3 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeKakuNumber3 = 72;

    /** Japanese Chou #3 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeChouNumber3 = 73;

    /** Japanese Chou #4 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeChouNumber4 = 74;

    /** Letter rotated paper (11 in. by 8.5 in.). */
    const LetterRotated = 75;

    /** A3 rotated paper (420 mm by 297 mm). */
    const A3Rotated = 76;

    /** A4 rotated paper (297 mm by 210 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const A4Rotated = 77;

    /** A5 rotated paper (210 mm by 148 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const A5Rotated = 78;

    /** JIS B4 rotated paper (364 mm by 257 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const B4JisRotated = 79;

    /** JIS B5 rotated paper (257 mm by 182 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const B5JisRotated = 80;

    /** Japanese rotated postcard (148 mm by 100 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const JapanesePostcardRotated = 81;

    /** Japanese rotated double postcard (148 mm by 200 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseDoublePostcardRotated = 82;

    /** A6 rotated paper (148 mm by 105 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const A6Rotated = 83;

    /** Japanese rotated Kaku #2 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeKakuNumber2Rotated = 84;

    /** Japanese rotated Kaku #3 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeKakuNumber3Rotated = 85;

    /** Japanese rotated Chou #3 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeChouNumber3Rotated = 86;

    /** Japanese rotated Chou #4 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeChouNumber4Rotated = 87;

    /** JIS B6 paper (128 mm by 182 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const B6Jis = 88;

    /** JIS B6 rotated paper (182 mm by 128 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const B6JisRotated = 89;

    /** Standard paper (12 in. by 11 in.). Requires Windows 98, Windows NT 4.0, or later. */
    const Standard12x11 = 90;

    /** Japanese You #4 envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeYouNumber4 = 91;

    /** Japanese You #4 rotated envelope. Requires Windows 98, Windows NT 4.0, or later. */
    const JapaneseEnvelopeYouNumber4Rotated = 92;

    /** 16K paper (146 mm by 215 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc16K = 93;

    /** 32K paper (97 mm by 151 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc32K = 94;

    /** 32K big paper (97 mm by 151 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc32KBig = 95;

    /** #1 envelope (102 mm by 165 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber1 = 96;

    /** #2 envelope (102 mm by 176 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber2 = 97;

    /** #3 envelope (125 mm by 176 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber3 = 98;

    /** #4 envelope (110 mm by 208 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber4 = 99;

    /** #5 envelope (110 mm by 220 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber5 = 100;

    /** #6 envelope (120 mm by 230 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber6 = 101;

    /** #7 envelope (160 mm by 230 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber7 = 102;

    /** #8 envelope (120 mm by 309 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber8 = 103;

    /** #9 envelope (229 mm by 324 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber9 = 104;

    /** #10 envelope (324 mm by 458 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber10 = 105;

    /** 16K rotated paper (146 mm by 215 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc16KRotated = 106;

    /** 32K rotated paper (97 mm by 151 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc32KRotated = 107;

    /** 32K big rotated paper (97 mm by 151 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const Prc32KBigRotated = 108;

    /** #1 rotated envelope (165 mm by 102 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber1Rotated = 109;

    /** #2 rotated envelope (176 mm by 102 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber2Rotated = 110;

    /** #3 rotated envelope (176 mm by 125 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber3Rotated = 111;

    /** #4 rotated envelope (208 mm by 110 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber4Rotated = 112;

    /** Envelope #5 rotated envelope (220 mm by 110 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber5Rotated = 113;

    /** #6 rotated envelope (230 mm by 120 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber6Rotated = 114;

    /** #7 rotated envelope (230 mm by 160 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber7Rotated = 115;

    /** #8 rotated envelope (309 mm by 120 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber8Rotated = 116;

    /** #9 rotated envelope (324 mm by 229 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber9Rotated = 117;

    /** #10 rotated envelope (458 mm by 324 mm). Requires Windows 98, Windows NT 4.0, or later. */
    const PrcEnvelopeNumber10Rotated = 118;
}