<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Define quais colunas podem ser utilizadas na ordenação.
    $colunasPermitidas = [
        'id' => 'codigo_hospede',
        'nome' => 'nome_hospede',
        'cpf' => 'cpf_hospede',
        'telefone' => 'telefone_hospede',
        'cidade' => 'cidade_hospede',
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

    try {
        // Busca todos os hóspedes e ordena pela coluna e direção escolhidas.
        $stmt = $pdo->query("SELECT * FROM tbHospedes ORDER BY $campo $ordem")->fetchAll();

        // Armazena os registros encontrados.
        $hospedes = $stmt;

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
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

    <!-- Caixa que organiza o conteúdo da listagem. -->
    <div class="caixa-listagem">

        <h2>Hospedes Cadastrados</h2>

        <!-- Links para voltar ao início ou cadastrar um novo hóspede. -->
        <div class="topo-pagina">
            <a class="btn btn-inicio" href="../index.php">Início</a>
            <a class="btn btn-novo" href="form_hospedes.php">+ Novo Hospede</a>
        </div>

        <!-- Recebe pela URL a mensagem enviada após uma operação. -->
        <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS); ?>

        <!-- Exibe a mensagem correspondente à operação realizada. -->
        <?php if ($msg === 'excluido'): ?>
            <p class="Sucesso">Hóspede excluído com sucesso!</p>
        <?php elseif ($msg === 'salvo'): ?>
            <p class="Sucesso">Hóspede cadastrado com sucesso!</p>
        <?php elseif ($msg === 'editado'): ?>
            <p class="Sucesso">Hóspede atualizado com sucesso!</p>
        <?php endif; ?>

        <p>Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

        <!-- Verifica se existem hóspedes cadastrados. -->
        <?php if (count($hospedes) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <!-- Cada título envia pela URL a coluna e a ordem desejadas. -->
                        <!-- Se já estiver em ASC, muda para DESC; caso contrário, muda para ASC. -->
                        <th><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                        <th><a href="?coluna=nome&ordem=<?= $coluna === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome</a></th>
                        <th><a href="?coluna=cpf&ordem=<?= $coluna === 'cpf' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">CPF</a></th>
                        <th><a href="?coluna=telefone&ordem=<?= $coluna === 'telefone' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Telefone</a></th>
                        <th><a href="?coluna=cidade&ordem=<?= $coluna === 'cidade' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Cidade</a></th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Percorre todos os hóspedes encontrados na consulta. -->
                    <?php foreach ($hospedes as $hospede): ?>
                        <tr>
                            <!-- Exibe os dados do hóspede protegendo a saída com htmlspecialchars. -->
                            <td><?= htmlspecialchars($hospede['codigo_hospede']) ?></td>
                            <td><?= htmlspecialchars($hospede['nome_hospede']) ?></td>
                            <td><?= htmlspecialchars($hospede['cpf_hospede']) ?></td>
                            <td><?= htmlspecialchars($hospede['telefone_hospede']) ?></td>
                            <td><?= htmlspecialchars($hospede['cidade_hospede']) ?></td>

                            <td>
                                <!-- Envia o ID pela URL para abrir o formulário de edição. -->
                                <a class="btn btn-editar" href="form_hospedes.php?id=<?= (int)$hospede['codigo_hospede'] ?>">Editar</a>

                                <!-- Envia o ID pela URL para abrir a confirmação de exclusão. -->
                                <a class="btn btn-excluir" href="excluir_hospedes.php?id=<?= (int)$hospede['codigo_hospede'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <!-- Exibe esta mensagem quando não existem hóspedes cadastrados. -->
            <p>Nenhum hospede encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
