// === GERENCIADOR DE CARRINHO ===
class CarrinhoManager {
    constructor() {
        this.storageKey = 'fiveanalysis_cart';
        this.init();
    }

    init() {
        // Garantir que o carrinho existe no localStorage
        if (!this.getCart()) {
            this.saveCart([]);
        }
    }

    // Obter carrinho do localStorage
    getCart() {
        try {
            const cart = localStorage.getItem(this.storageKey);
            return cart ? JSON.parse(cart) : [];
        } catch (e) {
            console.error('Erro ao ler carrinho:', e);
            return [];
        }
    }

    // Salvar carrinho no localStorage
    saveCart(cart) {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(cart));
            this.atualizarBadge();
        } catch (e) {
            console.error('Erro ao salvar carrinho:', e);
        }
    }

    // Adicionar produto ao carrinho
    adicionarProduto(produto) {
        const cart = this.getCart();
        
        // Verificar se o produto já está no carrinho
        const produtoExistente = cart.find(item => item.id === produto.id);
        
        if (produtoExistente) {
            // Incrementar quantidade
            produtoExistente.quantidade += 1;
        } else {
            // Adicionar novo produto
            cart.push({
                id: produto.id,
                nome: produto.nome,
                preco: parseFloat(produto.preco),
                imagem: produto.imagem || '../Assets/img/a1.jpg',
                categoria: produto.categoria || '',
                marca: produto.marca || '',
                quantidade: 1
            });
        }
        
        this.saveCart(cart);
        this.mostrarNotificacao('Produto adicionado ao carrinho!', 'success');
        return cart;
    }

    // Remover produto do carrinho
    removerProduto(produtoId) {
        const cart = this.getCart();
        const novoCart = cart.filter(item => item.id !== produtoId);
        this.saveCart(novoCart);
        this.mostrarNotificacao('Produto removido do carrinho', 'info');
        return novoCart;
    }

    // Atualizar quantidade de um produto
    atualizarQuantidade(produtoId, quantidade) {
        if (quantidade <= 0) {
            return this.removerProduto(produtoId);
        }
        
        const cart = this.getCart();
        const produto = cart.find(item => item.id === produtoId);
        
        if (produto) {
            produto.quantidade = parseInt(quantidade);
            this.saveCart(cart);
        }
        
        return cart;
    }

    // Limpar carrinho
    limparCarrinho() {
        this.saveCart([]);
        this.mostrarNotificacao('Carrinho limpo', 'info');
    }

    // Calcular subtotal
    calcularSubtotal() {
        const cart = this.getCart();
        return cart.reduce((total, item) => {
            return total + (item.preco * item.quantidade);
        }, 0);
    }

    // Calcular desconto (5% para PIX)
    calcularDesconto() {
        const subtotal = this.calcularSubtotal();
        return subtotal * 0.05;
    }

    // Calcular total
    calcularTotal() {
        const subtotal = this.calcularSubtotal();
        const desconto = this.calcularDesconto();
        return subtotal - desconto;
    }

    // Obter quantidade total de itens
    getTotalItens() {
        const cart = this.getCart();
        return cart.reduce((total, item) => total + item.quantidade, 0);
    }

    // Atualizar badge do carrinho
    atualizarBadge() {
        const badge = document.getElementById('cartBadge');
        if (badge) {
            const total = this.getTotalItens();
            badge.textContent = total;
            badge.style.display = total > 0 ? 'flex' : 'none';
        }
    }

    // Mostrar notificação
    mostrarNotificacao(mensagem, tipo = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${tipo}`;
        notification.textContent = mensagem;
        
        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            info: '#3b82f6',
            warning: '#f59e0b'
        };
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
            max-width: 300px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            background-color: ${colors[tipo] || colors.info};
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Instância global do gerenciador de carrinho
const carrinhoManager = new CarrinhoManager();

// === FUNÇÕES GLOBAIS ===

// Adicionar produto ao carrinho (chamada de outras páginas)
function addToCart(produtoId, produtoNome, produtoPreco, produtoImagem, produtoCategoria, produtoMarca) {
    // Se apenas o ID foi passado, buscar dados do produto via API ou DOM
    if (arguments.length === 1) {
        // Tentar buscar dados do produto na página atual
        const produtoElement = document.querySelector(`[data-product-id="${produtoId}"]`);
        if (produtoElement) {
            produtoNome = produtoElement.dataset.productName || 'Produto';
            produtoPreco = parseFloat(produtoElement.dataset.productPrice || 0);
            produtoImagem = produtoElement.dataset.productImage || '../Assets/img/a1.jpg';
            produtoCategoria = produtoElement.dataset.productCategory || '';
            produtoMarca = produtoElement.dataset.productBrand || '';
        } else {
            // Se não encontrar, fazer requisição para obter dados do produto
            fetch(`../Controller/get_produto.php?id=${produtoId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        carrinhoManager.adicionarProduto({
                            id: data.produto.id,
                            nome: data.produto.nome,
                            preco: parseFloat(data.produto.preco),
                            imagem: data.produto.imagem_url || '../Assets/img/a1.jpg',
                            categoria: data.produto.categoria_nome || '',
                            marca: data.produto.marca_nome || ''
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar produto:', error);
                    carrinhoManager.mostrarNotificacao('Erro ao adicionar produto ao carrinho', 'error');
                });
            return;
        }
    }
    
    carrinhoManager.adicionarProduto({
        id: produtoId,
        nome: produtoNome,
        preco: produtoPreco,
        imagem: produtoImagem,
        categoria: produtoCategoria,
        marca: produtoMarca
    });
}

// Remover produto do carrinho
function removerDoCarrinho(produtoId) {
    carrinhoManager.removerProduto(produtoId);
    atualizarCarrinho();
}

// Atualizar quantidade
function atualizarQuantidade(produtoId, quantidade) {
    carrinhoManager.atualizarQuantidade(produtoId, quantidade);
    atualizarCarrinho();
}

// Atualizar exibição do carrinho
function atualizarCarrinho() {
    const cart = carrinhoManager.getCart();
    const cartItemsList = document.getElementById('cartItemsList');
    const emptyCart = document.getElementById('emptyCart');
    const cartSummary = document.getElementById('cartSummary');
    
    if (!cartItemsList) return;
    
    // Limpar lista
    cartItemsList.innerHTML = '';
    
    if (cart.length === 0) {
        // Mostrar carrinho vazio
        if (emptyCart) {
            emptyCart.style.display = 'block';
        }
        if (cartSummary) {
            cartSummary.style.display = 'none';
        }
        return;
    }
    
    // Esconder carrinho vazio
    if (emptyCart) {
        emptyCart.style.display = 'none';
    }
    if (cartSummary) {
        cartSummary.style.display = 'block';
    }
    
    // Renderizar itens
    cart.forEach(item => {
        const itemElement = criarItemCarrinho(item);
        cartItemsList.appendChild(itemElement);
    });
    
    // Atualizar resumo
    atualizarResumo();
}

// Criar elemento de item do carrinho
function criarItemCarrinho(item) {
    const div = document.createElement('div');
    div.className = 'cart-item';
    div.innerHTML = `
        <div class="cart-item-image">
            <img src="${item.imagem}" alt="${item.nome}">
        </div>
        <div class="cart-item-info">
            <h3>${item.nome}</h3>
            ${item.categoria ? `<p class="item-category">${item.categoria}${item.marca ? ' - ' + item.marca : ''}</p>` : ''}
            <p class="item-price">R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
        </div>
        <div class="cart-item-quantity">
            <button class="quantity-btn" onclick="atualizarQuantidade(${item.id}, ${item.quantidade - 1})">
                <i class="bi bi-dash"></i>
            </button>
            <input type="number" value="${item.quantidade}" min="1" 
                   onchange="atualizarQuantidade(${item.id}, parseInt(this.value))">
            <button class="quantity-btn" onclick="atualizarQuantidade(${item.id}, ${item.quantidade + 1})">
                <i class="bi bi-plus"></i>
            </button>
        </div>
        <div class="cart-item-total">
            <span>R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</span>
        </div>
        <div class="cart-item-actions">
            <button class="remove-btn" onclick="removerDoCarrinho(${item.id})" title="Remover">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    return div;
}

// Atualizar resumo do orçamento
function atualizarResumo() {
    const subtotal = carrinhoManager.calcularSubtotal();
    const desconto = carrinhoManager.calcularDesconto();
    const total = carrinhoManager.calcularTotal();
    
    const subtotalEl = document.getElementById('subtotal');
    const discountEl = document.getElementById('discount');
    const totalEl = document.getElementById('totalPrice');
    
    if (subtotalEl) {
        subtotalEl.textContent = `R$ ${subtotal.toFixed(2).replace('.', ',')}`;
    }
    if (discountEl) {
        discountEl.textContent = `- R$ ${desconto.toFixed(2).replace('.', ',')}`;
    }
    if (totalEl) {
        totalEl.textContent = `R$ ${total.toFixed(2).replace('.', ',')}`;
    }
}

// Atualizar badge do carrinho
function atualizarBadgeCarrinho() {
    carrinhoManager.atualizarBadge();
}

// ========================================
// FUNÇÃO ATUALIZADA - REDIRECIONA PARA PAGAMENTO
// ========================================
function finalizarPedido() {
    const cart = carrinhoManager.getCart();
    
    if (cart.length === 0) {
        carrinhoManager.mostrarNotificacao('Adicione produtos ao carrinho antes de finalizar', 'warning');
        return;
    }
    
    // OPCIONAL: Verificar se usuário está logado
    // Descomente as linhas abaixo se você tiver sistema de login
    /*
    const isLoggedIn = localStorage.getItem('userLoggedIn') === 'true';
    if (!isLoggedIn) {
        if (confirm('Você precisa estar logado para finalizar o pedido. Deseja fazer login?')) {
            window.location.href = 'Login.php';
        }
        return;
    }
    */
    
    // Criar resumo do pedido
    const resumo = {
        itens: cart,
        subtotal: carrinhoManager.calcularSubtotal(),
        desconto: carrinhoManager.calcularDesconto(),
        total: carrinhoManager.calcularTotal(),
        data: new Date().toISOString()
    };
    
    // Salvar resumo no localStorage para a página de pagamento
    localStorage.setItem('pedido_resumo', JSON.stringify(resumo));
    
    // REDIRECIONAR PARA A PÁGINA DE PAGAMENTO
    window.location.href = 'Pagamento.php';
}

// Exportar para uso global
window.carrinhoManager = carrinhoManager;
window.addToCart = addToCart;
window.removerDoCarrinho = removerDoCarrinho;
window.atualizarQuantidade = atualizarQuantidade;
window.atualizarCarrinho = atualizarCarrinho;
window.atualizarBadgeCarrinho = atualizarBadgeCarrinho;
window.finalizarPedido = finalizarPedido;