# Auditoria de funcionamento e segurança

Data: 13/11/2026. Base recebida: 4.5.21. Distribuição corrigida: 5.0.0.

## Método e escopo

A cópia instalada foi preservada com inventário SHA-256 de 2.419 arquivos. A análise utilizou inspeção de inicialização, carregamento, arquivamento, SQL, API, downloads e integrações. O fluxo completo foi executado em cópia isolada do WordPress 7.1.4, com banco separado e PHP 8.3.26. Não foi executada restauração sobre o portal de origem.

## Achados

| Código | Severidade / confiança | Evidência e risco | Tratamento / situação |
| --- | --- | --- | --- |
| AUD-01 | Crítica / alta | 690 de 2.141 arquivos PHP recebidos não passavam no parser; hífens em namespaces e identificadores impediam execução | SAFE_FIX: identificadores e autoload corrigidos; varredura atual sem erros |
| AUD-02 | Alta / alta | Credencial fixa gravada no carregamento; risco de exposição e alteração indevida de configuração (CWE-798) | SAFE_FIX: atribuição removida antes do primeiro commit público; não é simulada licença |
| AUD-03 | Alta / alta | Namespaces de dependências, mapeamento de classes e nomes de arquivos divergiam; complemento tentava impedir a própria ativação | SAFE_FIX: caminhos e mapeamentos corrigidos; ativação isolada concluída |
| AUD-04 | Alta / alta | Identificadores de tabela com hífen eram usados em consultas sem delimitação uniforme | SAFE_FIX: tabelas próprias com identificadores válidos e sem importação automática de outra distribuição |
| AUD-05 | Média / alta | JavaScript continha identificadores inválidos e operador separado na biblioteca de árvore | SAFE_FIX: arquivos e trechos de templates corrigidos; conferência sintática automatizada |
| AUD-06 | Alta / alta | Detecção de criptografia dependia de phpinfo HTML e podia desabilitar silenciosamente a proteção solicitada (CWE-311) | SAFE_FIX: consulta nativa de suporte AES e recusa de pedido sem suporte/senha; round trip criptografado validado |
| AUD-07 | Alta / alta | Códigos numéricos de erro de abertura ZIP podiam ser tratados como sucesso | SAFE_FIX: contrato booleano; regressão com arquivo inválido |
| AUD-08 | Média / alta | Verificação final não lia todas as entradas por tamanho e CRC | SAFE_FIX: verificador sequencial, caminhos e duplicidades; ZIP truncado e corrupção recusados |
| AUD-09 | Alta / alta | Compartilhamento do diretório de backups com a base anterior poderia misturar pacotes e políticas de limpeza | SAFE_FIX: diretório próprio; arquivos antigos preservados |
| AUD-10 | Média / alta | Endpoints comerciais e de tradução apontavam para domínio não configurado da distribuição | SAFE_FIX: bloqueio dessas solicitações e telemetria desabilitada; integrações não fornecidas não são anunciadas como operacionais |
| AUD-11 | Média / alta | Detecção de mysqldump executava candidatos inexistentes e caminhos Windows sem citação apropriada | SAFE_FIX: existência conferida e executável citado como argumento completo |
| AUD-12 | Média / alta | Downloads sensíveis permitiam cache público (CWE-525) | SAFE_FIX: private, no-store e nosniff |
| AUD-13 | Média / alta | REST reutilizava verificação AJAX sem reconhecer nonce no cabeçalho; erro de autorização sem status apropriado | SAFE_FIX: nonce do pedido REST e HTTP 401/403 |
| ENV-01 | Operacional / alta | ZipArchive desabilitado no PHP normal do Laragon | Aviso administrativo; ensaio ZIP carregou a extensão somente nos processos isolados; configuração global preservada |
| DEP-01 | Média / alta | Ausência de composer.lock e de inventário completo das versões das bibliotecas incorporadas | REVIEW_REQUIRED: pendência documentada em DEPENDENCIES.md; não há declaração de atualização integral da cadeia |

## Validação observada

- 31 verificações independentes com ZIP e 13 sem ZIP, abrangendo autoload, caminhos, erros de abertura, CRC, truncamento, requisitos, senha e serviços bloqueados.
- Pacote completo concluído em sete ciclos, com banco, arquivos e instalador. Resultado: 32.449.207 bytes, 7.514 entradas e 6.403 arquivos conferidos.
- Segunda leitura independente em Python não encontrou erro de CRC. Extração de arquivos selecionados coincidiu por SHA-256 com os arquivos da cópia de origem.
- SQL exportado importado em outro banco isolado: 18 tabelas; wp_posts e wp_postmeta coincidiram integralmente com a origem do ensaio, incluindo 398 registros em wp_posts.
- Painéis de pacotes, configurações e ferramentas renderizados em navegador autenticado. Conferência móvel executada; isso não equivale a auditoria completa de acessibilidade.
- O pacote instalável e o repositório excluem bancos, pacotes reais, credenciais e evidências privadas.

## Limites e funcionalidades pendentes de homologação

Não foram homologados ZIP por shell, agendamento, provedores de nuvem, OAuth, multisite, execução integral do assistente de recuperação entre servidores ou migração de pacotes do fornecedor original. A restauração SQL e a extração de arquivos foram verificadas separadamente; não devem ser apresentadas como ensaio integral do assistente de migração.

Os códigos CWE descrevem classes de risco relacionadas aos controles analisados. O relatório não constitui certificação integral nem demonstra ausência de outras vulnerabilidades. Achados de cadeia de dependências e cenários não ensaiados permanecem explicitamente registrados.
