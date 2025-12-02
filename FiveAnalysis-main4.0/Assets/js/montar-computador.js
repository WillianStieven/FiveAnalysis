// === SISTEMA DE COMPATIBILIDADE DE HARDWARE ===

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

    // Verificar elementos DOM
    if (!categoryNav || !categoryTitle || !progressFill || !selectedList || 
        !totalPrice || !finalizeButton || !selectedPanel || !toggleButton || 
        !componentSelection || !gridContainer) {
        console.error('Elementos DOM não encontrados');
        return;
    }

    // Inicialização
    init();

    function init() {
        setupCategoryNavigation();
        setupComponentSelection();
        setupPanelToggle();
        updateProgress();
        updateSelectedComponents();
        adjustContainerHeight();
    }

    // === SISTEMA DE COMPATIBILIDADE ===
    function checkCompatibility(category) {
        const allCards = document.querySelectorAll(`#${category} .product-card`);
        let compatibleCount = 0;
        let incompatibleCount = 0;

        allCards.forEach(card => {
            let isCompatible = true;
            let incompatibilityReasons = [];

            // Verificar compatibilidade baseado na categoria
            switch(category) {
                case 'placa-mae':
                    isCompatible = checkMotherboardCompatibility(card, incompatibilityReasons);
                    break;
                case 'memoria-ram':
                    isCompatible = checkRAMCompatibility(card, incompatibilityReasons);
                    break;
                case 'placa-video':
                    isCompatible = checkGPUCompatibility(card, incompatibilityReasons);
                    break;
                case 'armazenamento':
                    isCompatible = checkStorageCompatibility(card, incompatibilityReasons);
                    break;
                case 'gabinete':
                    isCompatible = checkCaseCompatibility(card, incompatibilityReasons);
                    break;
                case 'fonte':
                    isCompatible = checkPSUCompatibility(card, incompatibilityReasons);
                    break;
            }

            // Aplicar estilo visual
            if (isCompatible) {
                card.classList.remove('incompatible');
                card.classList.add('compatible');
                compatibleCount++;
                removeIncompatibilityWarning(card);
            } else {
                card.classList.remove('compatible');
                card.classList.add('incompatible');
                incompatibleCount++;
                addIncompatibilityWarning(card, incompatibilityReasons);
            }
        });

        // Mostrar resumo de compatibilidade
        showCompatibilitySummary(category, compatibleCount, incompatibleCount);
    }

    // === VERIFICAÇÕES DE COMPATIBILIDADE ===

    function checkMotherboardCompatibility(card, reasons) {
        const processor = state.selectedComponents['processador'];
        if (!processor) return true;

        const processorSocket = processor.attributes['data-socket'];
        const motherboardSocket = card.getAttribute('data-socket');

        if (processorSocket && motherboardSocket) {
            if (processorSocket !== motherboardSocket) {
                reasons.push(`Socket incompatível: precisa ${processorSocket}, tem ${motherboardSocket}`);
                return false;
            }
        }

        return true;
    }

    function checkRAMCompatibility(card, reasons) {
        const motherboard = state.selectedComponents['placa-mae'];
        if (!motherboard) return true;

        const motherboardRamType = motherboard.attributes['data-ram-type'] || 
                                   motherboard.attributes['data-ramType'];
        const ramType = card.getAttribute('data-ram-type') || 
                       card.getAttribute('data-ramType');

        if (motherboardRamType && ramType) {
            if (motherboardRamType.toUpperCase() !== ramType.toUpperCase()) {
                reasons.push(`Tipo incompatível: placa suporta ${motherboardRamType}, RAM é ${ramType}`);
                return false;
            }
        }

        return true;
    }

    function checkGPUCompatibility(card, reasons) {
        let isCompatible = true;

        // Verificar fonte de alimentação
        const psu = state.selectedComponents['fonte'];
        if (psu) {
            const gpuWatts = parseInt(card.getAttribute('data-gpu-watts') || 
                                     card.getAttribute('data-gpuWatts') || '0');
            const psuWatts = parseInt(psu.attributes['data-psu-watts'] || 
                                     psu.attributes['data-psuWatts'] || '0');

            // Regra: PSU deve ter pelo menos 150W a mais que o consumo da GPU
            const minRequiredPSU = gpuWatts + 150;
            if (psuWatts > 0 && gpuWatts > 0 && psuWatts < minRequiredPSU) {
                reasons.push(`Fonte insuficiente: GPU precisa ~${minRequiredPSU}W, fonte tem ${psuWatts}W`);
                isCompatible = false;
            }
        }

        // Verificar gabinete
        const gabinete = state.selectedComponents['gabinete'];
        if (gabinete) {
            const gpuLength = parseInt(card.getAttribute('data-gpu-length') || 
                                      card.getAttribute('data-gpuLength') || '0');
            const maxGpuLength = parseInt(gabinete.attributes['data-gpu-max-length'] || 
                                         gabinete.attributes['data-gpuMaxLength'] || '999');

            if (gpuLength > 0 && gpuLength > maxGpuLength) {
                reasons.push(`GPU muito grande: ${gpuLength}mm, gabinete suporta até ${maxGpuLength}mm`);
                isCompatible = false;
            }
        }

        return isCompatible;
    }

    function checkStorageCompatibility(card, reasons) {
        const motherboard = state.selectedComponents['placa-mae'];
        if (!motherboard) return true;

        const storageType = card.getAttribute('data-storage-type') || 
                           card.getAttribute('data-storageType');
        const hasM2Support = motherboard.attributes['data-m2'];

        // Se for NVME/M.2 e a placa não tem suporte M.2
        if (storageType && storageType.toUpperCase() === 'NVME' && hasM2Support !== 'true') {
            reasons.push('Placa-mãe não tem slot M.2 para este SSD NVMe');
            return false;
        }

        return true;
    }

    function checkCaseCompatibility(card, reasons) {
        const gpu = state.selectedComponents['placa-video'];
        if (!gpu) return true;

        const gpuLength = parseInt(gpu.attributes['data-gpu-length'] || 
                                  gpu.attributes['data-gpuLength'] || '0');
        const maxGpuLength = parseInt(card.getAttribute('data-gpu-max-length') || 
                                     card.getAttribute('data-gpuMaxLength') || '999');

        if (gpuLength > 0 && gpuLength > maxGpuLength) {
            reasons.push(`GPU selecionada (${gpuLength}mm) não cabe neste gabinete (máx: ${maxGpuLength}mm)`);
            return false;
        }

        return true;
    }

    function checkPSUCompatibility(card, reasons) {
        const gpu = state.selectedComponents['placa-video'];
        if (!gpu) return true;

        const gpuWatts = parseInt(gpu.attributes['data-gpu-watts'] || 
                                 gpu.attributes['data-gpuWatts'] || '0');
        const psuWatts = parseInt(card.getAttribute('data-psu-watts') || 
                                 card.getAttribute('data-psuWatts') || '0');

        const minRequiredPSU = gpuWatts + 150;
        if (psuWatts > 0 && gpuWatts > 0 && psuWatts < minRequiredPSU) {
            reasons.push(`Potência insuficiente: recomendado ${minRequiredPSU}W para esta GPU`);
            return false;
        }

        return true;
    }

    // === AVISOS VISUAIS ===

    function addIncompatibilityWarning(card, reasons) {
        removeIncompatibilityWarning(card);

        const warning = document.createElement('div');
        warning.className = 'incompatibility-warning';
        warning.innerHTML = `
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Incompatível:</strong>
            <ul>
                ${reasons.map(r => `<li>${r}</li>`).join('')}
            </ul>
        `;

        card.appendChild(warning);

        // Desabilitar botão de seleção
        const button = card.querySelector('.select-component');
        if (button && !button.disabled) {
            button.disabled = true;
            button.style.opacity = '0.5';
            button.style.cursor = 'not-allowed';
        }
    }

    function removeIncompatibilityWarning(card) {
        const existing = card.querySelector('.incompatibility-warning');
        if (existing) {
            existing.remove();
        }

        // Reabilitar botão
        const button = card.querySelector('.select-component');
        if (button && button.disabled && !button.classList.contains('btn-secondary')) {
            button.disabled = false;
            button.style.opacity = '1';
            button.style.cursor = 'pointer';
        }
    }

    function showCompatibilitySummary(category, compatible, incompatible) {
        if (incompatible === 0) return;

        const total = compatible + incompatible;
        const message = `${compatible} de ${total} produtos compatíveis com sua configuração`;

        // Criar notificação informativa
        const notification = document.createElement('div');
        notification.className = 'compatibility-notification';
        notification.innerHTML = `
            <i class="bi bi-info-circle"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 80px;
            right: 20px;
            background: #3b82f6;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInRight 0.3s ease;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
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
        state.currentCategory = categoryId;
        
        // Atualizar navegação
        document.querySelectorAll('.component-category').forEach(cat => {
            cat.classList.remove('active');
        });
        const categoryElement = document.querySelector(`[data-category="${categoryId}"]`);
        if (categoryElement) {
            categoryElement.classList.add('active');
        }
        
        // Atualizar grid - ESCONDER TODOS e mostrar apenas o ativo
        document.querySelectorAll('.component-grid').forEach(grid => {
            grid.classList.remove('active');
            grid.style.display = 'none'; // Esconder completamente
        });
        const gridElement = document.getElementById(categoryId);
        if (gridElement) {
            gridElement.classList.add('active');
            gridElement.style.display = 'grid'; // Mostrar apenas o ativo
        }
        
        // IMPORTANTE: Verificar compatibilidade ao mudar de categoria
        checkCompatibility(categoryId);
        
        adjustContainerHeight();
        categoryTitle.textContent = `Selecione o ${state.categoryNames[categoryId]}`;
        updateProgress();
        
        componentSelection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // === SELEÇÃO DE COMPONENTES ===

    function setupComponentSelection() {
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
        const productName = productCard.querySelector('h3')?.textContent || 'Produto';
        const productPrice = productCard.querySelector('.price')?.textContent || 'R$ 0,00';
        const productImage = productCard.querySelector('img')?.src || '';
        const productPriceValue = parseFloat(productPrice.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
        
        // Extrair atributos de compatibilidade
        const attributes = {};
        ['data-socket', 'data-ram-type', 'data-ramType', 'data-ram-speed', 'data-ramSpeed',
         'data-gpu-watts', 'data-gpuWatts', 'data-gpu-length', 'data-gpuLength',
         'data-psu-watts', 'data-psuWatts', 'data-storage-type', 'data-storageType',
         'data-gpu-max-length', 'data-gpuMaxLength', 'data-m2', 'data-brand'].forEach(attr => {
            const value = productCard.getAttribute(attr);
            if (value) attributes[attr] = value;
        });
        
        // Armazenar componente com atributos
        state.selectedComponents[category] = {
            id: productId.replace('product-', ''),
            name: productName,
            price: productPrice,
            priceValue: productPriceValue,
            image: productImage,
            attributes: attributes
        };
        
        updateButtonState(productCard, true);
        
        // Desmarcar outros produtos
        const categoryGrid = document.getElementById(category);
        if (categoryGrid) {
            categoryGrid.querySelectorAll('.product-card').forEach(card => {
                if (card.id !== productId) {
                    updateButtonState(card, false);
                }
            });
        }
        
        updateSelectedComponents();
        
        // Verificar compatibilidade das próximas categorias
        const currentIndex = state.categories.indexOf(category);
        for (let i = currentIndex + 1; i < state.categories.length; i++) {
            checkCompatibility(state.categories[i]);
        }
        
        advanceToNextCategory();
        showNotification(`${productName} selecionado!`, 'success');
        addToCartFromSelection(category, productId, productName, productPriceValue, productImage);
    }

    function addToCartFromSelection(category, productId, productName, productPrice, productImage) {
        if (typeof window.carrinhoManager !== 'undefined') {
            const categoriaNome = state.categoryNames[category] || '';
            window.carrinhoManager.adicionarProduto({
                id: productId.replace('product-', ''),
                nome: productName,
                preco: productPrice,
                imagem: productImage,
                categoria: categoriaNome,
                marca: ''
            });
        }
    }

    function advanceToNextCategory() {
        const currentIndex = state.categories.indexOf(state.currentCategory);
        const nextIndex = currentIndex + 1;
        
        if (nextIndex < state.categories.length) {
            setTimeout(() => {
                switchToCategory(state.categories[nextIndex]);
            }, 500);
        } else {
            finalizeButton.disabled = false;
            showNotification('Montagem completa! Clique em "Finalizar Montagem"', 'success');
        }
    }

    // === ATUALIZAR INTERFACE ===

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
                total += component.priceValue;
                
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
        
        const essentialCategories = state.categories.filter(c => c !== 'perifericos');
        const essentialSelected = essentialCategories.filter(c => state.selectedComponents[c]).length;
        
        if (essentialSelected === essentialCategories.length) {
            finalizeButton.disabled = false;
        }
    }

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

    function setupPanelToggle() {
        let isPanelVisible = true;
        
        toggleButton.addEventListener('click', function() {
            isPanelVisible = !isPanelVisible;
            selectedPanel.style.display = isPanelVisible ? 'block' : 'none';
        });
    }

    function adjustContainerHeight() {
        const activeGrid = document.querySelector('.component-grid.active');
        if (activeGrid && gridContainer) {
            setTimeout(() => {
                const height = activeGrid.offsetHeight;
                gridContainer.style.minHeight = `${Math.max(height, 400)}px`;
            }, 100);
        }
    }

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
        
        const buildSummary = {
            components: state.selectedComponents,
            totalPrice: totalPrice.textContent,
            date: new Date().toLocaleDateString('pt-BR')
        };
        
        localStorage.setItem('buildSummary', JSON.stringify(buildSummary));
        showBuildSummary(buildSummary);
    });

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
                    <button class="btn btn-primary" onclick="saveBuild()" style="padding: 10px 20px;">Ver Carrinho</button>
                </div>
            </div>
        `;
        
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
        
        modal.querySelector('.close-modal').addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });
    }

    window.closeModal = function() {
        const modal = document.querySelector('.build-summary-modal');
        if (modal) modal.remove();
    };

    window.saveBuild = function() {
        showNotification('Redirecionando para o carrinho...', 'success');
        closeModal();
        setTimeout(() => {
            window.location.href = 'Carrinho.php';
        }, 1000);
    };

    window.resetBuild = function() {
        state.selectedComponents = {};
        state.currentCategory = 'processador';
        
        document.querySelectorAll('.select-component').forEach(button => {
            const card = button.closest('.product-card');
            if (card) updateButtonState(card, false);
        });
        
        switchToCategory('processador');
        updateSelectedComponents();
        finalizeButton.disabled = true;
        showNotification('Montagem resetada', 'info');
    };

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

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
});