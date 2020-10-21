<?php
/**
 * Função para checar a existencia de uma pasta, caso não encontre criar.
 * @param string $path - campo donde será checada o local da pasta no disco.
 */
function checkFolder(string $path)
{
    if(!file_exists($path) && !is_dir($path)){
        mkdir($path, 0777, true);
    }
}

function buildBlankFileJSON($path, $filename)
{
    $blank = new \Source\Core\LogJson($path);
    $data = [];
    $blank->init($filename, $data)->save();
}