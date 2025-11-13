// === MONTAR COMPUTADOR - JAVASCRIPT ===

document.addEventListener('DOMContentLoaded', function() {
    // Estado da aplicação
    const state = {
        currentCategory: 'processador',
        selectedComponents: {},
        categories: [
            'processador',
            'placa-mae', 
            'memoria-ram',
            'placa-video',
            'armazenamento',
            'gabinete',
            'fonte',
            'perifericos'
        ],
        categoryNames: {
            'processador': 'PROCESSADOR',
            'placa-mae': 'PLACA MÃE',
            'memoria-ram': 'MEMÓRIA RAM',
            'placa-video': 'PLACA DE VÍDEO',
            'armazenamento': 'ARMAZENAMENTO',
            'gabinete': 'GABINETE',
            'fonte': 'FONTE DE ALIMENTAÇÃO',
            'perifericos': 'PERIFÉRICOS'
        }
    };

    // Elementos DOM
    const categoryNav = document.querySelector('.component-nav');
    const categoryTitle = document.getElementById('category-title');
    const progressFill = document.querySelector('.progress-fill');
    const selectedList = document.getElementById('selected-list');
    const totalPrice = document.getElementById('total-price');
    const finalizeButton = document.getElementById('finalize-build');
    const selectedPanel = document.getElementById('selected-components-panel');
    const toggleButton = document.getElementById('toggle-panel-btn');
    const componentSelection = document.querySelector('.component-selection');
    const gridContainer = document.querySelector('.component-grid-container');

    // Inicialização
    init();

    function init() {
        setupCategoryNavigation();
        setupComponentSelection();
        setupPanelToggle();
        updateProgress();
        updateSelectedComponents();
        adjustContainerHeight();
        
        // Garantir estado inicial correto do botão para compatibilidade com Edge
        const selectedCount = Object.keys(state.selectedComponents).length;
        if (selectedCount === 0) {
            // Se não há componentes, esconder o painel e mostrar o botão
            hidePanel();
        } else {
            // Se há componentes, mostrar o painel e esconder o botão
            showPanel();
        }
    }

    // === NAVEGAÇÃO ENTRE CATEGORIAS ===
    function setupCategoryNavigation() {
        const categories = document.querySelectorAll('.component-category');
        
        categories.forEach(category => {
            category.addEventListener('click', function() {
                const categoryId = this.dataset.category;
                switchToCategory(categoryId);
            });
        });
    }

    function switchToCategory(categoryId) {
        // Atualizar estado
        state.currentCategory = categoryId;
        
        // Atualizar navegação visual
        document.querySelectorAll('.component-category').forEach(cat => {
            cat.classList.remove('active');
        });
        document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
        
        // Atualizar grid de componentes
        document.querySelectorAll('.component-grid').forEach(grid => {
            grid.classList.remove('active');
        });
        document.getElementById(categoryId).classList.add('active');
        
        // Ajustar altura do container
        adjustContainerHeight();
        
        // Atualizar título
        categoryTitle.textContent = `Selecione o ${state.categoryNames[categoryId]}`;
        
        // Atualizar progresso
        updateProgress();
        
        // Scroll suave para o topo da seção
        document.querySelector('.component-selection').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // === TOGGLE DO PAINEL ===
    function setupPanelToggle() {
        let isPanelVisible = true;
        
        toggleButton.addEventListener('click', function() {
            isPanelVisible = !isPanelVisible;
            
            if (isPanelVisible) {
                showPanel();
            } else {
                hidePanel();
            }
        });
        
        // Auto-hide panel after 5 seconds if no components selected
        setTimeout(() => {
            const selectedCount = Object.keys(state.selectedComponents).length;
            if (selectedCount === 0 && isPanelVisible) {
                hidePanel();
                isPanelVisible = false;
            }
        }, 5000);
    }

    // === SELEÇÃO DE COMPONENTES ===
    function setupComponentSelection() {
        const selectButtons = document.querySelectorAll('.select-component');
        
        selectButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productCard = this.closest('.product-card');
                const productId = productCard.dataset.product;
                const category = productCard.closest('.component-grid').id;
                
                selectComponent(category, productId, productCard);
            });
        });
    }

    function selectComponent(category, productId, productCard) {
        // Obter informações do produto
        const productName = productCard.querySelector('h3').textContent;
        const productPrice = productCard.querySelector('.price').textContent;
        
        // Adicionar ao estado
        state.selectedComponents[category] = {
            id: productId,
            name: productName,
            price: productPrice,
            element: productCard
        };
        
        // Atualizar visual do botão
        updateButtonState(productCard, true);
        
        // Atualizar lista de selecionados
        updateSelectedComponents();
        
        // Mostrar painel se estiver escondido
        showPanel();
        
        // Avançar para próxima categoria
        advanceToNextCategory();
        
        // Mostrar notificação
        if (window.FiveAnalysis) {
            window.FiveAnalysis.showNotification(`${productName} selecionado!`, 'success');
        }
    }

    function updateButtonState(productCard, selected) {
        const button = productCard.querySelector('.select-component');
        
        if (selected) {
            button.innerHTML = '<i class="bi bi-check"></i> Selecionado';
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary');
            button.disabled = true;
        } else {
            button.innerHTML = 'Selecionar';
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');
            button.disabled = false;
        }
    }

    // === AVANÇAR PARA PRÓXIMA CATEGORIA ===
    function advanceToNextCategory() {
        const currentIndex = state.categories.indexOf(state.currentCategory);
        const nextIndex = currentIndex + 1;
        
        if (nextIndex < state.categories.length) {
            // Aguardar um pouco antes de avançar
            setTimeout(() => {
                switchToCategory(state.categories[nextIndex]);
            }, 1000);
        } else {
            // Todas as categorias foram selecionadas
            finalizeButton.disabled = false;
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Montagem completa! Clique em "Finalizar Montagem"', 'success');
            }
        }
    }

    // === AJUSTAR ALTURA DO CONTAINER ===
    function adjustContainerHeight() {
        const activeGrid = document.querySelector('.component-grid.active');
        if (activeGrid) {
            // Usar setTimeout para aguardar a transição CSS
            setTimeout(() => {
                const height = activeGrid.offsetHeight;
                gridContainer.style.minHeight = `${Math.max(height, 400)}px`;
            }, 100);
        }
    }

    // === ATUALIZAR PROGRESSO ===
    function updateProgress() {
        const currentIndex = state.categories.indexOf(state.currentCategory);
        const progress = ((currentIndex + 1) / state.categories.length) * 100;
        progressFill.style.width = `${progress}%`;
    }

    // === CONTROLE DO PAINEL ===
    function showPanel() {
        selectedPanel.classList.remove('hidden');
        // Usar classe específica ao invés de depender de seletor CSS para compatibilidade com Edge
        toggleButton.classList.add('panel-visible');
        toggleButton.classList.remove('hidden');
        toggleButton.innerHTML = '<i class="bi bi-x"></i>';
        componentSelection.classList.remove('panel-hidden');
    }

    function hidePanel() {
        selectedPanel.classList.add('hidden');
        // Remover classe panel-visible e garantir que o botão volte à posição original
        toggleButton.classList.remove('panel-visible');
        toggleButton.classList.remove('hidden');
        toggleButton.innerHTML = '<i class="bi bi-list-ul"></i>';
        componentSelection.classList.add('panel-hidden');
    }

    // === ATUALIZAR LISTA DE COMPONENTES SELECIONADOS ===
    function updateSelectedComponents() {
        const selectedCount = Object.keys(state.selectedComponents).length;
        
        if (selectedCount === 0) {
            selectedList.innerHTML = '<p class="empty-message">Nenhum componente selecionado ainda</p>';
            totalPrice.textContent = '0,00';
            return;
        }
        
        let html = '';
        let total = 0;
        
        Object.entries(state.selectedComponents).forEach(([category, component]) => {
            const priceValue = parseFloat(component.price.replace('R$ ', '').replace(',', '.'));
            total += priceValue;
            
            html += `
                <div class="selected-item">
                    <span class="name">${component.name}</span>
                    <span class="price">${component.price}</span>
                    <button class="remove" onclick="removeComponent('${category}')">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            `;
        });
        
        selectedList.innerHTML = html;
        totalPrice.textContent = total.toFixed(2).replace('.', ',');
    }

    // === REMOVER COMPONENTE ===
    window.removeComponent = function(category) {
        if (state.selectedComponents[category]) {
            const component = state.selectedComponents[category];
            
            // Restaurar botão
            updateButtonState(component.element, false);
            
            // Remover do estado
            delete state.selectedComponents[category];
            
            // Atualizar lista
            updateSelectedComponents();
            
            // Voltar para a categoria removida
            switchToCategory(category);
            
            // Mostrar notificação
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification(`${component.name} removido`, 'info');
            }
        }
    };

    // === FINALIZAR MONTAGEM ===
    finalizeButton.addEventListener('click', function() {
        const selectedCount = Object.keys(state.selectedComponents).length;
        
        if (selectedCount < state.categories.length) {
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Selecione todos os componentes antes de finalizar', 'error');
            }
            return;
        }
        
        // Criar resumo da montagem
        const buildSummary = {
            components: state.selectedComponents,
            totalPrice: totalPrice.textContent,
            date: new Date().toLocaleDateString('pt-BR')
        };
        
        // Salvar no localStorage
        localStorage.setItem('buildSummary', JSON.stringify(buildSummary));
        
        // Mostrar modal de confirmação
        showBuildSummary(buildSummary);
    });

    // === MOSTRAR RESUMO DA MONTAGEM ===
    function showBuildSummary(summary) {
        const modal = document.createElement('div');
        modal.className = 'build-summary-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Resumo da Montagem</h2>
                    <button class="close-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="summary-list">
                        ${Object.entries(summary.components).map(([category, component]) => `
                            <div class="summary-item">
                                <strong>${state.categoryNames[category]}:</strong>
                                <span>${component.name} - ${component.price}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div class="summary-total">
                        <strong>Total: R$ ${summary.totalPrice}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal()">Fechar</button>
                    <button class="btn btn-primary" onclick="addBuildToCartFromModal()">Adicionar ao Carrinho</button>
                </div>
            </div>
        `;
        
        // Adicionar estilos do modal
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
        
        document.body.appendChild(modal);
        
        // Event listeners do modal
        modal.querySelector('.close-modal').addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

    // === FUNÇÕES GLOBAIS ===
    window.closeModal = function() {
        const modal = document.querySelector('.build-summary-modal');
        if (modal) {
            modal.remove();
        }
    };

    window.saveBuild = function() {
        if (window.FiveAnalysis) {
            window.FiveAnalysis.showNotification('Montagem salva com sucesso!', 'success');
        }
        closeModal();
    };

    // === ADICIONAR MONTAGEM AO CARRINHO ===
    window.addBuildToCartFromModal = function() {
        const buildSummaryJson = localStorage.getItem('buildSummary');
        if (!buildSummaryJson) {
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Nenhuma montagem encontrada', 'error');
            }
            return;
        }

        try {
            const buildSummary = JSON.parse(buildSummaryJson);
            
            // Adicionar manualmente ao carrinho
            const cart = JSON.parse(localStorage.getItem('cart') || '[]');
            
            Object.entries(buildSummary.components).forEach(([category, component]) => {
                const priceValue = parseFloat(component.price.replace('R$ ', '').replace(',', '.'));
                const item = {
                    id: component.id || `build-${category}-${Date.now()}`,
                    name: component.name,
                    price: priceValue,
                    category: state.categoryNames[category] || category,
                    image: '../Assets/img/a1.jpg',
                    type: 'component'
                };
                cart.push(item);
            });

            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Atualizar badge do carrinho
            const badges = document.querySelectorAll('#cartBadge, .cart-badge');
            badges.forEach(badge => {
                if (badge) {
                    const count = cart.length;
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
            
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Montagem adicionada ao carrinho!', 'success');
            }

            closeModal();
            
            // Opcional: redirecionar para o carrinho
            setTimeout(() => {
                if (confirm('Deseja ir para o carrinho agora?')) {
                    window.location.href = '../View/Carrinho.php';
                }
            }, 500);
            
        } catch (e) {
            console.error('Erro ao adicionar ao carrinho:', e);
            if (window.FiveAnalysis) {
                window.FiveAnalysis.showNotification('Erro ao adicionar ao carrinho', 'error');
            }
        }
    };

    // === ATALHOS DE TECLADO ===
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // === EXPORTAR ESTADO ===
    window.getBuildState = function() {
        return {
            currentCategory: state.currentCategory,
            selectedComponents: state.selectedComponents,
            totalPrice: totalPrice.textContent
        };
    };

    // === RESETAR MONTAGEM ===
    window.resetBuild = function() {
        // Limpar estado
        state.selectedComponents = {};
        state.currentCategory = 'processador';
        
        // Resetar botões
        document.querySelectorAll('.select-component').forEach(button => {
            updateButtonState(button.closest('.product-card'), false);
        });
        
        // Voltar para primeira categoria
        switchToCategory('processador');
        
        // Atualizar lista
        updateSelectedComponents();
        
        // Desabilitar botão finalizar
        finalizeButton.disabled = true;
        
        if (window.FiveAnalysis) {
            window.FiveAnalysis.showNotification('Montagem resetada', 'info');
        }
    };
});
