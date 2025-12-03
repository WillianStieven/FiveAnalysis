<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Dashboard - FiveAnalysis</title>
    
    <!--Link CSS-->
    <link rel="stylesheet" href="../Style/Global.css">
    <!--Link Favicon-->
    <link rel="shortcut icon" href="../Assets/img/logotipo.png" type="image/x-icon">
    <!--Link Google Fonts-->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <!--Link Font Awesome-->
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    
    <style>
        :root{
            --Background: #000000;
            background-color: var(--Background);
            --Input-Background: #131313;
            --Botão-Enviar: #0c43b1;
            --Botão-Hover: #0a3a9e;
            --Card-Background: rgba(17, 17, 17, 0.9);
            --Border-Color: #333333;
            --Text-Primary: #ffffff;
            --Text-Muted: #cccccc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        
        body {
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.1;
            z-index: -1;
        }
        
        .user-header {
            background: linear-gradient(135deg, var(--Botão-Enviar) 0%, var(--Botão-Hover) 100%);
            color: var(--Text-Primary);
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(12, 67, 177, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .user-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .user-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .user-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--Text-Primary);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-header .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-header .header-logo {
            height: 40px;
            border-radius: 8px;
        }
        
        .user-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-header .user-info span {
            font-size: 14px;
            color: var(--Text-Primary);
        }
        
        .logout-btn {
            background: #ef4444;
            color: var(--Text-Primary);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #dc2626;
        }
        
        .user-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            border-left: 4px solid var(--Botão-Enviar);
            backdrop-filter: blur(15px);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--Botão-Enviar), var(--Botão-Hover));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover::before {
            transform: scaleX(1);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(12, 67, 177, 0.2);
            border-color: var(--Botão-Enviar);
        }
        
        .stat-card h3 {
            color: var(--Text-Muted);
            font-size: 14px;
            margin-bottom: 8px;
        }
        
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--Text-Primary);
        }
        
        .dashboard-sections {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .section-card {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        
        .section-card h2 {
            color: var(--Text-Primary);
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-card h2 i {
            color: var(--Botão-Enviar);
        }
        
        .user-info-section {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }
        
        .user-info-section h2 {
            color: var(--Text-Primary);
            margin-bottom: 20px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-info-section h2 i {
            color: var(--Botão-Enviar);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--Border-Color);
            border-radius: 8px;
            padding: 20px;
        }
        
        .info-item h3 {
            color: var(--Botão-Enviar);
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .info-item p {
            color: var(--Text-Muted);
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .edit-btn {
            background: var(--Botão-Enviar);
            color: var(--Text-Primary);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        
        .edit-btn:hover {
            background: var(--Botão-Hover);
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .history-table th,
        .history-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--Border-Color);
        }
        
        .history-table th {
            background: var(--Background);
            font-weight: 600;
            color: var(--Text-Primary);
            border: 1px solid var(--Border-Color);
        }
        
        .history-table td {
            color: var(--Text-Muted);
            border: 1px solid var(--Border-Color);
        }
        
        .history-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-pending {
            background: #f59e0b;
            color: var(--Text-Primary);
        }
        
        .status-completed {
            background: #22c55e;
            color: var(--Text-Primary);
        }
        
        .status-cancelled {
            background: #ef4444;
            color: var(--Text-Primary);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--Text-Muted);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: var(--Border-Color);
        }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        
        .success-message {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
        }
        
        .modal-content {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            color: var(--Text-Primary);
            font-size: 20px;
        }
        
        .close {
            color: var(--Text-Muted);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: var(--Text-Primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--Text-Primary);
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            background: var(--Input-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 8px;
            color: var(--Text-Primary);
            font-size: 16px;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--Botão-Enviar);
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }
        
        .btn-save {
            background: var(--Botão-Enviar);
            color: var(--Text-Primary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-cancel {
            background: #6b7280;
            color: var(--Text-Primary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .user-header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .dashboard-sections {
                grid-template-columns: 1fr;
            }
            
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .history-table {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header class="user-header">
        <div class="container">
            <div class="header-left">
                <img src="../Assets/img/logotipo.png" alt="FiveAnalysis Logo" class="header-logo">
                <h1>Meu Perfil</h1>
            </div>
            <div class="user-info">
                <span id="userName">Carregando...</span>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        </div>
    </header>
    
    <div class="user-container">
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <!-- Estatísticas -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total de Montagens</h3>
                <div class="number" id="totalBuilds">-</div>
            </div>
            <div class="stat-card">
                <h3>Pedidos Realizados</h3>
                <div class="number" id="totalOrders">-</div>
            </div>
            <div class="stat-card">
                <h3>Valor Total Gasto</h3>
                <div class="number" id="totalSpent">-</div>
            </div>
            <div class="stat-card">
                <h3>Último Acesso</h3>
                <div class="number" id="lastAccess">-</div>
            </div>
        </div>
        
        <!-- Informações do Usuário -->
        <div class="user-info-section">
            <h2><i class="fas fa-user-edit"></i> Minhas Informações</h2>
            <div class="info-grid">
                <div class="info-item">
                    <h3>Dados Pessoais</h3>
                    <p><strong>Nome:</strong> <span id="userFullName">-</span></p>
                    <p><strong>Email:</strong> <span id="userEmail">-</span></p>
                    <p><strong>Telefone:</strong> <span id="userPhone">-</span></p>
                    <button class="edit-btn" onclick="editPersonalInfo()">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
                
                <div class="info-item">
                    <h3>Endereço</h3>
                    <p><strong>Endereço:</strong> <span id="userAddress">-</span></p>
                    <p><strong>Cidade:</strong> <span id="userCity">-</span></p>
                    <p><strong>CEP:</strong> <span id="userZipCode">-</span></p>
                    <button class="edit-btn" onclick="editAddress()">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Seções do Dashboard -->
        <div class="dashboard-sections">
            <!-- Histórico de Compras -->
            <div class="section-card">
                <h2><i class="fas fa-shopping-bag"></i> Histórico de Compras</h2>
                <div id="purchaseHistory">
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Nenhuma compra realizada ainda</p>
                    </div>
                </div>
            </div>
            
            <!-- Montagens Salvas -->
            <div class="section-card">
                <h2><i class="fas fa-microchip"></i> Minhas Montagens</h2>
                <div id="savedBuilds">
                    <div class="empty-state">
                        <i class="fas fa-microchip"></i>
                        <p>Nenhuma montagem salva ainda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal para editar informações -->
    <div id="editModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Editar Informações</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editForm" action="../Controller/update_user.php" method="POST">
                <div class="form-group">
                    <label for="editNome">Nome Completo</label>
                    <input type="text" id="editNome" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="editTelefone">Telefone</label>
                    <input type="tel" id="editTelefone" name="telefone">
                </div>
                
                <div class="form-group">
                    <label for="editEndereco">Endereço</label>
                    <input type="text" id="editEndereco" name="endereco">
                </div>
                
                <div class="form-group">
                    <label for="editCidade">Cidade</label>
                    <input type="text" id="editCidade" name="cidade">
                </div>
                
                <div class="form-group">
                    <label for="editCep">CEP</label>
                    <input type="text" id="editCep" name="cep">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Salvar</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../Assets/js/main.js"></script>
    <script>
        const API_BASE = '../Controller';
        let currentUser = null;
        
        // Verificar autenticação ao carregar a página
        window.addEventListener('load', async function() {
            // Simular carregamento de dados do usuário
            loadUserDashboard();
        });
        
        async function loadUserDashboard() {
            try {
                // Simular dados do usuário
                currentUser = {
                    nome: 'Usuário Teste',
                    email: 'usuario@teste.com',
                    telefone: '(11) 99999-9999',
                    endereco: 'Rua das Flores, 123',
                    cidade: 'São Paulo',
                    cep: '01234-567'
                };
                
                // Carregar dados do usuário
                document.getElementById('userName').textContent = currentUser.nome;
                document.getElementById('userFullName').textContent = currentUser.nome;
                document.getElementById('userEmail').textContent = currentUser.email;
                document.getElementById('userPhone').textContent = currentUser.telefone || 'Não informado';
                document.getElementById('userAddress').textContent = currentUser.endereco || 'Não informado';
                document.getElementById('userCity').textContent = currentUser.cidade || 'Não informado';
                document.getElementById('userZipCode').textContent = currentUser.cep || 'Não informado';
                
                // Carregar estatísticas
                await loadUserStats();
                
                // Carregar histórico de compras
                await loadPurchaseHistory();
                
                // Carregar montagens salvas
                await loadSavedBuilds();
                
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
                showError('Erro ao carregar dados do dashboard');
            }
        }
        
        async function loadUserStats() {
            try {
                // Simular dados de estatísticas
                document.getElementById('totalBuilds').textContent = '0';
                document.getElementById('totalOrders').textContent = '0';
                document.getElementById('totalSpent').textContent = 'R$ 0,00';
                document.getElementById('lastAccess').textContent = 'Hoje';
            } catch (error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        }
        
        async function loadPurchaseHistory() {
            try {
                // Simular histórico de compras vazio
                const historyContainer = document.getElementById('purchaseHistory');
                historyContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-bag"></i>
                        <p>Nenhuma compra realizada ainda</p>
                        <p><a href="PaginaInicial.php" style="color: var(--Botão-Enviar);">Começar a comprar</a></p>
                    </div>
                `;
            } catch (error) {
                console.error('Erro ao carregar histórico:', error);
            }
        }
        
        async function loadSavedBuilds() {
            try {
                // Simular montagens salvas vazias
                const buildsContainer = document.getElementById('savedBuilds');
                buildsContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-microchip"></i>
                        <p>Nenhuma montagem salva ainda</p>
                        <p><a href="Montagem.php" style="color: var(--Botão-Enviar);">Criar montagem</a></p>
                    </div>
                `;
            } catch (error) {
                console.error('Erro ao carregar montagens:', error);
            }
        }
        
        function editPersonalInfo() {
            document.getElementById('modalTitle').textContent = 'Editar Dados Pessoais';
            document.getElementById('editForm').reset();
            
            // Preencher campos
            document.getElementById('editNome').value = currentUser.nome || '';
            document.getElementById('editTelefone').value = currentUser.telefone || '';
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function editAddress() {
            document.getElementById('modalTitle').textContent = 'Editar Endereço';
            document.getElementById('editForm').reset();
            
            // Preencher campos
            document.getElementById('editEndereco').value = currentUser.endereco || '';
            document.getElementById('editCidade').value = currentUser.cidade || '';
            document.getElementById('editCep').value = currentUser.cep || '';
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const updateData = Object.fromEntries(formData);
            
            try {
                // Simular atualização
                showSuccess('Informações atualizadas com sucesso!');
                closeEditModal();
                
                // Atualizar dados locais
                Object.assign(currentUser, updateData);
                loadUserDashboard();
                
            } catch (error) {
                console.error('Erro ao atualizar informações:', error);
                showError('Erro ao atualizar informações');
            }
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 5000);
        }
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = 'Login.php';
            }
        }
        
        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
