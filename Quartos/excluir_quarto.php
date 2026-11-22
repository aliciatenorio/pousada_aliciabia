<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe e valida o ID do quarto enviado pelo formulário.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Se o ID for inválido, retorna para a listagem.
        if (!$id) {
            header('Location: listar_quartos.php');
            exit;
        }

        try {
            // Prepara o comando SQL para excluir o quarto pelo ID.
            $stmt = $pdo->prepare(
                'DELETE FROM tbQuartos WHERE codigo_quarto = :id'
            );

            // Executa a exclusão substituindo :id pelo ID recebido.
            $stmt->execute([':id' => $id]);

            // Após excluir, retorna à listagem com uma mensagem de sucesso.
            header('Location: listar_quartos.php?msg=excluido');
            exit;

        } catch (PDOException $e) {
            // Exibe uma mensagem caso a exclusão não seja possível,
            // por exemplo, se o quarto estiver vinculado a uma reserva.
            die('Não foi possível excluir o quarto, pois ele está vinculado a uma reserva. Erro técnico (' . $e->getMessage(). ')');
        }
    }

    // Recebe e valida o ID enviado pela URL pelo método GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Se o ID for inválido, retorna para a listagem.
    if (!$id) {
        header('Location: listar_quartos.php');
        exit;
    }

    try {
        // Busca os dados do quarto e o nome do seu tipo.
        // O INNER JOIN relaciona as tabelas tbQuartos e tbTipos_Quarto
        // por meio do código do tipo de quarto.
        $stmt = $pdo->prepare( 'SELECT tbQuartos.*, tbTipos_Quarto.nome_tipo
                                FROM tbQuartos INNER JOIN tbTipos_Quarto ON tbTipos_Quarto.codigo_tipo = tbQuartos.tipo_quarto_codigo
                                WHERE tbQuartos.codigo_quarto = :id'
        );

        // Executa a consulta utilizando o ID recebido.
        $stmt->execute([':id' => $id]);

        // Armazena os dados encontrados em uma variável.
        $quarto = $stmt->fetch();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
        die('Erro ao buscar quarto: ' . $e->getMessage());
    }

    // Se nenhum quarto for encontrado, retorna à listagem.
    if (!$quarto) {
        header('Location: listar_quartos.php');
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
    <form method="POST" action="excluir_quarto.php">

        <h2>Confirmar Exclusão</h2>

        <p>
            Tem certeza que deseja excluir o quarto
            <!-- htmlspecialchars evita que caracteres especiais sejam interpretados como HTML. -->
            <strong><?= htmlspecialchars($quarto['numero_quarto']) ?></strong>
            do tipo <strong><?= htmlspecialchars($quarto['nome_tipo']) ?></strong>,
            localizado no andar <strong><?= htmlspecialchars($quarto['andar_quarto']) ?></strong>
            e com situação <strong><?= htmlspecialchars($quarto['situacao_quarto']) ?></strong>?
        </p>

        <p>Esta ação não pode ser desfeita.</p>

        <!-- Envia o ID do quarto junto com o formulário, sem exibi-lo na página. -->
        <input type="hidden" name="id" value="<?= (int) $quarto['codigo_quarto'] ?>">

        <div class="botoes">
            <!-- Envia o formulário para executar a exclusão. -->
            <button type="submit" class="btn btn-excluir">Excluir</button>

            <!-- Redireciona para a listagem sem excluir o quarto. -->
            <button type="submit" class="btn btn-editar" formaction="listar_quartos.php">Cancelar</button>

        </div>
    </form>
    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
