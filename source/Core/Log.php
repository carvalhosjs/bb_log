<?php
namespace Source\Core;
/**
 * Class Log - BBLibs - 2020
 *
 * Essa classe é para fazer o gerenciamento de arquivos log
 * @package BBLibs\Core
 */
class Log {

    /**
     * @var string $path - Atributo para guardar o endereço da PATH.
     */
    protected $path;

    /**
     * @var string $filename - Atributo para guardar o nome do arquivo.
     */
    protected $filename;

    /**
     * @var string $data - Atributo para guardar o array dos dados do arquivo.
     */
    protected $data;

    /**
     * @var string $filter - Atributo para guardar a array filtrada.
     */
    protected $filter;

    /**
     * @var  string $values - Atributo para guradar novos valores de dados (create, update).
     */
    protected $values;

    /**
     * @var array $option - Atributo para guardar as opçoes de busca (Campo a ser comparado e Ordem asc e desc).
     */
    private static $option;

    /**
     * Método mágico para formatar um array dinanimico para salvar como campo do ARQUIVO.
     * @param $name - Parametro para gurdar o nome do campo.
     * @param $value - Parametro para gurdar o valor do campo.
     */
    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Construtor para guardar a raiz da pasta onde será armazenado os arquivos no disco
     * Log constructor.
     * @param string $path - Parametro para guardar o caminho do arquivo.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->filter = [];
        checkFolder(__DIR__ . '/' . $this->path);
    }

    /**
     * Método responspavel por abir o arquivo todo.
     * @param string $fileName - Nome do arquivo a sr gravado no disco.
     * @return $this - Retorno do objeto para ser concatenado.
     */
    public function load(string $fileName)
    {

        set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
        {
            throw new \ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
        }, E_WARNING);
        $data = false;
        try{
            $data =  file_get_contents(__DIR__ . "/" . $this->path  . $fileName);
            $this->data = $data;
        }catch (\Exception $e){
            $this->data = [];
            $this->init($fileName, $this->data)->save();
        }

        restore_error_handler();
        return $this;
    }

    /**
     * Método responsável por filtrar os dados pela chave type do arquivo.
     * @param string $type - Parametro de busca pelo chave type. - rever
     * @return $this
     */
    public function type(string $type)
    {
        if(empty($this->data)){
            $this->data = [];
        }

        $filter = array_values(array_filter($this->data, function($n) use($type){
            return $n['type']==$type;
        }));
        $this->data = $filter;
        return $this;
    }

    /**
     * Método responsável por buscar uma chave por multiplos parametros de array.
     * @param string $field - Campo da chave a ser buscado
     * @param array $typos - Campos de busca e array  ex. ['chave1', 'chave2'].
     * @return $this
     * @throws \Exception
     */
    public function In(string $field, array $typos){
        if(empty($typos)){
            throw new \Exception("Campo tipos está em branco");
            exit;
        }


        if(empty($this->data)){
            $this->data = [];
            $this->filter = [];
        }

        $filter = array_values(array_filter($this->data, function($n) use($field, $typos){
            return in_array($n[$field], $typos);
        }));

        $this->filter = $filter;
        return $this;
    }

    /**
     * Método responsável por ignorar uima busca por uma chave por multiplos parametros de array.
     * @param string $field - Campo da chave a ser buscado.
     * @param array $typos - Campos de busca e array  ex. ['chave1', 'chave2'].
     * @return $this
     * @throws \Exception
     */
    public function notIn(string $field, array $typos){
        if(empty($typos)){
            throw new \Exception("Campo tipos está em branco");
            exit;
        }

        if(empty($this->data)){
            $this->data = [];
            $this->filter = [];
        }


        $filter = array_values(array_filter($this->data, function($n) use($field, $typos){
            return !in_array($n[$field], $typos);
        }));


        $this->filter = $filter;
        return $this;
    }

    /**
     * Método responsável por buscar em range de DATE
     * @param string $start - Data de inicio ex. '2020-10-01';
     * @param string $end - Data final = ex. '2020-10-30';
     * @return $this
     */
    public function between(string $start, string $end){
        $this->filter = [];
        if(empty($this->data)){
            $this->data = [];
        }

        $filter = array_values(array_filter($this->data, function($n) use($start, $end){
            return ($n['date'] >= $start && $n['date'] <= $end);
        }));
        $this->data = $filter;
        return $this;

    }

    /**
     * Método responsável por ordernar um array buscado pelo load() - antes do get().
     * @param string $field - Campo da chave por onde irá buscar.
     * @param string $order - Campo de ordem do resultado ex. asc, desc
     * @return $this
     */
    public function order(string $field, string $order=null)
    {
        $this->filter = [];
        self::$option = [$field, $order];
        switch ($order){
            case 'asc':
                usort($this->data, array("Source\Core\Log", "dataCompare"));
                return $this;
                break;
            case 'desc':
                usort($this->data, array("Source\Core\Log", "dataCompare"));
                return $this;
                break;
            default:
                usort($this->data, array("Source\Core\Log", "dataCompare"));
                return $this;
        }
    }

    /**
     * Método responsável por limitar um array de dados do load() - antes do get().
     * @param int $limit - Numero de limite de registros.
     * @return $this
     */
    public function limit(int $limit)
    {
        if(empty($this->data)){
            $this->data = [];
        }

        $this->filter = array_slice($this->data, 0, $limit);
        return $this;

    }

    /**
     * Método responsável por pular um indice um array de dados do load() - antes do get().
     * @param int $limit - Numero de onde irá comecar a filtrar os registros.
     * @return $this
     */
    public function offset(int $startOf)
    {
        if(empty($this->data)){
            $this->data = [];
        }

        if($startOf==0){
            $startOf=1;
        }

        $startOf -= 1;

        $this->filter = array_slice($this->data, $startOf);
        return $this;

    }

    /**
     * Método responsável por buscar um determinado registro por id.
     * @param int $id - id do registro de acordo com load().
     * @return $this|array
     */
    public function id(int $id)
    {

        if(empty($this->data)){
            $this->data = [];
        }
        $filter = array_values(array_filter($this->data, function($n) use($id){
            return $n['id']==$id;
        }));
        if(!empty($filter)){
            return $filter[0];
        }else{
            return [];
        }

        return $this;
    }

    /**
     * Método responsável por pegar o ultimo id do documento json.
     * @return int|mixed
     */
    public function getLastId()
    {
        $ant = $this->data;
        if(empty($ant)){
            return 1;
        }
        $filter =  max(array_column($ant, 'id'));
        $filter = ($filter==0) ? 1 : $filter + 1;
        return $filter;
    }

    /**
     * Método final de retorno das informações buscadas ex. (new Log())->load('arquivo.json').get();
     * @return string
     */
    public function get()
    {
        $result = $this->filter;
        $this->filter = [];
        if(empty($result)){
            return $this->data;
        }
        return $result;
    }

    /**
     * Método final de retorno das informações buscadas em formato JSON, ex. (new Log())->load('arquivo.json').get();
     * @return false|string
     */
    public function json()
    {
        return json_encode($this->data);
    }


    /**
     * Método usado em conjunto com order() para manipular posições do array.
     * @param $a - valor a esquerda do array.
     * @param $b - valor a direita do array.
     * @return false|int
     */
    protected function dataCompare($a, $b){
        $option = self::$option;
        $t1 = strtotime($a[$option[0]]);
        $t2 = strtotime($b[$option[0]]);
        if($option[1] == 'desc'){
            return ($t1 < $t2) ? 1 : -1;
        }
        return $t1 - $t2;
    }

    /**
     * Método responsável por criar um arquivo em branco caso não exista na pasta
     * @param string $filename - Nome do arquivo inicial.
     * @param array $data - Valores iniciais que conterá o arquivo.
     * @return $this
     */
    public function init(string $filename, array $data=[])
    {
        $this->filename = $filename;
        $this->data = $data;
        return $this;
    }


    /**
     * Método final  por adicionar e gravar um novo registro no JSON.
     * @return bool|int
     */
    public function add()
    {
        $ant = $this->data;
        $this->values['id'] =  $this->getLastId();
        $ant[] = $this->values;
        $this->data = $ant;
        return $this->save();
    }

    /**
     * Método responsável por gravar as informações em um arquivo fisico no disco.
     * @return bool|int
     */
    public function save(){
        try{
            $ant =  $this->data;
            $path = __DIR__ . '/' . $this->path . $this->filename;
            if ( !file_exists($path) ) {
                touch($path);
                //throw new \Exception('Arquivo não encontrado.');
            }
            $fp = fopen(__DIR__ . '/' . $this->path . $this->filename, 'w');
            if(!$fp){
                throw new \Exception('Não foi possivel abrir o arquivo.');
            }
            $result= fwrite($fp, json_encode($ant));
            fclose($fp);
        }catch (\Exception $e){
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    /**
     * Método responsável por atualizar um registro no arquivo JSON,ex.
     * $l->load("te6.json")->id(9);
     * $l->type = "Nova informação";
     * $l->id = 9;
     * $l->update();
     * @return bool|int
     */
    public function update(){
        $ant = $this->data;
        $id = $this->values['id'];
        $ant = array_values(array_filter($ant, function($n) use($id){
            return ($n['id']!=$id);
        }));

        $ant[] = $this->values;
        $this->data = $ant;
        return $this->save();
    }

    /**
     * Método responsável por deletar um registro no arquivo JSON.
     * @param int $id - Parametro para buscar o codigo do registro a ser deletado.
     * @return int
     */
    public function delete(int $id)
    {
        if(empty($this->data)){
            $this->data = [];
        }

        $filter = array_values(array_filter($this->data, function($n) use($id){
            return ($n['id']!=$id);
        }));

        if(!empty($filter)){
            $this->data = $filter;
            $this->save();
            return 1;
        }
        return 0;

    }



}