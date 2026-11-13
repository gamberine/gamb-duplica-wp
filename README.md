# Gamb Duplica WP

![Gamb Duplica WP](assets/img/gamb-duplica-wp-header-logo.svg)

Extensão para WordPress destinada à criação de cópias de segurança e à preparação de pacotes de recuperação e migração. O projeto mantém código, documentação, auditoria e distribuição em repositório independente.

**Versão:** 5.0.0. **Manutenção:** [gamberine](https://github.com/gamberine). **Licença da distribuição:** GPL-3.0-or-later, com avisos das dependências preservados.

## Procedência e governança

Esta distribuição deriva de uma base modificada do Duplicator Pro 4.5.21. A base recebida apresentava renomeação incompleta e erros que impediam sua inicialização. A etiqueta `v4.5.21-audit` registra essa base, com uma credencial incorporada removida **antes** da importação pública. Não deve ser utilizada para instalação.

O histórico anterior ao recebimento não foi reconstruído. Direitos autorais, licenças e referências técnicas de terceiros são preservados nos arquivos correspondentes. A identidade Gamb não representa autoria exclusiva do código incorporado nem vínculo com os fornecedores originais. Consulte [Procedência e dependências](docs/DEPENDENCIES.md).

## Funcionamento

O fluxo de cópia percorre as etapas de seleção e varredura, exportação do banco, montagem do arquivo, inclusão do instalador, verificação e entrega ao armazenamento configurado. Um pacote só deve ser utilizado depois de atingir o estado de conclusão.

| Recurso | Condição operacional |
| --- | --- |
| Backup manual local | Fluxo completo validado em WordPress 7.1.4 e PHP 8.3.26 |
| Arquivo ZIP pelo PHP | Requer extensão `zip`/`ZipArchive` habilitada no PHP que atende a aplicação |
| ZIP por comando do sistema | Depende do executável e do ambiente; não homologado nesta entrega |
| Arquivo DAF | Formato próprio de recuperação; não equivale a ZIP |
| Banco de dados | Exportação por PHP validada; seleção de tabelas deve ser conferida pelo administrador |
| Instalador de recuperação | Incluído e conferido no pacote; assistente completo de migração entre servidores não homologado |
| Agendamento, nuvem e multisite | Código incorporado presente; dependem de configuração, permissões e condições das integrações; não homologados nesta auditoria |

Os serviços comerciais e de tradução associados ao domínio não configurado da base são bloqueados. A telemetria está desabilitada. O projeto não fornece credenciais, assinatura comercial, servidor OAuth ou mecanismo de atualização comercial. Não são simuladas ativações de licença.

## Instalação

1. Baixe o ZIP instalável e `SHA256SUMS` em [Releases](https://github.com/gamberine/gamb-duplica-wp/releases).
2. Confira o SHA-256 do pacote.
3. Instale em ambiente de homologação por **Plugins > Adicionar plugin > Enviar plugin**.
4. Ative **Gamb Duplica WP**, confira os diagnósticos e defina o mecanismo de arquivamento.
5. Gere uma cópia, aguarde a conclusão, baixe os arquivos e ensaie a recuperação em ambiente isolado.

A instalação do plugin não habilita extensões PHP do servidor. Se `ZipArchive` estiver ausente, o painel informa a condição. A base pode selecionar DAF quando o mecanismo ZIP solicitado não estiver disponível: confira sempre a extensão efetivamente gerada.

## Arquivos e proteção

O diretório padrão desta distribuição é `wp-content/gamb-duplica-wp-backups`. Arquivos anteriores em `backups-dup-pro` não são adotados, migrados nem removidos automaticamente. As tabelas próprias utilizam os sufixos `gamb_duplica_wp_backups` e `gamb_duplica_wp_entities`, além do prefixo definido pelo WordPress.

Backups podem conter banco de dados, configurações, credenciais e documentos reservados. O diretório deve impedir acesso HTTP direto. O Apache utiliza as regras geradas; outros servidores exigem configuração equivalente. Os downloads assinados utilizam tokens e devem ser tratados como credenciais de acesso temporário. Os cabeçalhos de download impedem armazenamento em cache público.

A conferência ZIP verifica abertura consistente, caminhos relativos, duplicidade, leitura, tamanho, CRC e arquivos obrigatórios. Uma solicitação de criptografia sem suporte AES-256 ou sem senha é recusada, sem conversão silenciosa para arquivo aberto. Essa conferência não demonstra que toda migração será compatível com um servidor de destino.

## Validação e auditoria

Na auditoria local, um pacote de **32.449.207 bytes** chegou ao estado concluído. Foram conferidos **6.403 arquivos**, distribuídos em **7.514 entradas**, sem falha de CRC. O SQL exportado foi importado em outro banco isolado, com **18 tabelas**; os registros de conteúdo e seus metadados coincidiram com a origem do ensaio. Arquivos selecionados foram extraídos e comparados por SHA-256.

Há 31 verificações independentes com ZIP, incluindo criptografia, senha incorreta, arquivos corrompidos, caminhos inseguros, autoload e bloqueio de serviços não configurados. O modo sem ZIP também é testado. A integração contínua verifica PHP 7.4, 8.1, 8.3 e 8.4; consulte o resultado do commit em [Actions](https://github.com/gamberine/gamb-duplica-wp/actions).

Consulte [Auditoria](docs/AUDIT.md), [Operação e recuperação](docs/OPERATIONS.md), [Registro de versões](CHANGELOG.md), [Segurança](SECURITY.md) e [Contribuição](CONTRIBUTING.md).

## Desenvolvimento e pacotes

```sh
php scripts/check-php.php
php tests/run.php
node scripts/check-js.cjs
python scripts/build.py
```

Os testes ZIP exigem a extensão habilitada no processo. O pacote instalável contém apenas a distribuição do plugin; exclui testes, registros locais, bancos, backups, credenciais e artefatos de auditoria. O ZIP determinístico e seu SHA-256 são gerados em `dist/`. Atualizações são instaladas manualmente a partir das versões publicadas.
