<?php
    require_once '../conexao.php';

    $sql = $pdo->prepare('SELECT tbHospedes.codigo_hospede, tbHospedes.nome_hospede, COUNT(tbReservas.codigo_reserva) AS total_reservas
                          FROM tbHospedes INNER JOIN tbReservas ON tbHospedes.codigo_hospede = tbReservas.hospede_codigo
                          GROUP BY tbHospedes.codigo_hospede, tbHospedes.nome_hospede
                          ORDER BY total_reservas DESC');

    $sql->execute();

    $reservas = $sql->fetchAll();

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Reservas</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <p><a class="btn btn-inicio" href="../index.php">Início</a></p>
    <h1>Relatório de Reservas por Hóspede</h1>

    <p>Este relatório apresenta a quantidade de reservas realizadas por cada hóspede cadastrado no hotel.</p>

    <table border="1">
        <tr>
            <th>Código</th>
            <th>Nome do Hóspede</th>
            <th>Total de Reservas</th>
        </tr>

        <?php foreach ($reservas as $reserva): ?>
            <tr>
                <td><?= htmlspecialchars($reserva['codigo_hospede']) ?></td>
                <td><?= htmlspecialchars($reserva['nome_hospede']) ?></td>
                <td><?= htmlspecialchars($reserva['total_reservas']) ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

    <p>Total de hóspedes com reservas: <?= count($reservas)?></p>

</body>
</html>
