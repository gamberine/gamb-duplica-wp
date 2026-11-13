<?php
define('ABSPATH', __DIR__ . '/runtime/');
define('GambDuplicaWP____PATH', dirname(__DIR__));
require GambDuplicaWP____PATH . '/src/Utils/AbstractAutoloader.php';
require GambDuplicaWP____PATH . '/src/Utils/Autoloader.php';
\GambDuplicaWP\Utils\Autoloader::register();
use GambDuplicaWP\Utils\ZipArchiveExtended;
use GambDuplicaWP\Utils\ZipIntegrityValidator;
use GambDuplicaWP\ProjectPolicy;
$checks = 0;
function expect($ok, $label) { global $checks; if (!$ok) { throw new RuntimeException($label); } $checks++; }
function reject($callback, $label) { try { $callback(); } catch (RuntimeException $error) { expect(true, $label); return; } throw new RuntimeException($label); }
class WP_Error { public $code; public function __construct($code, $message) { $this->code = $code; } }
expect(ProjectPolicy::guardUnconfiguredServices(false, [], 'https://connect.gamb-duplica-wp.com/test') instanceof WP_Error, 'Recusar serviço não configurado.');
expect(ProjectPolicy::guardUnconfiguredServices(false, [], 'https://gamb-duplica-wp.com/test') instanceof WP_Error, 'Recusar domínio principal não configurado.');
expect(ProjectPolicy::guardUnconfiguredServices(false, [], 'https://api.github.com/test') === false, 'Preservar outros serviços.');
expect(class_exists('GAMB_DUPLICA_WP_Constants'), 'Carregar classe legada renomeada.');
expect(class_exists('GambDuplicaWP\Addons\ProBase\License\License'), 'Carregar complemento e instalador.');
expect(class_exists('VendorGambDuplicaWP\Amk\JsonSerialize\AbstractJsonSerializable'), 'Carregar dependência prefixada.');
foreach (['../outside', '/etc/passwd', 'C:\\outside', 'dir/../../outside', "bad\0name"] as $name) { expect(!ZipIntegrityValidator::isSafePath($name), 'Recusar caminho inseguro.'); }
expect(ZipIntegrityValidator::isSafePath('wp-content/themes/example/index.php'), 'Aceitar caminho relativo.');
if (!class_exists('ZipArchive')) {
    reject(static function () { ZipIntegrityValidator::validate('missing.zip'); }, 'Informar extensão ausente.');
    echo $checks . " verificações concluídas sem extensão ZIP.\n"; exit;
}
$dir = __DIR__ . '/runtime'; if (!is_dir($dir)) { mkdir($dir, 0777, true); }
// Cada execução utiliza nomes novos; não interfere em pacotes anteriores.
$prefix = $dir . '/' . bin2hex(random_bytes(6));
file_put_contents($prefix . '-invalid.zip', 'not a zip archive');
$invalid = new ZipArchiveExtended($prefix . '-invalid.zip');
expect($invalid->open() === false, 'Retornar false para arquivo inválido, sem confundir código de erro com sucesso.');
$archive = new ZipArchiveExtended($prefix . '.zip');
expect($archive->open() === true, 'Abrir arquivo ZIP.');
expect($archive->addEmptyDir(''), 'Ignorar a raiz vazia do índice de arquivos.');
expect($archive->addFileFromString('database.sql', '-- synthetic fixture'), 'Adicionar SQL de teste.');
expect($archive->addFileFromString('wp-content/themes/example/style.css', 'body{color:#123456}'), 'Adicionar recurso de teste.');
expect($archive->addEmptyDir('empty/'), 'Adicionar diretório vazio.');
reject(static function () use ($archive) { $archive->addFileFromString('../escape.txt', 'unsafe'); }, 'Bloquear travessia na criação.');
expect($archive->close(), 'Concluir arquivo ZIP.');
$result = ZipIntegrityValidator::validate($prefix . '.zip', '', ['database.sql']);
expect($result['files'] === 2 && $result['entries'] === 3, 'Conferir todos os arquivos e diretórios.');
reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '.zip', '', ['absent.php']); }, 'Detectar arquivo obrigatório ausente.');
$unsafe = new ZipArchive(); $unsafe->open($prefix . '-unsafe.zip', ZipArchive::CREATE); $unsafe->addFromString('../escape.txt', 'unsafe'); $unsafe->close();
reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '-unsafe.zip'); }, 'Rejeitar caminho inseguro na validação.');
file_put_contents($prefix . '-broken.zip', substr(file_get_contents($prefix . '.zip'), 0, -20));
reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '-broken.zip'); }, 'Rejeitar ZIP truncado.');
$stored = new ZipArchive(); $stored->open($prefix . '-crc.zip', ZipArchive::CREATE); $stored->addFromString('file.txt', 'UNIQUE-CONTENT-TEST'); $stored->setCompressionName('file.txt', ZipArchive::CM_STORE); $stored->close();
$bytes = file_get_contents($prefix . '-crc.zip'); $bytes = str_replace('UNIQUE-CONTENT-TEST', 'BROKEN-CONTENT-TEST', $bytes); file_put_contents($prefix . '-crc.zip', $bytes);
reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '-crc.zip'); }, 'Detectar corrupção por CRC.');
$secure = new ZipArchiveExtended($prefix . '-encrypted.zip');
reject(static function () use ($secure) { $secure->setEncrypt(true, ''); }, 'Recusar criptografia sem senha.');
if (ZipArchiveExtended::isEncryptionAvaliable()) {
    expect($secure->setEncrypt(true, 'synthetic-fixture-password'), 'Habilitar AES-256.');
    expect($secure->open() === true && $secure->addFileFromString('database.sql', '-- encrypted fixture') && $secure->close(), 'Concluir ZIP criptografado.');
    expect(ZipIntegrityValidator::validate($prefix . '-encrypted.zip', 'synthetic-fixture-password')['files'] === 1, 'Validar ZIP com senha correta.');
    reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '-encrypted.zip', 'wrong-password'); }, 'Recusar senha incorreta.');
    reject(static function () use ($prefix) { ZipIntegrityValidator::validate($prefix . '-encrypted.zip'); }, 'Recusar leitura sem senha.');
}
echo $checks . " verificações concluídas com extensão ZIP.\n";
