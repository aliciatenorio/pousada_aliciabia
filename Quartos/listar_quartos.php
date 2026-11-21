
<?php
    require_once '../conexao.php';

    $colunasPermitidas = [
        'id' => 'tbQuartos.codigo_quarto',
        'numero' => 'tbQuartos.numero_quarto',
        'andar' => 'tbQuartos.andar_quarto',
        'situacao' => 'tbQuartos.situacao_quarto',
        'tipo_quarto' => 'tbTipos_Quarto.nome_tipo',
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
        $stmt = $pdo->query("SELECT * 
                             FROM tbQuartos INNER JOIN tbTipos_Quarto ON tbQuartos.tipo_quarto_codigo = tbTipos_Quarto.codigo_tipo 
                             ORDER BY $campo $ordem")->fetchAll();
        $quartos = $stmt;

    } catch(PDOException $e){
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>Lista de quartos</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <h2>Quartos cadastrados</h2>

    <div class='mudanca'>
        <a class="btn btn-inicio" href="../index.php">Início</a>
        <a class="btn btn-novo" href="form_quarto.php">+ Novo Quarto</a>
    </div>

    <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);?>

    <?php if($msg === 'excluido'): ?>
        <p class="Sucesso">Quarto excluído com sucesso!</p>
    <?php endif; ?>

    <p >Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

    <?php if(count($quartos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th class="ordenacao"><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th class="ordenacao"><a href="?coluna=numero&ordem=<?= $coluna === 'numero' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Número</a></th>
                    <th class="ordenacao"><a href="?coluna=andar&ordem=<?= $coluna === 'andar' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Andar</a></th>
                    <th class="ordenacao"><a href="?coluna=situacao&ordem=<?= $coluna === 'situacao' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Situação</a></th>
                    <th class="ordenacao"><a href="?coluna=tipo_quarto&ordem=<?= $coluna === 'tipo_quarto' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Tipo Quarto</a></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($quartos as $quarto): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($quarto['codigo_quarto']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($quarto['numero_quarto']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($quarto['andar_quarto']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($quarto['situacao_quarto']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($quarto['nome_tipo']) ?>
                    </td>
                    <td>
                        <a class="btn btn-editar" href="form_quarto.php?id=<?= (int)$quarto['codigo_quarto'] ?>">Editar</a>
                        <a class="btn btn-excluir" href="excluir_quarto.php?id=<?= (int)$quarto['codigo_quarto'] ?>">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            </table>
    <?php else: ?>
        <p>Nenhum quarto encontrado</p>
    <?php endif; ?>
</body>
</html>
