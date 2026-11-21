
<?php
    // Inclui o arquivo responsável pela conexão com o banco de dados.
    require_once '../conexao.php';

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recupera o ID da reserva enviado pelo formulário e valida se é um número inteiro.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Se o ID não for válido, retorna para a listagem de reservas.
        if (!$id) {
            header('Location: listar_reservas.php');
            exit;
        }

        try {
            // Prepara o comando SQL para excluir a reserva pelo seu código.
            $stmt = $pdo->prepare(
                'DELETE FROM tbReservas WHERE codigo_reserva = :id'
            );

            // Executa o DELETE utilizando o ID recebido.
            $stmt->execute([':id' => $id]);

            // Redireciona para a listagem com uma mensagem de sucesso.
            header('Location: listar_reservas.php?msg=excluido');
            exit;

        } catch (PDOException $e) {
            // Exibe uma mensagem caso a reserva não possa ser excluída.
            // Isso pode acontecer quando existe um quarto vinculado à reserva.
            die('Não foi possível excluir sua reserva, pois possui um quarto vinculado. Erro técnico (' . $e->getMessage(). ')');
        }
    }

    // Recupera o ID da reserva enviado pela URL para buscar seus dados.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Se o ID não for válido, retorna para a listagem.
    if (!$id) {
        header('Location: listar_reservas.php');
        exit;
    }

    try {
        // Prepara a consulta para buscar os dados da reserva e o nome do hóspede.
        // O INNER JOIN relaciona as tabelas tbReservas e tbHospedes.
        $stmt = $pdo->prepare( 'SELECT tbReservas.*, tbHospedes.nome_hospede
                                FROM tbReservas INNER JOIN tbHospedes ON tbHospedes.codigo_hospede = tbReservas.hospede_codigo
                                WHERE tbReservas.codigo_reserva = :id'
        );

        // Executa a consulta utilizando o código da reserva.
        $stmt->execute([':id' => $id]);

        // Recupera os dados da reserva encontrada.
        $reserva = $stmt->fetch();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra um erro ao buscar a reserva.
        die('Erro ao buscar reserva: ' . $e->getMessage());
    }

    // Se a reserva não for encontrada, retorna para a listagem.
    if (!$reserva) {
        header('Location: listar_reservas.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Exclusão</title>

    <!-- Importa o arquivo CSS responsável pela aparência da página. -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Formulário responsável por confirmar ou cancelar a exclusão da reserva. -->
    <form method="POST" action="excluir_reserva.php">

        <h2>Confirmar Exclusão</h2>

        <!-- Exibe os dados da reserva para que o usuário confirme a exclusão. -->
        <p>
            Tem certeza que deseja excluir a reserva do hóspede
            <strong><?= htmlspecialchars($reserva['nome_hospede']) ?></strong>,
            com entrada em
            <strong><?= DateTime::createFromFormat('Y-m-d', $reserva['data_entrada'])->format('d/m/Y') ?></strong>
            e saída em
            <strong><?= DateTime::createFromFormat('Y-m-d', $reserva['data_saida'])->format('d/m/Y') ?></strong>?
        </p>

        <!-- Informa que a exclusão não poderá ser desfeita. -->
        <p>Esta ação não pode ser desfeita.</p>

        <!-- Campo oculto que envia o código da reserva para o processamento da exclusão. -->
        <input type="hidden" name="id" value="<?= (int)$reserva['codigo_reserva'] ?>">

        <!-- Área com os botões de confirmação e cancelamento. -->
        <div class="botoes">

            <!-- Envia o formulário pelo POST para executar a exclusão. -->
            <button type="submit" class="btn btn-excluir">Excluir</button>

            <!-- Redireciona para a listagem sem executar a exclusão. -->
            <button type="submit" class="btn btn-editar" formaction="listar_reservas.php">Cancelar</button>

        </div>

    </form>

</body>
</html>
