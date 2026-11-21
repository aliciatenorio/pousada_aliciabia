<?php
    require_once '../conexao.php';

    $colunasPermitidas = [
        'id' => 'codigo_funcionario',
        'nome' => 'nome_funcionario',
        'cargo' => 'cargo_funcionario',
        'salario' => 'salario_funcionario',
        'telefone' => 'telefone_funcionario',
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
        $stmt = $pdo->query("SELECT * FROM tbFuncionarios ORDER BY $campo $ordem")->fetchAll();
        $funcionarios = $stmt;

    } catch (PDOException $e) {
        die("Erro ao buscar funcionarios: " . $e->getMessage());
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Funcionarios</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <h2>Funcionarios Cadastrados</h2>

    <div class='mudanca'>
        <a class="btn btn-inicio" href="../index.php">Início</a>
        <a class="btn btn-novo" href="form_funcionarios.php">+ Novo Funcionario</a>
    </div>

    <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);?>

    <?php if($msg === 'excluido'): ?>
        <p class="Sucesso">Funcionário excluído com sucesso!</p>
    <?php endif; ?>

    <p >Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>


    <?php if (count($funcionarios) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                    <th><a href="?coluna=nome&ordem=<?= $coluna === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome</a></th>
                    <th><a href="?coluna=cargo&ordem=<?= $coluna === 'cargo' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Cargo</a></th>
                    <th><a href="?coluna=salario&ordem=<?= $coluna === 'salario' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Salário</a></th>
                    <th><a href="?coluna=telefone&ordem=<?= $coluna === 'telefone' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Telefone</a></th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($funcionarios as $funcionario): ?>
                    <tr>
                        <td><?= htmlspecialchars($funcionario['codigo_funcionario']) ?></td>
                        <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                        <td><?= htmlspecialchars($funcionario['cargo_funcionario']) ?></td>
                        <td>R$ <?= number_format($funcionario['salario_funcionario'], 2, ',', '.') ?></td>
                        <td><?= htmlspecialchars($funcionario['telefone_funcionario']) ?></td>

                        <td>
                            <a class="btn btn-editar" href="form_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Editar</a>
                            <a class="btn btn-excluir" href="excluir_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>Nenhum funcionario encontrado.</p>
    <?php endif; ?>

</body>
</html>
