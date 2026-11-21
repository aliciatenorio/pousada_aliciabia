<?php
require_once '../conexao.php';

$Erro = '';
$Sucesso = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$funcionario = [
    'nome_funcionario' => '',
    'cargo_funcionario' => '',
    'salario_funcionario' => '',
    'telefone_funcionario' => ''
];

$editando = false;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbFuncionarios WHERE codigo_funcionario = :id");
        $stmt->execute([':id' => $id]);

        $funcionario = $stmt->fetch();

        if (!$funcionario) {
            header('Location: listar_funcionarios.php');
            exit;
        }

        $editando = true;

    } catch (PDOException $e) {
        die("Erro ao buscar funcionario: " . $e->getMessage());
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome_funcionario'] ?? '');
    $cargo = trim($_POST['cargo_funcionario'] ?? '');
    $salario = trim($_POST['salario_funcionario'] ?? '');
    $telefone = trim($_POST['telefone_funcionario'] ?? '');

    $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($nome === '' || $salario === ''|| $cargo === '' || $telefone === '') {

        $Erro = 'Preencha todos os campos obrigatórios.';

        $funcionario = [
            'nome_funcionario' => $nome,
            'cargo_funcionario' => $cargo,
            'salario_funcionario' => $salario,
            'telefone_funcionario' => $telefone
        ];

        $editando = (bool)$id_post;

    } else {

        try {

            if (!is_numeric($salario) || $salario < 0) {
                throw new Exception('Informe um salário válido.');
            }

            if ($id_post) {

                $stmt = $pdo->prepare("
                    UPDATE tbFuncionarios
                    SET nome_funcionario = :nome,
                        cargo_funcionario = :cargo,
                        salario_funcionario = :salario,
                        telefone_funcionario = :telefone
                    WHERE codigo_funcionario = :id
                ");

                $stmt->execute([
                    ':nome' => $nome,
                    ':cargo' => $cargo,
                    ':salario' => $salario,
                    ':telefone' => $telefone,
                    ':id' => $id_post
                ]);

                $Sucesso = 'Funcionario atualizado com sucesso!';

            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO tbFuncionarios
                    (nome_funcionario, cargo_funcionario, salario_funcionario, telefone_funcionario)
                    VALUES
                    (:nome, :cargo, :salario, :telefone)
                ");

                $stmt->execute([
                    ':nome' => $nome,
                    ':cargo' => $cargo,
                    ':salario' => $salario,
                    ':telefone' => $telefone
                ]);

                $Sucesso = 'Funcionario cadastrado com sucesso!';
            }

            $funcionario = [
                'nome_funcionario' => $nome,
                'cargo_funcionario' => $cargo,
                'salario_funcionario' => $salario,
                'telefone_funcionario' => $telefone
            ];

            $editando = (bool)$id_post;

        } catch (Exception $e) {
            $Erro = $e->getMessage();

        } catch (PDOException $e) {
            $Erro = 'Erro ao salvar funcionario: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Novo Funcionario</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2><?= $editando ? 'Editar Funcionario' : 'Cadastrar Novo Funcionario' ?></h2>

    <?php if ($Erro): ?>
        <p class="Erro"><?= htmlspecialchars($Erro) ?></p>
    <?php endif; ?>

    <?php if ($Sucesso): ?>
        <p id="msg-sucesso" class="Sucesso">
            <?= htmlspecialchars($Sucesso) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="form_funcionarios.php">

        <?php if ($editando && isset($funcionario['codigo_funcionario'])): ?>
            <input type="hidden" name="id"
                   value="<?= (int)$funcionario['codigo_funcionario'] ?>">
        <?php endif; ?>

        <label for="nome_funcionario">Nome:</label>
        <input type="text" id="nome_funcionario"
               name="nome_funcionario" maxlength="50"
               value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>">

        <label for="cargo_funcionario">Cargo:</label>
        <input type="text" id="cargo_funcionario"
               name="cargo_funcionario" maxlength="30"
               value="<?= htmlspecialchars($funcionario['cargo_funcionario']) ?>">

        <label for="salario_funcionario">Salario:</label>
        <input type="number" id="salario_funcionario"
               name="salario_funcionario" step="0.01" min="0"
               value="<?= htmlspecialchars($funcionario['salario_funcionario']) ?>">

        <label for="telefone_funcionario">Telefone:</label>
        <input type="text" id="telefone_funcionario"
               name="telefone_funcionario" maxlength="15"
               value="<?= htmlspecialchars($funcionario['telefone_funcionario']) ?>">

        <button type="submit">
            <?= $editando ? 'Salvar Alterações' : 'Cadastrar' ?>
        </button>

    </form>

    <p><a href="listar_funcionarios.php">Voltar para funcionarios</a></p>

</body>
</html>
