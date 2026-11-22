<?php
    // Inclui o arquivo responsável pela conexão com o banco de dados.
    require_once '../conexao.php';

    // Define quais colunas podem ser utilizadas na ordenação da tabela.
    // Cada opção recebida pela URL corresponde a uma coluna real do banco de dados.
    $colunasPermitidas = [
        'id' => 'tbReservas.codigo_reserva',
        'hospede' => 'tbHospedes.nome_hospede',
        'funcionario' => 'tbFuncionarios.nome_funcionario',
        'entrada' => 'tbReservas.data_entrada',
        'saida' => 'tbReservas.data_saida',
        'valor' => 'tbReservas.valor_total'
    ];

    // Recupera pela URL a coluna escolhida para ordenar os registros.
    $coluna = filter_input(INPUT_GET, 'coluna', FILTER_SANITIZE_SPECIAL_CHARS);

    // Recupera pela URL a direção da ordenação.
    $ordem = filter_input(INPUT_GET, 'ordem', FILTER_SANITIZE_SPECIAL_CHARS);

    // Verifica se a coluna recebida está entre as opções permitidas.
    // Caso contrário, utiliza o ID como coluna padrão.
    if (!isset($colunasPermitidas[$coluna])) {
        $coluna = 'id';
    }

    // Permite somente a ordenação DESC.
    // Qualquer outro valor será definido como ASC.
    if ($ordem !== 'DESC') {
        $ordem = 'ASC';
    }

    // Recupera o nome da coluna correspondente à opção escolhida.
    $campo = $colunasPermitidas[$coluna];

    try {
        // Busca os dados das reservas, incluindo os nomes dos hóspedes e funcionários.
        // O primeiro INNER JOIN relaciona as reservas aos hóspedes.
        // O segundo INNER JOIN relaciona as reservas aos funcionários.
        // ORDER BY organiza os resultados conforme a coluna e a direção escolhidas.
        $stmt = $pdo->query("SELECT tbReservas.*, tbHospedes.nome_hospede, tbFuncionarios.nome_funcionario
                             FROM tbReservas INNER JOIN tbHospedes ON tbReservas.hospede_codigo = tbHospedes.codigo_hospede
                                             INNER JOIN tbFuncionarios ON tbReservas.funcionario_codigo = tbFuncionarios.codigo_funcionario
                             ORDER BY $campo $ordem");

        // Recupera todas as reservas encontradas e armazena na variável $reservas.
        $reservas = $stmt->fetchAll();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra um erro na consulta.
        die("Erro ao buscar reservas: " . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Lista de reservas</title>

        <!-- Importa o arquivo CSS responsável pela aparência da página. -->
        <link rel="stylesheet" href="../style.css">
    </head>

    <body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>

        <!-- Caixa principal que organiza o conteúdo da listagem. -->
        <div class="caixa-listagem">

            <!-- Título da página. -->
            <h2>Reservas cadastradas</h2>

            <!-- Área com os botões para voltar ao início e cadastrar uma nova reserva. -->
            <div class="topo-pagina">
                <a class="btn btn-novo" href="form_reserva.php">+ Nova Reserva</a>
            </div>

            <!-- Recupera pela URL a mensagem referente à última operação realizada. -->
            <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS); ?>

            <!-- Exibe uma mensagem de acordo com a operação realizada. -->
            <?php if ($msg === 'excluido'): ?>
                <p class="Sucesso">Reserva excluída com sucesso!</p>
            <?php elseif ($msg === 'salvo'): ?>
                <p class="Sucesso">Reserva cadastrada com sucesso!</p>
            <?php elseif ($msg === 'editado'): ?>
                <p class="Sucesso">Reserva atualizada com sucesso!</p>
            <?php endif; ?>

            <!-- Orienta o usuário sobre a possibilidade de ordenar os registros. -->
            <p>Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

            <!-- Verifica se existem reservas cadastradas para exibir na tabela. -->
            <?php if (count($reservas) > 0): ?>

            <!-- Tabela que apresenta os dados das reservas. -->
            <table>
                <thead>
                    <tr>
                        <!-- Cada título possui um link que altera a coluna e a direção da ordenação. -->
                        <th class="ordenacao"><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                        <th class="ordenacao"><a href="?coluna=hospede&ordem=<?= $coluna === 'hospede' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome do Hóspede</a></th>
                        <th class="ordenacao"><a href="?coluna=funcionario&ordem=<?= $coluna === 'funcionario' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Nome do Funcionário</a></th>
                        <th class="ordenacao"><a href="?coluna=entrada&ordem=<?= $coluna === 'entrada' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Data de entrada</a></th>
                        <th class="ordenacao"><a href="?coluna=saida&ordem=<?= $coluna === 'saida' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Data de saída</a></th>
                        <th class="ordenacao"><a href="?coluna=valor&ordem=<?= $coluna === 'valor' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Valor</a></th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Percorre todas as reservas encontradas e cria uma linha para cada registro. -->
                    <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <!-- Exibe o código da reserva. -->
                        <td><?= htmlspecialchars($reserva['codigo_reserva']) ?></td>

                        <!-- Exibe o nome do hóspede obtido pelo INNER JOIN. -->
                        <td><?= htmlspecialchars($reserva['nome_hospede']) ?></td>

                        <!-- Exibe o nome do funcionário obtido pelo INNER JOIN. -->
                        <td><?= htmlspecialchars($reserva['nome_funcionario']) ?></td>

                        <!-- Converte a data de entrada do formato do banco para o formato brasileiro. -->
                        <td><?= DateTime::createFromFormat('Y-m-d', $reserva['data_entrada'])->format('d/m/Y') ?></td>

                        <!-- Converte a data de saída do formato do banco para o formato brasileiro. -->
                        <td><?= DateTime::createFromFormat('Y-m-d', $reserva['data_saida'])->format('d/m/Y') ?></td>

                        <!-- Exibe o valor total formatado como moeda brasileira. -->
                        <td>R$ <?= number_format((float)$reserva['valor_total'], 2, ',', '.') ?></td>

                        <!-- Disponibiliza os links para editar ou excluir a reserva. -->
                        <td>
                            <a class="btn btn-editar" href="form_reserva.php?id=<?= (int)$reserva['codigo_reserva'] ?>">Editar</a>
                            <a class="btn btn-excluir" href="excluir_reserva.php?id=<?= (int)$reserva['codigo_reserva'] ?>">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php else: ?>
                <!-- Mensagem exibida quando não existem reservas cadastradas. -->
                <p>Nenhuma reserva encontrada.</p>
            <?php endif; ?>

        </div>
        <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
