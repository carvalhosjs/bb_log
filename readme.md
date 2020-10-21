# Logs JSON 
Pacote desenvolvido por Carlos Mateus Carvalho com finalidade de gerar arquivos json através do php. 
### Log.php
Classe onde está localizado todo a rotina de manipulação de dados.
###### Metodos
* __construct(string $path)
* load(string $filename)
* type(string $type)
* in(string $field, array $typos)
* notIn(string $field, array $typos)
* between(string $start, string $end)
* order(string $field, string $order=null)
* limit(int $limit)
* offset(int $startOf)
* id(int $id)
* getLastId()
* get()
* json()
* dataCompare($a, $b)
* init(string $filename, array $data=[])
* add()
* save()
* update()
* delete()
### LogJson.php
Classe responsável por decodificar o arquivo JSON para PHP array.
* load(string $fileName)
