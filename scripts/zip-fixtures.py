"""ZIPs de ensaio independentes para ambos os mecanismos PHP."""
from pathlib import Path
import warnings,zipfile
root=Path(__file__).resolve().parents[1]/'tests/runtime/fixtures'
root.mkdir(parents=True,exist_ok=True)
with zipfile.ZipFile(root/'unsafe.zip','w') as z:z.writestr('../outside.txt','unsafe')
with zipfile.ZipFile(root/'normalized-traversal.zip','w') as z:z.writestr('dir/../outside.txt','unsafe')
with zipfile.ZipFile(root/'crc.zip','w',compression=zipfile.ZIP_STORED) as z:z.writestr('file.txt','UNIQUE-CONTENT-TEST')
p=root/'crc.zip';p.write_bytes(p.read_bytes().replace(b'UNIQUE-CONTENT-TEST',b'BROKEN-CONTENT-TEST'))
with warnings.catch_warnings():
    warnings.simplefilter('ignore')
    with zipfile.ZipFile(root/'duplicate.zip','w') as z:
        z.writestr('same.txt','first');z.writestr('same.txt','second')
with zipfile.ZipFile(root/'external.zip','w',compression=zipfile.ZIP_DEFLATED) as z:
    z.writestr('database.sql','-- external fixture');z.writestr('dir/file.txt','portable zip content')
(root/'truncated.zip').write_bytes((root/'external.zip').read_bytes()[:-20])
print('Fixtures ZIP independentes preparadas.')
