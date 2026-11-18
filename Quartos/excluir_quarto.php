<?php
    require_once '../conexao.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: listar_quartos.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                'DELETE FROM tbQuartos WHERE codigo_quarto = :id'
            );

            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            die('Não foi possível excluir o quarto, pois ele está vinculado a uma reserva. Erro técnico (' . $e->getMessage(). ')');
        }

        header('Location: listar_quartos.php');
        exit;
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: listar_quartos.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare( 'SELECT tbQuartos.*, tbTipos_Quarto.nome_tipo
                                FROM tbQuartos INNER JOIN tbTipos_Quarto ON tbTipos_Quarto.codigo_tipo = tbQuartos.tipo_quarto_codigo
                                WHERE tbQuartos.codigo_quarto = :id'
        );

        $stmt->execute([':id' => $id]);
        $quarto = $stmt->fetch();
    } catch (PDOException $e) {
        die('Erro ao buscar quarto: ' . $e->getMessage());
    }

    if (!$quarto) {
        header('Location: listar_quartos.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2>Confirmar Exclusão</h2>

    <p>
        Tem certeza que deseja excluir o quarto <strong><?= htmlspecialchars($quarto['numero_quarto']) ?></strong>
        do tipo <strong><?= htmlspecialchars($quarto['nome_tipo']) ?></strong>,
        localizado no andar <strong><?= htmlspecialchars($quarto['andar_quarto']) ?></strong>
        e com situação <strong><?= htmlspecialchars($quarto['situacao_quarto']) ?></strong>?
    </p>

    <p>Esta ação não pode ser desfeita.</p>

    <form method="POST" action="excluir_quarto.php">
        <input type="hidden" name="id" value="<?= (int) $quarto['codigo_quarto'] ?>">
        <button type="submit" class="btn btn-excluir">Excluir</button>
        <button type="submit" class="btn btn-editar" formaction="listar_quartos.php">Cancelar</button>
    </form>
</body>
</html>
