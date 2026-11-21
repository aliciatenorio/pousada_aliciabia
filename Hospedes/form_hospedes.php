<?php
require_once '../conexao.php';

$erro = '';
$sucesso = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$hospede = [
    'nome_hospede' => '',
    'cpf_hospede' => '',
    'telefone_hospede' => '',
    'cidade_hospede' => ''
];

$editando = false;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbHospedes WHERE codigo_hospede = :id");
        $stmt->execute([':id' => $id]);

        $hospede = $stmt->fetch();

        if (!$hospede) {
            header('Location: listar_hospedes.php');
            exit;
        }

        $editando = true;

    } catch (PDOException $e) {
        die("Erro ao buscar hospede: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome_hospede'] ?? '');
    $cpf = trim($_POST['cpf_hospede'] ?? '');
    $telefone = trim($_POST['telefone_hospede'] ?? '');
    $cidade = trim($_POST['cidade_hospede'] ?? '');

    $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($nome === '' || $cpf === ''|| $telefone ==='' || $cidade === '') {

        $erro = 'Preencha todos os campos obrigatórios.';

        $hospede = [
            'nome_hospede' => $nome,
            'cpf_hospede' => $cpf,
            'telefone_hospede' => $telefone,
            'cidade_hospede' => $cidade
        ];

        $editando = (bool)$id_post;

    } else {

        try {

            if ($id_post) {

                $stmt = $pdo->prepare("
                    UPDATE tbHospedes
                    SET nome_hospede = :nome,
                        cpf_hospede = :cpf,
                        telefone_hospede = :telefone,
                        cidade_hospede = :cidade
                    WHERE codigo_hospede = :id");

                $stmt->execute([
                    ':nome' => $nome,
                    ':cpf' => $cpf,
                    ':telefone' => $telefone,
                    ':cidade' => $cidade,
                    ':id' => $id_post
                ]);

                $sucesso = 'Hospede atualizado com sucesso!';

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO tbHospedes
                    (nome_hospede, cpf_hospede, telefone_hospede, cidade_hospede)
                    VALUES
                    (:nome, :cpf, :telefone, :cidade)
                ");

                $stmt->execute([
                    ':nome' => $nome,
                    ':cpf' => $cpf,
                    ':telefone' => $telefone,
                    ':cidade' => $cidade
                ]);

                $sucesso = 'Hospede cadastrado com sucesso!';
            }

            $hospede = [
                'nome_hospede' => $nome,
                'cpf_hospede' => $cpf,
                'telefone_hospede' => $telefone,
                'cidade_hospede' => $cidade
            ];

            $editando = (bool)$id_post;

        } catch (PDOException $e) {

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

    <h2><?= $editando ? 'Editar Hospede' : 'Cadastrar Novo Hospede' ?></h2>

    <?php if ($erro): ?>
        <p class="Erro"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <?php if ($sucesso): ?>
        <p id="msg-sucesso" class="Sucesso">
            <?= htmlspecialchars($sucesso) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="form_hospedes.php">

        <?php if ($editando && isset($hospede['codigo_hospede'])): ?>
            <input type="hidden" name="id"
                   value="<?= (int)$hospede['codigo_hospede'] ?>">
        <?php endif; ?>

        <label for="nome_hospede">Nome:</label>
        <input type="text" id="nome_hospede" name="nome_hospede"
               maxlength="50"
               value="<?= htmlspecialchars($hospede['nome_hospede']) ?>">

        <label for="cpf_hospede">CPF:</label>
        <input type="text" id="cpf_hospede" name="cpf_hospede"
               maxlength="14"
               value="<?= htmlspecialchars($hospede['cpf_hospede']) ?>">

        <label for="telefone_hospede">Telefone:</label>
        <input type="text" id="telefone_hospede" name="telefone_hospede"
               maxlength="15"
               value="<?= htmlspecialchars($hospede['telefone_hospede']) ?>">

        <label for="cidade_hospede">Cidade:</label>
        <input type="text" id="cidade_hospede" name="cidade_hospede"
               maxlength="30"
               value="<?= htmlspecialchars($hospede['cidade_hospede']) ?>">

        <button type="submit">
            <?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?>
        </button>

    </form>

    <p><a href="listar_hospedes.php">Voltar para hospedes</a></p>

</body>
</html>
