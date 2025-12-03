// === GERENCIADOR DE PAGAMENTO ===
class PagamentoManager {
    constructor() {
        this.metodoPagamento = 'pix';
        this.init();
    }

    init() {
        this.carregarDadosPedido();
        this.configurarMascaras();
        this.configurarCEP();
    }

    // Carregar dados do pedido
    carregarDadosPedido() {
        const resumo = JSON.parse(localStorage.getItem('pedido_resumo'));
        
        if (!resumo || !resumo.itens || resumo.itens.length === 0) {
            alert('Nenhum pedido encontrado. Redirecionando para o carrinho...');
            window.location.href = 'Carrinho.php';
            return;
        }

        this.renderizarItens(resumo.itens);
        this.atualizarResumo(resumo);
    }

    // Renderizar itens do pedido
    renderizarItens(itens) {
        const container = document.getElementById('orderItems');
        container.innerHTML = '';

        itens.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'order-item';
            itemElement.innerHTML = `
                <div class="order-item-image">
                    <img src="${item.imagem}" alt="${item.nome}">
                </div>
                <div class="order-item-info">
                    <h4>${item.nome}</h4>
                    <p>${item.quantidade}x R$ ${item.preco.toFixed(2).replace('.', ',')}</p>
                    <span class="order-item-price">R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</span>
                </div>
            `;
            container.appendChild(itemElement);
        });
    }

    // Atualizar resumo
    atualizarResumo(resumo) {
        const subtotalEl = document.getElementById('summarySubtotal');
        const discountEl = document.getElementById('summaryDiscount');
        const totalEl = document.getElementById('summaryTotal');
        const discountRow = document.getElementById('discountRow');

        if (subtotalEl) {
            subtotalEl.textContent = `R$ ${resumo.subtotal.toFixed(2).replace('.', ',')}`;
        }

        if (this.metodoPagamento === 'pix') {
            if (discountRow) discountRow.style.display = 'flex';
            if (discountEl) {
                discountEl.textContent = `- R$ ${resumo.desconto.toFixed(2).replace('.', ',')}`;
            }
            if (totalEl) {
                totalEl.textContent = `R$ ${resumo.total.toFixed(2).replace('.', ',')}`;
            }
        } else {
            if (discountRow) discountRow.style.display = 'none';
            if (totalEl) {
                totalEl.textContent = `R$ ${resumo.subtotal.toFixed(2).replace('.', ',')}`;
            }
        }
    }

    // Configurar máscaras de input
    configurarMascaras() {
        // Máscara de cartão de crédito
        const cardNumber = document.getElementById('cardNumber');
        if (cardNumber) {
            cardNumber.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\s/g, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }

        // Máscara de validade
        const cardExpiry = document.getElementById('cardExpiry');
        if (cardExpiry) {
            cardExpiry.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2, 4);
                }
                e.target.value = value;
            });
        }

        // Máscara de CVV
        const cardCVV = document.getElementById('cardCVV');
        if (cardCVV) {
            cardCVV.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3);
            });
        }

        // Máscara de CEP
        const cep = document.getElementById('cep');
        if (cep) {
            cep.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 5) {
                    value = value.slice(0, 5) + '-' + value.slice(5, 8);
                }
                e.target.value = value;
            });
        }
    }

    // Configurar busca de CEP
    configurarCEP() {
        const cep = document.getElementById('cep');
        if (cep) {
            cep.addEventListener('blur', async (e) => {
                const cepValue = e.target.value.replace(/\D/g, '');
                
                if (cepValue.length === 8) {
                    try {
                        const response = await fetch(`https://viacep.com.br/ws/${cepValue}/json/`);
                        const data = await response.json();
                        
                        if (!data.erro) {
                            document.getElementById('endereco').value = data.logradouro || '';
                            document.getElementById('bairro').value = data.bairro || '';
                            document.getElementById('cidade').value = data.localidade || '';
                            document.getElementById('estado').value = data.uf || '';
                            document.getElementById('numero').focus();
                        } else {
                            this.mostrarNotificacao('CEP não encontrado', 'warning');
                        }
                    } catch (error) {
                        console.error('Erro ao buscar CEP:', error);
                        this.mostrarNotificacao('Erro ao buscar CEP', 'error');
                    }
                }
            });
        }
    }

    // Validar formulário de cartão
    validarCartao() {
        const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
        const cardName = document.getElementById('cardName').value.trim();
        const cardExpiry = document.getElementById('cardExpiry').value;
        const cardCVV = document.getElementById('cardCVV').value;

        if (cardNumber.length !== 16) {
            this.mostrarNotificacao('Número do cartão inválido', 'error');
            return false;
        }

        if (cardName.length < 3) {
            this.mostrarNotificacao('Nome no cartão inválido', 'error');
            return false;
        }

        if (!/^\d{2}\/\d{2}$/.test(cardExpiry)) {
            this.mostrarNotificacao('Validade do cartão inválida', 'error');
            return false;
        }

        if (cardCVV.length !== 3) {
            this.mostrarNotificacao('CVV inválido', 'error');
            return false;
        }

        return true;
    }

    // Validar endereço
    validarEndereco() {
        const cep = document.getElementById('cep').value.replace(/\D/g, '');
        const endereco = document.getElementById('endereco').value.trim();
        const numero = document.getElementById('numero').value.trim();
        const bairro = document.getElementById('bairro').value.trim();
        const cidade = document.getElementById('cidade').value.trim();
        const estado = document.getElementById('estado').value;

        if (cep.length !== 8) {
            this.mostrarNotificacao('CEP inválido', 'error');
            return false;
        }

        if (!endereco || !numero || !bairro || !cidade || !estado) {
            this.mostrarNotificacao('Preencha todos os campos de endereço', 'error');
            return false;
        }

        return true;
    }

    // Processar pagamento
    async processar() {
        // Validar endereço
        if (!this.validarEndereco()) {
            return;
        }

        // Validar cartão se método for crédito
        if (this.metodoPagamento === 'credit') {
            if (!this.validarCartao()) {
                return;
            }
        }

        // Mostrar loading
        const btnFinish = document.querySelector('.btn-finish');
        const originalText = btnFinish.innerHTML;
        btnFinish.disabled = true;
        btnFinish.innerHTML = '<i class="bi bi-hourglass-split"></i> Processando...';

        // Simular processamento
        setTimeout(() => {
            // Obter dados do pedido
            const resumo = JSON.parse(localStorage.getItem('pedido_resumo'));
            
            // Criar objeto de pagamento
            const pagamento = {
                metodo: this.metodoPagamento,
                pedido: resumo,
                endereco: {
                    cep: document.getElementById('cep').value,
                    endereco: document.getElementById('endereco').value,
                    numero: document.getElementById('numero').value,
                    complemento: document.getElementById('complemento').value,
                    bairro: document.getElementById('bairro').value,
                    cidade: document.getElementById('cidade').value,
                    estado: document.getElementById('estado').value
                },
                data_pagamento: new Date().toISOString()
            };

            // Adicionar dados do cartão se for crédito
            if (this.metodoPagamento === 'credit') {
                pagamento.cartao = {
                    numero: document.getElementById('cardNumber').value.replace(/\s/g, '').slice(-4),
                    nome: document.getElementById('cardName').value,
                    parcelas: document.getElementById('installments').value
                };
            }

            // Salvar no localStorage
            const pedidos = JSON.parse(localStorage.getItem('pedidos_finalizados')) || [];
            pedidos.push(pagamento);
            localStorage.setItem('pedidos_finalizados', JSON.stringify(pedidos));

            // Limpar carrinho
            localStorage.removeItem('fiveanalysis_cart');
            localStorage.removeItem('pedido_resumo');

            // Mostrar sucesso
            this.mostrarNotificacao('Pagamento realizado com sucesso!', 'success');

            // Redirecionar após 2 segundos
            setTimeout(() => {
                window.location.href = 'PaginaInicial.php';
            }, 2000);
        }, 2000);
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

// Instância global
const pagamentoManager = new PagamentoManager();

// === FUNÇÕES GLOBAIS ===

// Selecionar método de pagamento
function selectPaymentMethod(method) {
    pagamentoManager.metodoPagamento = method;
    
    // Atualizar radio buttons
    document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
        radio.checked = radio.value === method;
    });

    // Atualizar bordas
    document.querySelectorAll('.payment-option').forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        if (radio.checked) {
            option.style.borderColor = 'var(--primary-color)';
            option.style.background = 'rgba(37, 99, 235, 0.05)';
        } else {
            option.style.borderColor = 'var(--border-color)';
            option.style.background = 'transparent';
        }
    });

    // Mostrar/esconder formulário de cartão
    const creditCardForm = document.getElementById('creditCardForm');
    if (creditCardForm) {
        creditCardForm.style.display = method === 'credit' ? 'block' : 'none';
    }

    // Atualizar resumo (mostrar/esconder desconto)
    const resumo = JSON.parse(localStorage.getItem('pedido_resumo'));
    if (resumo) {
        pagamentoManager.atualizarResumo(resumo);
    }
}

// Processar pagamento
function processarPagamento() {
    pagamentoManager.processar();
}

// Atualizar badge do carrinho ao carregar
document.addEventListener('DOMContentLoaded', function() {
    if (typeof carrinhoManager !== 'undefined') {
        carrinhoManager.atualizarBadge();
    }
});

// Exportar para uso global
window.selectPaymentMethod = selectPaymentMethod;
window.processarPagamento = processarPagamento;
window.pagamentoManager = pagamentoManager;