<?php
    // Inclui a conexão com o banco de dados.
    require_once '../conexao.php';

    // Recupera o ID enviado pela URL.
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Define os campos vazios para um novo cadastro.
    $tipo = [
        'codigo_tipo' => '',
        'nome_tipo' => '',
        'descricao_tipo' => '',
        'valor_diaria' => '',
        'capacidade' => ''
    ];

    // Variáveis para controlar erros e edição.
    $Erro = '';
    $editar = false;

    // Verifica se existe um ID para buscar os dados.
    if (isset($_GET['id'])) {

        if (!$id || $id < 1) {
            header('Location: listar_tiposQuarto.php');
            exit;
        }

        try {
            // Busca o tipo de quarto pelo código.
            $stmt = $pdo->prepare(
                'SELECT * FROM tbTipos_Quarto WHERE codigo_tipo = :id'
            );

            $stmt->execute([':id' => $id]);

            // Armazena os dados encontrados.
            $tipo = $stmt->fetch(PDO::FETCH_ASSOC);

            // Se não encontrar, volta para a listagem.
            if (!$tipo) {
                header('Location: listar_tiposQuarto.php');
                exit;
            }

            $editar = true;

        } catch (PDOException $e) {
            die('Erro ao buscar tipo de quarto: ' . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recupera os dados enviados pelo formulário.
        $id_post = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome_tipo'] ?? '');
        $descricao = trim($_POST['descricao_tipo'] ?? '');
        $valor = trim($_POST['valor_diaria'] ?? '');
        $capacidade = filter_input(INPUT_POST, 'capacidade', FILTER_VALIDATE_INT);

        // Mantém os dados preenchidos caso aconteça algum erro.
        $tipo = [
            'codigo_tipo' => $id_post ?: '',
            'nome_tipo' => $nome,
            'descricao_tipo' => $descricao,
            'valor_diaria' => $valor,
            'capacidade' => $capacidade ?? ''
        ];

        // Verifica se está editando ou cadastrando.
        $editar = isset($_POST['id']);

        // Verifica se o ID da edição é válido.
        if ($editar && (!$id_post || $id_post < 1)) {

            $Erro = 'Código do tipo de quarto inválido!';

        // Verifica se os campos foram preenchidos.
        } elseif (
            $nome === '' ||
            $descricao === '' ||
            $valor === '' ||
            $capacidade === false ||
            $capacidade === null ||
            $capacidade < 1
        ) {

            $Erro = 'Preencha todos os campos corretamente!';

        // Verifica se o valor da diária é válido.
        } elseif (!is_numeric($valor) || $valor < 0) {

            $Erro = 'Informe um valor de diária válido!';

        } else {

            try {

                if ($editar) {

                    // Confirma se o tipo de quarto existe antes de atualizar.
                    $verifica = $pdo->prepare(
                        'SELECT codigo_tipo FROM tbTipos_Quarto WHERE codigo_tipo = :id'
                    );

                    $verifica->execute([':id' => $id_post]);

                    if (!$verifica->fetch()) {
                        $Erro = 'Tipo de quarto não encontrado!';
                    } else {

                        // Atualiza os dados do tipo de quarto.
                        $stmt = $pdo->prepare("
                            UPDATE tbTipos_Quarto
                            SET nome_tipo = :nome,
                                descricao_tipo = :descricao,
                                valor_diaria = :valor,
                                capacidade = :capacidade
                            WHERE codigo_tipo = :id
                        ");

                        $stmt->execute([
                            ':nome' => $nome,
                            ':descricao' => $descricao,
                            ':valor' => $valor,
                            ':capacidade' => $capacidade,
                            ':id' => $id_post
                        ]);

                        header('Location: listar_tiposQuarto.php?msg=editado');
                        exit;
                    }

                } else {

                    // Cadastra um novo tipo de quarto.
                    $stmt = $pdo->prepare("
                        INSERT INTO tbTipos_Quarto
                        (nome_tipo, descricao_tipo, valor_diaria, capacidade)
                        VALUES
                        (:nome, :descricao, :valor, :capacidade)
                    ");

                    $stmt->execute([
                        ':nome' => $nome,
                        ':descricao' => $descricao,
                        ':valor' => $valor,
                        ':capacidade' => $capacidade
                    ]);

                    header('Location: listar_tiposQuarto.php?msg=salvo');
                    exit;
                }

            } catch (PDOException $e) {
                // Armazena a mensagem caso ocorra algum erro.
                $Erro = 'Erro ao salvar tipo de quarto: ' . $e->getMessage();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipo de Quarto</title>

    <!-- Importa o arquivo CSS do projeto. -->
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>


    <div class="caixa-formulario">

        <!-- O título muda conforme o cadastro ou a edição. -->
        <h2>
            <?= $editar ? 'Editar Tipo de Quarto' : 'Cadastrar Tipo de Quarto' ?>
        </h2>

        <!-- Exibe uma mensagem caso ocorra algum erro. -->
        <?php if ($Erro): ?>
            <p class="Erro">
                <?= htmlspecialchars($Erro) ?>
            </p>
        <?php endif; ?>

        <!-- Formulário utilizado para cadastrar ou editar. -->
        <form method="POST" action="form_tiposQuarto.php">

            <!-- Guarda o código do tipo de quarto durante a edição. -->
            <?php if ($editar): ?>
                <input type="hidden" name="id"
                       value="<?= (int)$tipo['codigo_tipo'] ?>">
            <?php endif; ?>

            <!-- Campo para informar o nome do tipo de quarto. -->
            <label for="nome_tipo">Nome do tipo:</label>
            <input type="text"
                   id="nome_tipo"
                   name="nome_tipo"
                   maxlength="20"
                   value="<?= htmlspecialchars($tipo['nome_tipo']) ?>"
                   required>

            <!-- Campo para informar a descrição do quarto. -->
            <label for="descricao_tipo">Descrição:</label>
            <input type="text"
                   id="descricao_tipo"
                   name="descricao_tipo"
                   maxlength="50"
                   value="<?= htmlspecialchars($tipo['descricao_tipo']) ?>"
                   required>

            <!-- Campo para informar o valor da diária. -->
            <label for="valor_diaria">Valor da diária:</label>
            <input type="number"
                   id="valor_diaria"
                   name="valor_diaria"
                   step="0.01"
                   min="0"
                   value="<?= htmlspecialchars((string)$tipo['valor_diaria']) ?>"
                   required>

            <!-- Campo para informar a capacidade de pessoas. -->
            <label for="capacidade">Capacidade:</label>
            <input type="number"
                   id="capacidade"
                   name="capacidade"
                   min="1"
                   value="<?= htmlspecialchars((string)$tipo['capacidade']) ?>"
                   required>

            <!-- O texto do botão muda conforme a operação. -->
            <button type="submit">
                <?= $editar ? 'Salvar alterações' : 'Cadastrar' ?>
            </button>

        </form>

        <br>

        <!-- Link para voltar à listagem. -->
        <a class="btn btn-inicio" href="listar_tiposQuarto.php">
            Voltar
        </a>

    </div>

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
