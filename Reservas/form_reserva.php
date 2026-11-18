<?php
    require_once '../conexao.php';

    $idReserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    $reserva = [
        'hospede_codigo' => '',
        'data_entrada' => 'd-m-y',
        'data_saida' => 'd-m-y',
        'valor_total' => ''
    ];

    $mensagemErro = '';
    $mensagemSucesso = '';
    $data_atual = date('Y-m-d');
    $editar = false;

    // Busca os dados da reserva caso exista um ID.
    if ($idReserva) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbReservas WHERE codigo_reserva = :id");
            $stmt->execute([':id' => $idReserva]);

            $reserva = $stmt->fetch();

            if (!$reserva) {
                header('Location: listar_reservas.php');
                exit;
            }

            $editar = true;

        } catch (PDOException $e) {
            die("Não foi possível carregar a reserva: " . $e->getMessage());
        }
    }

    // Verifica se o formulário foi enviado.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hospedeCodigo = filter_input(INPUT_POST, 'hospede_codigo', FILTER_VALIDATE_INT);
        $dataEntrada = trim($_POST['data_entrada'] ?? '');
        $dataSaida = trim($_POST['data_saida'] ?? '');
        $valorTotal = trim($_POST['valor_total'] ?? '');
        $idEnviado = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        // Verifica se todos os campos foram preenchidos.
        if (!$hospedeCodigo || $dataEntrada === '' || $dataSaida === '' || $valorTotal === '') {
            $mensagemErro = 'Preencha todos os campos corretamente!';

            $reserva = [
                'codigo_reserva' => $idEnviado,
                'hospede_codigo' => $hospedeCodigo,
                'data_entrada' => $dataEntrada,
                'data_saida' => $dataSaida,
                'valor_total' => $valorTotal
            ];

            $editar = (bool) $idEnviado;

        } elseif ($dataSaida <= $dataEntrada || $dataEntrada < $data_atual || $dataSaida < $data_atual || !is_numeric($valorTotal) || $valorTotal < 0) {
            $mensagemErro = 'Verifique as datas e o valor total da reserva!';

            $reserva = [
                'codigo_reserva' => $idEnviado,
                'hospede_codigo' => $hospedeCodigo,
                'data_entrada' => $dataEntrada,
                'data_saida' => $dataSaida,
                'valor_total' => $valorTotal
            ];

            $editar = (bool) $idEnviado;

        } else {
            try {
                if (!$idEnviado) {
                    // Prepara a consulta para cadastrar uma nova reserva.
                    $stmt = $pdo->prepare("INSERT INTO tbReservas(hospede_codigo, data_entrada, data_saida, valor_total)
                                           VALUES(:hospede, :entrada, :saida, :valor)");

                    $stmt->execute([
                        ':hospede' => $hospedeCodigo,
                        ':entrada' => $dataEntrada,
                        ':saida' => $dataSaida,
                        ':valor' => $valorTotal
                    ]);

                    $mensagemSucesso = 'Reserva cadastrada com sucesso!';
                } else {
                    // Prepara a consulta para atualizar uma reserva.
                    $stmt = $pdo->prepare("UPDATE tbReservas
                                           SET hospede_codigo = :hospede, 
                                           data_entrada = :entrada,
                                           data_saida = :saida,
                                           valor_total = :valor
                                           WHERE codigo_reserva = :id"
                    );

                    $stmt->execute([
                        ':hospede' => $hospedeCodigo,
                        ':entrada' => $dataEntrada,
                        ':saida' => $dataSaida,
                        ':valor' => $valorTotal,
                        ':id' => $idEnviado
                    ]);

                    $mensagemSucesso = 'Reserva atualizada com sucesso!';
                    $editar = true;
                }

                $reserva = [
                    'codigo_reserva' => $idEnviado,
                    'hospede_codigo' => $hospedeCodigo,
                    'data_entrada' => $dataEntrada,
                    'data_saida' => $dataSaida,
                    'valor_total' => $valorTotal
                ];

            } catch (PDOException $e) {
                $mensagemErro = 'Falha ao salvar a reserva: ' . $e->getMessage();
            }
        }
    }

    // Busca os hóspedes para preencher o SELECT.
    $hospedes = $pdo->query( "SELECT codigo_hospede, nome_hospede FROM tbHospedes ORDER BY nome_hospede")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva | Hotel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php if ($mensagemErro): ?>
        <p class="mensagemErro">
            <?= htmlspecialchars($mensagemErro) ?>
        </p>
    <?php endif; ?>

    <?php if ($mensagemSucesso): ?>
        <p class="mensagemSucesso">
            <?= htmlspecialchars($mensagemSucesso) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="form_reserva.php">

        <?php if ($editar && isset($reserva['codigo_reserva'])): ?>
            <input type="hidden" name="id" value="<?= (int)$reserva['codigo_reserva'] ?>">
        <?php endif; ?>

        <label for="hospede_codigo">Hóspede:</label>
        <select name="hospede_codigo" id="hospede_codigo">
            <option value="">Selecione o responsável pela reserva:</option>

            <?php foreach ($hospedes as $hospede): ?>
                <option value="<?= (int)$hospede['codigo_hospede'] ?>"
                    <?= $reserva['hospede_codigo'] == $hospede['codigo_hospede'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($hospede['nome_hospede']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="data_entrada">Data de entrada:</label>
        <input type="date" id="data_entrada" name="data_entrada" value="<?= htmlspecialchars($reserva['data_entrada']) ?>">

        <label for="data_saida">Data de saída:</label>
        <input type="date" id="data_saida" name="data_saida" value="<?= htmlspecialchars($reserva['data_saida']) ?>">

        <label for="valor_total">Valor total:</label>
        <input type="number" step="0.01" min="0" id="valor_total" name="valor_total" value="<?= htmlspecialchars((string)$reserva['valor_total']) ?>">

        <button type="submit">
            <?= $editar ? 'Salvar alterações' : 'Cadastrar reserva' ?>
        </button>

    </form>

    <p>
        <a href="listar_reservas.php">Acessar lista de reservas</a>
    </p>

</body>
</html>