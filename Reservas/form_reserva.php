
<?php
    require_once '../conexao.php';

    $idReserva = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    $reserva = [
        'hospede_codigo' => '',
        'nome_hospede' => '',
        'data_entrada' => '',
        'data_saida' => '',
        'valor_total' => ''
    ];

    $Erro = '';
    $Sucesso = '';
    $data_atual = date('Y-m-d');
    $editar = false;

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idEnviado = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $dataEntrada = trim($_POST['data_entrada'] ?? '');
        $dataSaida = trim($_POST['data_saida'] ?? '');
        $valorTotal = trim($_POST['valor_total'] ?? '');

        $editar = (bool) $idEnviado;

        if ($editar) {
            $hospedeCodigo = filter_input(INPUT_POST, 'hospede_codigo', FILTER_VALIDATE_INT);
            $nomeHospede = '';
        } else {
            $nomeHospede = trim($_POST['nome_hospede'] ?? '');
            $hospedeCodigo = null;
        }

        if ((!$editar && $nomeHospede === '') || ($editar && !$hospedeCodigo) || $dataEntrada === '' || $dataSaida === '' || $valorTotal === '') {
            $Erro = 'Preencha todos os campos corretamente!';
        } else {
            try {
                if (!$editar) {
                    $busca = $pdo->prepare("SELECT codigo_hospede FROM tbHospedes WHERE nome_hospede = :nome");
                    $busca->execute([':nome' => $nomeHospede]);
                    $hospede = $busca->fetch();

                    if (!$hospede) {
                        $Erro = 'Hóspede não encontrado! Cadastre o hóspede primeiro.';
                    } else {
                        $hospedeCodigo = $hospede['codigo_hospede'];
                    }
                }

                if (!$Erro) {
                    if ($dataSaida <= $dataEntrada || $dataEntrada < $data_atual || $dataSaida < $data_atual || !is_numeric($valorTotal) || $valorTotal < 0) {
                        $Erro = 'Verifique as datas e o valor total da reserva!';
                    } else {
                        if (!$idEnviado) {
                            $stmt = $pdo->prepare("INSERT INTO tbReservas(hospede_codigo, data_entrada, data_saida, valor_total) VALUES(:hospede, :entrada, :saida, :valor)");

                            $stmt->execute([':hospede' => $hospedeCodigo, ':entrada' => $dataEntrada, ':saida' => $dataSaida, ':valor' => $valorTotal]);

                            $Sucesso = 'Reserva cadastrada com sucesso!';
                        } else {
                            $stmt = $pdo->prepare("UPDATE tbReservas SET hospede_codigo = :hospede, data_entrada = :entrada, data_saida = :saida, valor_total = :valor WHERE codigo_reserva = :id");

                            $stmt->execute([':hospede' => $hospedeCodigo, ':entrada' => $dataEntrada, ':saida' => $dataSaida, ':valor' => $valorTotal, ':id' => $idEnviado]);

                            $Sucesso = 'Reserva atualizada com sucesso!';
                        }
                    }
                }

            } catch (PDOException $e) {
                $Erro = 'Falha ao salvar a reserva: ' . $e->getMessage();
            }
        }

        $reserva = ['codigo_reserva' => $idEnviado, 'hospede_codigo' => $hospedeCodigo, 'nome_hospede' => $nomeHospede, 'data_entrada' => $dataEntrada, 'data_saida' => $dataSaida, 'valor_total' => $valorTotal];
    }

    $hospedes = [];

    if ($editar) {
        $hospedes = $pdo->query("SELECT codigo_hospede, nome_hospede FROM tbHospedes ORDER BY nome_hospede")->fetchAll();
    }
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Reserva | Hotel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php if ($Erro): ?>
        <p class="Erro">
            <?= htmlspecialchars($Erro) ?>
        </p>
    <?php endif; ?>

    <?php if ($Sucesso): ?>
        <p class="Sucesso">
            <?= htmlspecialchars($Sucesso) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="form_reserva.php">

        <?php if ($editar && isset($reserva['codigo_reserva'])): ?>
            <input type="hidden" name="id" value="<?= (int)$reserva['codigo_reserva'] ?>">
        <?php endif; ?>

        <label for="hospede_codigo">Hóspede:</label>

        <?php if ($editar): ?>

            <select name="hospede_codigo" id="hospede_codigo">
                <option value="">Selecione o hóspede:</option>

                <?php foreach ($hospedes as $hospede): ?>
                    <option value="<?= (int)$hospede['codigo_hospede'] ?>" <?= $reserva['hospede_codigo'] == $hospede['codigo_hospede'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($hospede['nome_hospede']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

        <?php else: ?>

            <input type="text" id="nome_hospede" name="nome_hospede" placeholder="Digite o nome do hóspede" value="<?= htmlspecialchars($reserva['nome_hospede']) ?>">

        <?php endif; ?>

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
