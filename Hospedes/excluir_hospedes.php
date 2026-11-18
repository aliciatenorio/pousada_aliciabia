<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: listar_hospedes.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM tbHospedes WHERE codigo_hospede = :id");
        $stmt->execute([':id' => $id]);

    } catch (PDOException $e) {
        die("Erro ao excluir hospede: " . $e->getMessage());
    }

    header('Location: listar_hospedes.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: listar_hospedes.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM tbHospedes WHERE codigo_hospede = :id");
    $stmt->execute([':id' => $id]);

    $hospede = $stmt->fetch();

} catch (PDOException $e) {
    die("Erro ao buscar hospede: " . $e->getMessage());
}

if (!$hospede) {
    header('Location: listar_hospedes.php');
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
        Tem certeza que deseja excluir o hospede
        <strong><?= htmlspecialchars($hospede['nome_hospede']) ?></strong>
        (CPF: <?= htmlspecialchars($hospede['cpf_hospede']) ?>)?
    </p>

    <p>Esta ação não pode ser desfeita.</p>

    <form method="POST" action="excluir_hospedes.php">

        <input type="hidden" name="id"
               value="<?= (int)$hospede['codigo_hospede'] ?>">

        <button type="submit">Excluir</button>

        <button type="submit"
                formaction="listar_hospedes.php">
            Cancelar
        </button>

    </form>

</body>
</html>