// === NAVEGAÇÃO ENTRE PÁGINAS ===
document.addEventListener('DOMContentLoaded', function() {
    // Função para navegar entre páginas
    function navigateToPage(page) {
        window.location.href = page;
    }

    // Handlers para links de navegação
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            if (href === '#') return;
            
            // Adicionar efeito de loading
            this.style.opacity = '0.7';
            
            setTimeout(() => {
                navigateToPage(href);
            }, 200);
        });
    });

    // === HANDLERS PARA PRODUTOS ===
    const produtos = document.querySelectorAll('.produto');
    produtos.forEach(produto => {
        produto.addEventListener('click', function() {
            // Adicionar efeito visual
            this.style.transform = 'scale(0.95)';
            this.style.transition = 'transform 0.1s ease';
            
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 100);
            
            // Navegar para a página do produto após um pequeno delay
            setTimeout(() => {
                navigateToPage('Shop_page.html');
            }, 300);
        });
    });

    // === CARROSSEL DE PRODUTOS ===
    const carousel = document.querySelector('.carousel');
    const produtosContainer = document.querySelector('.produtos-container');
    const setas = document.querySelectorAll('.seta');
    
    if (produtosContainer && setas.length > 0) {
        const scrollAmount = 280; // Largura do produto + gap
        
        setas.forEach((seta, index) => {
            seta.addEventListener('click', function() {
                const direction = index === 0 ? -scrollAmount : scrollAmount;
                produtosContainer.scrollBy({
                    left: direction,
                    behavior: 'smooth'
                });
            });
        });

        // Auto-scroll do carrossel
        let autoScrollInterval;
        
        function startAutoScroll() {
            autoScrollInterval = setInterval(() => {
                produtosContainer.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
                
                // Resetar scroll quando chegar ao final
                if (produtosContainer.scrollLeft >= produtosContainer.scrollWidth - produtosContainer.clientWidth) {
                    setTimeout(() => {
                        produtosContainer.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    }, 1000);
                }
            }, 5000);
        }

        function stopAutoScroll() {
            clearInterval(autoScrollInterval);
        }

        // Pausar auto-scroll quando hover
        carousel.addEventListener('mouseenter', stopAutoScroll);
        carousel.addEventListener('mouseleave', startAutoScroll);
        
        // Iniciar auto-scroll
        startAutoScroll();
    }

    // === ANIMAÇÕES DE ENTRADA ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observar elementos para animação
    const animatedElements = document.querySelectorAll('.produto, .info-card, .rating-card');
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    // === FUNCIONALIDADES DO HEADER ===
    const searchIcon = document.querySelector('.icons .bi-search');
    const cartIcon = document.querySelector('.icons .bi-cart2');
    
    if (searchIcon) {
        searchIcon.addEventListener('click', function() {
            // Implementar funcionalidade de busca
            const searchTerm = prompt('Digite o que você está procurando:');
            if (searchTerm) {
                console.log('Buscar por:', searchTerm);
                // Aqui você pode implementar a lógica de busca
            }
        });
    }

    if (cartIcon) {
        cartIcon.addEventListener('click', function() {
            // Redirecionar para a página do carrinho
            window.location.href = '../View/Carrinho.php';
        });
    }
    
    // Atualizar badge do carrinho
    function updateCartBadge() {
        const cartJson = localStorage.getItem('cart');
        const cartBadges = document.querySelectorAll('#cartBadge, .cart-badge');
        
        cartBadges.forEach(cartBadge => {
            if (cartBadge) {
                if (cartJson) {
                    try {
                        const cart = JSON.parse(cartJson);
                        const count = cart.length;
                        cartBadge.textContent = count;
                        if (count > 0) {
                            cartBadge.style.display = 'flex';
                        } else {
                            cartBadge.style.display = 'none';
                        }
                    } catch (e) {
                        cartBadge.style.display = 'none';
                    }
                } else {
                    cartBadge.style.display = 'none';
                }
            }
        });
    }
    
    // Atualizar badge ao carregar
    updateCartBadge();
    
    // Atualizar badge quando o carrinho mudar (evento customizado)
    window.addEventListener('cartUpdated', function() {
        updateCartBadge();
    });

    // === EFEITOS DE HOVER ===
    const cards = document.querySelectorAll('.card, .produto, .component-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // === SCROLL SUAVE ===
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // === LOADING SCREEN ===
    window.addEventListener('load', function() {
        const loader = document.querySelector('.loader');
        if (loader) {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 500);
        }
    });

    // === NOTIFICAÇÕES ===
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // === FUNÇÕES UTILITÁRIAS ===
    window.FiveAnalysis = {
        navigateToPage,
        showNotification,
        // Adicionar mais funções conforme necessário
    };
});

// === FUNÇÕES GLOBAIS ===
function addToCart(productId) {
    // Implementar lógica de adicionar ao carrinho
    console.log('Adicionando produto ao carrinho:', productId);
    if (window.FiveAnalysis) {
        window.FiveAnalysis.showNotification('Produto adicionado ao carrinho!', 'success');
    }
}

function addToFavorites(productId) {
    // Implementar lógica de adicionar aos favoritos
    console.log('Adicionando produto aos favoritos:', productId);
    if (window.FiveAnalysis) {
        window.FiveAnalysis.showNotification('Produto adicionado aos favoritos!', 'success');
    }
}

function viewProduct(productId) {
    // Navegar para a página do produto
    window.location.href = `Shop_page.html?id=${productId}`;
}
