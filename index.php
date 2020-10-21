<?php
    require_once __DIR__ . '/vendor/autoload.php';
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    use Source\Core\LogJson;

    $path = "../../test/";

    //gravar um json novo
    $log1 = new LogJson($path);
    $log1->load("testfile1.json");
    $log1->type = "Employee";
    $log1->message = "É um teste de funcionario para o arquivo JSON";
    $log1->date = date("Y-m-d H:i:s");
    //$log1->add();
    //leitura
    $dados = $log1->get();
    echo "<h2>Get</h2>";
    var_dump($dados);

    echo '<hr>';
    echo "<h2>Filtro IN</h2>";
    $dados2 = $log1->in('type', ['Login', 'Employee'])->get();
    var_dump($dados2);
    echo '<hr>';
    echo "<h2>Filtro Not IN</h2>";
    $dados3 = $log1->notIn('type', ['Login', 'Documents'])->get();
    var_dump($dados3);
    echo '<hr>';
    echo "<h2>Order</h2>";
    $dados4 = $log1->order('date', 'desc')->get();
    var_dump($dados4);
    echo "<hr>";
    echo "<h2>Limit</h2>";
    $dados5 = $log1->limit(2)->get();
    var_dump($dados5);
    echo "<h>";
    echo "<h2>Offset</h2>";
    $dados6 = $log1->offset(2)->get();
    var_dump($dados6);
    echo "<hr>";
    echo "<h2>ID</h2>";
    $dados7 = $log1->id(1);
    var_dump($dados7);
    echo "<hr>";
    echo "<h2>GET</h2>";
    $dados8 = $log1->get();
    var_dump($dados8);

    echo "<hr>";
    echo "<h1>Edição</h1>";
    echo "<hr>";

    $log1->id(2);
    $log1->type = "Tipo Difirente";
    $log1->id = 2;
    //$log1->update();
    echo "<h2>Update</h2>";
    $dados9 = $log1->get();
    var_dump($dados9);

    echo "<hr>";
    echo "<h1>Exclusao</h1>";
    echo "<hr>";
    $a = $log1->delete(2);
    var_dump($a);


