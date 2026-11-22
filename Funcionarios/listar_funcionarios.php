<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Define quais colunas podem ser utilizadas na ordenação.
    // Isso impede que o usuário escolha campos não permitidos na consulta SQL.
    $colunasPermitidas = [
        'id' => 'codigo_funcionario',
        'nome' => 'nome_funcionario',
        'cargo' => 'cargo_funcionario',
        'salario' => 'salario_funcionario',
        'telefone' => 'telefone_funcionario',
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
        // Busca todos os funcionários e ordena pela coluna e direção escolhidas.
        $stmt = $pdo->query("SELECT * FROM tbFuncionarios ORDER BY $campo $ordem")->fetchAll();

        // Armazena os registros encontrados.
        $funcionarios = $stmt;

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
        die("Erro ao buscar funcionários.");
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Funcionários</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>


    <!-- Caixa que organiza o conteúdo da listagem. -->
    <div class="caixa-listagem">
        <h2>Funcionários Cadastrados</h2>

        <!-- Links para voltar ao início ou cadastrar um novo funcionário. -->
        <div class="topo-pagina">
            <a class="btn btn-novo" href="form_funcionarios.php">+ Novo Funcionário</a>
        </div>

        <!-- Recebe pela URL a mensagem enviada após uma operação. -->
        <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS); ?>

        <!-- Exibe a mensagem correspondente à operação realizada. -->
        <?php if ($msg === 'excluido'): ?>
            <p class="Sucesso">Funcionário excluído com sucesso!</p>
        <?php elseif ($msg === 'salvo'): ?>
            <p class="Sucesso">Funcionário cadastrado com sucesso!</p>
        <?php elseif ($msg === 'editado'): ?>
            <p class="Sucesso">Funcionário atualizado com sucesso!</p>
        <?php endif; ?>

        <p>Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

        <!-- Verifica se existem funcionários cadastrados. -->
        <?php if (count($funcionarios) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <!-- Cada título envia pela URL a coluna e a ordem desejadas. -->
                        <!-- Se já estiver em ASC, muda para DESC; caso contrário, muda para ASC. -->
                        <th><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                        <th><a href="?coluna=nome&ordem=<?= $coluna === 'nome' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome</a></th>
                        <th><a href="?coluna=cargo&ordem=<?= $coluna === 'cargo' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Cargo</a></th>
                        <th><a href="?coluna=salario&ordem=<?= $coluna === 'salario' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Salário</a></th>
                        <th><a href="?coluna=telefone&ordem=<?= $coluna === 'telefone' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Telefone</a></th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Percorre todos os funcionários encontrados na consulta. -->
                    <?php foreach ($funcionarios as $funcionario): ?>
                        <tr>
                            <!-- Exibe os dados do funcionário protegendo a saída com htmlspecialchars. -->
                            <td><?= htmlspecialchars($funcionario['codigo_funcionario']) ?></td>
                            <td><?= htmlspecialchars($funcionario['nome_funcionario']) ?></td>
                            <td><?= htmlspecialchars($funcionario['cargo_funcionario']) ?></td>

                            <!-- Formata o salário com duas casas decimais e separadores brasileiros. -->
                            <td>R$ <?= number_format((float)$funcionario['salario_funcionario'], 2, ',', '.') ?></td>

                            <td><?= htmlspecialchars($funcionario['telefone_funcionario']) ?></td>

                            <td>
                                <!-- Envia o ID pela URL para abrir o formulário de edição. -->
                                <a class="btn btn-editar" href="form_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Editar</a>

                                <!-- Envia o ID pela URL para abrir a confirmação de exclusão. -->
                                <a class="btn btn-excluir" href="excluir_funcionarios.php?id=<?= (int)$funcionario['codigo_funcionario'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <!-- Exibe esta mensagem quando não existem funcionários cadastrados. -->
            <p>Nenhum funcionário encontrado.</p>
        <?php endif; ?>
    </div>
    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
