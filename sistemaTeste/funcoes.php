<?php

    include("conexao.php");

    if (isset($_POST['idOperador'])) {
        // Verifica se o campo está vazio
        if (empty($_POST['idOperador'])) {
            echo "<span id=\"erro2\">Digite sua matrícula!</span>";
        } else {
            // Protege contra injeção de SQL
            $idOperador = $conn->real_escape_string($_POST['idOperador']);
    
            // Consulta ao banco de dados
            $sqlOperador = "SELECT * FROM operadores WHERE id = '$idOperador'";
            $queryOperador = $conn->query($sqlOperador) or die("Falha na execução do código SQL: " . $conn->error);
    
            $quantidade = $queryOperador->num_rows;
    
            if ($quantidade == 1) { // Encontrou o operador
                $operador = $queryOperador->fetch_assoc(); // Recupera os dados do operador
    
                if (!isset($_SESSION)) { // Se não há sessão, inicia sessão
                    session_start();
                }
    
                // guarda os dados na sessão
                $_SESSION['idOperador'] = $operador['id'];
                $_SESSION['operador'] = $operador['nome'];
                $_SESSION['path'] = $operador['path'];
    
                // Redireciona para outra página
                header("Location: aluno.php");
                exit;
            } else {
                echo "<span id=\"erro2\">Matrícula inválida!</span>";
            }
        }
        
    }
    

    if(isset($_POST['idAluno'])) {

        if(strlen($_POST['idAluno']) == ""){
            echo "<span id=\"erro2\">Digite sua matrícula!</span>";
        }else{

            $idAluno = $conn->real_escape_string($_POST['idAluno']);

            $sqlAluno = "SELECT * FROM alunos WHERE id = '$idAluno'";
            $queryAluno = $conn->query($sqlAluno) or die("Falha na conexão do código SQL : " . $conn->error);
            
            $quantidade = $queryAluno->num_rows;

            if($quantidade == 1){
                $aluno = $queryAluno->fetch_assoc();

                if(!isset($_SESSION)){
                    session_start();
                }

                $_SESSION['idAluno'] = $aluno['id'];
                $_SESSION['aluno'] = $aluno['nome'];
                $_SESSION['email'] = $aluno['email'];

                header("Location: equipamento.php");

            } else {
                echo "<span id=\"erro2\">Matrícula inválida!</span>";
            }
        }

    } //aluno.php

    if(isset($_POST['idEquipamento'])){
        if(strlen($_POST['idEquipamento']) == 0){
            echo "<span id=\"erro2\">Digite o patrimônio do equipamento</span>";
        }else{

            $idEquipamento = $conn->real_escape_string($_POST['idEquipamento']);

            $sqlEquipamento = "SELECT * FROM equipamentos WHERE id='$idEquipamento'";
            $queryEquipamento = $conn->query($sqlEquipamento) or die("Falha na conexão do código SQL : " . $conn->error);

            $quantidade = $queryEquipamento->num_rows;

            if($quantidade == 1){
                $equipamento = $queryEquipamento->fetch_assoc();

                if(!isset($_SESSION)){
                    session_start();
                }

                $_SESSION['idEquipamento'] = $equipamento['id'];
                $_SESSION['tipo'] = $equipamento['tipo'];
                $_SESSION['modelo'] = $equipamento['modelo'];

                $modelo = $_SESSION['modelo'];
                $nomeAluno = $_SESSION['aluno'];
                $idAluno = $_SESSION['idAluno'];

                $sqlEmprestimo = "INSERT INTO emprestimos (alunos_id, equipamentos_id, data_emprestimo, alunos_nome, equipamentos_modelo)
                VALUES ('$idAluno', '$idEquipamento', NOW(), '$nomeAluno', '$modelo')";

                $queryEmprestimo = $conn->query($sqlEmprestimo) or die("Falha na conexão do código SQL : " . $conn->error);

                header("Location: emprestimo.php");                               

            } else {
                echo "<span id=\"erro3\">Patrimônio Inválido!</span>";
            }
        }
    }

    if(isset($_POST['idDevolucao'])){
        if(empty($_POST['idDevolucao'])){
            echo "<span id=\"erro2\">Digite o patrimônio do equipamento</span>";
        } else {
            
            $idDevolucao = $conn->real_escape_string($_POST['idDevolucao']);

            $sqlEmprestimo = "SELECT * FROM emprestimos WHERE equipamentos_id = '$idDevolucao' AND data_devolucao IS NULL";
            $queryEmprestimo = $conn->query($sqlEmprestimo) or die("Falha na conexão do código SQL : " . $conn->error);

            $quantidade = $queryEmprestimo->num_rows;

            if($quantidade != 0){

                if(!isset($_SESSION)){
                    session_start();
                }

                $sqlDevolucao = "UPDATE emprestimos SET data_devolucao = NOW() WHERE (equipamentos_id = '$idDevolucao' AND data_devolucao IS NULL)";
                $queryDevolucao = $conn->query($sqlDevolucao) or die("Falhan na conexão do código SQL : " . $conn->error);

                header("Location: devolucao.php");  

            }

        }
    }

/*    function renovacao(){

        include("conexao.php")

        
        
    }*/

    function historico() {

        include("conexao.php");

        $idAluno = $_SESSION['idAluno'];

        $sql_emprestimos = "SELECT * FROM emprestimos WHERE alunos_id = $idAluno ORDER BY data_emprestimo DESC";
        $result_emprestimos = $conn->query($sql_emprestimos) or die("Falha na conexão do código SQL : " . $conn->error);
        
        if ($result_emprestimos->num_rows > 0) {
            echo "<form method='POST'>";
            echo "<table border='1'>
                <tr>
                    <th>Matrícula</th>
                    <th>Nome do aluno</th>
                    <th>Patrimônio</th>
                    <th>Modelo do Equipamento</th>
                    <th>Empréstimo</th>
                    <th>Devolução</th>
                    <th>✓</th>
                </tr>";
            
            while ($row = $result_emprestimos->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['alunos_id']}</td>
                    <td>{$row['alunos_nome']}</td>
                    <td>{$row['equipamentos_id']}</td>
                    <td>{$row['equipamentos_modelo']}</td>
                    <td>" . date("d/m/Y H:i:s", strtotime($row['data_emprestimo'])) . "</td>
                    <td>" . (!empty($row['data_devolucao']) ? date("d/m/Y H:i:s", strtotime($row['data_devolucao'])) : "") . "</td>
                    <td>
                        <input type=\"checkbox\" name=\"renovar\" value=\"". $row['id'] ."\">
                    </td>
                </tr>";
            }
            echo "</table>";
            echo "<button type='submit' name='btnRenovar' id='botaoRenovar'>Renovar Itens Marcados</buttom>";
            echo "</form>";

            if(isset($_POST['renovar']) && isset($_POST['btnRenovar'])){
                $sqlEmprestimo = "SELECT * FROM emprestimos WHERE equipamentos_id = '$idAluno' AND data_devolucao IS NULL";
                $queryEmprestimo = $conn->query($sqlEmprestimo) or die("Falha na conexão do código SQL : " . $conn->error);
    
                $quantidade = $queryEmprestimo->num_rows;
    
                if($quantidade != 0){
    
                    if(!isset($_SESSION)){
                        session_start();
                    }

                    $idEquipamento = $_SESSION['idEquipamento'];
                    $modelo = $_SESSION['modelo'];
                    $nomeAluno = $_SESSION['aluno'];
    
                    $sqlRenovacao = "UPDATE emprestimos SET data_devolucao = NOW() WHERE (equipamentos_id = '$idAluno' AND data_devolucao IS NULL); INSERT INTO emprestimos (alunos_id, equipamentos_id, data_emprestimo, alunos_nome, equipamentos_modelo)
                                     VALUES ('$idAluno', '$idEquipamento', NOW(), '$nomeAluno', '$modelo')";
                    $queryRenovacao = $conn->query($sqlRenovacao) or die("Falhan na conexão do código SQL : " . $conn->error);


                echo "<p>Renovação realizada com sucesso!</p>";
                echo "<meta http-equiv='refresh' content='1'>"; // Atualiza a página automaticamente
            }
        }

        } else {
            echo "<span id=\"erro3\">Nenhum empréstimo registrado.</span>";
        }
    }
        
    function cabecalho(){
        echo '<div class="container">
            
                <h1 id="titulo" style="color:rgb(17, 62, 121);">EMPRÉSTIMO DE EQUIPAMENTOS</h1>
            
                <a href="https://portal.pucrs.br/ensino/escola-de-comunicacao-artes-e-design-famecos/" target="_blind">
                 <img class="img" src="arquivos/pucrs.png" width="270" height="60">
                </a>
            </div>';
    }

    function entradaOperador(){

        echo '<div class="entrada">
            

                <p><b>Comece inserindo a matrícula do operador:</b></p>
        
                <div class="inputs-container">
                    <form action="" method="post">
                        <input type="number" id="caixaMatricula" name="idOperador" placeholder="Digite sua matrícula">
                        <button type="submit" id="botaoEntrar">Entrar</button>
                    </form>
                </div>
        
            </div>';

    }

    function entradaAluno(){

        echo '<div class="entrada">
            

                <p><b>Insira a matrícula do aluno:</b></p>
            
                <div class="inputs-container">
                    <form action="" method="post">
                        <input type="number" id="caixaMatricula" name="idAluno" placeholder="Digite a matrícula do aluno">
                        <button type="submit" id="botaoEntrar">Entrar</button>
                    </form>
                </div>
            
            </div>';
    }

    function entradaEquipamento(){
 
        echo '<div class="patrimonio">

                <p><b>Insira o patrimônio do equipamento:</b></p>
        
                <div class="inputs-container2">
                    <form action="" method="post">
                        <input type="number" id="caixaPatrimonio" name="idEquipamento" placeholder="Digite número de patrimônio">
                        <button type="submit" id="botaoEntrar2">Entrar</button>
                        <button type="submit" id="botaoRenovar2">Renovar Todos</buttom>
                    </form>
                </div>
        
            </div>';
    }

    function devolucao(){
        echo '<div class="patrimonio">

                <p><b>Insira o patrimônio do equipamento:</b></p>

                <div class="inputs-container2">
                    <form action="" method="post">
                        <input type="number" id="caixaPatrimonio" name="idDevolucao" placeholder="Digite número de patrimônio">
                        <button type="submit" id="botaoEntrar2">Entrar</button>
                        <button type="submit" id="botaoRenovar">Renovar Itens Marcados</buttom>
                        <button type="submit" id="botaoRenovar2">Renovar Todos</buttom>
                    </form>
                </div>
            </div>';
    }

    function confere(){
        echo '<div class="concluido">
                    <img id="fotoConfere" src="arquivos/confere2.jpg">
                    <p>Retirada do equipamento ' . $_SESSION['modelo'] . ' (' . $_SESSION['idEquipamento'] . ') <br> efetuada com sucesso!</p>
            </div>';
    }

    function confere2(){
        echo '<div class="concluido">
                    <img id="fotoConfere" src="arquivos/confere2.jpg">
                    <p>Devolução do equipamento ' . $_SESSION['modelo'] . ' (' . $_SESSION['idEquipamento'] . ') <br> efetuada com sucesso!</p>
            </div>';
    }

    function timer(){
        echo '<div id="timer">
                00:00
            </div>';
    }

    function perfilOperador(){
        echo '<div class="perfilOperador">
                <p id="identidadeOperador"> <b>Operador:</b><br><br>' . $_SESSION['operador'] . '</p>
                <img id="fotoOperador" src="' . $_SESSION['path'] . '">
            </div>';
    }

    function aviso(){
        echo '<span class="aviso">
                Para trocar de operador, efetue a leitura do código do seu crachá.<br>
                <p>Para fazer "log-off", leia novamente o seu crachá.</p>
            </span>';
    }

    function logout() {
        echo '<a class="logout" href="logout.php">Sair</a>';
    }

    function perfilAluno(){

        echo '<div class="perfilAluno">

                <p id="idaluno"><b>Aluno: </b>' . $_SESSION['aluno'] . '</p>
                <p id="idaluno"><b>E-mail: </b>' . $_SESSION['email'] . '</p>

            </div>';
    }

    function aluno(){
        echo '<div class="perfilAluno">

                <p id="idaluno"><b>Aluno: </b>' . $_SESSION['aluno'] . '</p>

            </div>';
    }

?>