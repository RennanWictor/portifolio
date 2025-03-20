<html>
<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="UTF-8">
        <title>Empréstimo de Equipamentos</title>
        <link rel="stylesheet" href="style.css">
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
    
    timer();

    logout();

    devolucao();

    perfilOperador();

    aluno();

    aviso();

    confere();

    historico();

?>