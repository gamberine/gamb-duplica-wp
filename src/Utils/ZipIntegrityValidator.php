<?php
namespace GambDuplicaWP\Utils;

/** Validação de tamanho e CRC; o mecanismo portátil utiliza extração temporária privada. */
final class ZipIntegrityValidator
{
    public static function isSafePath($name)
    {
        return is_string($name) && $name !== '' && !preg_match('/[\x00-\x1f:]/', $name)
            && !preg_match('~^(?:[/\\\\]|[A-Za-z]:)|(?:^|[/\\\\])\.\.(?:[/\\\\]|$)~', $name);
    }

    public static function validate($path, $password = '', array $requiredPaths = [])
    {
        if (!ZipArchiveExtended::isPhpZipAvailable()) { throw new \RuntimeException('A validação ZIP requer ZipArchive ou PclZip do WordPress com zlib e pasta temporária privada.'); }
        $zip = ZipArchiveExtended::createHandle();
        $result = $zip->open($path, ZipArchiveExtended::CHECKCONS);
        if ($result !== true) { throw new \RuntimeException('Arquivo ZIP inválido ou inconsistente. Código: ' . $result); }
        try {
            if ($password !== '' && !$zip->setPassword($password)) { throw new \RuntimeException('Não foi possível definir a senha para validação.'); }
            $seen = []; $bytes = 0; $files = 0;
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $stat = $zip->statIndex($i);
                if (!$stat || !self::isSafePath($stat['name'])) { throw new \RuntimeException('O ZIP contém um caminho inválido.'); }
                $name = str_replace('\\', '/', $stat['name']);
                if (isset($seen[$name])) { throw new \RuntimeException('O ZIP contém entradas duplicadas.'); }
                $seen[$name] = true;
                if (substr($name, -1) === '/') { continue; }
                $stream = @$zip->getStream($stat['name']);
                if (!$stream) { throw new \RuntimeException('Não foi possível ler uma entrada do ZIP. Verifique integridade e senha.'); }
                $hash = hash_init('crc32b'); $size = 0;
                try {
                    while (!feof($stream)) {
                        $chunk = @fread($stream, 1048576);
                        if ($chunk === false || ($chunk === '' && !feof($stream))) { throw new \RuntimeException('Falha na leitura de uma entrada do ZIP.'); }
                        $size += strlen($chunk); hash_update($hash, $chunk);
                    }
                } finally { fclose($stream); }
                if ($size !== $stat['size'] || hash_final($hash) !== sprintf('%08x', $stat['crc'])) { throw new \RuntimeException('Falha na conferência de tamanho ou CRC do ZIP.'); }
                $bytes += $size; $files++;
            }
            foreach ($requiredPaths as $required) { if (!isset($seen[str_replace('\\', '/', $required)])) { throw new \RuntimeException('Arquivo obrigatório ausente no pacote: ' . $required); } }
            return ['files' => $files, 'bytes' => $bytes, 'entries' => count($seen)];
        } finally { $zip->close(); }
    }
}
