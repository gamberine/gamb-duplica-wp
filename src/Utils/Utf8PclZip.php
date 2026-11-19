<?php
namespace GambDuplicaWP\Utils;

/** Preserva nomes UTF-8 conforme o sinalizador padrão do formato ZIP. */
final class Utf8PclZip extends \PclZip
{
    public function privWriteFileHeader(&$header)
    {
        if (preg_match('//u',$header['stored_filename'])) { $header['flag']|=0x0800; }
        return parent::privWriteFileHeader($header);
    }
    public function privWriteCentralFileHeader(&$header)
    {
        if (preg_match('//u',$header['stored_filename'])) { $header['flag']|=0x0800; }
        return parent::privWriteCentralFileHeader($header);
    }
}
