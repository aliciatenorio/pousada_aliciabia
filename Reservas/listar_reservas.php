<?php
    require_once '../conexao.php';
    try {
        $stmt = $pdo->query("SELECT tbReservas.*, tbHospedes.nome_hospede
                             FROM tbReservas INNER JOIN tbHospedes ON tbReservas.hospede_codigo = tbHospedes.codigo_hospede
                             ORDER BY tbReservas.codigo_reserva ASC");
        $reservas = $stmt->fetchAll();

    } catch(PDOException $e){
        die("Erro ao buscar reservas: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Lista de reservas</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body class="paginas-internas">
        <h2>Reservas cadastradas</h2>

        <p><a class="btn btn-inicio" href="../index.php">Início</a></p>
        <p><a class="btn btn-novo" href="form_reserva.php">+Nova Reserva</a></p>

        <?php if(count($reservas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Hóspede</th>
                    <th>Data de entrada</th>
                    <th>Data de saída</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reservas as $reserva): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($reserva['codigo_reserva']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($reserva['nome_hospede']) ?>
                    </td>
                    <td>
                        <?= DateTime::createFromFormat('Y-m-d', $reserva['data_entrada'])->format('d/m/Y') ?>
                    </td>
                    <td>
                        <?= DateTime::createFromFormat('Y-m-d', $reserva['data_saida'])->format('d/m/Y') ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($reserva['valor_total']) ?>
                    </td>
                    <td>
                        <a class="btn btn-editar" href="form_reserva.php?id=<?= (int)$reserva['codigo_reserva'] ?>">Editar</a>
                        <a class="btn btn-excluir" href="excluir_reserva.php?id=<?= (int)$reserva['codigo_reserva'] ?>">Excluir</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Nenhuma reserva encontrada</p>
        <?php endif; ?>
    </body>
</html>
