<!DOCTYPE html>
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
  <link rel="stylesheet" href="../Style/global.css">
  <link rel="stylesheet" href="../Style/montar-computador.css">
</head>
<body>
  <header>
    <div class="logo">Five<span>Analysis.</span></div>
    <nav>
      <ul>
        <li><a href="../View/PaginaInicial.php" class="active">Início</a></li>
        <li><a href="../View/Sobre.php">Sobre</a></li>
        <li><a href="../View/Montagem.php">Montar computador</a></li>
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
      <div class="product-card" data-product="ryzen-5600gt">
        <img src="../Assets/img/a1.jpg" alt="AMD Ryzen 5 5600GT">
        <h3>Processador AMD Ryzen 5 5600GT</h3>
        <p>Processador AMD Ryzen 5 5600GT, 3.9GHz (4.4GHz Max Turbo), AM4, 6 Cores, 12 Threads, Cache 35MB</p>
        <div class="price">R$ 839,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="intel-i5-12900p">
        <img src="../Assets/img/a1.jpg" alt="Intel Core i5 12900P">
        <h3>Processador Intel Core i5 12900P</h3>
        <p>Processador Intel Core i5 12900P, 2.4GHz (4.6GHz Max Turbo), LGA1700, 12 Cores, 16 Threads</p>
        <div class="price">R$ 774,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="ryzen-7600">
        <img src="../Assets/img/a1.jpg" alt="AMD Ryzen 5 7600">
        <h3>Processador AMD Ryzen 5 7600</h3>
        <p>Processador AMD Ryzen 5 7600, 3.8GHz (5.1GHz Max Turbo), AM5, 6 Cores, 12 Threads, Cache 38MB</p>
        <div class="price">R$ 1.439,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="intel-i3">
        <img src="../Assets/img/a1.jpg" alt="Intel Core i3">
        <h3>Processador Intel Core i3</h3>
        <p>Processador Intel Core i3, 3.0GHz (4.0GHz Max Turbo), LGA1700, 4 Cores, 8 Threads</p>
        <div class="price">R$ 654,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="ryzen-3">
        <img src="../Assets/img/a1.jpg" alt="AMD Ryzen 3">
        <h3>Processador AMD Ryzen 3</h3>
        <p>Processador AMD Ryzen 3, 3.5GHz (4.0GHz Max Turbo), AM4, 4 Cores, 8 Threads, Cache 18MB</p>
        <div class="price">R$ 479,99</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="intel-ultra-7">
        <img src="../Assets/img/a1.jpg" alt="Intel Core Ultra 7">
        <h3>Processador Intel Core Ultra 7</h3>
        <p>Processador Intel Core Ultra 7, 2.0GHz (4.8GHz Max Turbo), LGA1700, 16 Cores, 22 Threads</p>
        <div class="price">R$ 300,00</div>
        <button class="btn btn-primary select-component">
          <i class="bi bi-plus"></i> Adicionar
        </button>
      </div>
    </div>

    <!-- Placa Mãe Section -->
    <div class="component-grid" id="placa-mae">
      <div class="product-card" data-product="asus-prime-b450">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=ASUS+Prime+B450" alt="ASUS Prime B450">
        <h3>Placa Mãe ASUS Prime B450M-A II</h3>
        <p>Placa Mãe ASUS Prime B450M-A II, AMD AM4, Micro ATX, DDR4, USB 3.2</p>
        <div class="price">R$ 599,99</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="msi-b550">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=MSI+B550" alt="MSI B550">
        <h3>Placa Mãe MSI B550M PRO-VDH</h3>
        <p>Placa Mãe MSI B550M PRO-VDH, AMD AM4, Micro ATX, DDR4, PCIe 4.0</p>
        <div class="price">R$ 699,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="gigabyte-h610">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Gigabyte+H610" alt="Gigabyte H610">
        <h3>Placa Mãe Gigabyte H610M H</h3>
        <p>Placa Mãe Gigabyte H610M H, Intel LGA1700, Micro ATX, DDR4</p>
        <div class="price">R$ 549,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Memória RAM Section -->
    <div class="component-grid" id="memoria-ram">
      <div class="product-card" data-product="corsair-vengeance-16gb">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Corsair+Vengeance+16GB" alt="Corsair Vengeance 16GB">
        <h3>Memória Corsair Vengeance LPX 16GB</h3>
        <p>Memória Corsair Vengeance LPX 16GB (2x8GB) DDR4 3200MHz, CL16</p>
        <div class="price">R$ 399,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="kingston-fury-32gb">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Kingston+Fury+32GB" alt="Kingston Fury 32GB">
        <h3>Memória Kingston Fury Beast 32GB</h3>
        <p>Memória Kingston Fury Beast 32GB (2x16GB) DDR4 3200MHz, CL16</p>
        <div class="price">R$ 799,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Placa de Vídeo Section -->
    <div class="component-grid" id="placa-video">
      <div class="product-card" data-product="rtx-4060">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=RTX+4060" alt="RTX 4060">
        <h3>Placa de Vídeo RTX 4060 8GB</h3>
        <p>Placa de Vídeo NVIDIA GeForce RTX 4060 8GB GDDR6, 128-bit</p>
        <div class="price">R$ 1.899,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="rx-7600">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=RX+7600" alt="RX 7600">
        <h3>Placa de Vídeo RX 7600 8GB</h3>
        <p>Placa de Vídeo AMD Radeon RX 7600 8GB GDDR6, 128-bit</p>
        <div class="price">R$ 1.599,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Armazenamento Section -->
    <div class="component-grid" id="armazenamento">
      <div class="product-card" data-product="ssd-1tb">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=SSD+1TB" alt="SSD 1TB">
        <h3>SSD Kingston NV2 1TB</h3>
        <p>SSD Kingston NV2 1TB M.2 NVMe PCIe 4.0, Leitura 3500MB/s</p>
        <div class="price">R$ 399,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>

      <div class="product-card" data-product="hdd-2tb">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=HDD+2TB" alt="HDD 2TB">
        <h3>HDD Seagate Barracuda 2TB</h3>
        <p>HDD Seagate Barracuda 2TB 7200RPM, SATA 6Gb/s</p>
        <div class="price">R$ 299,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Gabinete Section -->
    <div class="component-grid" id="gabinete">
      <div class="product-card" data-product="gabinete-gamer">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Gabinete+Gamer" alt="Gabinete Gamer">
        <h3>Gabinete Gamer Pichau Pouter 2</h3>
        <p>Gabinete Gamer Pichau Pouter 2, Mid Tower, Vidro Temperado, RGB</p>
        <div class="price">R$ 199,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Fonte Section -->
    <div class="component-grid" id="fonte">
      <div class="product-card" data-product="fonte-650w">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Fonte+650W" alt="Fonte 650W">
        <h3>Fonte Corsair CV650 650W</h3>
        <p>Fonte Corsair CV650 650W 80 Plus Bronze, Semi Modular</p>
        <div class="price">R$ 399,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
    </div>

    <!-- Periféricos Section -->
    <div class="component-grid" id="perifericos">
      <div class="product-card" data-product="teclado-mouse">
        <img src="https://via.placeholder.com/200x150/2563eb/fff?text=Teclado+Mouse" alt="Teclado e Mouse">
        <h3>Kit Teclado e Mouse Gamer</h3>
        <p>Kit Teclado e Mouse Gamer RGB, USB, Retroiluminação</p>
        <div class="price">R$ 149,90</div>
        <button class="btn btn-primary select-component">Selecionar</button>
      </div>
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
  <script src="js/main.js"></script>
  <script src="js/montar-computador.js"></script>
</body>
</html>
