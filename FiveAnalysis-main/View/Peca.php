<?php
// Peca.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$peca_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($peca_id <= 0) {
    die("Peça inválida.");
}

// Consulta peça
$sql = "SELECT p.*, c.nome as categoria_nome, m.nome as marca_nome
        FROM pecas p
        LEFT JOIN categorias c ON c.id = p.categoria_id
        LEFT JOIN marcas m ON m.id = p.marca_id
        WHERE p.id = :id AND p.ativo = TRUE";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $peca_id]);
$peca = $stmt->fetch();

if (!$peca) {
    die("Peça não encontrada.");
}

// Converte especificações JSON
$especificacoes = json_decode($peca['especificacoes'] ?? "{}", true);

// Busca preços da peça em lojas
$sql_lojas = "SELECT l.nome as loja_nome, lp.preco_centavos, lp.link
              FROM loja_peca lp
              JOIN lojas l ON l.id = lp.loja_id
              WHERE lp.peca_id = :id
              ORDER BY lp.preco_centavos ASC";
$stmt = $pdo->prepare($sql_lojas);
$stmt->execute([':id' => $peca_id]);
$lojas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($peca['nome']) ?> - Detalhes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <a href="Catalogo.php" class="text-indigo-600">&larr; Voltar ao catálogo</a>

        <div class="bg-white p-6 mt-4 rounded shadow">
            <h1 class="text-2xl font-bold"><?= htmlspecialchars($peca['nome']) ?></h1>
            <p class="text-gray-600"><?= htmlspecialchars($peca['modelo']) ?> - <?= htmlspecialchars($peca['marca_nome'] ?? "Sem marca") ?></p>
            <p class="mt-1 text-sm text-gray-500">Categoria: <?= htmlspecialchars($peca['categoria_nome'] ?? "N/D") ?></p>

            <!-- Especificações -->
            <div class="mt-4">
                <h2 class="font-semibold">Especificações</h2>
                <?php if (empty($especificacoes)): ?>
                    <p class="text-gray-600">Nenhuma especificação cadastrada.</p>
                <?php else: ?>
                    <ul class="list-disc pl-5 text-gray-700">
                        <?php foreach ($especificacoes as $chave => $valor): ?>
                            <li><strong><?= htmlspecialchars($chave) ?>:</strong> <?= htmlspecialchars($valor) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <!-- Preços nas lojas -->
            <div class="mt-6">
                <h2 class="font-semibold">Preços disponíveis</h2>
                <?php if (empty($lojas)): ?>
                    <p class="text-gray-600">Nenhuma loja cadastrada para esta peça.</p>
                <?php else: ?>
                    <table class="w-full mt-2 border">
                        <thead>
                            <tr class="bg-gray-200 text-left">
                                <th class="p-2">Loja</th>
                                <th class="p-2">Preço</th>
                                <th class="p-2">Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lojas as $l): ?>
                                <tr class="border-b">
                                    <td class="p-2"><?= htmlspecialchars($l['loja_nome']) ?></td>
                                    <td class="p-2 text-green-600 font-semibold">R$ <?= number_format($l['preco_centavos'] / 100, 2, ',', '.') ?></td>
                                    <td class="p-2">
                                        <a href="<?= htmlspecialchars($l['link']) ?>" target="_blank" class="text-indigo-600 hover:underline">Visitar loja</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <!-- Botões de ação -->
            <div class="mt-6 flex gap-3">
                <?php if (isLogged()): ?>
                    <form method="post" action="Carrinho.php">
                        <input type="hidden" name="peca_id" value="<?= $peca['id'] ?>">
                        <button class="bg-green-600 text-white px-4 py-2 rounded">Adicionar ao carrinho</button>
                    </form>
                    <form method="post" action="Montagem.php">
                        <input type="hidden" name="peca_id" value="<?= $peca['id'] ?>">
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded">Adicionar à montagem</button>
                    </form>
                <?php else: ?>
                    <p class="text-gray-600">Faça login para adicionar ao carrinho ou montagem.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
