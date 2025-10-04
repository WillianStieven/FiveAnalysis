<?php
// Catalogo.php
require_once __DIR__ . '/db.php'; // conexão PDO
require_once __DIR__ . '/auth.php';

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Pega categoria selecionada
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;

// Lista categorias
$stmt = $pdo->query("SELECT id, nome FROM categorias ORDER BY nome");
$categorias = $stmt->fetchAll();

// Lista peças da categoria selecionada
$pecas = [];
if ($categoria_id) {
    $sql = "SELECT p.id, p.nome, p.modelo,
                   (SELECT MIN(lp.preco_centavos) FROM loja_peca lp WHERE lp.peca_id = p.id) as menor_preco
            FROM pecas p
            WHERE p.categoria_id = :cat AND p.ativo = TRUE
            ORDER BY p.nome";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':cat' => $categoria_id]);
    $pecas = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Peças</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Catálogo de Peças</h1>

        <!-- Lista de categorias -->
        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Categorias</h2>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($categorias as $c): ?>
                    <a href="Catalogo.php?categoria=<?= $c['id'] ?>"
                       class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        <?= htmlspecialchars($c['nome']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lista de peças -->
        <?php if ($categoria_id): ?>
            <h2 class="text-lg font-semibold mb-3">Peças da categoria selecionada</h2>
            <?php if (empty($pecas)): ?>
                <p class="text-gray-600">Nenhuma peça encontrada nessa categoria.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach ($pecas as $p): ?>
                        <div class="bg-white p-4 rounded shadow">
                            <h3 class="font-bold"><?= htmlspecialchars($p['nome']) ?></h3>
                            <p class="text-sm text-gray-600"><?= htmlspecialchars($p['modelo']) ?></p>
                            <p class="mt-2 text-indigo-600 font-semibold">
                                <?php if ($p['menor_preco']): ?>
                                    R$ <?= number_format($p['menor_preco'] / 100, 2, ',', '.') ?>
                                <?php else: ?>
                                    Preço não disponível
                                <?php endif; ?>
                            </p>
                            <a href="Peca.php?id=<?= $p['id'] ?>"
                               class="mt-3 inline-block bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                Ver detalhes
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
