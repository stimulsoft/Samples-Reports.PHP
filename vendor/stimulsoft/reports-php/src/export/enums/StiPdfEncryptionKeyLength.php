<?php

namespace Stimulsoft\Export\Enums;

/** Enumeration which sets an encryption key length of the resulting pdf file. */
class StiPdfEncryptionKeyLength
{
    /** RC4 algorithm, 40 bit encryption key length (Acrobat 3). */
    const Bit40 = 1;

    /** RC4 algorithm, 128 bit encryption key length (Acrobat 5). */
    const Bit128 = 2;

    /** AES algorithm, 128 bit encryption key length, revision 4 (Acrobat 7). */
    const Bit128_r4 = 3;

    /** AES algorithm, 256 bit encryption key length, revision 5 (Acrobat 9). */
    const Bit256_r5 = 4;

    /** AES algorithm, 256 bit encryption key length, revision 6 (Acrobat X). */
    const Bit256_r6 = 5;
}