<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Empréstimo de Equipamentos</title>
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Aluno</title>

    </head>
</html>

<?php

    include('conexao.php');
    include('protect.php');
    include('funcoes.php');

    if(!isset($_SESSION)){
        session_start();
    }

    if(!isset($_SESSION['idOperador'])){
        die("Você não tem acesso a esta página porque você não é o Batman!<p><a id=\"entrada\" href=\"interface.php\">Entrar</a></p>");
    }
    
    cabecalho();

    entradaAluno();

    timer();

    perfilOperador();

    aviso();

    logout();

?>