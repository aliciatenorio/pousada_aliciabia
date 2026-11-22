<?php
    require_once '../conexao.php';

    // Define quais colunas podem ser utilizadas na ordenação.
    $colunasPermitidas = [
        'id' => 'codigo_tipo',
        'nome' => 'nome_tipo',
        'descricao' => 'descricao_tipo',
        'valor' => 'valor_diaria',
        'capacidade' => 'capacidade',
    ];

    // Recebe pela URL a coluna que será utilizada para ordenar.
    $coluna = filter_input(INPUT_GET, 'coluna', FILTER_SANITIZE_SPECIAL_CHARS);

    // Recebe pela URL a direção da ordenação (ASC ou DESC).
    $ordem = filter_input(INPUT_GET, 'ordem', FILTER_SANITIZE_SPECIAL_CHARS);

    // Se a coluna recebida não estiver na lista permitida, utiliza o ID.
    if (!isset($colunasPermitidas[$coluna])) {
        $coluna = 'id';
    }

    // Se a ordem não for DESC, define como ASC.
    if ($ordem !== 'DESC') {
        $ordem = 'ASC';
    }

    // Obtém o nome real da coluna correspondente à opção escolhida.
    $campo = $colunasPermitidas[$coluna];

    // Busca todos os tipos de quarto cadastrados e ordena os registros.
    try {
        $tiposQuarto = $pdo->query(
            "SELECT * FROM tbTipos_Quarto ORDER BY $campo $ordem"
        )->fetchAll();

    } catch (PDOException $e) {
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
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>

    <div class="caixa-listagem">

        <h2>Tipos de Quarto cadastrados</h2>

        <div class="topo-pagina">
            <a class="btn btn-novo" href="form_tiposQuarto.php"> + Novo Tipo de Quarto</a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'excluido'): ?>
            <p class="Sucesso">Tipo de quarto excluído com sucesso!</p>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'salvo'): ?>
            <p class="Sucesso">Tipo de quarto cadastrado com sucesso!</p>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'editado'): ?>
            <p class="Sucesso">Tipo de quarto atualizado com sucesso!</p>
        <?php endif; ?>

        <p>Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

        <?php if (count($tiposQuarto) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <!-- Cada título envia pela URL a coluna e a ordem desejadas. -->
                        <!-- Se já estiver em ASC, muda para DESC; caso contrário, muda para ASC. -->
                        <th><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Código</a></th>
                        <th><a href="?coluna=nome&ordem=<?= $coluna === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome</a></th>
                        <th><a href="?coluna=descricao&ordem=<?= $coluna === 'descricao' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Descrição</a></th>
                        <th><a href="?coluna=valor&ordem=<?= $coluna === 'valor' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Valor da Diária</a></th>
                        <th><a href="?coluna=capacidade&ordem=<?= $coluna === 'capacidade' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Capacidade</a></th>
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
                                <a class='btn btn-editar' href="form_tiposQuarto.php?id=<?= (int)$tipo['codigo_tipo'] ?>">
                                    Editar
                                </a>

                                <a class='btn btn-excluir' href="excluir_tiposQuarto.php?id=<?= (int)$tipo['codigo_tipo'] ?>">
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

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
