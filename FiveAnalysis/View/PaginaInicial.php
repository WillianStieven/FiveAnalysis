<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FiveAnalysis - Análises com Confiança</title>

  <!-- Fonte Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <!-- CSS Externo -->
  <link rel="stylesheet" href="../Style/styles.css">
  <link rel="stylesheet" href="../Style/Global.css">
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

  <section class="banner">
    <div class="banner-text">
      <h1>Monte seu <span>Computador</span></h1>
      <p>Escolha os melhores componentes e construa a máquina dos seus sonhos em um só lugar!</p>
      <a href="#" class="btn">Começar Agora</a>
    </div>
    <div class="banner-img">
      <img src="https://i.pinimg.com/474x/56/4a/d2/564ad2491acd9655d933d1efaa06e155.jpg" alt="Montagem de PC">
    </div>
  </section>

  <section class="produtos">
    <h2>Produtos Novos e Lançamentos</h2>
    <div class="carousel">
      <div class="seta"><i class="bi bi-chevron-left"></i></div>
      <div class="produtos-container">
        <div class="produto">
          <img src="a1.jpg" alt="Cadeira Gamer" />
          <p>Cadeira Ergonomica TGIF T0</p>
          <p><strong>R$ 3699,90</strong></p>
        </div>
        <div class="produto">
          <img src="a2.jpg" alt="Placa Mãe" />
          <p>Placa Mãe Asus Prime</p>
          <p><strong>R$ 1599,99</strong></p>
        </div>
        <div class="produto">
          <img src="a3.jpg" alt="Monitor" />
          <p>Monitor BenQ Mobiuz EX271</p>
          <p><strong>R$ 2892,90</strong></p>
        </div>
        <div class="produto">
          <img src="a4.jpg" alt="Placa de Vídeo" />
          <p>Placa de Video XFX Radeon RX 9060 XT</p>
          <p><strong>R$ 2.249,90</strong></p>
        </div>
      </div>
      <div class="seta"><i class="bi bi-chevron-right"></i></div>
    </div>
  </section>

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
</body>
</html>

