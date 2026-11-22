<?php
require_once '../conexao.php';

// Verifica se recebeu o código do tipo
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$erro = '';

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        try {
            // Exclui o tipo de quarto
            $sql = "DELETE FROM tbTipos_Quarto
                    WHERE codigo_tipo = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            // Volta para a listagem
            header('Location: listar_tiposQuarto.php?msg=excluido');
            exit;

        } catch (PDOException $e) {
            // Caso existam quartos utilizando esse tipo
            $erro = "Não foi possível excluir este tipo de quarto, pois existem quartos cadastrados utilizando ele.";
        }
    }
}

// Busca os dados do tipo de quarto
if ($id) {
    $sql = "SELECT * FROM tbTipos_Quarto
            WHERE codigo_tipo = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tipo) {
        header('Location: listar_tiposQuarto.php');
        exit;
    }
} else {
    header('Location: listar_tiposQuarto.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Excluir Tipo de Quarto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="caixa-formulario">
    <h2>Excluir Tipo de Quarto</h2>

    <?php if ($erro): ?>
        <p style="color: red;">
            <?= htmlspecialchars($erro) ?>
        </p>
    <?php else: ?>
        <p>
            Tem certeza que deseja excluir o tipo
            <?= htmlspecialchars($tipo['nome_tipo']) ?>?
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="id"
               value="<?= $tipo['codigo_tipo'] ?>">

        <button type="submit" <?= $erro ? 'disabled' : '' ?>>
            Confirmar exclusão
        </button>

        <a href="listar_tiposQuarto.php">Voltar</a>
    </form>
</div>

</body>
</html>
