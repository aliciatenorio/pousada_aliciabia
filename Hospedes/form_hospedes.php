<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Variável que armazena possíveis mensagens de erro.
    $erro = '';

    // Recebe o ID pela URL e verifica se é um número inteiro válido.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Define os campos vazios para o formulário de cadastro.
    $hospede = [
        'nome_hospede' => '',
        'cpf_hospede' => '',
        'telefone_hospede' => '',
        'cidade_hospede' => ''
    ];

    // Indica se o formulário está sendo usado para editar.
    $editando = false;

    // Se existir um ID na URL, busca os dados do hóspede.
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbHospedes WHERE codigo_hospede = :id");
            $stmt->execute([':id' => $id]);

            // Armazena os dados encontrados.
            $hospede = $stmt->fetch();

            // Se o hóspede não existir, volta para a listagem.
            if (!$hospede) {
                header('Location: listar_hospedes.php');
                exit;
            }

            // Indica que o formulário está no modo de edição.
            $editando = true;

        } catch (PDOException $e) {
            // Exibe o erro caso não seja possível buscar o hóspede.
            die("Erro ao buscar hospede: " . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe os dados digitados e remove espaços do início e do final.
        $nome = trim($_POST['nome_hospede'] ?? '');
        $cpf = trim($_POST['cpf_hospede'] ?? '');
        $telefone = trim($_POST['telefone_hospede'] ?? '');
        $cidade = trim($_POST['cidade_hospede'] ?? '');

        // Recebe e valida o ID enviado pelo formulário.
        $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Atualiza o array com os valores enviados, mantendo os dados no formulário.
        $hospede = [
            'codigo_hospede' => $id_post,
            'nome_hospede' => $nome,
            'cpf_hospede' => $cpf,
            'telefone_hospede' => $telefone,
            'cidade_hospede' => $cidade
        ];

        // Verifica se o formulário está no modo de edição.
        $editando = (bool)$id_post;

        // Verifica se algum campo obrigatório está vazio.
        if ($nome === '' || $cpf === '' || $telefone === '' || $cidade === '') {

            $erro = 'Preencha todos os campos obrigatórios.';

        } else {

            try {

                // Se existir ID, atualiza os dados do hóspede.
                if ($id_post) {

                    $stmt = $pdo->prepare("
                        UPDATE tbHospedes
                        SET nome_hospede = :nome,
                            cpf_hospede = :cpf,
                            telefone_hospede = :telefone,
                            cidade_hospede = :cidade
                        WHERE codigo_hospede = :id");

                    // Executa a atualização substituindo os parâmetros pelos valores recebidos.
                    $stmt->execute([
                        ':nome' => $nome,
                        ':cpf' => $cpf,
                        ':telefone' => $telefone,
                        ':cidade' => $cidade,
                        ':id' => $id_post
                    ]);

                    // Redireciona para a listagem com a mensagem de edição realizada.
                    header('Location: listar_hospedes.php?msg=editado');
                    exit;

                } else {

                    // Se não existir ID, prepara o cadastro de um novo hóspede.
                    $stmt = $pdo->prepare("
                        INSERT INTO tbHospedes
                        (nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
                        VALUES
                        (:nome, :cpf, :telefone, :cidade)
                    ");

                    // Executa a inserção dos dados no banco.
                    $stmt->execute([
                        ':nome' => $nome,
                        ':cpf' => $cpf,
                        ':telefone' => $telefone,
                        ':cidade' => $cidade
                    ]);

                    // Redireciona para a listagem com a mensagem de cadastro realizado.
                    header('Location: listar_hospedes.php?msg=salvo');
                    exit;
                }

            } catch (PDOException $e) {
                // Armazena a mensagem caso ocorra um erro relacionado ao banco de dados.
                $erro = 'Erro ao salvar hospede: ' . $e->getMessage();

            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Hospede</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>


    <!-- Altera o título conforme o modo do formulário. -->
    <h2><?= $editando ? 'Editar Hospede' : 'Cadastrar Novo Hospede' ?></h2>

    <!-- Exibe a mensagem de erro, caso exista. -->
    <?php if ($erro): ?>
        <p class="Erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <!-- Formulário que envia os dados pelo método POST. -->
    <form method="POST" action="form_hospedes.php">

        <!-- Envia o ID oculto somente quando estiver editando um hóspede. -->
        <?php if ($editando && isset($hospede['codigo_hospede'])): ?>
            <input type="hidden" name="id" value="<?= (int)$hospede['codigo_hospede'] ?>">
        <?php endif; ?>

        <!-- Campo para informar o nome do hóspede. -->
        <label for="nome_hospede">Nome:</label>
        <input type="text" id="nome_hospede" name="nome_hospede"
               maxlength="50"
               value="<?= htmlspecialchars($hospede['nome_hospede']) ?>">

        <!-- Campo para informar o CPF do hóspede. -->
        <label for="cpf_hospede">CPF:</label>
        <input type="text" id="cpf_hospede" name="cpf_hospede"
               maxlength="14"
               value="<?= htmlspecialchars($hospede['cpf_hospede']) ?>">

        <!-- Campo para informar o telefone do hóspede. -->
        <label for="telefone_hospede">Telefone:</label>
        <input type="text" id="telefone_hospede" name="telefone_hospede"
               maxlength="15"
               value="<?= htmlspecialchars($hospede['telefone_hospede']) ?>">

        <!-- Campo para informar a cidade do hóspede. -->
        <label for="cidade_hospede">Cidade:</label>
        <input type="text" id="cidade_hospede" name="cidade_hospede"
               maxlength="30"
               value="<?= htmlspecialchars($hospede['cidade_hospede']) ?>">

        <!-- Altera o texto do botão conforme o modo do formulário. -->
        <button type="submit">
            <?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?>
        </button>

    </form>

    <!-- Link para voltar à listagem de hóspedes. -->
    <p><a class="btn btn-inicio" href="listar_hospedes.php">Acessar lista de hóspedes</a></p>

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
