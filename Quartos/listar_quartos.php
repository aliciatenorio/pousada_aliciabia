
<?php
    require_once '../conexao.php';

    try {
        $stmt = $pdo->query("SELECT * FROM tbQuartos INNER JOIN tbTipos_Quarto ON tbQuartos.tipo_quarto_codigo = tbTipos_Quarto.codigo_tipo ORDER BY codigo_quarto ASC")->fetchAll();
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

    <p><a class="btn btn-novo" href="form_quarto.php">Novo Quarto</a></p>

    <?php if(count($quartos) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número</th>
                    <th>Andar</th>
                    <th>Situação</th>
                    <th>Tipo de quarto</th>
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
                    </td>
                    <td>
                        <a class="btn btn-editar" href="excluir_quarto.php?id=<?= (int)$quarto['codigo_quarto'] ?>">Excluir</a>
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