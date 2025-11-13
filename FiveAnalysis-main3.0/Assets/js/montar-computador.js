// === MONTAR COMPUTADOR - JAVASCRIPT COM COMPATIBILIDADE ===

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
        console.error('Elementos DOM não encontrados');
        return;
    }

    // Inicialização
    init();

    function init() {
        console.log('Inicializando montagem...');
        
        // Verificar se há produtos carregados
        const productCards = document.querySelectorAll('.product-card');
        console.log(`Total de produtos encontrados: ${productCards.length}`);
        
        setupCategoryNavigation();
        setupComponentSelection();
        setupPanelToggle();
        updateProgress();
        updateSelectedComponents();
        adjustContainerHeight();
        
        // Garantir que produtos estão visíveis inicialmente
        filterIncompatibleProducts();
        
        console.log('Montagem inicializada com sucesso');
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
        const categoryElement = document.querySelector(`[data-category="${categoryId}"]`);
        if (categoryElement) {
            categoryElement.classList.add('active');
        }
        
        // Atualizar grid de componentes
        document.querySelectorAll('.component-grid').forEach(grid => {
            grid.classList.remove('active');
        });
        const gridElement = document.getElementById(categoryId);
        if (gridElement) {
            gridElement.classList.add('active');
        }
        
        // Filtrar produtos incompatíveis
        filterIncompatibleProducts();
        
        // Ajustar altura do container
        adjustContainerHeight();
        
        // Atualizar título
        categoryTitle.textContent = `Selecione o ${state.categoryNames[categoryId]}`;
        
        // Atualizar progresso
        updateProgress();
        
        // Scroll suave para o topo da seção
        componentSelection.scrollIntoView({
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
                selectedPanel.style.display = 'block';
            } else {
                selectedPanel.style.display = 'none';
            }
        });
    }

    // === SELEÇÃO DE COMPONENTES ===
    function setupComponentSelection() {
        // Usar delegação de eventos para produtos dinâmicos
        document.addEventListener('click', function(e) {
            if (e.target.closest('.select-component')) {
                const button = e.target.closest('.select-component');
                const productCard = button.closest('.product-card');
                
                if (productCard && !button.disabled) {
                    const productId = productCard.id;
                    const category = productCard.closest('.component-grid').id;
                    
                    selectComponent(category, productId, productCard);
                }
            }
        });
    }

    function selectComponent(category, productId, productCard) {
        // Extrair informações do produto
        const productName = productCard.querySelector('h3')?.textContent || 'Produto';
        const productPrice = productCard.querySelector('.price')?.textContent || 'R$ 0,00';
        const productImage = productCard.querySelector('img')?.src || '';
        
        // Extrair atributos de compatibilidade
        const attributes = getAttributesFromCard(productCard);
        
        // Armazenar componente selecionado com dados de compatibilidade
        state.selectedComponents[category] = {
            id: productId,
            name: productName,
            price: productPrice,
            image: productImage,
            attributes: attributes
        };
        
        // Atualizar estado visual
        updateButtonState(productCard, true);
        
        // Desmarcar outros produtos da mesma categoria
        const categoryGrid = document.getElementById(category);
        if (categoryGrid) {
            categoryGrid.querySelectorAll('.product-card').forEach(card => {
                if (card.id !== productId) {
                    updateButtonState(card, false);
                }
            });
        }
        
        // Filtrar produtos incompatíveis
        filterIncompatibleProducts();
        
        // Atualizar lista de selecionados
        updateSelectedComponents();
        
        // Avançar para próxima categoria automaticamente
        advanceToNextCategory();
        
        // Mostrar notificação
        showNotification(`${productName} selecionado!`, 'success');
    }

    // === VERIFICAÇÃO DE COMPATIBILIDADE ===
    function isCompatible(productCard, category) {
        const productAttrs = getAttributesFromCard(productCard);
        
        // Processador: sempre compatível (primeiro passo)
        if (category === 'processador') {
            return true;
        }
        
        // Placa Mãe: deve ter socket compatível com processador
        if (category === 'placa-mae') {
            const cpu = state.selectedComponents['processador'];
            // Se não houver processador selecionado, mostrar todos
            if (!cpu) return true;
            
            const cpuSocket = cpu.attributes['socket'];
            const moboSocket = productAttrs['socket'];
            
            if (cpuSocket && moboSocket) {
                return cpuSocket === moboSocket;
            }
            // Se não houver informação de socket, permitir (pode ser genérico)
            return true;
        }
        
        // Memória RAM: deve ser compatível com placa mãe
        if (category === 'memoria-ram') {
            const mobo = state.selectedComponents['placa-mae'];
            // Se não houver placa mãe selecionada, mostrar todos
            if (!mobo) return true;
            
            const moboRamType = mobo.attributes['ramType'];
            const ramType = productAttrs['ramType'];
            
            if (moboRamType && ramType) {
                return moboRamType.toUpperCase() === ramType.toUpperCase();
            }
            // Se não houver informação, permitir
            return true;
        }
        
        // Placa de Vídeo: verificar consumo e tamanho
        if (category === 'placa-video') {
            // Verificar se cabe no gabinete (se já selecionado)
            const gabinete = state.selectedComponents['gabinete'];
            if (gabinete) {
                const gpuLength = parseInt(productAttrs['gpuLength']) || 0;
                const caseMaxLength = parseInt(gabinete.attributes['gpuMaxLength']) || 9999;
                
                if (gpuLength > caseMaxLength) {
                    return false;
                }
            }
            
            // Verificar consumo de energia (se fonte já selecionada)
            const fonte = state.selectedComponents['fonte'];
            if (fonte) {
                const totalConsumption = calculateTotalConsumption();
                const psuWatts = parseInt(fonte.attributes['psuWatts']) || 0;
                const gpuWatts = parseInt(productAttrs['gpuWatts']) || 0;
                
                if (totalConsumption + gpuWatts > psuWatts) {
                    return false;
                }
            }
            
            return true;
        }
        
        // Armazenamento: verificar se placa mãe suporta
        if (category === 'armazenamento') {
            const mobo = state.selectedComponents['placa-mae'];
            if (!mobo) return true; // Pode selecionar antes da placa mãe
            
            const storageType = productAttrs['storageType'];
            
            // Se for NVMe, verificar se placa mãe tem slot M.2
            if (storageType === 'NVME') {
                const hasM2 = mobo.attributes['m2'] === 'true' || mobo.attributes['m2'] === true;
                return hasM2;
            }
            
            // SATA sempre compatível
            return true;
        }
        
        // Gabinete: verificar se cabe placa de vídeo (se já selecionada)
        if (category === 'gabinete') {
            const gpu = state.selectedComponents['placa-video'];
            if (!gpu) return true;
            
            const gpuLength = parseInt(gpu.attributes['gpuLength']) || 0;
            const caseMaxLength = parseInt(productAttrs['gpuMaxLength']) || 9999;
            
            return gpuLength <= caseMaxLength;
        }
        
        // Fonte: verificar se tem potência suficiente
        if (category === 'fonte') {
            const totalConsumption = calculateTotalConsumption();
            const psuWatts = parseInt(productAttrs['psuWatts']) || 0;
            
            // Adicionar margem de segurança de 20%
            const requiredWatts = totalConsumption * 1.2;
            
            return psuWatts >= requiredWatts;
        }
        
        // Periféricos: sempre compatíveis
        if (category === 'perifericos') {
            return true;
        }
        
        return true;
    }

    // Calcular consumo total de energia
    function calculateTotalConsumption() {
        let total = 0;
        
        // Consumo base do sistema (estimado)
        total += 50; // Placa mãe, RAM, etc.
        
        // Processador (estimado baseado em socket comum)
        const cpu = state.selectedComponents['processador'];
        if (cpu) {
            // Estimativa: Intel/AMD modernos consomem entre 65W-125W
            total += 100; // Valor médio
        }
        
        // Placa de vídeo
        const gpu = state.selectedComponents['placa-video'];
        if (gpu) {
            const gpuWatts = parseInt(gpu.attributes['gpuWatts']) || 0;
            total += gpuWatts;
        }
        
        // Armazenamento
        const storage = state.selectedComponents['armazenamento'];
        if (storage) {
            total += 10; // SSD/HDD consomem pouco
        }
        
        return total;
    }

    // === FILTRAR PRODUTOS INCOMPATÍVEIS ===
    function filterIncompatibleProducts() {
        // Mostrar todos os produtos primeiro
        document.querySelectorAll('.product-card').forEach(card => {
            card.style.display = '';
            card.classList.remove('incompatible');
        });
        
        // Apenas filtrar se houver componentes selecionados
        const hasSelectedComponents = Object.keys(state.selectedComponents).length > 0;
        if (!hasSelectedComponents) {
            return; // Não filtrar nada se não houver seleções
        }
        
        // Para cada categoria, filtrar produtos incompatíveis
        state.categories.forEach(category => {
            const categoryGrid = document.getElementById(category);
            if (!categoryGrid) return;
            
            const productCards = categoryGrid.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                if (!isCompatible(card, category)) {
                    card.style.display = 'none';
                    card.classList.add('incompatible');
                }
            });
        });
    }

    // === AVANÇAR PARA PRÓXIMA CATEGORIA ===
    function advanceToNextCategory() {
        const currentIndex = state.categories.indexOf(state.currentCategory);
        const nextIndex = currentIndex + 1;
        
        if (nextIndex < state.categories.length) {
            // Avançar para a próxima categoria após um pequeno delay
            setTimeout(() => {
                switchToCategory(state.categories[nextIndex]);
            }, 500);
        } else {
            // Todas as categorias foram selecionadas
            finalizeButton.disabled = false;
            showNotification('Montagem completa! Clique em "Finalizar Montagem"', 'success');
        }
    }

    // === ATUALIZAR COMPONENTES SELECIONADOS ===
    function updateSelectedComponents() {
        if (!selectedList) return;
        
        const selectedCount = Object.keys(state.selectedComponents).length;
        
        if (selectedCount === 0) {
            selectedList.innerHTML = '<p class="empty-message">Nenhum componente selecionado ainda</p>';
            totalPrice.textContent = '0,00';
            return;
        }
        
        let html = '';
        let total = 0;
        
        state.categories.forEach(category => {
            const component = state.selectedComponents[category];
            if (component) {
                const priceValue = parseFloat(component.price.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                total += priceValue;
                
                html += `
                    <div class="selected-item">
                        <strong>${state.categoryNames[category]}:</strong>
                        <span>${component.name}</span>
                        <span class="item-price">${component.price}</span>
                    </div>
                `;
            }
        });
        
        selectedList.innerHTML = html;
        totalPrice.textContent = total.toFixed(2).replace('.', ',');
        
        // Habilitar botão finalizar se todos os componentes essenciais foram selecionados
        const essentialCategories = state.categories.filter(c => c !== 'perifericos');
        const essentialSelected = essentialCategories.filter(c => state.selectedComponents[c]).length;
        
        if (essentialSelected === essentialCategories.length) {
            finalizeButton.disabled = false;
        }
    }

    // === ATUALIZAR ESTADO DO BOTÃO ===
    function updateButtonState(productCard, selected) {
        const button = productCard.querySelector('.select-component');
        if (!button) return;
        
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

    // === OBTER ATRIBUTOS DO CARD ===
    function getAttributesFromCard(card) {
        const attrs = {};
        
        // Extrair todos os data-attributes
        Object.keys(card.dataset).forEach(key => {
            attrs[key] = card.dataset[key];
        });
        
        // Normalizar nomes de atributos
        const normalized = {};
        normalized['socket'] = attrs['socket'];
        normalized['ramType'] = attrs['ramType'] || attrs['ram-type'];
        normalized['ramSpeed'] = attrs['ramSpeed'] || attrs['ram-speed'];
        normalized['gpuWatts'] = attrs['gpuWatts'] || attrs['gpu-watts'];
        normalized['gpuLength'] = attrs['gpuLength'] || attrs['gpu-length'];
        normalized['psuWatts'] = attrs['psuWatts'] || attrs['psu-watts'];
        normalized['storageType'] = attrs['storageType'] || attrs['storage-type'];
        normalized['formFactor'] = attrs['formFactor'] || attrs['form-factor'];
        normalized['gpuMaxLength'] = attrs['gpuMaxLength'] || attrs['gpu-max-length'];
        normalized['m2'] = attrs['m2'];
        
        return normalized;
    }

    // === AJUSTAR ALTURA DO CONTAINER ===
    function adjustContainerHeight() {
        const activeGrid = document.querySelector('.component-grid.active');
        if (activeGrid && gridContainer) {
            setTimeout(() => {
                const height = activeGrid.offsetHeight;
                gridContainer.style.minHeight = `${Math.max(height, 400)}px`;
            }, 100);
        }
    }

    // === ATUALIZAR PROGRESSO ===
    function updateProgress() {
        if (!progressFill) return;
        
        const selectedCount = Object.keys(state.selectedComponents).length;
        const progress = (selectedCount / state.categories.length) * 100;
        progressFill.style.width = `${progress}%`;
    }

    // === FINALIZAR MONTAGEM ===
    finalizeButton.addEventListener('click', function() {
        const essentialCategories = state.categories.filter(c => c !== 'perifericos');
        const essentialSelected = essentialCategories.filter(c => state.selectedComponents[c]).length;
        
        if (essentialSelected < essentialCategories.length) {
            showNotification('Selecione todos os componentes essenciais antes de finalizar', 'error');
            return;
        }
        
        // Verificar compatibilidade final
        const compatibilityIssues = checkFinalCompatibility();
        if (compatibilityIssues.length > 0) {
            showNotification('Aviso: ' + compatibilityIssues.join(', '), 'warning');
        }
        
        // Criar resumo da montagem
        const buildSummary = {
            components: state.selectedComponents,
            totalPrice: totalPrice.textContent,
            date: new Date().toLocaleDateString('pt-BR'),
            compatibilityChecked: compatibilityIssues.length === 0
        };
        
        // Salvar no localStorage
        localStorage.setItem('buildSummary', JSON.stringify(buildSummary));
        
        // Adicionar ao carrinho
        addBuildToCart(buildSummary);
        
        // Mostrar modal de confirmação
        showBuildSummary(buildSummary);
    });

    // Verificar compatibilidade final
    function checkFinalCompatibility() {
        const issues = [];
        
        // Verificar socket CPU/Motherboard
        const cpu = state.selectedComponents['processador'];
        const mobo = state.selectedComponents['placa-mae'];
        if (cpu && mobo) {
            const cpuSocket = cpu.attributes['socket'];
            const moboSocket = mobo.attributes['socket'];
            if (cpuSocket && moboSocket && cpuSocket !== moboSocket) {
                issues.push('Processador e Placa Mãe têm sockets incompatíveis');
            }
        }
        
        // Verificar tipo de RAM
        const ram = state.selectedComponents['memoria-ram'];
        if (mobo && ram) {
            const moboRamType = mobo.attributes['ramType'];
            const ramType = ram.attributes['ramType'];
            if (moboRamType && ramType && moboRamType.toUpperCase() !== ramType.toUpperCase()) {
                issues.push('Memória RAM incompatível com Placa Mãe');
            }
        }
        
        // Verificar consumo de energia
        const fonte = state.selectedComponents['fonte'];
        if (fonte) {
            const totalConsumption = calculateTotalConsumption();
            const psuWatts = parseInt(fonte.attributes['psuWatts']) || 0;
            if (psuWatts < totalConsumption * 1.2) {
                issues.push('Fonte pode não ter potência suficiente');
            }
        }
        
        return issues;
    }

    // Adicionar montagem ao carrinho
    function addBuildToCart(buildSummary) {
        if (typeof window.carrinhoManager !== 'undefined') {
            Object.entries(buildSummary.components).forEach(([category, component]) => {
                const priceValue = parseFloat(component.price.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                window.carrinhoManager.adicionarProduto({
                    id: component.id.replace('product-', ''),
                    nome: component.name,
                    preco: priceValue,
                    imagem: component.image,
                    categoria: state.categoryNames[category],
                    marca: ''
                });
            });
        }
    }

    // === MOSTRAR RESUMO DA MONTAGEM ===
    function showBuildSummary(summary) {
        const modal = document.createElement('div');
        modal.className = 'build-summary-modal';
        modal.innerHTML = `
            <div class="modal-content" style="background: var(--background-card); border-radius: 12px; padding: 30px; max-width: 600px; max-height: 80vh; overflow-y: auto;">
                <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                    <h2 style="color: var(--text-primary); margin: 0;">Resumo da Montagem</h2>
                    <button class="close-modal" style="background: none; border: none; color: var(--text-primary); font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="summary-list" style="margin-bottom: 20px;">
                        ${Object.entries(summary.components).map(([category, component]) => `
                            <div class="summary-item" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                                <div>
                                    <strong style="color: var(--text-primary);">${state.categoryNames[category]}:</strong>
                                    <span style="color: var(--text-muted); margin-left: 10px;">${component.name}</span>
                                </div>
                                <span style="color: var(--primary-color); font-weight: 600;">${component.price}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div class="summary-total" style="text-align: right; margin-top: 20px; padding-top: 20px; border-top: 2px solid var(--primary-color);">
                        <strong style="color: var(--text-primary); font-size: 20px;">Total: R$ ${summary.totalPrice}</strong>
                    </div>
                </div>
                <div class="modal-footer" style="display: flex; gap: 10px; margin-top: 20px; justify-content: flex-end;">
                    <button class="btn btn-secondary" onclick="closeModal()" style="padding: 10px 20px;">Fechar</button>
                    <button class="btn btn-primary" onclick="saveBuild()" style="padding: 10px 20px;">Salvar e Adicionar ao Carrinho</button>
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
        showNotification('Montagem salva e adicionada ao carrinho!', 'success');
        closeModal();
        
        // Redirecionar para carrinho após 1 segundo
        setTimeout(() => {
            window.location.href = 'Carrinho.php';
        }, 1000);
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
            const card = button.closest('.product-card');
            if (card) {
                updateButtonState(card, false);
            }
        });
        
        // Voltar para primeira categoria
        switchToCategory('processador');
        
        // Atualizar lista
        updateSelectedComponents();
        
        // Desabilitar botão finalizar
        finalizeButton.disabled = true;
        
        showNotification('Montagem resetada', 'info');
    };

    // === SISTEMA DE NOTIFICAÇÕES ===
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
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
            background-color: ${colors[type] || colors.info};
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
});
