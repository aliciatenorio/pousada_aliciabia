<?php
    // Inclui o arquivo responsável pela conexão com o banco de dados.
    require_once '../conexao.php';

    // Recupera o ID da reserva enviado pela URL e valida se é um número inteiro.
    $idReserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    // Define os valores iniciais dos campos do formulário.
    // Eles ficam vazios quando uma nova reserva será cadastrada.
    $reserva = [
        'hospede_codigo' => '',
        'nome_hospede' => '',
        'funcionario_codigo' => '',
        'data_entrada' => '',
        'data_saida' => '',
        'valor_total' => ''
    ];

    // Variável que armazena possíveis mensagens de erro.
    $Erro = '';

    // Obtém a data atual no formato utilizado pelo banco de dados (AAAA-MM-DD).
    $data_atual = date('Y-m-d');

    // Indica se o formulário está sendo utilizado para editar uma reserva.
    $editar = false;

    // Se existir um ID válido na URL, significa que uma reserva será editada.
    if ($idReserva) {
        try {
            // Prepara a consulta para buscar a reserva pelo seu código.
            $stmt = $pdo->prepare("SELECT * FROM tbReservas WHERE codigo_reserva = :id");

            // Executa a consulta utilizando o ID recebido.
            $stmt->execute([':id' => $idReserva]);

            // Recupera os dados da reserva encontrada.
            $reserva = $stmt->fetch();

            // Se nenhuma reserva for encontrada, retorna para a listagem.
            if (!$reserva) {
                header('Location: listar_reservas.php');
                exit;
            }

            // Indica que o formulário está no modo de edição.
            $editar = true;

        } catch (PDOException $e) {
            // Exibe uma mensagem caso ocorra um erro ao carregar a reserva.
            die("Não foi possível carregar a reserva: " . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado pelo método POST.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recupera o ID enviado pelo formulário, caso seja uma edição.
        $idEnviado = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Recupera as datas e o valor total, removendo espaços desnecessários.
        $dataEntrada = trim($_POST['data_entrada'] ?? '');
        $dataSaida = trim($_POST['data_saida'] ?? '');
        $valorTotal = trim($_POST['valor_total'] ?? '');

        // Recupera e valida o código do funcionário responsável.
        $funcionarioCodigo = filter_input(INPUT_POST, 'funcionario_codigo', FILTER_VALIDATE_INT);

        // Verifica se o formulário está sendo utilizado para editar ou cadastrar.
        $editar = (bool)$idEnviado;

        // Se for uma edição, recupera o código do hóspede selecionado.
        if ($editar) {
            $hospedeCodigo = filter_input(INPUT_POST, 'hospede_codigo', FILTER_VALIDATE_INT);
            $nomeHospede = '';

        // Se for um cadastro, recupera o nome do hóspede digitado.
        } else {
            $nomeHospede = trim($_POST['nome_hospede'] ?? '');
            $hospedeCodigo = null;
        }

        // Atualiza o array com os valores enviados pelo formulário.
        // Isso permite manter os dados preenchidos caso ocorra um erro.
        $reserva = [
            'codigo_reserva' => $idEnviado,
            'hospede_codigo' => $hospedeCodigo,
            'nome_hospede' => $nomeHospede,
            'funcionario_codigo' => $funcionarioCodigo,
            'data_entrada' => $dataEntrada,
            'data_saida' => $dataSaida,
            'valor_total' => $valorTotal
        ];

        // Verifica se os campos obrigatórios foram preenchidos.
        // No cadastro, exige o nome do hóspede; na edição, exige seu código.
        if ((!$editar && $nomeHospede === '') || ($editar && !$hospedeCodigo) || !$funcionarioCodigo || $dataEntrada === '' || $dataSaida === '' || $valorTotal === '') {
            $Erro = 'Preencha todos os campos corretamente!';
        } else {
            try {
                // Se for um cadastro, procura o hóspede pelo nome informado.
                if (!$editar) {
                    $busca = $pdo->prepare("SELECT codigo_hospede FROM tbHospedes WHERE nome_hospede = :nome");

                    // Executa a busca utilizando o nome digitado.
                    $busca->execute([':nome' => $nomeHospede]);

                    // Recupera os dados do hóspede encontrado.
                    $hospede = $busca->fetch();

                    // Se o hóspede não existir, informa que ele precisa ser cadastrado primeiro.
                    if (!$hospede) {
                        $Erro = 'Hóspede não encontrado! Cadastre o hóspede primeiro.';
                    } else {
                        // Armazena o código do hóspede encontrado.
                        $hospedeCodigo = $hospede['codigo_hospede'];

                        // Atualiza o array com o código do hóspede.
                        $reserva['hospede_codigo'] = $hospedeCodigo;
                    }
                }

                // Continua o processamento somente se não houver erro na busca do hóspede.
                if (!$Erro) {

                    // Verifica se a data de saída é posterior à entrada,
                    // se as datas não são anteriores à data atual e se o valor é numérico e não negativo.
                    if ($dataSaida <= $dataEntrada || $dataEntrada < $data_atual || $dataSaida < $data_atual || !is_numeric($valorTotal) || $valorTotal < 0) {
                        $Erro = 'Verifique as datas e o valor total da reserva!';

                    } else {

                        // Se não existir ID, realiza o cadastro de uma nova reserva.
                        if (!$editar) {

                            // Prepara o comando SQL para inserir os dados da reserva.
                            $stmt = $pdo->prepare("INSERT INTO tbReservas(hospede_codigo, funcionario_codigo, data_entrada, data_saida, valor_total) VALUES(:hospede, :funcionario, :entrada, :saida, :valor)");

                            // Executa o INSERT substituindo os parâmetros pelos valores informados.
                            $stmt->execute([
                                ':hospede' => $hospedeCodigo,
                                ':funcionario' => $funcionarioCodigo,
                                ':entrada' => $dataEntrada,
                                ':saida' => $dataSaida,
                                ':valor' => $valorTotal
                            ]);

                            // Redireciona para a listagem com uma mensagem de sucesso.
                            header('Location: listar_reservas.php?msg=salvo');
                            exit;

                        } else {
                            // Se existir ID, prepara o comando SQL para atualizar a reserva.
                            $stmt = $pdo->prepare("UPDATE tbReservas SET hospede_codigo = :hospede, funcionario_codigo = :funcionario, data_entrada = :entrada, data_saida = :saida, valor_total = :valor WHERE codigo_reserva = :id");

                            // Executa o UPDATE com os novos valores e o código da reserva.
                            $stmt->execute([
                                ':hospede' => $hospedeCodigo,
                                ':funcionario' => $funcionarioCodigo,
                                ':entrada' => $dataEntrada,
                                ':saida' => $dataSaida,
                                ':valor' => $valorTotal,
                                ':id' => $idEnviado
                            ]);

                            // Redireciona para a listagem com uma mensagem de sucesso.
                            header('Location: listar_reservas.php?msg=editado');
                            exit;
                        }
                    }
                }

            } catch (PDOException $e) {
                // Armazena a mensagem caso ocorra um erro ao cadastrar ou editar a reserva.
                $Erro = 'Falha ao salvar a reserva: ' . $e->getMessage();
            }
        }
    }

    // Inicializa o array de hóspedes que será utilizado na seleção durante a edição.
    $hospedes = [];

    // Busca os funcionários cadastrados e organiza seus nomes em ordem alfabética.
    $funcionarios = $pdo->query("SELECT codigo_funcionario, nome_funcionario FROM tbFuncionarios ORDER BY nome_funcionario")->fetchAll();

    // Se for uma edição, busca todos os hóspedes para preencher o campo de seleção.
    if ($editar) {
        $hospedes = $pdo->query("SELECT codigo_hospede, nome_hospede FROM tbHospedes ORDER BY nome_hospede")->fetchAll();
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva | Hotel</title>

    <!-- Importa o arquivo CSS responsável pela aparência da página. -->
    <link rel="stylesheet" href="../style.css">
</head>

<!-- Aplica a classe utilizada para estilizar as páginas internas do sistema. -->
<body class="paginas-internas">
    <?php require_once __DIR__ . '/../includes/cabecalho.php'; ?>


    <!-- O título muda conforme o cadastro ou a edição da reserva. -->
    <h2><?= $editar ? 'Editar Reserva' : 'Cadastrar Nova Reserva' ?></h2>

    <!-- Exibe a mensagem de erro somente quando existir alguma. -->
    <?php if ($Erro): ?>
        <p class="Erro"><?= htmlspecialchars($Erro) ?></p>
    <?php endif; ?>

    <!-- Formulário responsável pelo cadastro e pela edição das reservas. -->
    <!-- Na edição, mantém o ID na URL para identificar a reserva. -->
    <form method="POST" action="form_reserva.php<?= $editar ? '?id=' . (int)$reserva['codigo_reserva'] : '' ?>">

        <!-- Na edição, envia o código da reserva em um campo oculto. -->
        <?php if ($editar && isset($reserva['codigo_reserva'])): ?>
            <input type="hidden" name="id" value="<?= (int)$reserva['codigo_reserva'] ?>">
        <?php endif; ?>

        <!-- Campo para selecionar ou informar o hóspede da reserva. -->
        <label for="hospede_codigo">Hóspede:</label>

        <!-- Durante a edição, permite selecionar um hóspede já cadastrado. -->
        <?php if ($editar): ?>

            <select name="hospede_codigo" id="hospede_codigo">
                <option value="">Selecione o hóspede:</option>

                <!-- Percorre os hóspedes cadastrados e cria uma opção para cada um. -->
                <?php foreach ($hospedes as $hospede): ?>
                    <option value="<?= (int)$hospede['codigo_hospede'] ?>" <?= $reserva['hospede_codigo'] == $hospede['codigo_hospede'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($hospede['nome_hospede']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <!-- Durante o cadastro, permite digitar o nome do hóspede. -->
        <?php else: ?>

            <input type="text" id="nome_hospede" name="nome_hospede" placeholder="Digite o nome do hóspede" value="<?= htmlspecialchars($reserva['nome_hospede']) ?>">

        <?php endif; ?>

        <!-- Campo para selecionar o funcionário responsável pela reserva. -->
        <label for="funcionario_codigo">Funcionário responsável:</label>
        <select name="funcionario_codigo" id="funcionario_codigo">
            <option value="">Selecione o funcionário:</option>

            <!-- Percorre os funcionários cadastrados e cria uma opção para cada um. -->
            <?php foreach ($funcionarios as $funcionario): ?>
                <option value="<?= (int)$funcionario['codigo_funcionario'] ?>" <?= $reserva['funcionario_codigo'] == $funcionario['codigo_funcionario'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($funcionario['nome_funcionario']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Campo para informar a data de entrada. -->
        <label for="data_entrada">Data de entrada:</label>
        <input type="date" id="data_entrada" name="data_entrada" value="<?= htmlspecialchars($reserva['data_entrada']) ?>">

        <!-- Campo para informar a data de saída. -->
        <label for="data_saida">Data de saída:</label>
        <input type="date" id="data_saida" name="data_saida" value="<?= htmlspecialchars($reserva['data_saida']) ?>">

        <!-- Campo para informar o valor total da reserva. -->
        <label for="valor_total">Valor total:</label>
        <input type="number" step="0.01" min="0" id="valor_total" name="valor_total" value="<?= htmlspecialchars((string)$reserva['valor_total']) ?>">

        <!-- O texto do botão muda conforme o cadastro ou a edição. -->
        <button type="submit">
            <?= $editar ? 'Salvar alterações' : 'Cadastrar reserva' ?>
        </button>

    </form>

    <!-- Link para acessar novamente a listagem de reservas. -->
    <p>
        <a class="btn btn-inicio" href="listar_reservas.php">Acessar lista de reservas</a>
    </p>

    <?php require_once __DIR__ . '/../includes/rodape.php'; ?>
</body>
</html>
