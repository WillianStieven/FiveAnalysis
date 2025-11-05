// === MONTAR COMPUTADOR - JAVASCRIPT ===
document.getElementById("product-3").style.display = "none";

document.addEventListener('DOMContentLoaded', function() {
    console.log('Montagem JavaScript carregado');
    
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

    // Verificar se todos os elementos foram encontrados
    if (!categoryNav || !categoryTitle || !progressFill || !selectedList || !totalPrice || !finalizeButton || !selectedPanel || !toggleButton || !componentSelection || !gridContainer) {
        console.error('Elementos DOM não encontrados:', {
            categoryNav: !!categoryNav,
            categoryTitle: !!categoryTitle,
            progressFill: !!progressFill,
            selectedList: !!selectedList,
            totalPrice: !!totalPrice,
            finalizeButton: !!finalizeButton,
            selectedPanel: !!selectedPanel,
            toggleButton: !!toggleButton,
            componentSelection: !!componentSelection,
            gridContainer: !!gridContainer
        });
        return;
    }

    // Inicialização
    init();

    function init() {
        console.log('Inicializando montagem...');
        setupCategoryNavigation();
        setupComponentSelection();
        setupPanelToggle();
        ensureProductCardIds();
        updateProgress();
        updateSelectedComponents();
        adjustContainerHeight();
        console.log('Montagem inicializada com sucesso');
    }

    // === NAVEGAÇÃO ENTRE CATEGORIAS ===
    // function setupCategoryNavigation() {
    //     const categories = document.querySelectorAll('.component-category');
        
    //     categories.forEach(category => {
    //         category.addEventListener('click', function() {
    //             const categoryId = this.dataset.category;
    //             switchToCategory(categoryId);
    //         });
    //     });
    // }

    // function switchToCategory(categoryId) {
    //     // Atualizar estado
    //     state.currentCategory = categoryId;
        
    //     // Atualizar navegação visual
    //     document.querySelectorAll('.component-category').forEach(cat => {
    //         cat.classList.remove('active');
    //     });
    //     document.querySelector(`[data-category="${categoryId}"]`).classList.add('active');
        
    //     // Atualizar grid de componentes
    //     document.querySelectorAll('.component-grid').forEach(grid => {
    //         grid.classList.remove('active');
    //     });
    //     document.getElementById(categoryId).classList.add('active');
        
        
    //     // Ajustar altura do container
    //     adjustContainerHeight();
        
    //     // Atualizar título
    //     categoryTitle.textContent = `Selecione o ${state.categoryNames[categoryId]}`;
        
    //     // Atualizar progresso
    //     updateProgress();
        
    //     // Scroll suave para o topo da seção
    //     document.querySelector('.component-selection').scrollIntoView({
    //         behavior: 'smooth',
    //         block: 'start'
    //     });
        
    // }

    // // === TOGGLE DO PAINEL ===
    // function setupPanelToggle() {
    //     let isPanelVisible = true;
        
    //     toggleButton.addEventListener('click', function() {
    //         isPanelVisible = !isPanelVisible;
            
    //         if (isPanelVisible) {
    //             showPanel();
    //         } else {
    //             hidePanel();
    //         }
    //     });
        
    //     // Auto-hide panel after 5 seconds if no components selected
    //     setTimeout(() => {
    //         const selectedCount = Object.keys(state.selectedComponents).length;
    //         if (selectedCount === 0 && isPanelVisible) {
    //             hidePanel();
    //             isPanelVisible = false;
    //         }
    //     }, 5000);
    // }

    // // === SELEÇÃO DE COMPONENTES ===
    // function setupComponentSelection() {
    //     const selectButtons = document.querySelectorAll('.select-component');
        
    //     selectButtons.forEach(button => {
    //         button.addEventListener('click', function() {
    //             const productCard = this.closest('.product-card');
    //             const productId = productCard.id; // Usar ID ao invés de data-product
    //             const category = productCard.closest('.component-grid').id;
                
    //             // Sempre permitir seleção e processar compatibilidade
    //             selectComponent(category, productId, productCard);
    //         });
    //     });
    // }


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
    // function advanceToNextCategory() {
    //     const currentIndex = state.categories.indexOf(state.currentCategory);
    //     const nextIndex = currentIndex + 1;
        
    //     if (nextIndex < state.categories.length) {
    //         // Avançar imediatamente para a próxima categoria
    //         switchToCategory(state.categories[nextIndex]);
    //     } else {
    //         // Todas as categorias foram selecionadas
    //         finalizeButton.disabled = false;
    //         showNotification('Montagem completa! Clique em "Finalizar Montagem"', 'success');
    //     }
    // }

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

    // === COMPATIBILIDADE ===
    function getAttributesFromCard(card) {
        return { ...card.dataset };
    }

    function ensureCardId(card) {
        if (!card.id) {
            // Extrair ID do formato product-X ou gerar um novo
            const idMatch = card.id?.match(/product-(\d+)/);
            if (!idMatch) {
                card.id = `product-${Math.random().toString(36).slice(2)}`;
            }
        }
        return card.id;
    }

    // Função para esconder um produto pelo ID
    function esconderProduto(productId) {
        const produto = document.getElementById(productId);
        if (produto) {
            produto.style.display = 'none';
            produto.classList.add('incompatible');
        }
    }

    // Função para mostrar um produto pelo ID
    function mostrarProduto(productId) {
        const produto = document.getElementById(productId);
        if (produto) {
            produto.style.display = '';
            produto.classList.remove('incompatible');
        }
    }
    // === FINALIZAR MONTAGEM ===
    finalizeButton.addEventListener('click', function() {
        const selectedCount = Object.keys(state.selectedComponents).length;
        
        if (selectedCount < state.categories.length) {
            showNotification('Selecione todos os componentes antes de finalizar', 'error');
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
                    <button class="btn btn-primary" onclick="saveBuild()">Salvar Montagem</button>
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
        showNotification('Montagem salva com sucesso!', 'success');
        closeModal();
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
        
        showNotification('Montagem resetada', 'info');

        // Mostrar todos os produtos novamente após reset
        mostrarTodosProdutos();
    };

    // === SISTEMA DE NOTIFICAÇÕES ===
    function showNotification(message, type = 'info') {
        // Criar elemento de notificação
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Estilos da notificação
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
        `;
        
        // Cores baseadas no tipo
        const colors = {
            success: '#22c55e',
            error: '#ef4444',
            info: '#3b82f6',
            warning: '#f59e0b'
        };
        
        notification.style.backgroundColor = colors[type] || colors.info;
        
        // Adicionar ao DOM
        document.body.appendChild(notification);
        
        // Animar entrada
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remover após 3 segundos
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
});
