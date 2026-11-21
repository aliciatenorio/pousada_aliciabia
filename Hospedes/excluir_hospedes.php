<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe e valida o ID do hóspede enviado pelo formulário.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Se o ID for inválido, retorna para a listagem.
        if (!$id) {
            header('Location: listar_hospedes.php');
            exit;
        }

        try {
            // Prepara o comando SQL para excluir o hóspede pelo ID.
            $stmt = $pdo->prepare("DELETE FROM tbHospedes WHERE codigo_hospede = :id");

            // Executa a exclusão substituindo :id pelo ID recebido.
            $stmt->execute([':id' => $id]);

        } catch (PDOException $e) {
            // Exibe uma mensagem caso a exclusão não seja possível,
            // por exemplo, se o hóspede possuir uma reserva vinculada.
            die('Não foi possível excluir seu hóspede, pois possui uma reserva vinculada. Erro técnico (' . $e->getMessage() . ')');
        }

        // Após excluir, retorna à listagem com uma mensagem de sucesso.
        header('Location: listar_hospedes.php?msg=excluido');
        exit;
    }

    // Recebe e valida o ID enviado pela URL pelo método GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Se o ID for inválido, retorna para a listagem.
    if (!$id) {
        header('Location: listar_hospedes.php');
        exit;
    }

    try {
        // Busca os dados do hóspede que será excluído.
        $stmt = $pdo->prepare("SELECT * FROM tbHospedes WHERE codigo_hospede = :id");

        // Executa a consulta utilizando o ID recebido.
        $stmt->execute([':id' => $id]);

        // Armazena os dados encontrados em uma variável.
        $hospede = $stmt->fetch();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
        die("Erro ao buscar hospede: " . $e->getMessage());
    }

    // Se nenhum hóspede for encontrado, retorna à listagem.
    if (!$hospede) {
        header('Location: listar_hospedes.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmar Exclusão</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Formulário responsável por confirmar a exclusão. -->
    <form method="POST" action="excluir_hospedes.php">
        <h2>Confirmar Exclusão</h2>

        <p>
            Tem certeza que deseja excluir o hospede
            <!-- htmlspecialchars evita que caracteres especiais sejam interpretados como HTML. -->
            <strong><?= htmlspecialchars($hospede['nome_hospede']) ?></strong>
            (CPF: <?= htmlspecialchars($hospede['cpf_hospede']) ?>)?
        </p>

        <p>Esta ação não pode ser desfeita.</p>

        <!-- Envia o ID do hóspede junto com o formulário, sem exibi-lo na página. -->
        <input type="hidden" name="id" value="<?= (int)$hospede['codigo_hospede'] ?>">

        <div>
            <!-- Envia o formulário para executar a exclusão. -->
            <button type="submit" class="btn btn-excluir">Excluir</button>

            <!-- Redireciona para a listagem sem excluir o hóspede. -->
            <button type="submit" class="btn btn-editar" formaction="listar_hospedes.php">Cancelar</button>
        </div>
    </form>
</body>
</html>
