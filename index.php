<?php
require_once 'conexao.php';
require_once 'includes/rodape.php';

try {
    $totalHospedes = $pdo->query('SELECT COUNT(*) FROM tbHospedes')->fetchColumn();
    $totalFuncionarios = $pdo->query('SELECT COUNT(*) FROM tbFuncionarios')->fetchColumn();
    $totalTiposQuarto = $pdo->query('SELECT COUNT(*) FROM tbTipos_Quarto')->fetchColumn();
    $totalReserva = $pdo->query('SELECT COUNT(*) FROM tbReservas')->fetchColumn();
    $totalQuartos = $pdo->query('SELECT COUNT(*) FROM tbQuartos')->fetchColumn();
} catch (PDOException $e) {
    die('Erro ao carregar o resumo: ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pousada</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- CABEÇALHO -->
    <header class="cabecalho">
        <h2>Sistema Pousada</h2>
        <p>Gerenciamento de hóspedes, funcionários e reservas</p>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="inicio">

        <h1>Sistema Pousada</h1>

        <p>
            <a class="btn btn-opcao" href="hospedes/listar_hospedes.php">
                Hóspedes (<?= (int) $totalHospedes ?>)
            </a>
        </p>

        <p>
            <a class="btn btn-opcao" href="funcionarios/listar_funcionarios.php">
                Funcionários (<?= (int) $totalFuncionarios ?>)
            </a>
        </p>

        <p>
            <a class="btn btn-opcao" href="tipoQuartos/listar_tiposQuarto.php">
                Tipos de Quarto (<?= (int) $totalTiposQuarto ?>)
            </a>
        </p>

        <p>
            <a class="btn btn-opcao" href="reservas/listar_reservas.php">
                Reservas (<?= (int) $totalReserva ?>)
            </a>
        </p>

        <p>
            <a class="btn btn-opcao" href="quartos/listar_quartos.php">
                Quartos (<?= (int) $totalQuartos ?>)
            </a>
        </p>

        <p>
            <a class="btn btn-opcao" href="relatório/reservas_por_hospede.php">
                Reservas por hóspede
            </a>
        </p>

    </div>

    <!-- RODAPÉ -->
    <?php require_once 'Includes/rodape.php'; ?>

</body>
</html>
