<?php
    // Inclui o arquivo responsável pela conexão com o banco de dados.
    require_once '../conexao.php';

    // Recupera o ID enviado pela URL e verifica se é um número inteiro válido.
    $idQuarto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Define os valores iniciais dos campos do formulário.
    // Eles ficam vazios quando um novo quarto será cadastrado.
    $quarto = [
        'numero_quarto' => '',
        'andar_quarto' => '',
        'situacao_quarto' => '',
        'tipo_quarto_codigo' => ''
    ];

    // Variável que armazena possíveis mensagens de erro.
    $Erro = '';

    // Indica se o formulário está sendo utilizado para editar um quarto.
    $editar = false;

    // Se existir um ID válido na URL, significa que o quarto será editado.
    if ($idQuarto) {
        try {
            // Prepara a consulta para buscar o quarto pelo seu código.
            $stmt = $pdo->prepare("SELECT * FROM tbQuartos WHERE codigo_quarto = :id");

            // Executa a consulta utilizando o ID recebido.
            $stmt->execute([':id' => $idQuarto]);

            // Recupera os dados do quarto encontrado.
            $quarto = $stmt->fetch();

            // Se nenhum quarto for encontrado, retorna para a listagem.
            if (!$quarto) {
                header('Location: listar_quartos.php');
                exit;
            }

            // Indica que o formulário está no modo de edição.
            $editar = true;

        } catch (PDOException $e) {
            // Exibe uma mensagem caso ocorra um erro ao carregar o quarto.
            die("Não foi possível carregar o quarto: " . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recupera e valida o número do quarto como um número inteiro.
        $numeroQuarto = filter_input(INPUT_POST, 'numero_quarto', FILTER_VALIDATE_INT);

        // Recupera e valida o andar como um número inteiro.
        $andarQuarto = filter_input(INPUT_POST, 'andar_quarto', FILTER_VALIDATE_INT);

        // Recupera a situação do quarto e remove espaços desnecessários.
        $situacaoQuarto = trim($_POST['situacao_quarto'] ?? '');

        // Recupera e valida o código do tipo de quarto.
        $tipoQuartoCodigo = filter_input(INPUT_POST, 'tipo_quarto_codigo', FILTER_VALIDATE_INT);

        // Recupera o ID enviado pelo formulário, caso seja uma edição.
        $idEnviado = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Atualiza o array com os valores enviados pelo formulário.
        // Isso permite manter os dados preenchidos caso ocorra um erro.
        $quarto = [
            'codigo_quarto' => $idEnviado,
            'numero_quarto' => $numeroQuarto,
            'andar_quarto' => $andarQuarto,
            'situacao_quarto' => $situacaoQuarto,
            'tipo_quarto_codigo' => $tipoQuartoCodigo
        ];

        // Define se o formulário está editando um quarto ou cadastrando um novo.
        $editar = (bool)$idEnviado;

        // Verifica se todos os campos foram preenchidos corretamente.
        if (!$numeroQuarto || !$andarQuarto || $situacaoQuarto === '' || !$tipoQuartoCodigo) {
            $Erro = 'Preencha todos os campos corretamente!';

        } else {

            try {
                // Se não existir ID, realiza o cadastro de um novo quarto.
                if (!$idEnviado) {

                    // Prepara o comando SQL para inserir os dados na tabela.
                    $stmt = $pdo->prepare("INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
                                        VALUES(:numero, :andar, :situacao, :tipo)");

                    // Executa o INSERT substituindo os parâmetros pelos valores informados.
                    $stmt->execute([
                        ':numero' => $numeroQuarto,
                        ':andar' => $andarQuarto,
                        ':situacao' => $situacaoQuarto,
                        ':tipo' => $tipoQuartoCodigo
                    ]);

                    // Redireciona para a listagem com uma mensagem de sucesso.
                    header('Location: listar_quartos.php?msg=salvo');
                    exit;

                } else {
                    // Se existir ID, prepara o comando SQL para atualizar o quarto.
                    $stmt = $pdo->prepare("UPDATE tbQuartos
                                        SET numero_quarto = :numero,
                                        andar_quarto = :andar,
                                        situacao_quarto = :situacao,
                                        tipo_quarto_codigo = :tipo
                                        WHERE codigo_quarto = :id"
                    );

                    // Executa o UPDATE com os novos valores e o ID do quarto.
                    $stmt->execute([
                        ':numero' => $numeroQuarto,
                        ':andar' => $andarQuarto,
                        ':situacao' => $situacaoQuarto,
                        ':tipo' => $tipoQuartoCodigo,
                        ':id' => $idEnviado
                    ]);

                    // Redireciona para a listagem com uma mensagem de sucesso.
                    header('Location: listar_quartos.php?msg=editado');
                    exit;
                }

            } catch (PDOException $e) {
                // Armazena a mensagem caso ocorra um erro ao cadastrar ou editar.
                $Erro = 'Falha ao salvar o quarto: ' . $e->getMessage();
            }
        }
    }

    // Busca os tipos de quarto cadastrados para preencher o campo de seleção.
    // Os tipos são organizados em ordem alfabética.
    $tipos = $pdo->query("SELECT codigo_tipo, nome_tipo FROM tbTipos_Quarto ORDER BY nome_tipo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quarto | Hotel</title>

    <!-- Importa o arquivo CSS utilizado para estilizar a página. -->
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- O título muda conforme a operação: cadastro ou edição. -->
    <h2><?= $editar ? 'Editar Quarto' : 'Cadastrar Novo Quarto' ?></h2>

    <!-- Exibe a mensagem de erro somente quando existir alguma. -->
    <?php if ($Erro): ?>
        <p class="Erro">
            <?= htmlspecialchars($Erro) ?>
        </p>
    <?php endif; ?>

    <!-- Formulário responsável pelo cadastro e pela edição dos quartos. -->
    <form method="POST" action="form_quarto.php">

        <!-- Na edição, envia o código do quarto em um campo oculto. -->
        <?php if ($editar && isset($quarto['codigo_quarto'])): ?>
            <input type="hidden" name="id" value="<?= (int)$quarto['codigo_quarto'] ?>">
        <?php endif; ?>

        <!-- Campo para informar o número do quarto. -->
        <label for="numero_quarto">Número do quarto:</label>
        <input type="number" id="numero_quarto" name="numero_quarto" min="1" value="<?= htmlspecialchars((string)$quarto['numero_quarto']) ?>">

        <!-- Campo para informar o andar do quarto. -->
        <label for="andar_quarto">Andar:</label>
        <input type="number" id="andar_quarto" name="andar_quarto" min="1" value="<?= htmlspecialchars((string)$quarto['andar_quarto']) ?>">

        <!-- Campo de seleção da situação atual do quarto. -->
        <label for="situacao_quarto">Situação:</label>
        <select name="situacao_quarto" id="situacao_quarto">
            <option value="">Selecione a situação:</option>
            <option value="Disponivel" <?= $quarto['situacao_quarto'] == 'Disponivel' ? 'selected' : '' ?>>Disponível</option>
            <option value="Ocupado" <?= $quarto['situacao_quarto'] == 'Ocupado' ? 'selected' : '' ?>>Ocupado</option>
        </select>

        <!-- Campo de seleção do tipo de quarto. -->
        <label for="tipo_quarto_codigo">Tipo de quarto:</label>
        <select name="tipo_quarto_codigo" id="tipo_quarto_codigo">
            <option value="">Selecione o tipo de quarto:</option>

            <!-- Percorre os tipos encontrados no banco e cria uma opção para cada um. -->
            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= (int)$tipo['codigo_tipo'] ?>"
                    <?= $quarto['tipo_quarto_codigo'] == $tipo['codigo_tipo'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo['nome_tipo']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- O texto do botão muda conforme o cadastro ou a edição. -->
        <button type="submit">
            <?= $editar ? 'Salvar alterações' : 'Cadastrar quarto' ?>
        </button>

    </form>

    <!-- Link para voltar à listagem dos quartos. -->
    <p>
        <a class="btn btn-inicio" href="listar_quartos.php">Acessar lista de quartos</a>
    </p>

</body>
</html>
