<?php
namespace GambDuplicaWP\Utils;

/** Alternativa ZIP32 baseada no PclZip distribuído pelo WordPress. Não simula ZipArchive. */
final class WordPressZipBackend
{
    public $numFiles = 0;
    private $path = '';
    private $headers = [];
    private $indices = [];
    private $pending = [];
    private $directory = '';
    private $extracted = false;
    private $opened = false;
    const MAX_SIZE = 4294967294;
    const MAX_ENTRIES = 65534;

    public static function available()
    {
        if (PHP_INT_SIZE < 8 || !function_exists('gzopen') || !function_exists('hash_init')) { return false; }
        $temp = realpath(sys_get_temp_dir());
        if (!$temp || !is_writable($temp)) { return false; }
        if (!defined('PCLZIP_TEMPORARY_DIR')) { define('PCLZIP_TEMPORARY_DIR', rtrim(str_replace('\\','/',$temp),'/').'/'); }
        $configured = realpath(PCLZIP_TEMPORARY_DIR);
        $site = defined('ABSPATH') ? realpath(ABSPATH) : false;
        if (!$configured || !is_writable($configured) || ($site && strpos(strtolower(str_replace('\\','/',$configured).'/'),strtolower(str_replace('\\','/',$site).'/'))===0)) { return false; }
        if (!class_exists('PclZip', false)) {
            $core = defined('ABSPATH') ? ABSPATH.'wp-admin/includes/class-pclzip.php' : '';
            if (!is_file($core)) { return false; }
            require_once $core;
        }
        return class_exists('PclZip', false) && method_exists('PclZip','privReadEndCentralDir') && method_exists('PclZip','privReadCentralFileHeader');
    }

    public function open($path, $flags = 0)
    {
        if (!self::available() || is_link($path)) { return false; }
        $this->path=$path; $this->headers=[]; $this->indices=[]; $this->pending=[];
        if (is_file($path)) {
            if (filesize($path)<22) { return false; }
            try { $this->headers=$this->readHeaders($path); } catch (\Throwable $error) { return false; }
        } elseif (!($flags&1)) { return false; }
        $this->indices=array_column($this->headers,'index','name');
        $this->opened=true; $this->numFiles=count($this->headers); return true;
    }

    /** Ler nomes originais antes da redução de caminhos realizada por listContent(). */
    private function readHeaders($path)
    {
        if (filesize($path)>self::MAX_SIZE) { throw new \RuntimeException('O mecanismo portátil suporta ZIP32 até 4 GiB. ZIP64 requer ZipArchive.'); }
        $zip=new Utf8PclZip($path); $headers=[]; $seen=[]; $bytes=0;
        if ($zip->privOpenFd('rb')!==1) { throw new \RuntimeException('Não foi possível abrir o ZIP.'); }
        try {
            if (@$zip->privReadEndCentralDir($central)!==1 || $central['entries']>self::MAX_ENTRIES || $central['offset']===0xffffffff || $central['disk']!==0 || $central['disk_start']!==0) { throw new \RuntimeException('ZIP32 inválido, multipartes ou ZIP64 não suportado no mecanismo portátil.'); }
            fseek($zip->zip_fd,$central['offset']);
            for ($i=0;$i<$central['entries'];$i++) {
                if (@$zip->privReadCentralFileHeader($header)!==1) { throw new \RuntimeException('Diretório central ZIP inválido.'); }
                $raw=$header['stored_filename'];
                if (!ZipIntegrityValidator::isSafePath($raw) || strpos($raw,'\\')!==false || (($header['external']>>16)&0170000)===0120000) { throw new \RuntimeException('Caminho ou link não permitido no ZIP.'); }
                if (DIRECTORY_SEPARATOR==='\\') {
                    foreach (explode('/',rtrim($raw,'/')) as $part) {
                        if (preg_match('/[<>"|?*]|[. ]$/',$part) || preg_match('/^(?:CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])(?:\..*)?$/i',$part)) { throw new \RuntimeException('Nome de arquivo não portátil no ZIP.'); }
                    }
                }
                $name=\PclZipUtilPathReduction($raw); $identity=DIRECTORY_SEPARATOR==='\\' ? strtolower($name) : $name;
                if (isset($seen[$identity])) { throw new \RuntimeException('Entradas duplicadas ou ambíguas no ZIP.'); }
                $seen[$identity]=true; $bytes+=$header['size'];
                if ($header['version_extracted']>=45 || !in_array($header['compression'],[0,8],true) || $header['size']>=0xffffffff || $header['offset']>=0xffffffff || $header['compressed_size']>=0xffffffff || $bytes>self::MAX_SIZE || $header['offset']+30+$header['compressed_size']>$central['offset'] || ($header['flag']&1)) { throw new \RuntimeException('ZIP64, criptografia ou tamanho não suportado no mecanismo portátil.'); }
                $header['name']=$name; $header['index']=$i; $headers[]=$header;
            }
        } finally { $zip->privCloseFd(); }
        return $headers;
    }

    private function workspace()
    {
        if ($this->directory==='') {
            $this->directory=rtrim(sys_get_temp_dir(),'/\\').'/gamb-zip-'.bin2hex(random_bytes(12));
            if (!mkdir($this->directory,0700)) { throw new \RuntimeException('Não foi possível criar a área privada temporária do ZIP.'); }
        }
        return $this->directory;
    }
    public function addEmptyDir($name)
    {
        $dir=$this->workspace().'/empty-'.bin2hex(random_bytes(6)); mkdir($dir,0700);
        return $this->queue(rtrim($name,'/').'/',[PCLZIP_ATT_FILE_NAME=>$dir,PCLZIP_ATT_FILE_NEW_FULL_NAME=>rtrim($name,'/')]);
    }
    public function addFile($file,$name) { if (!is_file($file) || is_link($file) || !is_readable($file) || filesize($file)>self::MAX_SIZE) { return false; } return $this->queue($name,[PCLZIP_ATT_FILE_NAME=>$file,PCLZIP_ATT_FILE_NEW_FULL_NAME=>$name]); }
    public function addFromString($name,$content) { return $this->queue($name,[PCLZIP_ATT_FILE_NAME=>$name,PCLZIP_ATT_FILE_NEW_FULL_NAME=>$name,PCLZIP_ATT_FILE_CONTENT=>$content]); }
    private function queue($name,array $attributes)
    {
        if (!$this->opened || !ZipIntegrityValidator::isSafePath($name)) { return false; }
        $this->pending[$name]=['attributes'=>$attributes,'store'=>false];
        return true;
    }
    public function setCompressionName($name,$method) { if (isset($this->pending[$name]) && in_array($method,[0,8],true)) { $this->pending[$name]['store']=$method===0; return true; } return false; }
    public function setPassword($password) { return $password===''; }
    public function deleteName($name) { unset($this->pending[$name]); return true; }

    public function close()
    {
        if (!$this->opened) { return true; }
        if (!$this->pending && is_file($this->path)) { $this->opened=false; $this->cleanup(); return true; }
        $temporary=tempnam(dirname($this->path),'.gamb-zip-');
        if ($temporary===false) { return false; }
        @chmod($temporary,0600);
        try {
            if (count($this->headers)+count($this->pending)>self::MAX_ENTRIES) { throw new \RuntimeException('O limite ZIP32 foi atingido. Utilize ZipArchive para ZIP64.'); }
            if (is_file($this->path) && !copy($this->path,$temporary)) { return false; }
            $zip=new Utf8PclZip($temporary); $indices=[];
            foreach ($this->headers as $h) { if (isset($this->pending[$h['name']])) { $indices[]=$h['index']; } }
            if ($indices && $zip->delete(PCLZIP_OPT_BY_INDEX,$indices)===0) { throw new \RuntimeException($zip->errorInfo(true)); }
            foreach ([false,true] as $store) {
                $files=[]; foreach ($this->pending as $item) { if ($item['store']===$store) { $files[]=$item['attributes']; } }
                if (!$files) { continue; }
                $arguments=[$files,PCLZIP_OPT_TEMP_FILE_ON];
                if ($store) { $arguments[]=PCLZIP_OPT_NO_COMPRESSION; }
                $result=filesize($temporary)>0 ? $zip->add(...$arguments) : $zip->create(...$arguments);
                if ($result===0) { throw new \RuntimeException('Falha no ZIP portátil: '.$zip->errorInfo(true)); }
                foreach ($result as $file) { if (!in_array($file['status'],['ok','already_a_directory'],true)) { throw new \RuntimeException('Um arquivo do ZIP não foi concluído.'); } }
                clearstatcache(true,$temporary);
            }
            if (filesize($temporary)===0) { file_put_contents($temporary,"PK\x05\x06".str_repeat("\0",18)); }
            $headers=$this->readHeaders($temporary);
            if (!rename($temporary,$this->path)) { return false; }
            $this->headers=$headers; $this->indices=array_column($headers,'index','name'); $this->numFiles=count($headers); $this->pending=[]; $this->opened=false; return true;
        } finally { if (is_file($temporary)) { unlink($temporary); } $this->opened=false; $this->pending=[]; $this->cleanup(); }
    }
    public function statIndex($index) { if (!isset($this->headers[$index])) { return false; } $h=$this->headers[$index]; return ['name'=>$h['name'],'index'=>$index,'size'=>$h['size'],'crc'=>$h['crc'],'mtime'=>$h['mtime'],'comp_size'=>$h['compressed_size'],'comp_method'=>$h['compression'],'encryption_method'=>0]; }
    public function locateName($name,$flags=0) { if ($flags!==2) { return $this->indices[$name]??false; } foreach ($this->headers as $i=>$h) { if (basename($h['name'])===basename($name)) { return $i; } } return false; }
    public function statName($name,$flags=0) { $index=$this->locateName($name,$flags); return $index===false ? false : $this->statIndex($index); }
    public function getStream($name)
    {
        if ($this->locateName($name)===false) { return false; }
        if (!$this->extracted) {
            $space=@disk_free_space($this->workspace()); $bytes=array_sum(array_column($this->headers,'size'));
            if ($space!==false && $bytes>$space*0.9) { throw new \RuntimeException('Espaço temporário insuficiente para conferir o ZIP portátil.'); }
            $zip=new Utf8PclZip($this->path);
            $result=$zip->extract(PCLZIP_OPT_PATH,$this->workspace(),PCLZIP_OPT_EXTRACT_DIR_RESTRICTION,$this->workspace(),PCLZIP_OPT_TEMP_FILE_ON);
            if ($result===0) { throw new \RuntimeException('Falha ao conferir conteúdo do ZIP: '.$zip->errorInfo(true)); }
            foreach ($result as $file) { if ($file['status']!=='ok' && !($file['status']==='already_a_directory' && !empty($file['folder']))) { throw new \RuntimeException('Entrada ZIP não pôde ser conferida.'); } }
            $this->extracted=true;
        }
        $file=realpath($this->workspace().'/'.$name); $base=realpath($this->workspace());
        if (!$file || !is_file($file) || is_link($file) || strpos(str_replace('\\','/',$file),str_replace('\\','/',$base).'/')!==0) { return false; }
        $stat=$this->statName($name);
        if (filesize($file)!==$stat['size'] || hash_file('crc32b',$file)!==sprintf('%08x',$stat['crc'])) { throw new \RuntimeException('Falha na conferência de tamanho ou CRC do ZIP.'); }
        return fopen($file,'rb');
    }
    public function getFromIndex($index) { $s=$this->statIndex($index); if (!$s) { return false; } return $this->getFromName($s['name']); }
    public function getFromName($name) { $stream=$this->getStream($name); if (!$stream) { return false; } try { return stream_get_contents($stream); } finally { fclose($stream); } }
    public function extractTo($destination,$entries=null)
    {
        if (is_link($destination) || (!is_dir($destination) && !mkdir($destination,0755,true))) { return false; }
        $base=realpath($destination);if ($base===false) { return false; }
        $names=$entries===null ? array_column($this->headers,'name') : (array)$entries;
        foreach ($names as $name) {
            if (!ZipIntegrityValidator::isSafePath($name) || $this->locateName($name)===false) { return false; }
            $target=rtrim($base,'/\\').'/'.$name;
            $directory=substr($name,-1)==='/';$parent=$base;
            $relative=$directory ? rtrim($name,'/') : dirname($name);
            if ($relative!=='.') {
                foreach (explode('/',$relative) as $part) {
                    $parent.='/'.$part;
                    if (is_link($parent) || (!is_dir($parent) && !mkdir($parent,0755))) { return false; }
                }
            }
            if ($directory) { continue; }
            $parent=realpath(dirname($target));
            if (is_link($target) || !$parent || strpos(str_replace('\\','/',$parent).'/',rtrim(str_replace('\\','/',$base),'/').'/')!==0) { return false; }
            $stream=$this->getStream($name);if (!$stream) { return false; }
            try { $out=fopen($target,'wb');if (!$out) { return false; } try { if (stream_copy_to_stream($stream,$out)===false) { return false; } } finally { fclose($out); } } finally { fclose($stream); }
        }
        return true;
    }
    private function cleanup()
    {
        if ($this->directory==='' || !is_dir($this->directory)) { return; }
        $iterator=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->directory,\FilesystemIterator::SKIP_DOTS),\RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($iterator as $file) { if ($file->isLink() || $file->isFile()) { @unlink($file->getPathname()); } else { @rmdir($file->getPathname()); } }
        @rmdir($this->directory); $this->directory=''; $this->extracted=false;
    }
    public function __destruct() { $this->cleanup(); }
}
