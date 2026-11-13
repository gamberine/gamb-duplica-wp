# Registro de versões

## 5.0.1 - 2026-11-13

- Corrigir o requisito mínimo declarado para PHP 8.1, após a matriz de CI demonstrar incompatibilidade das dependências incorporadas com PHP 7.4. A versão 7.4 não era operacional nesta distribuição.
- Verificar PHP 8.1, 8.2, 8.3 e 8.4 e normalizar finais de linha de arquivos já incorporados.
- Preservar a versão 5.0.0 como registro do primeiro lote auditado, sem recomendação de instalação.

## 5.0.0 - 2026-11-13

### Identidade e compatibilidade

- Recuperação de identificadores PHP inválidos, namespace `GambDuplicaWP`, dependências prefixadas, autoload, nomes de arquivos e complementos.
- Correção de 690 arquivos PHP que apresentavam erro de sintaxe na base recebida.
- Tabelas com identificadores SQL válidos e armazenamento padrão independente, sem adoção automática de backups da instalação anterior.
- Identidade visual, metadados, documentação e endereços institucionais próprios.
- Base importada com credencial incorporada removida; telemetria desabilitada e serviços remotos não configurados bloqueados.

### Arquivos e funcionamento

- Correção de identificadores JavaScript, propriedades com hífen e operador inválido na biblioteca de árvore de arquivos.
- Correção da detecção de executáveis em caminhos Windows com espaços, sem execução de candidatos inexistentes.
- Erros de arquivamento propagados ao controle de falha, sem saída de texto que contamine a resposta.
- Retorno booleano da abertura ZIP, sem tratar código de erro numérico como sucesso.
- Verificação de todas as entradas ZIP por tamanho e CRC, além da consistência e dos arquivos obrigatórios.
- Recusa de caminhos inseguros, duplicidades e criptografia indisponível ou sem senha.
- Detecção de suporte AES pelo mecanismo nativo e tratamento de falhas de criptografia de entradas.
- Downloads com cache privado desabilitado; permissões REST com suporte ao nonce no cabeçalho e códigos HTTP de acesso apropriados.
- Aviso explícito sobre ausência de ZipArchive e diferença entre ZIP e DAF.

### Governança

- Relatório de auditoria com evidências e limites, testes independentes, integração contínua e pacotes acompanhados de SHA-256.

## 4.5.21-audit - base recebida

Registro histórico da cópia recebida em 13/11/2026. Foram preservados seus arquivos e hashes em ambiente privado. Uma credencial fixa foi suprimida antes da importação pública. A base contém erros de sintaxe e não é recomendada para instalação. Não foram criadas datas ou etapas de desenvolvimento anteriores ao recebimento.
