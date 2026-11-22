<?php
// Identifica o caminho da pasta principal do projeto.
    $baseUrl = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

    $pastasInternas = [
    'funcionarios',
    'hospedes',
    'quartos',
    'reservas',
    'relatório',
    'tipoQuartos'
];

    if (in_array(basename($baseUrl), $pastasInternas, true)) {
        $baseUrl = dirname($baseUrl);
    }

    $baseUrl = rtrim($baseUrl, '/');

    // Evita que o caminho fique vazio quando o projeto estiver na raiz do servidor.
    if ($baseUrl === '') {
        $baseUrl = '';
    }
?>

<!-- Cabeçalho compartilhado por todas as páginas do sistema. -->
<header class="cabecalho">
    <div class="cabecalho-conteudo">
        <h1>Pousada Encanto de Natal</h1>
        <a href="<?= htmlspecialchars($baseUrl . '/index.php') ?>">Início</a>
    </div>
</header>
