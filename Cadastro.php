<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os dados foram enviados corretamente
    if (empty($_POST['nome']) || empty($_POST['setor']) || empty($_POST['cidade']) || empty($_POST['checkin'])) {
        die(" Erro: Todos os campos são obrigatórios!");
    }

    // Captura e protege os dados recebidos do formulário
    $nome = $_POST['nome'];
    $setor = $_POST['setor'];
    $cidade = $_POST['cidade'];
    $checkin = $_POST['checkin'];

    // Informações de conexão com o banco de dados PostgreSQL
    $host = "localhost"; 
    $port = "5432";
    $dbname = "postgres"; 
    $user = "postgres"; 
    $password = "nova_senha_123"; 

    // Conecta ao banco de dados
    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

    if (!$conn) {
        die(" Erro ao conectar ao banco de dados: " . pg_last_error());
    }

    // Verifica se a tabela "Usuarios" existe
    $checkTable = pg_query($conn, "SELECT 1 FROM information_schema.tables WHERE table_name = 'Usuarios'");
    if (pg_num_rows($checkTable) == 0) {
        die(" Erro: A tabela \"Usuarios\" não existe no banco de dados.");
    }

    // Query SQL segura utilizando parâmetros
    $sql = 'INSERT INTO public."Usuarios" (nome, setor, cidade, checkin) VALUES ($1, $2, $3, $4)';

    // Prepara a query
    $stmt = pg_prepare($conn, "insert_usuario", $sql);

    if ($stmt) {
        // Executa a query com os valores do formulário
        $result = pg_execute($conn, "insert_usuario", array($nome, $setor, $cidade, $checkin));

        if ($result) {
            echo " Dados salvos com sucesso no banco de dados!";
        } else {
            echo " Erro ao salvar os dados: " . pg_last_error($conn);
        }
    } else {
        echo " Erro ao preparar a query: " . pg_last_error($conn);
    }

    // Exibir todos os registros armazenados após a inserção
    echo "<h2 class='text-center mt-3'>Lista de Usuários</h2>";
    echo "<div class='container'>";
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead class='table-dark'>";
    echo "<tr><th>ID</th><th>Nome</th><th>Setor</th><th>Cidade</th><th>Check-in</th></tr>";
    echo "</thead><tbody>";

    // Consultar todos os registros
    $sql_select = 'SELECT * FROM public."Usuarios" ORDER BY id ASC';
    $result_select = pg_query($conn, $sql_select);

    if ($result_select) {
        while ($row = pg_fetch_assoc($result_select)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nome']}</td>";
            echo "<td>{$row['setor']}</td>";
            echo "<td>{$row['cidade']}</td>";
            echo "<td>{$row['checkin']}</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'> Erro ao buscar os dados.</td></tr>";
    }

    echo "</tbody></table>";
    echo "</div>";

    // Fecha a conexão com o banco de dados
    pg_close($conn);
} else {
    echo " Nenhum dado enviado.";
}
?>
