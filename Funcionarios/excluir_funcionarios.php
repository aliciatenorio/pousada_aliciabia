<?php
require_once '../conexao.php';

// POST = confirmação enviada -> executa a exclusão
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: listar_funcionarios.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM tbFuncionarios WHERE codigo_funcionario = :id");
        $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        die("Erro ao excluir funcionario: " . $e->getMessage());
    }

    header('Location: listar_funcionarios.php');
    exit;
}

// GET = mostra a tela de confirmação
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: listar_funcionarios.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tbFuncionarios WHERE codigo_funcionario = :id");
    $stmt->execute([':id' => $id]);

    $funcionario = $stmt->fetch();

} catch (PDOException $e) {
    die("Erro ao buscar funcionario: " . $e->getMessage());
}

if (!$funcionario) {
    header('Location: listar_funcionarios.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2>Confirmar Exclusão</h2>

    <p>
        Tem certeza que deseja excluir o funcionario
        <strong><?= htmlspecialchars($funcionario['nome_funcionario']) ?></strong>
        (cargo: <?= htmlspecialchars($funcionario['cargo_funcionario']) ?>)?
    </p>

    <p>Esta ação não pode ser desfeita.</p>

    <form method="POST" action="excluir_funcionarios.php">

        <input type="hidden" name="id"
               value="<?= (int)$funcionario['codigo_funcionario'] ?>">

        <button type="submit">Excluir</button>

        <button type="submit"
                formaction="listar_funcionarios.php">
            Cancelar
        </button>

    </form>

</body>
</html>