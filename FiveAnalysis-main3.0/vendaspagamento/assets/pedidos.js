/**
 * pedidos.js
 * Lógica para página de pedidos
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar
    inicializarPedidos();
    carregarItensPedidos();
    configurarFiltros();
});

function inicializarPedidos() {
    // Adicionar eventos aos cards
    const pedidoCards = document.querySelectorAll('.pedido-card');
    
    pedidoCards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Não redirecionar se clicou em um botão
            if (!e.target.closest('.btn-acao')) {
                const pedidoId = this.dataset.pedidoId;
                if (pedidoId) {
                    window.location.href = `PedidoDetalhes.php?id=${pedidoId}`;
                }
            }
        });
    });
}

async function carregarItensPedidos() {
    const pedidoCards = document.querySelectorAll('.pedido-card');
    
    for (const card of pedidoCards) {
        const pedidoId = card.dataset.pedidoId;
        
        if (pedidoId) {
            try {
                const response = await fetch(`../Controller/PedidoController.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=detalhes&pedido_id=${pedidoId}`
                });
                
                const resultado = await response.json();
                
                if (resultado.success && resultado.itens) {
                    atualizarItensPedido(card, resultado.itens);
                }
            } catch (error) {
                console.error('Erro ao carregar itens:', error);
            }
        }
    }
}

function atualizarItensPedido(card, itens) {
    const itensContainer = card.querySelector('.itens-lista');
    
    if (!itensContainer) return;
    
    // Limitar a 3 itens para visualização
    const itensLimitados = itens.slice(0, 3);
    
    itensContainer.innerHTML = '';
    
    itensLimitados.forEach(item => {
        const itemElement = document.createElement('div');
        itemElement.className = 'item-resumo';
        itemElement.innerHTML = `
            <div class="item-nome">${item.nome_produto}</div>
            <div class="item-quantidade">${item.quantidade}x</div>
        `;
        itensContainer.appendChild(itemElement);
    });
    
    // Mostrar contador se houver mais itens
    if (itens.length > 3) {
        const maisElement = document.createElement('div');
        maisElement.className = 'item-resumo';
        maisElement.innerHTML = `
            <div class="item-nome">+${itens.length - 3} mais</div>
            <div class="item-quantidade">Ver todos</div>
        `;
        itensContainer.appendChild(maisElement);
    }
}

function configurarFiltros() {
    const btnFiltrar = document.getElementById('btnFiltrar');
    
    if (btnFiltrar) {
        btnFiltrar.addEventListener('click', aplicarFiltros);
    }
    
    // Aplicar filtro ao pressionar Enter nos campos
    const filtros = document.querySelectorAll('.filtro-group input, .filtro-group select');
    filtros.forEach(filtro => {
        filtro.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                aplicarFiltros();
            }
        });
    });
}

function aplicarFiltros() {
    const status = document.getElementById('filtroStatus').value;
    const dataInicio = document.getElementById('filtroDataInicio').value;
    const dataFim = document.getElementById('filtroDataFim').value;
    
    // Resetar todos os filtros visuais primeiro
    const todosCards = document.querySelectorAll('.pedido-card');
    todosCards.forEach(card => {
        card.style.display = 'flex';
    });
    
    // Aplicar filtro de status
    if (status) {
        todosCards.forEach(card => {
            const statusCard = card.querySelector('.pedido-status').textContent.toLowerCase();
            if (statusCard !== status.toLowerCase()) {
                card.style.display = 'none';
            }
        });
    }
    
    // Aplicar filtro de data
    const cardsVisiveis = Array.from(todosCards).filter(card => card.style.display !== 'none');
    
    if (dataInicio) {
        cardsVisiveis.forEach(card => {
            const dataText = card.querySelector('.pedido-data').textContent;
            const match = dataText.match(/(\d{2})\/(\d{2})\/(\d{4})/);
            
            if (match) {
                const dataCard = `${match[3]}-${match[2]}-${match[1]}`;
                if (dataCard < dataInicio) {
                    card.style.display = 'none';
                }
            }
        });
    }
    
    if (dataFim) {
        const cardsAindaVisiveis = Array.from(cardsVisiveis).filter(card => card.style.display !== 'none');
        
        cardsAindaVisiveis.forEach(card => {
            const dataText = card.querySelector('.pedido-data').textContent;
            const match = dataText.match(/(\d{2})\/(\d{2})\/(\d{4})/);
            
            if (match) {
                const dataCard = `${match[3]}-${match[2]}-${match[1]}`;
                if (dataCard > dataFim) {
                    card.style.display = 'none';
                }
            }
        });
    }
    
    // Verificar se há cards visíveis
    const cardsFinaisVisiveis = Array.from(todosCards).filter(card => card.style.display !== 'none');
    
    if (cardsFinaisVisiveis.length === 0) {
        mostrarMensagemSemResultados();
    } else {
        esconderMensagemSemResultados();
    }
}

function mostrarMensagemSemResultados() {
    let mensagem = document.getElementById('mensagemSemResultados');
    
    if (!mensagem) {
        mensagem = document.createElement('div');
        mensagem.id = 'mensagemSemResultados';
        mensagem.className = 'sem-pedidos';
        mensagem.innerHTML = `
            <i class="bi bi-search"></i>
            <h3>Nenhum pedido encontrado</h3>
            <p>Tente ajustar os filtros de busca</p>
            <button onclick="limparFiltros()" class="btn" style="margin-top: 20px; display: inline-block;">
                <i class="bi bi-arrow-clockwise"></i> Limpar Filtros
            </button>
        `;
        
        const lista = document.getElementById('pedidosLista');
        if (lista) {
            lista.appendChild(mensagem);
        }
    } else {
        mensagem.style.display = 'block';
    }
}

function esconderMensagemSemResultados() {
    const mensagem = document.getElementById('mensagemSemResultados');
    if (mensagem) {
        mensagem.style.display = 'none';
    }
}

function limparFiltros() {
    document.getElementById('filtroStatus').value = '';
    document.getElementById('filtroDataInicio').value = '';
    document.getElementById('filtroDataFim').value = '';
    
    const todosCards = document.querySelectorAll('.pedido-card');
    todosCards.forEach(card => {
        card.style.display = 'flex';
    });
    
    esconderMensagemSemResultados();
}

function verDetalhesPedido(pedidoId) {
    window.location.href = `PedidoDetalhes.php?id=${pedidoId}`;
}

function cancelarPedido(pedidoId) {
    if (confirm('Tem certeza que deseja cancelar este pedido? Esta ação não pode ser desfeita.')) {
        const motivo = prompt('Por favor, informe o motivo do cancelamento:');
        
        if (motivo === null) return;
        
        fetch('../Controller/PedidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=cancelar&pedido_id=${pedidoId}&motivo=${encodeURIComponent(motivo)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pedido cancelado com sucesso!');
                window.location.reload();
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao cancelar pedido');
        });
    }
}

function rastrearPedido(codigoRastreio) {
    // Em produção, integrar com API de rastreio (Correios, etc.)
    const janela = window.open('', '_blank');
    janela.document.write(`
        <html>
        <head>
            <title>Rastreio - Pedido ${codigoRastreio}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .container { max-width: 600px; margin: 0 auto; }
                .status { padding: 15px; border-radius: 8px; margin: 10px 0; }
                .entregue { background: #d1fae5; border: 1px solid #10b981; }
                .transito { background: #dbeafe; border: 1px solid #3b82f6; }
                .postado { background: #fef3c7; border: 1px solid #f59e0b; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Rastreamento de Envio</h1>
                <p><strong>Código:</strong> ${codigoRastreio}</p>
                <p><strong>Transportadora:</strong> Correios</p>
                
                <h2>Status do Envio</h2>
                
                <div class="status entregue">
                    <strong>✔ Entregue</strong><br>
                    <small>25/12/2023 14:30</small><br>
                    Entregue ao destinatário
                </div>
                
                <div class="status transito">
                    <strong>↻ Em Trânsito</strong><br>
                    <small>24/12/2023 09:15</small><br>
                    Saiu para entrega
                </div>
                
                <div class="status postado">
                    <strong>📦 Postado</strong><br>
                    <small>23/12/2023 16:45</small><br>
                    Objeto postado na agência
                </div>
                
                <p style="margin-top: 20px; font-size: 12px; color: #666;">
                    <em>Nota: Esta é uma simulação. Em produção, esta tela integraria 
                    com a API dos Correios ou outra transportadora.</em>
                </p>
            </div>
        </body>
        </html>
    `);
    janela.document.close();
}

// Exportar relatório (se implementado)
function exportarRelatorio(formato = 'pdf') {
    // Em produção, implementar geração de relatório
    alert(`Exportando relatório em ${formato.toUpperCase()}...\n\nEm produção, esta função geraria um arquivo para download.`);
}