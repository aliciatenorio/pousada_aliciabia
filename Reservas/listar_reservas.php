<?php
    require_once '../conexao.php';

    $colunasPermitidas = [
        'id' => 'tbReservas.codigo_reserva',
        'hospede' => 'tbHospedes.nome_hospede',
        'funcionario' => 'tbFuncionarios.nome_funcionario',
        'entrada' => 'tbReservas.data_entrada',
        'saida' => 'tbReservas.data_saida',
        'valor' => 'tbReservas.valor_total'
    ];

    $coluna = filter_input(INPUT_GET, 'coluna', FILTER_SANITIZE_SPECIAL_CHARS);
    $ordem = filter_input(INPUT_GET, 'ordem', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!isset($colunasPermitidas[$coluna])) {
        $coluna = 'id';
    }

    if ($ordem !== 'DESC') {
        $ordem = 'ASC';
    }

    $campo = $colunasPermitidas[$coluna];

    try {
        $stmt = $pdo->query("SELECT tbReservas.*, tbHospedes.nome_hospede, tbFuncionarios.nome_funcionario
                             FROM tbReservas
                             INNER JOIN tbHospedes ON tbReservas.hospede_codigo = tbHospedes.codigo_hospede
                             INNER JOIN tbFuncionarios ON tbReservas.funcionario_codigo = tbFuncionarios.codigo_funcionario
                             ORDER BY $campo $ordem");
        $reservas = $stmt->fetchAll();

    } catch (PDOException $e) {
        die("Erro ao buscar reservas: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Lista de reservas</title>
        <link rel="stylesheet" href="../style.css">
    </head>
    <body>
        <h2>Reservas cadastradas</h2>
        <div class='mudanca'>
            <a class="btn btn-inicio" href="../index.php">Início</a>
            <a class="btn btn-novo" href="form_reserva.php">+ Nova Reserva</a>
        </div>

        <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);?>

        <?php if($msg === 'excluido'): ?>
            <p class="Sucesso">Reserva excluída com sucesso!</p>
        <?php endif; ?>

        <p >Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

        <?php if(count($reservas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th class="ordenacao"><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th class="ordenacao"><a href="?coluna=hospede&ordem=<?= $coluna === 'hospede' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome do Hóspede</a></th>
                    <th class="ordenacao"><a href="?coluna=funcionario&ordem=<?= $coluna === 'funcionario' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome do Funcionário</a></th>
                    <th class="ordenacao"><a href="?coluna=entrada&ordem=<?= $coluna === 'entrada' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Data de entrada</a></th>
                    <th class="ordenacao"><a href="?coluna=saida&ordem=<?= $coluna === 'saida' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Data de saída</a></th>
                    <th class="ordenacao"><a href="?coluna=valor&ordem=<?= $coluna === 'valor' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Valor</a></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($reservas as $reserva): ?>
                <tr>
                    <td><?= htmlspecialchars($reserva['codigo_reserva']) ?></td>
                    <td><?= htmlspecialchars($reserva['nome_hospede']) ?></td>
                    <td><?= htmlspecialchars($reserva['nome_funcionario']) ?></td>
                    <td><?= DateTime::createFromFormat('Y-m-d', $reserva['data_entrada'])->format('d/m/Y') ?></td>
                    <td><?= DateTime::createFromFormat('Y-m-d', $reserva['data_saida'])->format('d/m/Y') ?></td>
                    <td>R$<?= htmlspecialchars($reserva['valor_total']) ?></td>
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
