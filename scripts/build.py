"""Empacota somente a distribuição, de forma determinística."""
from pathlib import Path
import hashlib,re,zipfile
root=Path(__file__).resolve().parents[1]
version=re.search(r'Version:\s*([\d.a-z-]+)',(root/'gamb-duplica-wp.php').read_text(encoding='utf-8')).group(1)
output=root/'dist';output.mkdir(exist_ok=True)
package=output/f'gamb-duplica-wp-{version}.zip'
roots=('addons','assets','classes','installer','lang','src','template','vendor-prefixed')
files=[p for folder in roots for p in (root/folder).rglob('*') if p.is_file()]
files.extend(root/name for name in ('define.php','gamb-duplica-wp-main.php','gamb-duplica-wp.php','helper.php','index.php','LICENSE.TXT','readme.txt','uninstall.php','NOTICE.md'))
with zipfile.ZipFile(package,'w',zipfile.ZIP_DEFLATED,compresslevel=9) as archive:
    for path in sorted(files):
        if path.is_symlink():raise ValueError('Link simbólico não permitido.')
        info=zipfile.ZipInfo('gamb-duplica-wp/'+path.relative_to(root).as_posix(),(2020,1,1,0,0,0));info.compress_type=zipfile.ZIP_DEFLATED;info.external_attr=0o100644<<16;archive.writestr(info,path.read_bytes())
digest=hashlib.sha256(package.read_bytes()).hexdigest()
(output/'SHA256SUMS').write_text(f'{digest}  {package.name}\n',encoding='ascii')
print(f'{package.name}: {digest}')
