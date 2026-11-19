<?php
use GambDuplicaWP\Utils\WordPressZipBackend;
use GambDuplicaWP\Utils\ZipArchiveExtended;
use GambDuplicaWP\Utils\ZipIntegrityValidator;

expect(ZipArchiveExtended::isPhpZipAvailable(),'Disponibilizar ZIP PHP nativo ou portátil.');
expect(WordPressZipBackend::available(),'Disponibilizar PclZip oficial sem bibliotecas externas.');
reject(static function(){ZipIntegrityValidator::validate(__DIR__.'/runtime/missing.zip');},'Não considerar arquivo ausente como ZIP vazio válido.');
$fixtures=__DIR__.'/runtime/fixtures/';
foreach(['unsafe.zip','normalized-traversal.zip','duplicate.zip','truncated.zip','crc.zip'] as $name){reject(static function()use($fixtures,$name){ZipIntegrityValidator::validate($fixtures.$name);},'Recusar travessia, duplicatas, truncamento e CRC inválido.');}
expect(ZipIntegrityValidator::validate($fixtures.'external.zip')['files']===2,'Conferir ZIP produzido por implementação independente.');
$prefix=__DIR__.'/runtime/portable-'.bin2hex(random_bytes(6));
$backend=new WordPressZipBackend();
expect($backend->open($prefix.'.zip',1)===true,'Criar arquivo pelo mecanismo portátil.');
expect($backend->addFromString('database.sql','-- fixture'),'Adicionar SQL pelo mecanismo portátil.');
expect($backend->addFromString('dir/ação.txt','conteúdo UTF-8'),'Preservar nomes e conteúdo UTF-8.');
expect($backend->addEmptyDir('empty/'),'Adicionar diretório vazio portátil.');
expect($backend->close(),'Concluir ZIP portátil.');
expect(ZipIntegrityValidator::validate($prefix.'.zip')['entries']===3,'Validar o ZIP portátil pelo mecanismo disponível.');
$reader=new WordPressZipBackend();expect($reader->open($prefix.'.zip')===true,'Reabrir ZIP portátil.');
expect($reader->getFromName('dir/ação.txt')==='conteúdo UTF-8','Recuperar o conteúdo original.');
expect($reader->statName('ação.txt',ZipArchiveExtended::FL_NODIR)['name']==='dir/ação.txt','Localizar arquivo pelo nome no importador.');
$reader->close();
$archive=new ZipArchiveExtended($prefix.'.zip');expect($archive->open(),'Reabrir ZIP para continuação em outra etapa.');
expect($archive->addFileFromString('database.sql','-- updated fixture'),'Substituir uma entrada sem duplicá-la.');
expect($archive->addFileFromString('new.txt','new'),'Acrescentar arquivo após reabertura.');expect($archive->close(),'Concluir a continuação do ZIP.');
$result=ZipIntegrityValidator::validate($prefix.'.zip','',['database.sql']);
expect($result['files']===3 && $result['entries']===4,'Manter diretórios e arquivos após substituição.');
$reader=ZipArchiveExtended::createHandle();expect($reader->open($prefix.'.zip')===true,'Abrir pacote no importador.');
expect($reader->getFromName('database.sql')==='-- updated fixture','Ler conteúdo substituído.');
expect($reader->extractTo($prefix.'-restore',['dir/ação.txt']),'Restaurar arquivo selecionado.');
expect(file_get_contents($prefix.'-restore/dir/ação.txt')==='conteúdo UTF-8','Conferir arquivo restaurado.');$reader->close();
$empty=new WordPressZipBackend();$empty->open($prefix.'-empty.zip',1);expect($empty->close(),'Criar ZIP vazio consistente.');
expect(ZipIntegrityValidator::validate($prefix.'-empty.zip')['files']===0,'Distinguir ZIP vazio de arquivo ausente.');
$safe=new WordPressZipBackend();$safe->open($prefix.'.zip');expect(!$safe->setPassword('must-not-downgrade'),'Não aceitar senha no mecanismo sem criptografia.');$safe->close();
$large=$prefix.'-large.bin';$stream=fopen($large,'wb');for($i=0;$i<320;$i++)fwrite($stream,str_repeat('X',65536));fclose($stream);
$largeZip=new WordPressZipBackend();$largeZip->open($prefix.'-large.zip',1);expect($largeZip->addFile($large,'large.bin'),'Adicionar arquivo maior que os buffers.');expect($largeZip->close(),'Compactar utilizando arquivos temporários.');
expect(ZipIntegrityValidator::validate($prefix.'-large.zip')['bytes']===filesize($large),'Conferir arquivo grande sem tratar DAF como ZIP.');
$ordered=new WordPressZipBackend();$ordered->open($prefix.'-order.zip',1);$ordered->addFromString('parent/child.txt','ordered');$ordered->addEmptyDir('parent');$ordered->close();
expect(ZipIntegrityValidator::validate($prefix.'-order.zip')['files']===1,'Aceitar diretório registrado depois de seus arquivos.');
if(!class_exists('ZipArchive')){expect(!ZipArchiveExtended::isEncryptionAvaliable(),'Não anunciar AES quando a extensão nativa está ausente.');reject(static function()use($prefix){$zip=new ZipArchiveExtended($prefix.'-aes.zip');$zip->setEncrypt(true,'fixture-password');},'Recusar AES sem suporte nativo.');}
file_put_contents(__DIR__.'/runtime/last-portable-package.txt',$prefix.'.zip');
