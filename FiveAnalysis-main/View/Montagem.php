<!DOCTYPE html>
<?php
include '../Model/conexao.php';

// Carrega produtos ativos com categoria e marca
$stmt = $conexao->prepare("SELECT p.*, c.nome AS categoria_nome, c.id AS categoria_id, m.nome AS marca_nome FROM produtos p LEFT JOIN categorias c ON p.categoria_id = c.id LEFT JOIN marcas m ON p.marca_id = m.id WHERE p.ativo = true ORDER BY p.data_criacao DESC");
$stmt->execute();
$produtos = $stmt->fetchAll(); 


// Mapa de nome da categoria -> slug usado nas seções
$categoriaSlug = [
  'Processador' => 'processador',
  'Placa Mãe' => 'placa-mae',
  'Memória RAM' => 'memoria-ram',
  'Placa de Vídeo' => 'placa-video',
  'Armazenamento' => 'armazenamento',
  'Gabinete' => 'gabinete',
  'Fonte' => 'fonte',
  'Periféricos' => 'perifericos'
];

// Agrupa produtos por slug
$bySlug = [];
foreach ($produtos as $p) {
  $nomeCat = $p['categoria_nome'] ?? '';
  if (!isset($categoriaSlug[$nomeCat])) continue;
  $slug = $categoriaSlug[$nomeCat];
  if (!isset($bySlug[$slug])) $bySlug[$slug] = [];
  $bySlug[$slug][] = $p;
}

function attr($key, $value) {
  if ($value === null || $value === '') return '';
  return ' ' . $key . '="' . htmlspecialchars($value) . '"';
}

function extrairAtributosCompat($produto) {
  $attrs = [];
  $espec = $produto['especificacoes'] ? json_decode($produto['especificacoes'], true) : [];
  $comp = $produto['compatibilidade'] ? json_decode($produto['compatibilidade'], true) : [];

  $categoria = $produto['categoria_nome'] ?? '';
  $marca = $produto['marca_nome'] ?? '';
  if ($marca) $attrs['data-brand'] = $marca;

  if ($categoria === 'Processador') {
    $socket = $espec['socket'] ?? $comp['socket'] ?? null;
    if ($socket) $attrs['data-socket'] = $socket;
  }

  if ($categoria === 'Placa Mãe') {
    $socket = $espec['socket'] ?? $comp['socket'] ?? null;
    if ($socket) $attrs['data-socket'] = $socket;
    $ramTipo = $comp['memoria_tipo'] ?? $espec['memoria_tipo'] ?? null;
    if ($ramTipo) $attrs['data-ram-type'] = strtoupper($ramTipo);
    // Assumir slot M.2 presente quando placa-mãe é moderna, caso não exista dado explícito
    $attrs['data-m2'] = 'true';
  }

  if ($categoria === 'Memória RAM') {
    $ramTipo = $espec['tipo'] ?? null;
    if ($ramTipo) $attrs['data-ram-type'] = strtoupper($ramTipo);
    // Extrair frequência numérica, se existir
    $freq = $espec['frequencia'] ?? null; // ex: "3200MHz"
    if ($freq && preg_match('/(\d+)/', $freq, $m)) $attrs['data-ram-speed'] = $m[1];
  }

  if ($categoria === 'Placa de Vídeo') {
    // Usar consumo máximo como referência mínima de PSU
    $consumo = $comp['consumo_max'] ?? $espec['consumo'] ?? null; // ex: "115W" ou 115W
    if ($consumo && preg_match('/(\d+)/', (string)$consumo, $m)) $attrs['data-gpu-watts'] = $m[1];
    // Comprimento não disponível no seed; manter sem se não houver dado
  }

  if ($categoria === 'Fonte') {
    $pot = $espec['potencia'] ?? null; // ex: "650W"
    if ($pot && preg_match('/(\d+)/', (string)$pot, $m)) $attrs['data-psu-watts'] = $m[1];
  }

  if ($categoria === 'Armazenamento') {
    $interface = strtoupper($espec['interface'] ?? ($comp['interface'] ?? ''));
    $formato = strtoupper($espec['formato'] ?? ($comp['formato'] ?? ''));
    if (strpos($interface, 'PCIE') !== false) {
      $attrs['data-storage-type'] = 'NVMe';
    } elseif (strpos($interface, 'SATA') !== false) {
      $attrs['data-storage-type'] = 'SATA';
    }
    if ($formato) $attrs['data-form-factor'] = $formato;
  }

  if ($categoria === 'Gabinete') {
    $alturaGpu = $comp['altura_max_gpu'] ?? null; // ex: "360mm"
    if ($alturaGpu && preg_match('/(\d+)/', (string)$alturaGpu, $m)) $attrs['data-gpu-max-length'] = $m[1];
  }

  return $attrs;
}
?>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Montar Computador - FiveAnalysis</title>

  <!-- Fonte Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- CSS Externo -->
  <link rel="stylesheet" href="../Style/styles.css">
  <link rel="stylesheet" href="../Style/Global.css">
  <link rel="stylesheet" href="../Style/montar-computador.css">
</head>
<body>
  <header>
    <div class="logo">
      <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" style="height: 40px;">
    </div>
    <nav>
      <ul>
        <li><a href="../View/PaginaInicial.php">Início</a></li>
        <li><a href="Sobre.php">Sobre</a></li>
        <li><a href="Catalogo.php">Catálogo</a></li>
        <li><a href="Montagem.php" class="active">Montar computador</a></li>
      </ul>
    </nav>
    <div class="icons">
      <i class="bi bi-search"></i>
      <i class="bi bi-cart2"></i>
    </div>
  </header>

  <!-- Hardware Analysis Section -->
  <section class="hardware-analysis">
    <div class="analysis-header">
      <div class="analysis-title">
        <i class="bi bi-cart2"></i>
        <h2>ANÁLISE DE HARDWARE</h2>
      </div>
    </div>
    
    <!-- Component Categories Navigation -->
    <div class="component-nav">
      <div class="component-category active" data-category="processador">
        <div class="category-icon">
          <i class="bi bi-cpu"></i>
        </div>
        <span>Processador</span>
      </div>
      <div class="component-category" data-category="placa-mae">
        <div class="category-icon">
          <i class="bi bi-motherboard"></i>
        </div>
        <span>Placa Mãe</span>
      </div>
      <div class="component-category" data-category="memoria-ram">
        <div class="category-icon">
          <i class="bi bi-memory"></i>
        </div>
        <span>Memória RAM</span>
      </div>
      <div class="component-category" data-category="placa-video">
        <div class="category-icon">
          <i class="bi bi-gpu-card"></i>
        </div>
        <span>Placa de Vídeo</span>
      </div>
      <div class="component-category" data-category="armazenamento">
        <div class="category-icon">
          <i class="bi bi-hdd"></i>
        </div>
        <span>HDD / SSD</span>
      </div>
      <div class="component-category" data-category="gabinete">
        <div class="category-icon">
          <i class="bi bi-pc-display"></i>
        </div>
        <span>Gabinete</span>
      </div>
      <div class="component-category" data-category="fonte">
        <div class="category-icon">
          <i class="bi bi-lightning"></i>
        </div>
        <span>Fonte de Alimentação</span>
      </div>
      <div class="component-category" data-category="perifericos">
        <div class="category-icon">
          <i class="bi bi-keyboard"></i>
        </div>
        <span>Periféricos</span>
      </div>
    </div>
  </section>

  <!-- Component Selection Area -->
  <main class="component-selection">
    <div class="selection-header">
      <h2 id="category-title">Selecione o PROCESSADOR</h2>
      <div class="progress-bar">
        <div class="progress-fill" style="width: 12.5%"></div>
      </div>
    </div>

    <!-- Component Grid Container -->
    <div class="component-grid-container">
      <!-- Processador Section -->
      <div class="component-grid active" id="processador">
<?php if (!empty($bySlug['processador'])): foreach ($bySlug['processador'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= attr('data-brand', $p['marca_nome']) ?><?= isset($attrs['data-socket']) ? attr('data-socket', $attrs['data-socket']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a1.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button on click="esconderProduto()" class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem CPUs ativas.</p>
<?php endif; ?>
    </div>

    <!-- Placa Mãe Section -->
    <div class="component-grid" id="placa-mae">
<?php if (!empty($bySlug['placa-mae'])): foreach ($bySlug['placa-mae'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-socket']) ? attr('data-socket', $attrs['data-socket']) : '' ?><?= isset($attrs['data-ram-type']) ? attr('data-ram-type', $attrs['data-ram-type']) : '' ?><?= attr('data-m2', $attrs['data-m2'] ?? 'true') ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/placamae.webp' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem placas-mãe ativas.</p>
<?php endif; ?>
    </div>

    <!-- Memória RAM Section -->
    <div class="component-grid" id="memoria-ram">
<?php if (!empty($bySlug['memoria-ram'])): foreach ($bySlug['memoria-ram'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-ram-type']) ? attr('data-ram-type', $attrs['data-ram-type']) : '' ?><?= isset($attrs['data-ram-speed']) ? attr('data-ram-speed', $attrs['data-ram-speed']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a1.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem memórias ativas.</p>
<?php endif; ?>
    </div>

    <!-- Placa de Vídeo Section -->
    <div class="component-grid" id="placa-video">
<?php if (!empty($bySlug['placa-video'])): foreach ($bySlug['placa-video'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-gpu-watts']) ? attr('data-gpu-watts', $attrs['data-gpu-watts']) : '' ?><?= isset($attrs['data-gpu-length']) ? attr('data-gpu-length', $attrs['data-gpu-length']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/Placadevideo.webp' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem GPUs ativas.</p>
<?php endif; ?>
    </div>

    <!-- Armazenamento Section -->
    <div class="component-grid" id="armazenamento">
<?php if (!empty($bySlug['armazenamento'])): foreach ($bySlug['armazenamento'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-storage-type']) ? attr('data-storage-type', $attrs['data-storage-type']) : '' ?><?= isset($attrs['data-form-factor']) ? attr('data-form-factor', $attrs['data-form-factor']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a3.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem unidades de armazenamento ativas.</p>
<?php endif; ?>
    </div>

    <!-- Gabinete Section -->
    <div class="component-grid" id="gabinete">
<?php if (!empty($bySlug['gabinete'])): foreach ($bySlug['gabinete'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-gpu-max-length']) ? attr('data-gpu-max-length', $attrs['data-gpu-max-length']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a8.jpeg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem gabinetes ativos.</p>
<?php endif; ?>
    </div>

    <!-- Fonte Section -->
    <div class="component-grid" id="fonte">
<?php if (!empty($bySlug['fonte'])): foreach ($bySlug['fonte'] as $p): $attrs = extrairAtributosCompat($p); ?>
      <div class="product-card" id="product-<?= $p['id'] ?>"<?= isset($attrs['data-psu-watts']) ? attr('data-psu-watts', $attrs['data-psu-watts']) : '' ?>>
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a5.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem fontes ativas.</p>
<?php endif; ?>
    </div>

    <!-- Periféricos Section -->
    <div class="component-grid" id="perifericos">
<?php if (!empty($bySlug['perifericos'])): foreach ($bySlug['perifericos'] as $p): ?>
      <div class="product-card" id="product-<?= $p['id'] ?>">
        <img src="<?= $p['imagem_url'] ?: '../Assets/img/a1.jpg' ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
        <h3><?= htmlspecialchars($p['nome']) ?></h3>
        <p><?= htmlspecialchars($p['descricao']) ?></p>
        <div class="price">R$ <?= number_format($p['preco'], 2, ',', '.') ?></div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
<?php endforeach; else: ?>
      <p style="grid-column: 1/-1; text-align:center; color: var(--text-muted);">Sem periféricos ativos.</p>
<?php endif; ?>
    </div>
    </div> <!-- Fechar component-grid-container -->
  </main>

  <!-- Selected Components Summary -->
  <aside class="selected-components" id="selected-components-panel">
    <h3>Componentes Selecionados</h3>
    <div class="selected-list" id="selected-list">
      <p class="empty-message">Nenhum componente selecionado ainda</p>
    </div>
    <div class="total-price">
      <strong>Total: R$ <span id="total-price">0,00</span></strong>
    </div>
    <button class="btn btn-secondary" id="finalize-build" disabled>Finalizar Montagem</button>
  </aside>

  <!-- Toggle Panel Button -->
  <button class="toggle-panel-btn" id="toggle-panel-btn">
    <i class="bi bi-list-ul"></i>
  </button>

  <footer>
    <div class="footer-content">
      <div class="links">
        <h4>HiperLinks</h4>
        <ul>
          <li><a href="#">Hardware</a></li>
          <li><a href="#">Documentação</a></li>
          <li><a href="sobre.html">Sobre</a></li>
          <li><a href="#">Guia de Montagem</a></li>
        </ul>
      </div>
      <div class="contato">
        <h4>Contato</h4>
        <p>email: grigolodev@gmail.com</p>
      </div>
      <div class="social">
        <a href="#"><i class="bi bi-linkedin"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
      </div>
    </div>
    <p class="copyright">Todos os Direitos Reservados a FiveAnalysis.</p>
  </footer>


  <!-- JavaScript -->
  <script src="../Assets/js/main.js"></script>
  <!-- <script>
    document.getElementById("product-3").style.display = "none";
  </script> -->
  <script src="../Assets/js/montar-computador.js"></script>s
</body>
</html>
