<?php

    $servername = "localhost"; //servidor
    $username = "root"; //usuário do banco
    $password = ""; // senha do banco (geralmente em localhost está vazia)
    $dbname = "loginoperador"; //nome do banco

    //criando a conexão

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
            die("Falha na conexão : " . $conn->connect_error);
    } else 
       echo "";
       
?>