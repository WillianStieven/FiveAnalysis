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
  
  <style>
    .user-menu {
      position: absolute;
      top: 100%;
      right: 0;
      background: rgba(17, 17, 17, 0.95);
      border: 1px solid #333333;
      border-radius: 12px;
      padding: 20px;
      min-width: 200px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.4);
      backdrop-filter: blur(15px);
      z-index: 1000;
    }
    
    .user-menu-content {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }
    
    .user-info {
      padding-bottom: 15px;
      border-bottom: 1px solid #333333;
    }
    
    .user-info p {
      color: #ffffff;
      margin-bottom: 10px;
      font-size: 14px;
    }
    
    .logout-btn {
      background: #ef4444;
      color: #ffffff;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      transition: all 0.3s ease;
    }
    
    .logout-btn:hover {
      background: #dc2626;
    }
    
    .user-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    
    .menu-link {
      color: #cccccc;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 6px;
      transition: all 0.3s ease;
      font-size: 14px;
    }
    
    .menu-link:hover {
      background: rgba(12, 67, 177, 0.1);
      color: #0c43b1;
    }
    
    .admin-link {
      color: #f59e0b;
    }
    
    .admin-link:hover {
      background: rgba(245, 158, 11, 0.1);
      color: #f59e0b;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">Five<span>Analysis.</span></div>
    <nav>
      <ul>
        <li><a href="PaginaInicial.php" class="active">Início</a></li>
        <li><a href="Sobre.php">Sobre</a></li>
        <li><a href="Montagem.php">Montar computador</a></li>
      </ul>
    </nav>
    <div class="icons">
      <i class="bi bi-search"></i>
      <i class="bi bi-cart2"></i>
      <i class="bi bi-person" id="userIcon" onclick="toggleUserMenu()"></i>
    </div>
    
    <!-- Menu do usuário -->
    <div class="user-menu" id="userMenu" style="display: none;">
      <div class="user-menu-content">
        <div class="user-info" id="userInfo" style="display: none;">
          <p>Bem-vindo, <span id="userName"></span></p>
          <button onclick="logout()" class="logout-btn">Sair</button>
        </div>
        <div class="user-actions" id="userActions">
          <a href="Login.php" class="menu-link">Login</a>
          <a href="Registro.php" class="menu-link">Cadastrar</a>
          <a href="AdminLogin.php" class="menu-link admin-link">Área Admin</a>
        </div>
        <div class="user-actions" id="userLoggedActions" style="display: none;">
          <a href="UserDashboard.php" class="menu-link">Meu Dashboard</a>
          <a href="Montagem.php" class="menu-link">Montar PC</a>
          <a href="AdminLogin.php" class="menu-link admin-link">Área Admin</a>
        </div>
      </div>
    </div>
  </header>

  <section class="banner">
    <div class="banner-text">
      <h1>Monte seu <span>Computador</span></h1>
      <p>Escolha os melhores componentes e construa a máquina dos seus sonhos em um só lugar!</p>
      <a href="Montagem.php" class="btn">Começar Agora</a>
    </div>
    <div class="banner-img">
      <img src="../Assets/img/banner.png" alt="Montagem de PC">
    </div>
  </section>

  <section class="produtos">
    <h2>Produtos Novos e Lançamentos</h2>
    <div class="carousel">
      <div class="seta"><i class="bi bi-chevron-left"></i></div>
      <div class="produtos-container">
        <div class="produto">
          <img src="../Assets/img/a1.jpg" alt="Cadeira Gamer" />
          <p>Cadeira Ergonomica TGIF T0</p>
          <p><strong>R$ 3699,90</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/a2.jpg" alt="Placa Mãe" />
          <p>Placa Mãe Asus Prime</p>
          <p><strong>R$ 1599,99</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/a3.jpg" alt="Monitor" />
          <p>Monitor BenQ Mobiuz EX271</p>
          <p><strong>R$ 2892,90</strong></p>
        </div>
        <div class="produto">
          <img src="../Assets/img/a4.jpg" alt="Placa de Vídeo" />
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
          <li><a href="Sobre.php">Sobre</a></li>
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
  <script src="../Assets/js/main.js"></script>
  <script>
    // Função para alternar menu do usuário
    function toggleUserMenu() {
      const menu = document.getElementById('userMenu');
      menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }

    // Fechar menu ao clicar fora
    document.addEventListener('click', function(event) {
      const menu = document.getElementById('userMenu');
      const icon = document.getElementById('userIcon');
      
      if (!menu.contains(event.target) && !icon.contains(event.target)) {
        menu.style.display = 'none';
      }
    });

    // Verificar status de login ao carregar a página
    window.addEventListener('load', function() {
      // Simular verificação de usuário logado
      const isLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
      const userName = localStorage.getItem('userName') || 'Usuário';
      
      if (isLoggedIn) {
        // Usuário logado
        document.getElementById('userInfo').style.display = 'block';
        document.getElementById('userActions').style.display = 'none';
        document.getElementById('userLoggedActions').style.display = 'block';
        document.getElementById('userName').textContent = userName;
      } else {
        // Usuário não logado
        document.getElementById('userInfo').style.display = 'none';
        document.getElementById('userActions').style.display = 'block';
        document.getElementById('userLoggedActions').style.display = 'none';
      }
    });

    // Função de logout
    function logout() {
      localStorage.removeItem('userLoggedIn');
      localStorage.removeItem('userName');
      window.location.reload();
    }
  </script>
</body>
</html>