<?php
    require_once '../conexao.php';

    $idQuarto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    $quarto = [
        'numero_quarto' => '',
        'andar_quarto' => '',
        'situacao_quarto' => '',
        'tipo_quarto_codigo' => ''
    ];
    $Erro = '';
    $Sucesso = '';
    $editar = false;

    if ($idQuarto) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tbQuartos WHERE codigo_quarto = :id");
            $stmt->execute([':id' => $idQuarto]);

            $quarto = $stmt->fetch();

            if (!$quarto) {
                header('Location: listar_quartos.php');
                exit;
            }

            $editar = true;

        } catch (PDOException $e) {
            die("Não foi possível carregar o quarto: " . $e->getMessage());
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $numeroQuarto = filter_input(INPUT_POST, 'numero_quarto', FILTER_VALIDATE_INT);
        $andarQuarto = filter_input(INPUT_POST, 'andar_quarto', FILTER_VALIDATE_INT);
        $situacaoQuarto = trim($_POST['situacao_quarto'] ?? '');
        $tipoQuartoCodigo = filter_input(INPUT_POST, 'tipo_quarto_codigo', FILTER_VALIDATE_INT);
        $idEnviado = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if (!$numeroQuarto || !$andarQuarto || $situacaoQuarto === '' || !$tipoQuartoCodigo) {
            $Erro = 'Preencha todos os campos corretamente!';

            $quarto = [
                'codigo_quarto' => $idEnviado,
                'numero_quarto' => $numeroQuarto,
                'andar_quarto' => $andarQuarto,
                'situacao_quarto' => $situacaoQuarto,
                'tipo_quarto_codigo' => $tipoQuartoCodigo
            ];

            $editar = (bool) $idEnviado;

        } else {
            
            try {
                if (!$idEnviado) {
                    $stmt = $pdo->prepare("INSERT INTO tbQuartos(numero_quarto, andar_quarto, situacao_quarto, tipo_quarto_codigo)
                                           VALUES(:numero, :andar, :situacao, :tipo)");

                    $stmt->execute([
                        ':numero' => $numeroQuarto,
                        ':andar' => $andarQuarto,
                        ':situacao' => $situacaoQuarto,
                        ':tipo' => $tipoQuartoCodigo
                    ]);

                    $Sucesso = 'Quarto cadastrado com sucesso!';
                } else {
                    $stmt = $pdo->prepare("UPDATE tbQuartos
                                           SET numero_quarto = :numero,
                                           andar_quarto = :andar,
                                           situacao_quarto = :situacao,
                                           tipo_quarto_codigo = :tipo
                                           WHERE codigo_quarto = :id"
                    );

                    $stmt->execute([
                        ':numero' => $numeroQuarto,
                        ':andar' => $andarQuarto,
                        ':situacao' => $situacaoQuarto,
                        ':tipo' => $tipoQuartoCodigo,
                        ':id' => $idEnviado
                    ]);

                    $Sucesso = 'Quarto atualizado com sucesso!';
                    $editar = true;
                }

                $quarto = [
                    'codigo_quarto' => $idEnviado,
                    'numero_quarto' => $numeroQuarto,
                    'andar_quarto' => $andarQuarto,
                    'situacao_quarto' => $situacaoQuarto,
                    'tipo_quarto_codigo' => $tipoQuartoCodigo
                ];

            } catch (PDOException $e) {
                $Erro = 'Falha ao salvar o quarto: ' . $e->getMessage();
            }
        }
    }

    $tipos = $pdo->query("SELECT codigo_tipo, nome_tipo FROM tbTipos_Quarto ORDER BY nome_tipo")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quarto | Hotel</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <?php if ($Erro): ?>
        <p class="Erro">
            <?= htmalspecialchars($Erro) ?>
        </p>
    <?php endif; ?>

    <?php if ($Sucesso): ?>
        <p class="Sucesso">
            <?= htmlspecialchars($Sucesso) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="form_quarto.php">

        <?php if ($editar && isset($quarto['codigo_quarto'])): ?>
            <input type="hidden" name="id" value="<?= (int)$quarto['codigo_quarto'] ?>">
        <?php endif; ?>

        <label for="numero_quarto">Número do quarto:</label>
        <input type="number" id="numero_quarto" name="numero_quarto" min="1" value="<?= htmlspecialchars((string)$quarto['numero_quarto']) ?>">

        <label for="andar_quarto">Andar:</label>
        <input type="number" id="andar_quarto" name="andar_quarto" min="1" value="<?= htmlspecialchars((string)$quarto['andar_quarto']) ?>">

        <label for="situacao_quarto">Situação:</label>
        <select name="situacao_quarto" id="situacao_quarto">
            <option value="">Selecione a situação:</option>
            <option value="Disponivel" <?= $quarto['situacao_quarto'] == 'Disponivel' ? 'selected' : '' ?>>Disponível</option>
            <option value="Ocupado" <?= $quarto['situacao_quarto'] == 'Ocupado' ? 'selected' : '' ?>>Ocupado</option>
        </select>

        <label for="tipo_quarto_codigo">Tipo de quarto:</label>
        <select name="tipo_quarto_codigo" id="tipo_quarto_codigo">
            <option value="">Selecione o tipo de quarto:</option>

            <?php foreach ($tipos as $tipo): ?>
                <option value="<?= (int)$tipo['codigo_tipo'] ?>"
                    <?= $quarto['tipo_quarto_codigo'] == $tipo['codigo_tipo'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tipo['nome_tipo']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">
            <?= $editar ? 'Salvar alterações' : 'Cadastrar quarto' ?>
        </button>

    </form>

    <p>
        <a href="listar_quartos.php">Acessar lista de quartos</a>
    </p>

</body>
</html>
