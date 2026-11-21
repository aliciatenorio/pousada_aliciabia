<?php
require_once '../conexao.php';

try {

    $stmt = $pdo->prepare("SELECT * FROM tbHospedes ORDER BY nome_hospede ASC");
    $stmt->execute();
    

    $hospedes = $stmt->fetchAll();
    
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

    <?php if (count($hospedes) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Cidade</th>
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
