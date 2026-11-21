<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recebe e valida o ID do funcionário enviado pelo formulário.
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Se o ID for inválido, retorna para a listagem.
        if (!$id) {
            header('Location: listar_funcionarios.php');
            exit;
        }

        try {
            // Prepara o comando SQL para excluir o funcionário pelo ID.
            $stmt = $pdo->prepare("DELETE FROM tbFuncionarios WHERE codigo_funcionario = :id");

            // Executa a exclusão substituindo :id pelo ID recebido.
            $stmt->execute([':id' => $id]);

        } catch (PDOException $e) {
            // Exibe uma mensagem caso a exclusão não seja possível,
            // por exemplo, se o funcionário possuir uma reserva vinculada.
            die('Não foi possível excluir seu funcionário, pois possui uma reserva vinculada. Erro técnico (' . $e->getMessage() . ')');
        }

        // Após excluir, retorna à listagem com uma mensagem de sucesso.
        header('Location: listar_funcionarios.php?msg=excluido');
        exit;
    }

    // Recebe e valida o ID enviado pela URL pelo método GET.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Se o ID for inválido, retorna para a listagem.
    if (!$id) {
        header('Location: listar_funcionarios.php');
        exit;
    }

    try {
        // Busca os dados do funcionário que será excluído.
        $stmt = $pdo->prepare("SELECT * FROM tbFuncionarios WHERE codigo_funcionario = :id");

        // Executa a consulta utilizando o ID recebido.
        $stmt->execute([':id' => $id]);

        // Armazena os dados encontrados em uma variável.
        $funcionario = $stmt->fetch();

    } catch (PDOException $e) {
        // Exibe uma mensagem caso ocorra erro na consulta.
        die("Erro ao buscar funcionario: " . $e->getMessage());
    }

    // Se nenhum funcionário for encontrado, retorna à listagem.
    if (!$funcionario) {
        header('Location: listar_funcionarios.php');
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
    <form method="POST" action="excluir_funcionarios.php">
        <h2>Confirmar Exclusão</h2>

        <p>
            Tem certeza que deseja excluir o funcionario
            <!-- htmlspecialchars evita que caracteres especiais sejam interpretados como HTML. -->
            <strong><?= htmlspecialchars($funcionario['nome_funcionario']) ?></strong>
            (cargo: <?= htmlspecialchars($funcionario['cargo_funcionario']) ?>)?
        </p>

        <p>Esta ação não pode ser desfeita.</p>

        <!-- Envia o ID do funcionário junto com o formulário, sem exibi-lo na página. -->
        <input type="hidden" name="id" value="<?= (int)$funcionario['codigo_funcionario'] ?>">

        <div>
            <!-- Envia o formulário para executar a exclusão. -->
            <button type="submit" class="btn btn-excluir">Excluir</button>

            <!-- Redireciona para a listagem sem excluir o funcionário. -->
            <button type="submit" class="btn btn-editar" formaction="listar_funcionarios.php">Cancelar</button>
        </div>
    </form>

</body>
</html>
