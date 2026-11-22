<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe e valida o ID do tipo de quarto enviado pelo formulário.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Se o ID for inválido, retorna para a listagem.
        if (!$id) {
            header('Location: listar_tiposQuarto.php');
            exit;
        }

        try {
            // Prepara o comando SQL para excluir o tipo de quarto pelo ID.
            $stmt = $pdo->prepare(
                'DELETE FROM tbTipos_Quarto WHERE codigo_tipo = :id'
            );

            // Executa a exclusão substituindo :id pelo ID recebido.
            $stmt->execute([':id' => $id]);

            // Após excluir, retorna à listagem com uma mensagem de sucesso.
            header('Location: listar_tiposQuarto.php?msg=excluido');
            exit;

        } catch (PDOException $e) {
            // Exibe uma mensagem caso existam quartos utilizando esse tipo.
            die('Não foi possível excluir o tipo de quarto, pois existem quartos cadastrados utilizando ele. Erro técnico (' . $e->getMessage() . ')');
        }
    }

    // Recebe e valida o ID enviado pela URL pelo método GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Se o ID for inválido, retorna para a listagem.
    if (!$id) {
        header('Location: listar_tiposQuarto.php');
        exit;
    }

    try {
        // Busca os dados do tipo de quarto pelo ID.
        $stmt = $pdo->prepare(
            'SELECT * FROM tbTipos_Quarto WHERE codigo_tipo = :id'
        );

        // Executa a consulta utilizando o ID recebido.
        $stmt->execute([':id' => $id]);

        // Armazena os dados encontrados em uma variável.
        $tipo = $stmt->fetch();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
        die('Erro ao buscar tipo de quarto: ' . $e->getMessage());
    }

    // Se nenhum tipo de quarto for encontrado, retorna à listagem.
    if (!$tipo) {
        header('Location: listar_tiposQuarto.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>

    <!-- Formulário responsável por confirmar a exclusão. -->
    <form method="POST" action="excluir_tiposQuarto.php">

        <h2>Confirmar Exclusão</h2>

        <p>
            Tem certeza que deseja excluir o tipo de quarto
            <strong><?= htmlspecialchars($tipo['nome_tipo']) ?></strong>,
            com descrição
            <strong><?= htmlspecialchars($tipo['descricao_tipo']) ?></strong>,
            valor da diária de
            <strong>R$ <?= number_format($tipo['valor_diaria'], 2, ',', '.') ?></strong>
            e capacidade para
            <strong><?= htmlspecialchars($tipo['capacidade']) ?></strong>
            pessoas?
        </p>

        <p>Esta ação não pode ser desfeita.</p>

        <!-- Envia o ID do tipo de quarto junto com o formulário. -->
        <input type="hidden" name="id" value="<?= (int)$tipo['codigo_tipo'] ?>">

        <div class="botoes">
            <!-- Envia o formulário para executar a exclusão. -->
            <button type="submit" class="btn btn-excluir">Excluir</button>

            <!-- Retorna para a listagem sem realizar a exclusão. -->
            <a href="listar_tiposQuarto.php" class="btn btn-editar">Cancelar</a>
        </div>

    </form>

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
