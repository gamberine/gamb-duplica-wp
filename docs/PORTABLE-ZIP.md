# ZIP portátil

A partir da versão 5.1.0, o mecanismo **ZIP PHP** utiliza ZipArchive quando disponível e PclZip do próprio WordPress quando a extensão nativa está ausente. A seleção é automática e não altera o servidor. Não há instalação de DLL, dependência de comando ZIP do sistema ou download de biblioteca durante a operação.

## Requisitos e limites

- PHP 8.1 ou posterior, de 64 bits, com zlib e hash.
- PclZip fornecido pela instalação íntegra do WordPress.
- Pasta temporária do sistema gravável e fora da raiz do WordPress. A conferência utiliza uma área privada e pode exigir espaço equivalente ao conteúdo descompactado, além do pacote em construção.
- ZIP32: até 65.534 entradas, arquivo e soma dos conteúdos descompactados inferiores a 4 GiB. Arquivos multipartes, links simbólicos, compressões diferentes de Store/Deflate e ZIP64 são recusados.
- Criptografia AES e ZIP64 exigem ZipArchive. Uma senha solicitada sem suporte não é ignorada nem produz pacote aberto silenciosamente.

O mecanismo verifica caminhos originais, duplicidades, tamanho e CRC. A montagem utiliza arquivo intermediário e substitui o pacote após sua conclusão. Temporários são removidos ao fechar a operação; uma interrupção forçada do processo pode exigir limpeza administrativa dos temporários do sistema.

## Operação

Em **Configurações > Backups > Arquivo**, selecione **ZIP PHP**. Confira o diagnóstico, gere um pacote e aguarde seu estado concluído. DAF continua sendo outro formato; renomeá-lo para `.zip` não o converte.

A alternativa funciona dentro do WordPress carregado. O instalador autônomo executado em um destino sem WordPress não recebe essa alternativa automaticamente: utilize extração prévia com ferramenta compatível ou disponibilize o mecanismo nativo exigido pelo instalador. O assistente completo de migração entre servidores exige ensaio próprio.

## Verificação

Os testes incluem produção de ZIP por implementação independente, CRC inválido, truncamento, travessia, nomes duplicados, UTF-8, substituição e continuação de entradas, arquivo maior que os buffers, diretórios e extração de arquivos selecionados. A matriz de CI executa PHP 8.1 a 8.4 com e sem ZipArchive. Os resultados de uma versão não substituem a validação do backup e da recuperação no ambiente de destino.
