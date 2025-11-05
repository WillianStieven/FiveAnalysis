<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sobre - FiveAnalysis</title>

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
    <div class="logo">
      <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" style="height: 40px;">
    </div>
    <nav>
      <ul>
        <li><a href="../View/PaginaInicial.php">Início</a></li>
        <li><a href="Sobre.php" class="active">Sobre</a></li>
        <li><a href="Catalogo.php">Catálogo</a></li>
        <li><a href="Montagem.php">Montar computador</a></li>
      </ul>
    </nav>
    <div class="icons">
      <i class="bi bi-search"></i>
      <i class="bi bi-cart2"></i>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="banner">
    <div class="banner-text">
      <h1>Sobre a <span>FiveAnalysis</span></h1>
      <p>Somos especialistas em análise de componentes de computador, oferecendo as melhores recomendações para sua montagem ideal.</p>
      <a href="montarcomputador.html" class="btn">Começar Montagem</a>
    </div>
    <div class="banner-img">
      <img src="https://via.placeholder.com/400x300/2563eb/fff?text=FiveAnalysis+Team" alt="Equipe FiveAnalysis">
    </div>
  </section>

  <!-- Sobre Nós -->
  <section class="produtos">
    <h2>Nossa História</h2>
    <div class="info-card">
      <h3>Missão</h3>
      <p>Nossa missão é democratizar o acesso à tecnologia, fornecendo análises precisas e recomendações personalizadas para que cada pessoa possa montar o computador perfeito para suas necessidades, seja para trabalho, estudo ou entretenimento.</p>
    </div>
    
    <div class="info-card">
      <h3>Visão</h3>
      <p>Ser a plataforma de referência em análise de componentes de computador no Brasil, reconhecida pela qualidade de nossas análises e pela confiança que depositamos em cada recomendação.</p>
    </div>

    <div class="info-card">
      <h3>Valores</h3>
      <p>Transparência, qualidade, inovação e compromisso com nossos usuários são os pilares que guiam nosso trabalho diário. Acreditamos que a tecnologia deve ser acessível e que cada pessoa merece o melhor custo-benefício.</p>
    </div>
  </section>

  <!-- Nossa Equipe -->
  <section class="produtos">
    <h2>Nossa Equipe</h2>
    <div class="carousel">
      <div class="seta"><i class="bi bi-chevron-left"></i></div>
      <div class="produtos-container">
        <div class="produto">
          <img src="../Assets/img/Minski.jpg" alt="CEO" />
          <p>Minski</p>
          <p><strong>Gerente</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/Willian.jpg"  alt="DEV" />
          <p>Willian Stieven</p>
          <p><strong>Desenvolvedor</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/Felipe.jpg"  alt="DEV" />
          <p>Felipe Grigolo</p>
          <p><strong>Desenvolvedor</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/Erik.jpg" alt="DEV" />
          <p>Erik Ogliari</p>
          <p><strong>Desenvolvedor</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/Lucas.jpg" alt="DEV" />
          <p>Lucas Eduardo</p>
          <p><strong>Desenvolvedor</strong></p>
        </div>
      </div>
      <div class="seta"><i class="bi bi-chevron-right"></i></div>
    </div>
  </section>

  <!-- Contato -->
  <section class="produtos">
    <h2>Entre em Contato</h2>
    <div class="info-card">
      <h3>Fale Conosco</h3>
      <p>Estamos sempre disponíveis para ajudar você a encontrar os melhores componentes para sua montagem. Entre em contato conosco através dos canais abaixo:</p>
      
      <div style="margin-top: 20px;">
        <p><i class="bi bi-envelope"></i> <strong>Email:</strong> grigolodev@gmail.com</p>
        <p><i class="bi bi-telephone"></i> <strong>Telefone:</strong> (11) 99999-9999</p>
        <p><i class="bi bi-clock"></i> <strong>Horário de Atendimento:</strong> Segunda a Sexta, 9h às 18h</p>
      </div>
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
        <ul>
          <li><a href="#">Email: FiveAnalysis@gmail.com</a></li>
          <li><a href="#">Telefone: (49) 99898-9898</a></li>
          <li><a href="#">Efapi, 7199 - Aventureiro, Chapecó - SC - 89219-731</a></li>
        </ul>
      </div>
      <div class="social">
        <a href="#"><i class="bi bi-linkedin"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-facebook"></i></a>
      </div>
    </div>
    <p class="copyright">Todos os Direitos Reservados a FiveAnalysis. CNPJ: 09.321.222/0001-00</p>
  </footer>

  <!-- JavaScript -->
  <script src="js/main.js"></script>
</body>
</html>
