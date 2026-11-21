<?php
    require_once '../conexao.php';

    $sql = $pdo->prepare('SELECT tbFuncionarios.codigo_funcionario, tbFuncionarios.nome_funcionario, COUNT(tbReservas.codigo_reserva) AS total_reservas
                          FROM tbFuncionarios INNER JOIN tbReservas ON tbFuncionarios.codigo_funcionario = tbReservas.funcionario_codigo
                          GROUP BY tbFuncionarios.codigo_funcionario, tbFuncionarios.nome_funcionario
                          ORDER BY total_reservas DESC');

    $sql->execute();

    $reservas = $sql->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Funcionários</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="paginas-internas">
    <p><a class="btn btn-inicio" href="../index.php">Início</a></p>
    <h1>Relatório de Funcionários por Reservas</h1>

    <p>Este relatório apresenta os funcionários responsáveis pelas reservas.</p>

    <table>
        <tr>
            <th>Código do Funcionário</th>
            <th>Nome do Funcionário</th>
            <th>Total de Reservas</th>
        </tr>

        <?php foreach ($reservas as $reserva): ?>
            <tr>
                <td><?= htmlspecialchars($reserva['codigo_funcionario']) ?></td>
                <td><?= htmlspecialchars($reserva['nome_funcionario']) ?></td>
                <td><?= htmlspecialchars($reserva['total_reservas']) ?></td>
            </tr>
        <?php endforeach; ?>

    </table>

    <p>Total de funcionários com reservas: <?= count($reservas)?></p>

</body>
</html>
