<?php
$root=dirname(__DIR__);$files=0;$failures=[];
foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS)) as $file){
    if($file->getExtension()!=='php'||strpos($file->getPathname(),DIRECTORY_SEPARATOR.'.git'.DIRECTORY_SEPARATOR)!==false||strpos($file->getPathname(),DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR)!==false)continue;
    $files++;try{token_get_all(file_get_contents($file),TOKEN_PARSE);}catch(ParseError $error){$failures[]=substr($file->getPathname(),strlen($root)+1).':'.$error->getLine().' '.$error->getMessage();}
}
foreach($failures as $failure)fwrite(STDERR,$failure."\n");
echo $files.' arquivos PHP; '.count($failures)." erros de sintaxe.\n";exit($failures?1:0);
