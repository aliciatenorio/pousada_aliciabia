
<?php
    require_once '../conexao.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: listar_reservas.php');
            exit;
        }

        try {
            $stmt = $pdo->prepare(
                'DELETE FROM tbReservas WHERE codigo_reserva = :id'
            );

            $stmt->execute([':id' => $id]);

            header('Location: listar_reservas.php?msg=excluido');
            exit;

        } catch (PDOException $e) {
            die('Não foi possível excluir sua reserva, pois possui um quarto vinculado. Erro técnico (' . $e->getMessage(). ')');
        }
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: listar_reservas.php');
        exit;
    }

    try {
        $stmt = $pdo->prepare( 'SELECT tbReservas.*, tbHospedes.nome_hospede
                                FROM tbReservas INNER JOIN tbHospedes ON tbHospedes.codigo_hospede = tbReservas.hospede_codigo
                                WHERE tbReservas.codigo_reserva = :id'
        );

        $stmt->execute([':id' => $id]);
        $reserva = $stmt->fetch();
    } catch (PDOException $e) {
        die('Erro ao buscar reserva: ' . $e->getMessage());
    }

    if (!$reserva) {
        header('Location: listar_reservas.php');
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
    <form method="POST" action="excluir_reserva.php">

        <h2>Confirmar Exclusão</h2>

        <p>
            Tem certeza que deseja excluir a reserva do hóspede
            <strong><?= htmlspecialchars($reserva['nome_hospede']) ?></strong>,
            com entrada em
            <strong><?= DateTime::createFromFormat('Y-m-d', $reserva['data_entrada'])->format('d/m/Y') ?></strong>
            e saída em
            <strong><?= DateTime::createFromFormat('Y-m-d', $reserva['data_saida'])->format('d/m/Y') ?></strong>?
        </p>

        <p>Esta ação não pode ser desfeita.</p>

        <input type="hidden" name="id" value="<?= (int)$reserva['codigo_reserva'] ?>">

        <div class="botoes">
            <button type="submit" class="btn btn-excluir">Excluir</button>
            <button type="submit" class="btn btn-editar" formaction="listar_reservas.php">Cancelar</button>
        </div>

    </form>

</body>
</html>
