=== Gamb Duplica WP ===
Contributors: gamberine
Tags: backup, migration, zip, database
Requires at least: 5.3
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 5.0.1
License: GPL-3.0-or-later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Cópias de segurança e preparação de pacotes de recuperação para WordPress.

== Descrição ==

Distribuição independente derivada de uma base modificada do Duplicator Pro 4.5.21.
Os avisos de autoria e licença dos componentes incorporados são preservados.
Não são fornecidas assinatura comercial, chaves ou credenciais de integrações.

O backup manual local inclui banco, arquivos e instalador. ZIP pelo PHP exige
ZipArchive habilitado no servidor web. DAF é um formato próprio e não equivale
a ZIP. Agendamento, nuvem, multisite e migração completa entre servidores não
foram homologados nesta auditoria.

== Instalação e operação ==

Confira o SHA-256 do pacote, instale e homologue em ambiente isolado.
Gere uma cópia, aguarde a conclusão e ensaie a recuperação antes de uso operacional.
Proteja o diretório de backups contra acesso HTTP direto. Não publique pacotes reais.

Diretório padrão: wp-content/gamb-duplica-wp-backups.
Pacotes anteriores em backups-dup-pro não são migrados nem excluídos automaticamente.
Downloads assinados exigem reserva de seus tokens. A telemetria está desabilitada.

== Registro de versões ==

= 5.0.1 =
Recuperação de inicialização e autoload, identificadores PHP/SQL/JavaScript válidos,
identidade própria, proteção de ZIP, validação de integridade, testes e auditoria.

= 4.5.21-audit =
Base histórica não operacional, com credencial removida antes da importação pública.

== Documentação ==

https://github.com/gamberine/gamb-duplica-wp
