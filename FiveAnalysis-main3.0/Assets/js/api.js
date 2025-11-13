/**
 * Cliente API para FiveAnalysis
 * Conecta o frontend com o backend PHP
 */

class FiveAnalysisAPI {
    constructor() {
        // Backend REST ainda não implementado neste projeto PHP puro.
        // Mantemos a classe para futura expansão e evitamos chamadas inválidas.
        this.baseURL = '';
        this.token = localStorage.getItem('fiveanalysis_token');
    }

    /**
     * Fazer requisição HTTP
     */
    async request(endpoint, options = {}) {
        if (!this.baseURL) {
            throw new Error('API não disponível no ambiente atual');
        }
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };

        // Adicionar token de autenticação se disponível
        if (this.token) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Erro na requisição');
            }

            return data;
        } catch (error) {
            console.error('Erro na API:', error);
            throw error;
        }
    }

    /**
     * Autenticação
     */
    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (response.success) {
            this.token = response.data.access_token;
            localStorage.setItem('fiveanalysis_token', this.token);
            localStorage.setItem('fiveanalysis_user', JSON.stringify(response.data.user));
        }

        return response;
    }

    async register(nome, email, password) {
        const response = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify({ nome, email, password })
        });

        if (response.success) {
            this.token = response.data.access_token;
            localStorage.setItem('fiveanalysis_token', this.token);
            localStorage.setItem('fiveanalysis_user', JSON.stringify(response.data.user));
        }

        return response;
    }

    logout() {
        this.token = null;
        localStorage.removeItem('fiveanalysis_token');
        localStorage.removeItem('fiveanalysis_user');
    }

    async verifyToken() {
        if (!this.token) {
            return false;
        }

        try {
            const response = await this.request('/auth/verify');
            return response.success;
        } catch (error) {
            this.logout();
            return false;
        }
    }

    /**
     * Produtos
     */
    async getProducts(filters = {}) {
        const params = new URLSearchParams();
        
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== '') {
                params.append(key, filters[key]);
            }
        });

        const endpoint = `/products${params.toString() ? '?' + params.toString() : ''}`;
        return await this.request(endpoint);
    }

    async getProduct(id) {
        return await this.request(`/products/${id}`);
    }

    async getCategories() {
        return await this.request('/products/categories');
    }

    async getBrands() {
        return await this.request('/products/brands');
    }

    async getPromotionalProducts(limit = 10) {
        return await this.request(`/products/promotional?limit=${limit}`);
    }

    async getTopRatedProducts(limit = 10) {
        return await this.request(`/products/top-rated?limit=${limit}`);
    }

    async getBestSellingProducts(limit = 10) {
        return await this.request(`/products/best-selling?limit=${limit}`);
    }

    async getProductsByCategory(category, page = 1, limit = 20) {
        return await this.request(`/products/${category}?page=${page}&limit=${limit}`);
    }

    /**
     * Montagens
     */
    async getBuilds(page = 1, limit = 20) {
        return await this.request(`/builds?page=${page}&limit=${limit}`);
    }

    async getBuild(id) {
        return await this.request(`/builds/${id}`);
    }

    async createBuild(nomeMontagem, componentes = []) {
        return await this.request('/builds', {
            method: 'POST',
            body: JSON.stringify({
                nome_montagem: nomeMontagem,
                componentes: componentes
            })
        });
    }

    async updateBuild(id, data) {
        return await this.request(`/builds/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteBuild(id) {
        return await this.request(`/builds/${id}`, {
            method: 'DELETE'
        });
    }

    async addComponentToBuild(buildId, produtoId) {
        return await this.request(`/builds/${buildId}/components`, {
            method: 'POST',
            body: JSON.stringify({ produto_id: produtoId })
        });
    }

    async removeComponentFromBuild(buildId, produtoId) {
        return await this.request(`/builds/${buildId}/components`, {
            method: 'DELETE',
            body: JSON.stringify({ produto_id: produtoId })
        });
    }

    async checkBuildCompatibility(buildId) {
        return await this.request(`/builds/${buildId}/compatibility`);
    }

    async duplicateBuild(buildId, novoNome = null) {
        return await this.request(`/builds/${buildId}/duplicate`, {
            method: 'POST',
            body: JSON.stringify({ nome: novoNome })
        });
    }

    async getPublicBuilds(page = 1, limit = 20, filters = {}) {
        const params = new URLSearchParams();
        params.append('page', page);
        params.append('limit', limit);
        
        Object.keys(filters).forEach(key => {
            if (filters[key] !== null && filters[key] !== '') {
                params.append(key, filters[key]);
            }
        });

        return await this.request(`/builds/public?${params.toString()}`);
    }

    async getPopularBuilds(limit = 10) {
        return await this.request(`/builds/popular?limit=${limit}`);
    }

    /**
     * Utilitários
     */
    async healthCheck() {
        return await this.request('/health');
    }

    isAuthenticated() {
        return !!this.token;
    }

    getCurrentUser() {
        const userStr = localStorage.getItem('fiveanalysis_user');
        return userStr ? JSON.parse(userStr) : null;
    }

    /**
     * Notificações
     */
    showNotification(message, type = 'info') {
        // Implementar sistema de notificações
        console.log(`[${type.toUpperCase()}] ${message}`);
        
        // Exemplo de notificação visual
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            z-index: 10000;
            animation: slideIn 0.3s ease;
        `;

        // Cores por tipo
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };
        notification.style.backgroundColor = colors[type] || colors.info;

        document.body.appendChild(notification);

        // Remover após 5 segundos
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}

// Instância global da API
window.FiveAnalysisAPI = new FiveAnalysisAPI();

// CSS para animações das notificações
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Verificar autenticação ao carregar
document.addEventListener('DOMContentLoaded', async () => {
    if (window.FiveAnalysisAPI.isAuthenticated()) {
        const isValid = await window.FiveAnalysisAPI.verifyToken();
        if (!isValid) {
            window.FiveAnalysisAPI.logout();
        }
    }
});
?>
