<?php
require_once '../conexao.php';

// Busca todos os tipos de quarto cadastrados
try {
    $tiposQuarto = $pdo->query('SELECT * FROM tbTipos_Quarto')->fetchAll();
} catch(PDOException $e) {
    die("Erro ao buscar tipos de quarto: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Quarto</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="caixa-listagem">

        <h2>Tipos de Quarto cadastrados</h2>

        <div class="topo-pagina">

            <a class="btn btn-inicio" href="../Quartos/listar_quartos.php">
                Voltar para Quartos
            </a>

            <a class="btn btn-novo" href="form_tiposQuarto.php">
                + Novo Tipo de Quarto
            </a>

        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'excluido'): ?>
            <p>Tipo de quarto excluído com sucesso!</p>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'salvo'): ?>
            <p>Tipo de quarto cadastrado com sucesso!</p>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'editado'): ?>
            <p>Tipo de quarto atualizado com sucesso!</p>
        <?php endif; ?>

        <?php if (count($tiposQuarto) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Valor da Diária</th>
                        <th>Capacidade</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($tiposQuarto as $tipo): ?>
                        <tr>
                            <td><?= htmlspecialchars($tipo['codigo_tipo']) ?></td>
                            <td><?= htmlspecialchars($tipo['nome_tipo']) ?></td>
                            <td><?= htmlspecialchars($tipo['descricao_tipo']) ?></td>
                            <td>R$ <?= number_format($tipo['valor_diaria'], 2, ',', '.') ?></td>
                            <td><?= htmlspecialchars($tipo['capacidade']) ?></td>

                            <td>
                                <a href="form_tiposQuarto.php?id=<?= $tipo['codigo_tipo'] ?>">
                                    Editar
                                </a>

                                <a href="excluir_tiposQuarto.php?id=<?= $tipo['codigo_tipo'] ?>">
                                    Excluir
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <p>Nenhum tipo de quarto cadastrado.</p>
        <?php endif; ?>

    </div>

</body>
</html>

