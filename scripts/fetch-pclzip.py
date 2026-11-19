"""Obtém a implementação oficial para os ensaios; não integra a distribuição."""
from pathlib import Path
from urllib.request import Request, urlopen
import hashlib
url='https://raw.githubusercontent.com/WordPress/WordPress/6.2/wp-admin/includes/class-pclzip.php'
target=Path(__file__).resolve().parents[1]/'tests/runtime/wp-admin/includes/class-pclzip.php'
target.parent.mkdir(parents=True,exist_ok=True)
with urlopen(Request(url,headers={'User-Agent':'Gamb-Duplica-WP-Tests'}),timeout=45) as response:
    data=response.read(1024*1024)
assert b'class PclZip' in data and data.startswith(b'<?php')
assert hashlib.sha256(data).hexdigest() == 'e82876116d18e46e4973e97d14604ac4a2dca876ec34c55f540b37f40cd3ae1f', 'PclZip diverge da referência auditada.'
target.write_bytes(data)
print('PclZip oficial para os testes: '+hashlib.sha256(data).hexdigest())
