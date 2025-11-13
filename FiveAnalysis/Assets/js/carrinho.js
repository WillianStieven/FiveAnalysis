// === CARRINHO DE COMPRAS - JAVASCRIPT ===

document.addEventListener('DOMContentLoaded', function() {
    // Elementos DOM
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    const itemsCount = document.getElementById('itemsCount');
    const subtotalEl = document.getElementById('subtotal');
    const freteEl = document.getElementById('frete');
    const descontoEl = document.getElementById('desconto');
    const totalEl = document.getElementById('total');
    const finalizeOrderBtn = document.getElementById('finalizeOrder');
    const cartBadge = document.getElementById('cartBadge');

    // Inicialização
    init();

    function init() {
        loadCart();
        updateCartDisplay();
        setupEventListeners();
    }

    // === CARREGAR CARRINHO ===
    function loadCart() {
        const cart = getCart();
        return cart;
    }

    // === OBTER CARRINHO DO LOCALSTORAGE ===
    function getCart() {
        const cartJson = localStorage.getItem('cart');
        if (cartJson) {
            try {
                return JSON.parse(cartJson);
            } catch (e) {
                console.error('Erro ao ler carrinho:', e);
                return [];
            }
        }
        return [];
    }

    // === SALVAR CARRINHO NO LOCALSTORAGE ===
    function saveCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartBadge();
    }

    // === ATUALIZAR BADGE DO CARRINHO ===
    function updateCartBadge() {
        const cart = getCart();
        const count = cart.length;
        const badges = document.querySelectorAll('#cartBadge, .cart-badge');
        
        badges.forEach(badge => {
            if (badge) {
                badge.textContent = count;
                if (count > 0) {
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }
        });
        
        // Disparar evento para outras páginas
        window.dispatchEvent(new CustomEvent('cartUpdated'));
    }

    // === ATUALIZAR EXIBIÇÃO DO CARRINHO ===
    function updateCartDisplay() {
        const cart = getCart();
        
        if (cart.length === 0) {
            showEmptyCart();
            updateSummary(0);
            return;
        }

        hideEmptyCart();
        renderCartItems(cart);
        updateSummary(calculateTotal(cart));
        updateItemsCount(cart.length);
    }

    // === MOSTRAR CARRINHO VAZIO ===
    function showEmptyCart() {
        if (emptyCart) {
            emptyCart.style.display = 'block';
        }
        if (cartItems) {
            cartItems.innerHTML = '';
            cartItems.appendChild(emptyCart);
        }
        if (finalizeOrderBtn) {
            finalizeOrderBtn.disabled = true;
        }
    }

    // === ESCONDER CARRINHO VAZIO ===
    function hideEmptyCart() {
        if (emptyCart) {
            emptyCart.style.display = 'none';
        }
        if (finalizeOrderBtn) {
            finalizeOrderBtn.disabled = false;
        }
    }

    // === RENDERIZAR ITENS DO CARRINHO ===
    function renderCartItems(cart) {
        if (!cartItems) return;

        cartItems.innerHTML = '';
        
        cart.forEach((item, index) => {
            const itemElement = createCartItemElement(item, index);
            cartItems.appendChild(itemElement);
        });
    }

    // === CRIAR ELEMENTO DE ITEM DO CARRINHO ===
    function createCartItemElement(item, index) {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.dataset.index = index;

        const image = item.image || '../Assets/img/a1.jpg';
        const category = item.category || 'Produto';
        const price = formatPrice(item.price);

        div.innerHTML = `
            <img src="${image}" alt="${item.name}" class="cart-item-image">
            <div class="cart-item-details">
                <h3 class="cart-item-name">${item.name}</h3>
                <span class="cart-item-category">${category}</span>
                <div class="cart-item-price">${price}</div>
            </div>
            <div class="cart-item-actions">
                <button class="remove-item-btn" onclick="removeCartItem(${index})" title="Remover item">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        return div;
    }

    // === REMOVER ITEM DO CARRINHO ===
    window.removeCartItem = function(index) {
        const cart = getCart();
        if (index >= 0 && index < cart.length) {
            const item = cart[index];
            cart.splice(index, 1);
            saveCart(cart);
            updateCartDisplay();
            
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification(`${item.name} removido do carrinho`, 'info');
            }
        }
    };

    // === CALCULAR TOTAL ===
    function calculateTotal(cart) {
        let total = 0;
        cart.forEach(item => {
            let price = item.price;
            // Se o preço é uma string, converter para número
            if (typeof price === 'string') {
                // Remover "R$ " e converter vírgula para ponto
                price = price.replace('R$ ', '').replace(/\./g, '').replace(',', '.');
                price = parseFloat(price);
            }
            // Se o preço é um número, usar diretamente
            if (!isNaN(price)) {
                total += price;
            }
        });
        return total;
    }

    // === ATUALIZAR RESUMO ===
    function updateSummary(subtotal) {
        const frete = subtotal >= 1000 ? 0 : 50; // Frete grátis acima de R$ 1.000
        const desconto = subtotal >= 2000 ? subtotal * 0.05 : 0; // 5% de desconto acima de R$ 2.000
        const total = subtotal + frete - desconto;

        if (subtotalEl) subtotalEl.textContent = formatPrice(subtotal);
        if (freteEl) freteEl.textContent = frete === 0 ? 'Grátis' : formatPrice(frete);
        if (descontoEl) descontoEl.textContent = desconto === 0 ? 'R$ 0,00' : formatPrice(desconto);
        if (totalEl) totalEl.textContent = formatPrice(total);
    }

    // === ATUALIZAR CONTADOR DE ITENS ===
    function updateItemsCount(count) {
        if (itemsCount) {
            itemsCount.textContent = count === 1 ? '1 item' : `${count} itens`;
        }
    }

    // === FORMATAR PREÇO ===
    function formatPrice(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    // === CONFIGURAR EVENT LISTENERS ===
    function setupEventListeners() {
        if (finalizeOrderBtn) {
            finalizeOrderBtn.addEventListener('click', function() {
                finalizeOrder();
            });
        }
    }

    // === FINALIZAR ORÇAMENTO ===
    function finalizeOrder() {
        const cart = getCart();
        
        if (cart.length === 0) {
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Adicione produtos ao carrinho primeiro', 'error');
            }
            return;
        }

        const subtotal = calculateTotal(cart);
        const frete = subtotal >= 1000 ? 0 : 50;
        const desconto = subtotal >= 2000 ? subtotal * 0.05 : 0;
        const total = subtotal + frete - desconto;

        const order = {
            items: cart,
            subtotal: subtotal,
            frete: frete,
            desconto: desconto,
            total: total,
            date: new Date().toLocaleString('pt-BR')
        };

        // Salvar orçamento
        localStorage.setItem('lastOrder', JSON.stringify(order));

        // Mostrar modal de confirmação
        showOrderConfirmation(order);
    }

    // === MOSTRAR CONFIRMAÇÃO DO ORÇAMENTO ===
    function showOrderConfirmation(order) {
        const modal = document.createElement('div');
        modal.className = 'order-confirmation-modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        `;

        const totalFormatted = formatPrice(order.total);
        const itemsList = order.items.map(item => `
            <div class="order-item">
                <span>${item.name}</span>
                <span>${formatPrice(item.price)}</span>
            </div>
        `).join('');

        modal.innerHTML = `
            <div class="modal-content" style="
                background-color: var(--background-card);
                border: 1px solid var(--border-color);
                border-radius: var(--border-radius);
                max-width: 600px;
                width: 90%;
                max-height: 80vh;
                overflow-y: auto;
                padding: 30px;
            ">
                <div class="modal-header" style="
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                    padding-bottom: 15px;
                    border-bottom: 1px solid var(--border-color);
                ">
                    <h2 style="
                        font-size: 24px;
                        font-weight: 700;
                        color: var(--text-primary);
                        margin: 0;
                    "><i class="bi bi-check-circle" style="color: #10b981;"></i> Orçamento Gerado!</h2>
                    <button class="close-modal" onclick="closeOrderModal()" style="
                        background: none;
                        border: none;
                        color: var(--text-muted);
                        font-size: 24px;
                        cursor: pointer;
                    ">&times;</button>
                </div>
                <div class="modal-body">
                    <div style="margin-bottom: 20px;">
                        <h3 style="color: var(--text-primary); margin-bottom: 15px;">Itens do Orçamento:</h3>
                        <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 20px;">
                            ${itemsList}
                        </div>
                    </div>
                    <div style="
                        padding: 20px;
                        background: var(--background-dark);
                        border-radius: var(--border-radius);
                        margin-bottom: 20px;
                    ">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--text-muted);">Subtotal:</span>
                            <span style="color: var(--text-primary);">${formatPrice(order.subtotal)}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--text-muted);">Frete:</span>
                            <span style="color: var(--text-primary);">${order.frete === 0 ? 'Grátis' : formatPrice(order.frete)}</span>
                        </div>
                        ${order.desconto > 0 ? `
                        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                            <span style="color: var(--text-muted);">Desconto:</span>
                            <span style="color: #10b981;">- ${formatPrice(order.desconto)}</span>
                        </div>
                        ` : ''}
                        <div style="
                            display: flex;
                            justify-content: space-between;
                            padding-top: 15px;
                            border-top: 2px solid var(--border-color);
                            margin-top: 15px;
                        ">
                            <span style="color: var(--text-primary); font-size: 20px; font-weight: 600;">Total:</span>
                            <span style="color: var(--primary-color); font-size: 24px; font-weight: 700;">${totalFormatted}</span>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 14px; text-align: center; margin-bottom: 20px;">
                        Orçamento gerado em: ${order.date}
                    </p>
                </div>
                <div class="modal-footer" style="
                    display: flex;
                    gap: 15px;
                    padding-top: 20px;
                    border-top: 1px solid var(--border-color);
                ">
                    <button class="btn btn-secondary" onclick="closeOrderModal()" style="flex: 1;">
                        Fechar
                    </button>
                    <button class="btn btn-primary" onclick="printOrder()" style="flex: 1;">
                        <i class="bi bi-printer"></i> Imprimir Orçamento
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Fechar ao clicar fora
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeOrderModal();
            }
        });
    }

    // === FECHAR MODAL ===
    window.closeOrderModal = function() {
        const modal = document.querySelector('.order-confirmation-modal');
        if (modal) {
            modal.remove();
        }
    };

    // === IMPRIMIR ORÇAMENTO ===
    window.printOrder = function() {
        const orderJson = localStorage.getItem('lastOrder');
        if (orderJson) {
            const order = JSON.parse(orderJson);
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Orçamento - FiveAnalysis</title>
                        <style>
                            body { font-family: Arial, sans-serif; padding: 20px; }
                            h1 { color: #2563eb; }
                            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                            th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
                            th { background-color: #2563eb; color: white; }
                            .total { font-size: 20px; font-weight: bold; color: #2563eb; }
                        </style>
                    </head>
                    <body>
                        <h1>Orçamento - FiveAnalysis</h1>
                        <p>Data: ${order.date}</p>
                        <table>
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Preço</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${order.items.map(item => `
                                    <tr>
                                        <td>${item.name}</td>
                                        <td>${formatPrice(item.price)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td><strong>Total</strong></td>
                                    <td class="total">${formatPrice(order.total)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    };

    // Atualizar badge ao carregar
    updateCartBadge();
});

// === FUNÇÕES GLOBAIS PARA ADICIONAR AO CARRINHO ===

// Adicionar item ao carrinho
window.addToCart = function(item) {
    const cart = getCart();
    
    // Verificar se item já existe (opcional - pode permitir múltiplos)
    const existingIndex = cart.findIndex(cartItem => 
        cartItem.id === item.id && cartItem.name === item.name
    );

    if (existingIndex === -1) {
        cart.push(item);
        saveCart(cart);
        
        if (window.FiveAnalysis) {
            window.FiveAnalysis.showNotification(`${item.name} adicionado ao carrinho!`, 'success');
        }
    } else {
        if (window.FiveAnalysis) {
            window.FiveAnalysis.showNotification('Item já está no carrinho', 'info');
        }
    }
    
    updateCartBadge();
};

// Adicionar montagem completa ao carrinho
window.addBuildToCart = function(buildSummary) {
    if (!buildSummary || !buildSummary.components) {
        console.error('Resumo de montagem inválido');
        return;
    }

    const cart = getCart();
    
    // Adicionar cada componente como item separado
    Object.entries(buildSummary.components).forEach(([category, component]) => {
        const item = {
            id: component.id || `build-${category}-${Date.now()}`,
            name: component.name,
            price: parseFloat(component.price.replace('R$ ', '').replace(',', '.')),
            category: category,
            image: '../Assets/img/a1.jpg',
            type: 'component'
        };
        
        cart.push(item);
    });

    saveCart(cart);
    updateCartBadge();
    
    if (window.FiveAnalysis) {
        window.FiveAnalysis.showNotification('Montagem adicionada ao carrinho!', 'success');
    }
};

// Função auxiliar para obter carrinho (acessível globalmente)
function getCart() {
    const cartJson = localStorage.getItem('cart');
    if (cartJson) {
        try {
            return JSON.parse(cartJson);
        } catch (e) {
            console.error('Erro ao ler carrinho:', e);
            return [];
        }
    }
    return [];
}

// Função auxiliar para salvar carrinho
function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    const cartBadge = document.getElementById('cartBadge');
    if (cartBadge) {
        const count = cart.length;
        cartBadge.textContent = count;
        cartBadge.style.display = count > 0 ? 'flex' : 'none';
    }
}

