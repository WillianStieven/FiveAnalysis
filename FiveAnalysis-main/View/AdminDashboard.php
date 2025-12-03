

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - FiveAnalysis</title>
    
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
        .header-logo {
            height: 40px;
            border-radius: 8px;
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
        
        .admin-header {
            background: linear-gradient(135deg, var(--Botão-Enviar) 0%, var(--Botão-Hover) 100%);
            color: var(--Text-Primary);
            padding: 25px 0;
            box-shadow: 0 4px 20px rgba(12, 67, 177, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .admin-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }
        
        .admin-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        
        .admin-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--Text-Primary);
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-header .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .admin-header .user-info span {
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
        
        .admin-container {
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
        
        .admin-actions {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
        }
        
        .admin-actions h2 {
            color: var(--Text-Primary);
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .action-btn {
            background: linear-gradient(135deg, var(--Botão-Enviar) 0%, var(--Botão-Hover) 100%);
            color: var(--Text-Primary);
            border: none;
            padding: 18px 24px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(12, 67, 177, 0.4);
        }
        
        .action-btn.secondary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }
        
        .action-btn.secondary:hover {
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .products-section {
            background: var(--Card-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
        }
        
        .products-section h2 {
            color: var(--Text-Primary);
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .products-table th,
        .products-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid var(--Border-Color);
        }
        
        .products-table th {
            background: var(--Background);
            font-weight: 600;
            color: var(--Text-Primary);
            border: 1px solid var(--Border-Color);
        }
        
        .products-table td {
            border: 1px solid var(--Border-Color);
            color: var(--Text-Muted);
        }
        
        .products-table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .product-actions {
            display: flex;
            gap: 8px;
        }
        
        .btn-edit {
            background: var(--Botão-Enviar);
            color: var(--Text-Primary);
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .btn-delete {
            background: #ef4444;
            color: var(--Text-Primary);
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.ativo {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .status-badge.inativo {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
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
            max-width: 600px;
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
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: var(--Input-Background);
            border: 1px solid var(--Border-Color);
            border-radius: 8px;
            color: var(--Text-Primary);
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
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
            background: #27ae60;
            color: var(--Text-Primary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn-cancel {
            background: #95a5a6;
            color: var(--Text-Primary);
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .loading {
            text-align: center;
            padding: 20px;
            color: var(--Text-Muted);
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
        
        @media (max-width: 768px) {
            .admin-header .container {
                flex-direction: column;
                gap: 15px;
            }
            
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .products-table {
                font-size: 14px;
            }
            
            .product-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <div class="header-left">
                <img src="../Assets/img/logotipo.png" alt="FiveAnalysis" class="header-logo">
                <h1>Lojas Filiadas</h1>
            </div>
            <div class="user-info">
                <span id="adminUserName">Usuario</span>
                <button class="logout-btn" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </button>
            </div>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="error-message" id="errorMessage"></div>
        <div class="success-message" id="successMessage"></div>
        
        <!-- Estatísticas -->
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total de Produtos</h3>
                <?php 
               
                    include '../Model/conexao.php';
                    
                
                    $sql = "SELECT COUNT(*) AS total FROM produtos";
                    $stmt = $conexao->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $totalProducts = $result ? $result['total'] : 0;
                ?>
                <div class="number" id="totalProducts"><?PHP echo $totalProducts ?></div>
            </div>
            <div class="stat-card">
                <h3>Produtos Ativos</h3>
                <?php 
                    $sql = "SELECT COUNT(*) AS ativos FROM produtos WHERE ativo = true";
                    $stmt = $conexao->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $activeProducts = $result ? $result['ativos'] : 0;
                ?>
                <div class="number" id="activeProducts"><?php echo $activeProducts ?></div>
            </div>
            <div class="stat-card">
                <h3>Vendas Hoje</h3>
                <div class="number" id="todaySales">-</div>
            </div>
            <div class="stat-card">
                <h3>Receita Total</h3>
                <div class="number" id="totalRevenue">-</div>
            </div>
        </div>
        
        <!-- Ações Administrativas -->
        <div class="admin-actions">
            <h2>Ações Administrativas</h2>
            <div class="action-buttons">
                <button class="action-btn" onclick="openAddProductModal()">
                    <i class="fas fa-plus-circle"></i>
                    Adicionar Produto
                </button>
                <button class="action-btn secondary" onclick="loadProducts()">
                    <i class="fas fa-sync-alt"></i>
                    Gerenciar Produtos
                </button>
            </div>
        </div>
    
    <!-- Modal para Adicionar/Editar Produto -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Adicionar Produto</h3>
                <span class="close" onclick="closeProductModal()">&times;</span>
            </div>
            <form id="productForm" action="../Controller/admin_products.php" method="POST">
                <div class="form-group">
                    <label for="productName">Nome do Produto</label>
                    <input type="text" id="productName" name="nome" required>
                </div>
                
                <div class="form-group">
                    <label for="productDescription">Descrição</label>
                    <textarea id="productDescription" name="descricao" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="productPrice">Preço</label>
                    <input type="number" id="productPrice" name="preco" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="productCategory">Categoria</label>
                    <select id="productCategory" name="categoria_id" required>
                        <option value="">Selecione uma categoria</option>
                        <option value="1">Processadores</option>
                        <option value="2">Placas de Vídeo</option>
                        <option value="3">Memória RAM</option>
                        <option value="4">Armazenamento</option>
                        <option value="5">Placas Mãe</option>
                        <option value="6">Fontes</option>
                        <option value="7">Gabinete</option>
                        <option value="8">Periféricos</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="productBrand">Marca</label>
                    <select id="productBrand" name="marca_id" required>
                        <option value="">Selecione uma marca</option>
                        <option value="1">Intel</option>
                        <option value="2">AMD</option>
                        <option value="3">ASUS</option>
                        <option value="4">MSI</option>
                        <option value="5">Gigabyte</option>
                        <option value="6">Corsair</option>
                        <option value="7">Kingston</option>
                        <option value="8">Samsung</option>
                        <option value="9">Western Digital</option>
                        <option value="10">Seagate</option>
                        <option value="11">NVIDIA</option>
                        <option value="12">EVGA</option>
                        <option value="13">Cooler Master</option>
                        <option value="14">Thermaltake</option>
                        <option value="15">BenQ</option>
                        <option value="16">LG</option>
                        <option value="17">Dell</option>
                        <option value="18">HP</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="productStock">Estoque</label>
                    <input type="number" id="productStock" name="estoque" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="productImage">URL da Imagem</label>
                    <input type="url" id="productImage" name="imagem_url">
                </div>
                
                <div class="form-group">
                    <label for="productSpecs">Especificações (JSON)</label>
                    <textarea id="productSpecs" name="especificacoes" rows="4" placeholder='{"socket": "LGA1700", "nucleos": 6, "frequencia": "3.7GHz"}'></textarea>
                </div>
                
                <div class="form-group">
                    <label for="productCompatibility">Compatibilidade (JSON)</label>
                    <textarea id="productCompatibility" name="compatibilidade" rows="4" placeholder='{"socket": "LGA1700", "chipset": ["B660", "H670"]}'></textarea>
                </div>
                
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="produto_id" id="produtoId">
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeProductModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Salvar Produto</button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../Assets/js/main.js"></script>
    <script>
        const API_BASE = '../Controller';
        let currentProductId = null;
        
        // Verificar autenticação ao carregar a página
        window.addEventListener('load', async function() {
            // Simular carregamento de dados
            loadDashboardData();
            
            // Adicionar event listener para o formulário
            document.getElementById('productForm').addEventListener('submit', handleProductSubmit);
        });
        

        async function loadProducts() {
            try {
                const response = await fetch(`${API_BASE}/admin_products.php?action=list`);
                const products = await response.json();
                displayProducts(products);
            } catch (error) {
                console.error('Erro ao carregar produtos:', error);
                showError('Erro de conexão ao carregar produtos');
            }
        }
        
        function displayProducts(products) {
            // Criar container se não existir
            let container = document.getElementById('productsTableContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'productsTableContainer';
                document.querySelector('.admin-container').appendChild(container);
            }
            
            if (!products || products.length === 0) {
                container.innerHTML = '<div class="products-section"><h2>Gerenciar Produtos</h2><p>Nenhum produto encontrado.</p></div>';
                return;
            }
            
            let tableHTML = `
                <div class="products-section">
                    <h2>Gerenciar Produtos</h2>
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Categoria</th>
                                <th>Marca</th>
                                <th>Estoque</th>
                                <th>Status</th>
                                <th>Loja</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            products.forEach(product => {
                const statusClass = product.ativo ? 'ativo' : 'inativo';
                const statusText = product.ativo ? 'Ativo' : 'Inativo';
                
                tableHTML += `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.nome}</td>
                        <td>R$ ${parseFloat(product.preco).toFixed(2)}</td>
                        <td>${product.categoria_nome || 'N/A'}</td>
                        <td>${product.marca_nome || 'N/A'}</td>
                        <td>${product.estoque || 0}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>${product.nome_loja || 'Admin'}</td>
                        <td>
                            <div class="product-actions">
                                <button class="btn-edit" onclick="editProduct(${product.id})">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <button class="btn-delete" onclick="deleteProduct(${product.id})">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table></div>';
            container.innerHTML = tableHTML;
        }
        
        function openAddProductModal() {
            currentProductId = null;
            document.getElementById('modalTitle').textContent = 'Adicionar Produto';
            document.getElementById('produtoId').value = '';
            document.getElementById('productForm').reset();
            document.getElementById('productModal').style.display = 'block';
        }
        
        async function editProduct(productId) {
            currentProductId = productId;
            document.getElementById('modalTitle').textContent = 'Editar Produto';
            document.getElementById('produtoId').value = productId;
            
            try {
                // Carregar dados do produto
                const response = await fetch(`${API_BASE}/admin_products.php?action=list`);
                const products = await response.json();
                const product = products.find(p => p.id == productId);
                
                if (product) {
                    document.getElementById('productName').value = product.nome;
                    document.getElementById('productDescription').value = product.descricao;
                    document.getElementById('productPrice').value = product.preco;
                    document.getElementById('productCategory').value = product.categoria_id;
                    document.getElementById('productBrand').value = product.marca_id;
                    document.getElementById('productStock').value = product.estoque;
                    document.getElementById('productImage').value = product.imagem_url || '';
                    document.getElementById('productSpecs').value = product.especificacoes || '';
                    document.getElementById('productCompatibility').value = product.compatibilidade || '';
                }
            } catch (error) {
                console.error('Erro ao carregar dados do produto:', error);
                showError('Erro ao carregar dados do produto');
            }
            
            document.getElementById('productModal').style.display = 'block';
        }
        
        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
            currentProductId = null;
        }
        
        async function deleteProduct(productId) {
            if (!confirm('Tem certeza que deseja excluir este produto?')) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'delete');
                formData.append('produto_id', productId);
                
                const response = await fetch(`${API_BASE}/admin_products.php`, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    showSuccess('Produto excluído com sucesso');
                    loadProducts();
                } else {
                    showError('Erro ao excluir produto');
                }
            } catch (error) {
                console.error('Erro ao excluir produto:', error);
                showError('Erro de conexão ao excluir produto');
            }
        }
        
        function exportProducts() {
            // Implementar exportação de produtos
            alert('Funcionalidade de exportação será implementada em breve');
        }
        
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
        
        async function handleProductSubmit(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const action = currentProductId ? 'edit' : 'add';
            formData.set('action', action);
            
            try {
                const response = await fetch(`${API_BASE}/admin_products.php`, {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    const message = action === 'add' ? 'Produto adicionado com sucesso' : 'Produto editado com sucesso';
                    showSuccess(message);
                    closeProductModal();
                    loadProducts();
                } else {
                    showError('Erro ao salvar produto');
                }
            } catch (error) {
                console.error('Erro ao salvar produto:', error);
                showError('Erro de conexão ao salvar produto');
            }
        }
        
        function logout() {
            if (confirm('Tem certeza que deseja sair?')) {
                window.location.href = '../Controller/logout_admin.php';
            }
        }
        
        // Fechar modal ao clicar fora dele
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                closeProductModal();
            }
        }
    </script>
</body>
</html>
