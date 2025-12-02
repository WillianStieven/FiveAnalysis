/**
 * checkout.js
 * Lógica para página de checkout
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar
    inicializarCheckout();
    configurarEventos();
    validarCarrinho();
});

function inicializarCheckout() {
    // Formatar campos de entrada
    formatarCampos();
    
    // Configurar seleção de método de pagamento
    configurarMetodosPagamento();
    
    // Buscar endereço via CEP
    configurarBuscaCEP();
    
    // Validar formulário em tempo real
    configurarValidacaoTempoReal();
}

function configurarEventos() {
    // Envio do formulário
    const formCheckout = document.getElementById('checkoutForm');
    if (formCheckout) {
        formCheckout.addEventListener('submit', processarCheckout);
    }
    
    // Botão de finalizar
    const btnFinalizar = document.getElementById('btnFinalizar');
    if (btnFinalizar) {
        btnFinalizar.addEventListener('click', function(e) {
            if (!validarFormulario()) {
                e.preventDefault();
                mostrarErro('Por favor, preencha todos os campos obrigatórios corretamente.');
            }
        });
    }
}

function configurarMetodosPagamento() {
    const metodos = document.querySelectorAll('.payment-method');
    
    metodos.forEach(metodo => {
        metodo.addEventListener('click', function() {
            // Remover seleção anterior
            metodos.forEach(m => m.classList.remove('selected'));
            
            // Selecionar atual
            this.classList.add('selected');
            
            // Atualizar radio button
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
                atualizarDetalhesPagamento(radio.value);
            }
        });
    });
}

function atualizarDetalhesPagamento(metodo) {
    const cartaoDetalhes = document.getElementById('cartaoDetalhes');
    
    if (metodo === 'cartao_credito') {
        cartaoDetalhes.classList.add('active');
    } else {
        cartaoDetalhes.classList.remove('active');
    }
}

function configurarBuscaCEP() {
    const inputCEP = document.getElementById('cep');
    
    if (inputCEP) {
        inputCEP.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                buscarEnderecoPorCEP(cep);
            }
        });
        
        // Formatar CEP
        inputCEP.addEventListener('input', function() {
            let valor = this.value.replace(/\D/g, '');
            
            if (valor.length > 5) {
                valor = valor.substring(0, 5) + '-' + valor.substring(5, 8);
            }
            
            this.value = valor;
        });
    }
}

async function buscarEnderecoPorCEP(cep) {
    try {
        mostrarCarregamento('Buscando endereço...');
        
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();
        
        if (!data.erro) {
            document.getElementById('endereco').value = data.logradouro || '';
            document.getElementById('bairro').value = data.bairro || '';
            document.getElementById('cidade').value = data.localidade || '';
            document.getElementById('estado').value = data.uf || '';
            
            if (data.logradouro) {
                document.getElementById('numero').focus();
            }
            
            mostrarSucesso('Endereço encontrado!');
        } else {
            mostrarErro('CEP não encontrado.');
        }
    } catch (error) {
        console.error('Erro ao buscar CEP:', error);
        mostrarErro('Erro ao buscar CEP. Tente novamente.');
    } finally {
        esconderCarregamento();
    }
}

function formatarCampos() {
    // Formatar telefone
    const telefone = document.getElementById('telefone');
    if (telefone) {
        telefone.addEventListener('input', function() {
            let valor = this.value.replace(/\D/g, '');
            
            if (valor.length > 10) {
                valor = '(' + valor.substring(0, 2) + ') ' + 
                        valor.substring(2, 7) + '-' + 
                        valor.substring(7, 11);
            } else if (valor.length > 6) {
                valor = '(' + valor.substring(0, 2) + ') ' + 
                        valor.substring(2, 6) + '-' + 
                        valor.substring(6, 10);
            } else if (valor.length > 2) {
                valor = '(' + valor.substring(0, 2) + ') ' + valor.substring(2);
            } else if (valor.length > 0) {
                valor = '(' + valor;
            }
            
            this.value = valor;
        });
    }
    
    // Formatar número do cartão
    const numeroCartao = document.getElementById('numero_cartao');
    if (numeroCartao) {
        numeroCartao.addEventListener('input', function() {
            let valor = this.value.replace(/\D/g, '');
            
            if (valor.length > 0) {
                valor = valor.match(/.{1,4}/g).join(' ');
            }
            
            this.value = valor.substring(0, 19);
        });
    }
    
    // Formatar validade do cartão
    const validadeCartao = document.getElementById('validade_cartao');
    if (validadeCartao) {
        validadeCartao.addEventListener('input', function() {
            let valor = this.value.replace(/\D/g, '');
            
            if (valor.length >= 2) {
                valor = valor.substring(0, 2) + '/' + valor.substring(2, 4);
            }
            
            this.value = valor;
        });
    }
}

function validarFormulario() {
    // Campos obrigatórios
    const camposObrigatorios = [
        'cep', 'endereco', 'numero', 'bairro', 'cidade', 'estado',
        'nome_completo', 'telefone', 'email', 'metodo_pagamento'
    ];
    
    let valido = true;
    
    camposObrigatorios.forEach(campo => {
        const elemento = document.querySelector(`[name="${campo}"]`);
        
        if (elemento && !elemento.value.trim()) {
            marcarInvalido(elemento);
            valido = false;
        } else if (elemento) {
            marcarValido(elemento);
        }
    });
    
    // Validar email
    const email = document.getElementById('email');
    if (email && email.value) {
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regexEmail.test(email.value)) {
            marcarInvalido(email);
            valido = false;
        }
    }
    
    // Validar termos
    const termos = document.getElementById('termos');
    if (termos && !termos.checked) {
        marcarInvalido(termos);
        valido = false;
    } else if (termos) {
        marcarValido(termos);
    }
    
    // Validar cartão se selecionado
    const metodoSelecionado = document.querySelector('input[name="metodo_pagamento"]:checked');
    if (metodoSelecionado && metodoSelecionado.value === 'cartao_credito') {
        const camposCartao = ['numero_cartao', 'nome_cartao', 'validade_cartao', 'cvv_cartao'];
        
        camposCartao.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento && !elemento.value.trim()) {
                marcarInvalido(elemento);
                valido = false;
            } else if (elemento) {
                marcarValido(elemento);
            }
        });
        
        // Validar validade do cartão
        const validade = document.getElementById('validade_cartao');
        if (validade && validade.value) {
            const regexValidade = /^(0[1-9]|1[0-2])\/([0-9]{2})$/;
            if (!regexValidade.test(validade.value)) {
                marcarInvalido(validade);
                valido = false;
            }
        }
    }
    
    return valido;
}

function configurarValidacaoTempoReal() {
    const campos = document.querySelectorAll('input, select, textarea');
    
    campos.forEach(campo => {
        campo.addEventListener('blur', function() {
            if (this.value.trim()) {
                marcarValido(this);
            } else {
                marcarInvalido(this);
            }
        });
    });
}

function marcarValido(elemento) {
    elemento.style.borderColor = '#10b981';
    elemento.style.boxShadow = '0 0 0 1px rgba(16, 185, 129, 0.2)';
}

function marcarInvalido(elemento) {
    elemento.style.borderColor = '#ef4444';
    elemento.style.boxShadow = '0 0 0 1px rgba(239, 68, 68, 0.2)';
}

async function processarCheckout(event) {
    event.preventDefault();
    
    if (!validarFormulario()) {
        mostrarErro('Por favor, preencha todos os campos obrigatórios corretamente.');
        return;
    }
    
    // Coletar dados do formulário
    const formData = new FormData(event.target);
    const dados = Object.fromEntries(formData.entries());
    
    // Coletar dados do endereço
    dados.endereco = {
        cep: dados.cep,
        rua: dados.endereco,
        numero: dados.numero,
        complemento: dados.complemento || '',
        bairro: dados.bairro,
        cidade: dados.cidade,
        estado: dados.estado
    };
    
    // Coletar dados do cartão se aplicável
    if (dados.metodo_pagamento === 'cartao_credito') {
        dados.dados_cartao = {
            numero: dados.numero_cartao?.replace(/\s/g, ''),
            nome: dados.nome_cartao,
            validade: dados.validade_cartao,
            cvv: dados.cvv_cartao,
            parcelas: parseInt(dados.parcelas) || 1
        };
    }
    
    // Obter itens do carrinho
    const carrinho = JSON.parse(sessionStorage.getItem('carrinho') || localStorage.getItem('carrinho') || '[]');
    dados.itens = carrinho;
    
    if (carrinho.length === 0) {
        mostrarErro('Seu carrinho está vazio. Adicione produtos antes de finalizar.');
        return;
    }
    
    // Mostrar carregamento
    mostrarCarregamento('Processando seu pedido...');
    
    try {
        // Enviar para o servidor
        const response = await fetch('../Controller/PedidoController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'criar',
                ...dados
            })
        });
        
        const resultado = await response.json();
        
        if (resultado.success) {
            // Limpar carrinho
            sessionStorage.removeItem('carrinho');
            localStorage.removeItem('carrinho');
            
            // Redirecionar para confirmação
            if (dados.metodo_pagamento === 'pix' || dados.metodo_pagamento === 'boleto') {
                // Pagamento pendente
                window.location.href = `Confirmacao.php?pedido=${resultado.pedido_id}&tipo=pendente`;
            } else {
                // Pagamento aprovado
                window.location.href = `Confirmacao.php?pedido=${resultado.pedido_id}&tipo=sucesso`;
            }
        } else {
            mostrarErro(resultado.message || 'Erro ao processar pedido');
        }
    } catch (error) {
        console.error('Erro:', error);
        mostrarErro('Erro ao processar checkout. Tente novamente.');
    } finally {
        esconderCarregamento();
    }
}

function validarCarrinho() {
    const carrinho = JSON.parse(sessionStorage.getItem('carrinho') || localStorage.getItem('carrinho') || '[]');
    
    if (carrinho.length === 0) {
        window.location.href = 'Carrinho.php';
        return false;
    }
    
    return true;
}

// Funções de feedback
function mostrarSucesso(mensagem) {
    const alerta = document.getElementById('successAlert');
    if (alerta) {
        alerta.textContent = mensagem;
        alerta.style.display = 'block';
        
        setTimeout(() => {
            alerta.style.display = 'none';
        }, 3000);
    }
}

function mostrarErro(mensagem) {
    const alerta = document.getElementById('errorAlert');
    if (alerta) {
        alerta.textContent = mensagem;
        alerta.style.display = 'block';
        
        setTimeout(() => {
            alerta.style.display = 'none';
        }, 5000);
    }
}

function mostrarCarregamento(mensagem) {
    const btnFinalizar = document.getElementById('btnFinalizar');
    if (btnFinalizar) {
        btnFinalizar.disabled = true;
        btnFinalizar.innerHTML = `<i class="bi bi-hourglass"></i> ${mensagem}`;
    }
}

function esconderCarregamento() {
    const btnFinalizar = document.getElementById('btnFinalizar');
    if (btnFinalizar) {
        btnFinalizar.disabled = false;
        btnFinalizar.innerHTML = `<i class="bi bi-check-circle"></i> Finalizar Pedido`;
    }
}