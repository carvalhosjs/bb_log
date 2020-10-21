<?php
namespace Source\Core;
/**
 * Class LogJson - BBLibs - 2020
 *
 * Essa classe é para fazer o gerenciamento de arquivos log JSON
 * @package BBLibs\Core
 */

class LogJson extends Log{

    public function __construct($path)
    {
        parent::__construct($path);
    }


    /**
     * Método responsável por abrir um arquivo json e tratar como array php.
     * @param string $fileName
     * @return $this|Log
     */
    public function load(string $fileName)
    {
        $this->filename = $fileName;
        parent::load($fileName);
        if(!empty($this->data)){
            $json=str_replace('},]',"}]",$this->data);
            $this->data = json_decode($json, true);
        }
        return $this;
    }




    //search json

    //delete json

}