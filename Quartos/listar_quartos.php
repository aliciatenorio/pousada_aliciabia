<?php
    // Inclui o arquivo responsável pela conexão com o banco de dados.
    require_once '../conexao.php';

    // Define quais colunas podem ser utilizadas na ordenação da tabela.
    // Os nomes recebidos pela URL são associados às colunas reais do banco.
    $colunasPermitidas = [
        'id' => 'tbQuartos.codigo_quarto',
        'numero' => 'tbQuartos.numero_quarto',
        'andar' => 'tbQuartos.andar_quarto',
        'situacao' => 'tbQuartos.situacao_quarto',
        'tipo_quarto' => 'tbTipos_Quarto.nome_tipo',
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
        // Busca os quartos e seus respectivos tipos.
        // O INNER JOIN relaciona as duas tabelas por meio da chave estrangeira.
        // ORDER BY organiza os resultados conforme a coluna e a direção escolhidas.
        $stmt = $pdo->query("SELECT *
                            FROM tbQuartos INNER JOIN tbTipos_Quarto ON tbQuartos.tipo_quarto_codigo = tbTipos_Quarto.codigo_tipo
                            ORDER BY $campo $ordem")->fetchAll();

        // Armazena todos os quartos encontrados na variável $quartos.
        $quartos = $stmt;

    } catch(PDOException $e) {
        // Exibe uma mensagem caso ocorra um erro na consulta.
        die("Erro ao buscar quartos: " . $e->getMessage());
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de quartos</title>

    <!-- Importa o arquivo CSS responsável pela aparência da página. -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Caixa principal que organiza o conteúdo da listagem. -->
    <div class="caixa-listagem">

        <!-- Título da página. -->
        <h2>Quartos cadastrados</h2>

        <!-- Área com os botões para voltar ao início e cadastrar um novo quarto. -->
        <div class="topo-pagina">
            <a class="btn btn-inicio" href="../index.php">Início</a>
            <a class="btn btn-novo" href="form_quarto.php">+ Novo Quarto</a>
        </div>

        <!-- Recupera pela URL a mensagem referente à última operação realizada. -->
        <?php $msg = filter_input(INPUT_GET, 'msg', FILTER_SANITIZE_SPECIAL_CHARS);?>

        <!-- Exibe uma mensagem de acordo com a operação realizada. -->
        <?php if($msg === 'excluido'): ?>
            <p class="Sucesso">Quarto excluído com sucesso!</p>
        <?php elseif($msg === 'salvo'): ?>
            <p class="Sucesso">Quarto cadastrado com sucesso!</p>
        <?php elseif($msg === 'editado'): ?>
            <p class="Sucesso">Quarto atualizado com sucesso!</p>
        <?php endif; ?>

        <!-- Orienta o usuário sobre a possibilidade de ordenar os registros. -->
        <p>Clique no título das colunas para ordenar os registros (A–Z ou Z–A).</p>

        <!-- Verifica se existem quartos cadastrados para exibir na tabela. -->
        <?php if(count($quartos) > 0): ?>

            <!-- Tabela que apresenta os dados dos quartos. -->
            <table>
                <thead>
                    <tr>
                        <!-- Cada título possui um link que altera a coluna e a direção da ordenação. -->
                        <th class="ordenacao"><a href="?coluna=id&ordem=<?= $coluna === 'id' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">ID</a></th>
                        <th class="ordenacao"><a href="?coluna=numero&ordem=<?= $coluna === 'numero' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Número</a></th>
                        <th class="ordenacao"><a href="?coluna=andar&ordem=<?= $coluna === 'andar' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Andar</a></th>
                        <th class="ordenacao"><a href="?coluna=situacao&ordem=<?= $coluna === 'situacao' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Situação</a></th>
                        <th class="ordenacao"><a href="?coluna=tipo_quarto&ordem=<?= $coluna === 'tipo_quarto' && $ordem === 'ASC' ? 'DESC' : 'ASC' ?>">Tipo Quarto</a></th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Percorre todos os quartos encontrados e cria uma linha para cada registro. -->
                    <?php foreach($quartos as $quarto): ?>
                        <tr>
                            <!-- Exibe o código do quarto. -->
                            <td>
                                <?= htmlspecialchars($quarto['codigo_quarto']) ?>
                            </td>

                            <!-- Exibe o número do quarto. -->
                            <td>
                                <?= htmlspecialchars($quarto['numero_quarto']) ?>
                            </td>

                            <!-- Exibe o andar do quarto. -->
                            <td>
                                <?= htmlspecialchars($quarto['andar_quarto']) ?>
                            </td>

                            <!-- Exibe a situação atual do quarto. -->
                            <td>
                                <?= htmlspecialchars($quarto['situacao_quarto']) ?>
                            </td>

                            <!-- Exibe o nome do tipo de quarto obtido pelo INNER JOIN. -->
                            <td>
                                <?= htmlspecialchars($quarto['nome_tipo']) ?>
                            </td>

                            <!-- Disponibiliza os links para editar ou excluir o quarto. -->
                            <td>
                                <a class="btn btn-editar" href="form_quarto.php?id=<?= (int)$quarto['codigo_quarto'] ?>">Editar</a>
                                <a class="btn btn-excluir" href="excluir_quarto.php?id=<?= (int)$quarto['codigo_quarto'] ?>">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <!-- Mensagem exibida quando não existem quartos cadastrados. -->
            <p>Nenhum quarto encontrado</p>
        <?php endif; ?>

    </div>
</body>
</html>
