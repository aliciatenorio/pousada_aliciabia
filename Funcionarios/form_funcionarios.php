<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    $Erro = '';

    // Recebe o ID pela URL e verifica se é um número inteiro válido.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Define os campos vazios para o formulário de cadastro.
    $funcionario = [
        'nome_funcionario' => '',
        'cargo_funcionario' => '',
        'salario_funcionario' => '',
        'telefone_funcionario' => ''
    ];

    // Indica se o formulário está sendo usado para editar.
    $editando = false;

    // Se existir um ID na URL, busca os dados do funcionário.
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbFuncionarios WHERE codigo_funcionario = :id");
            $stmt->execute([':id' => $id]);

            // Armazena os dados encontrados.
            $funcionario = $stmt->fetch();

            // Se o funcionário não existir, volta para a listagem.
            if (!$funcionario) {
                header('Location: listar_funcionarios.php');
                exit;
            }

            // Indica que o formulário está no modo de edição.
            $editando = true;

        } catch (PDOException $e) {
            // Exibe o erro caso não seja possível buscar o funcionário.
            die("Erro ao buscar funcionário: " . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe os dados digitados e remove espaços do início e do final.
        $nome = trim($_POST['nome_funcionario'] ?? '');
        $cargo = trim($_POST['cargo_funcionario'] ?? '');
        $salario = trim($_POST['salario_funcionario'] ?? '');
        $telefone = trim($_POST['telefone_funcionario'] ?? '');

        // Recebe e valida o ID enviado pelo formulário.
        $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Atualiza o array com os valores enviados, mantendo os dados no formulário.
        $funcionario = [
            'nome_funcionario' => $nome,
            'cargo_funcionario' => $cargo,
            'salario_funcionario' => $salario,
            'telefone_funcionario' => $telefone
        ];

        // Verifica se o formulário está no modo de edição.
        $editando = (bool)$id_post;

        // Se houver ID, adiciona ao array do funcionário.
        if ($id_post) {
            $funcionario['codigo_funcionario'] = $id_post;
        }

        // Verifica se algum campo obrigatório está vazio.
        if ($nome === '' || $salario === '' || $cargo === '' || $telefone === '') {
            $Erro = 'Preencha todos os campos obrigatórios.';

        } else {

            try {

                // Verifica se o salário é numérico e não é negativo.
                if (!is_numeric($salario) || $salario < 0) {
                    throw new Exception('Informe um salário válido.');
                }

                // Se existir ID, atualiza os dados do funcionário.
                if ($id_post) {
                    $stmt = $pdo->prepare(" UPDATE tbFuncionarios
                                            SET nome_funcionario = :nome, cargo_funcionario = :cargo, salario_funcionario = :salario, telefone_funcionario = :telefone
                                            WHERE codigo_funcionario = :id
                    ");

                    // Executa a atualização substituindo os parâmetros pelos valores recebidos.
                    $stmt->execute([
                        ':nome' => $nome,
                        ':cargo' => $cargo,
                        ':salario' => $salario,
                        ':telefone' => $telefone,
                        ':id' => $id_post
                    ]);

                    // Redireciona para a listagem com a mensagem de edição realizada.
                    header('Location: listar_funcionarios.php?msg=editado');
                    exit;

                } else {

                    // Se não existir ID, prepara o cadastro de um novo funcionário.
                    $stmt = $pdo->prepare("
                        INSERT INTO tbFuncionarios
                        (nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
                        VALUES
                        (:nome, :cargo, :salario, :telefone)
                    ");

                    // Executa a inserção dos dados no banco.
                    $stmt->execute([
                        ':nome' => $nome,
                        ':cargo' => $cargo,
                        ':salario' => $salario,
                        ':telefone' => $telefone
                    ]);

                    // Redireciona para a listagem com a mensagem de cadastro realizado.
                    header('Location: listar_funcionarios.php?msg=salvo');
                    exit;
                }

            } catch (PDOException $e) {
                // Armazena a mensagem caso ocorra um erro relacionado ao banco de dados.
                $Erro = 'Erro ao salvar funcionário: ' . $e->getMessage();

            } catch (Exception $e) {
                // Armazena a mensagem de erro da validação do salário.
                $Erro = $e->getMessage();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Funcionário</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>


    <!-- Altera o título conforme o modo do formulário. -->
    <h2><?= $editando ? 'Editar Funcionário' : 'Cadastrar Novo Funcionário' ?></h2>

    <!-- Exibe a mensagem de erro, caso exista. -->
    <?php if ($Erro): ?>
        <p class="Erro"><?= htmlspecialchars($Erro) ?></p>
    <?php endif; ?>

    <!-- Formulário que envia os dados pelo método POST. -->
    <form method="POST" action="form_funcionarios.php">

        <!-- Envia o ID oculto somente quando estiver editando um funcionário. -->
        <?php if ($editando && isset($funcionario['codigo_funcionario'])): ?>
            <input type="hidden" name="id"
                   value="<?= (int)$funcionario['codigo_funcionario'] ?>">
        <?php endif; ?>

        <!-- Campo para informar o nome do funcionário. -->
        <label for="nome_funcionario">Nome:</label>
        <input type="text" id="nome_funcionario"
               name="nome_funcionario" maxlength="50"
               value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>">

        <!-- Campo para informar o cargo do funcionário. -->
        <label for="cargo_funcionario">Cargo:</label>
        <input type="text" id="cargo_funcionario"
               name="cargo_funcionario" maxlength="30"
               value="<?= htmlspecialchars($funcionario['cargo_funcionario']) ?>">

        <!-- Campo numérico para informar o salário. -->
        <label for="salario_funcionario">Salário:</label>
        <input type="number" id="salario_funcionario"
               name="salario_funcionario" step="0.01" min="0"
               value="<?= htmlspecialchars($funcionario['salario_funcionario']) ?>">

        <!-- Campo para informar o telefone do funcionário. -->
        <label for="telefone_funcionario">Telefone:</label>
        <input type="text" id="telefone_funcionario"
               name="telefone_funcionario" maxlength="15"
               value="<?= htmlspecialchars($funcionario['telefone_funcionario']) ?>">

        <!-- Altera o texto do botão conforme o modo do formulário. -->
        <button type="submit">
            <?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?>
        </button>

    </form>

    <!-- Link para voltar à listagem de funcionários. -->
    <p><a class="btn btn-inicio" href="listar_funcionarios.php">Acessar lista de funcionários</a></p>

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
