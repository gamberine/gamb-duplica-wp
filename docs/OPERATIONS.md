# Operação, backup e recuperação

## Mecanismos de arquivamento

**ZIP pelo PHP:** utiliza ZipArchive. Habilite a extensão no PHP utilizado pelo servidor web e confira a disponibilidade no painel. Habilitar a extensão apenas na linha de comando não a habilita no Apache ou PHP-FPM.

**ZIP pelo sistema:** depende de executável compatível, permissões e comandos disponíveis. A existência de uma função shell não demonstra disponibilidade do executável ZIP.

**DAF:** utiliza o mecanismo de arquivamento próprio incorporado à base. Não renomeie um arquivo DAF para ZIP; isso não converte seu formato.

## Criação manual

1. Abra Gamb Duplica WP e inicie um novo backup.
2. Confira conteúdo, destino, filtros, espaço disponível e mecanismo de arquivamento.
3. Execute a varredura e examine avisos de leitura, tamanho e banco.
4. Inicie a geração e aguarde a conclusão das etapas de banco, arquivo, instalador e entrega.
5. Confirme a extensão efetiva do arquivo e os registros de integridade.
6. Baixe os arquivos necessários e guarde-os em local reservado e separado da instalação de origem.

O arquivo ZIP de distribuição do plugin e o arquivo ZIP de backup são produtos diferentes. O primeiro instala a extensão; o segundo pode conter informações e credenciais da instalação.

## Recuperação

O pacote incorpora componentes do instalador e descritores necessários à recuperação. Preserve o conjunto gerado. Utilize ambiente isolado, credenciais próprias do destino e uma cópia recuperável da situação anterior.

A auditoria validou extração de arquivos e importação do SQL em banco separado. Não validou o assistente completo de migração entre domínios, alterações de caminhos, multisite ou substituição de uma instalação ativa. Esses cenários exigem ensaio específico antes de uso operacional.

## Proteção e retenção

O diretório padrão é `wp-content/gamb-duplica-wp-backups`. As regras do servidor devem negar acesso HTTP direto a pacotes, SQL, logs e descritores. Confira o bloqueio depois de instalar ou mudar de servidor. Downloads assinados devem utilizar o fluxo administrativo previsto.

A retenção é definida no armazenamento. Esta distribuição preserva o padrão incorporado de **20 pacotes no armazenamento local padrão**, configurável pelo administrador. O limite de seis cópias do WP Static Guard pertence àquele outro plugin e não foi aplicado automaticamente aqui.

Backups em `backups-dup-pro` pertencem à base anterior e permanecem separados. Não é executada migração automática de pacotes ou credenciais do fornecedor original.
