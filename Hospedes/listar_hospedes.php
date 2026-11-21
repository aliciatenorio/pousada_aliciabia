<?php
    require_once '../conexao.php';

    $colunasPermitidas = [
        'id' => 'codigo_hospede',
        'nome' => 'nome_hospede',
        'cpf' => 'cpf_hospede',
        'telefone' => 'telefone_hospede',
        'cidade' => 'cidade_hospede',
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
        $stmt = $pdo->query("SELECT * FROM tbHospedes ORDER BY $campo $ordem")->fetchAll();
        $hospedes = $stmt;

    } catch (PDOException $e) {
        die("Erro ao buscar seus hospedes: " . $e->getMessage());
    }
    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Hospedes Cadastrados</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2>Hospedes Cadastrados</h2>

    <div class='mudanca'>
        <a class="btn btn-inicio" href="../index.php">Início</a>
        <a class="btn btn-novo" href="form_hospedes.php">+ Novo Hospede</a>
    </div>

    <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);?>

    <?php if($msg === 'excluido'): ?>
        <p class="Sucesso">Hóspede excluído com sucesso!</p>
    <?php endif; ?>

    <p >Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

    <?php if (count($hospedes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th><a href="?coluna=nome&ordem=<?= $coluna === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome</a></th>
                    <th><a href="?coluna=cpf&ordem=<?= $coluna === 'cpf' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">CPF</a></th>
                    <th><a href="?coluna=telefone&ordem=<?= $coluna === 'telefone' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Telefone</a></th>
                    <th><a href="?coluna=cidade&ordem=<?= $coluna === 'cidade' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Cidade</a></th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hospedes as $hospede): ?>
                    <tr>
                        <td><?= htmlspecialchars($hospede['codigo_hospede']) ?></td>
                        <td><?= htmlspecialchars($hospede['nome_hospede']) ?></td>
                        <td><?= htmlspecialchars($hospede['cpf_hospede']) ?></td>
                        <td><?= htmlspecialchars($hospede['telefone_hospede']) ?></td>
                        <td><?= htmlspecialchars($hospede['cidade_hospede']) ?></td>
                        <td>
                            <a class="btn btn-editar" href="form_hospedes.php?id=<?= (int)$hospede['codigo_hospede'] ?>">Editar</a>
                            <a class="btn btn-excluir" href="excluir_hospedes.php?id=<?= (int)$hospede['codigo_hospede'] ?>">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nenhum hospede encontrado.</p>
    <?php endif; ?>

</body>
</html>
