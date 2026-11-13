const fs=require('node:fs'),path=require('node:path'),vm=require('node:vm');
const root=path.dirname(__dirname);let count=0,failed=0;
function walk(dir){for(const item of fs.readdirSync(dir,{withFileTypes:true})){if(['.git','dist','runtime'].includes(item.name))continue;const file=path.join(dir,item.name);if(item.isDirectory())walk(file);else if(item.name.endsWith('.js')||item.name.endsWith('.cjs')){count++;try{new vm.Script(fs.readFileSync(file,'utf8'),{filename:path.relative(root,file)});}catch(error){failed++;console.error(error.message+' - '+path.relative(root,file));}}}}
walk(root);console.log(count+' scripts; '+failed+' erros de sintaxe.');process.exitCode=failed?1:0;
