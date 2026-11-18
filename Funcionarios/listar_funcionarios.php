<?php
require_once '../conexao.php';

try {

    $stmt = $pdo->prepare("SELECT * FROM tbFuncionarios ORDER BY nome_funcionario ASC");
    $stmt->execute();


    $funcionarios = $stmt->fetchAll();

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

    <p><a href="../index.php">Início</a></p>

    <p><a href="form_funcionarios.php">+ Novo Funcionario</a></p>

    <?php if (count($funcionarios) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cargo</th>
                    <th>Salario</th>
                    <th>Telefone</th>
                    <th>Acoes</th>
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
                            <a href="form_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Editar</a>

                            <a href="excluir_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Excluir</a>
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