SISTEMA DE VENDAS E PEDIDOS - FIVEANALYSIS

FUNCIONALIDADES

PEDIDOS
- Criação de pedidos a partir do carrinho
- Histórico de pedidos por usuário
- Detalhes completos do pedido
- Cancelamento de pedidos (quando permitido)
- Rastreamento de envio (simulado)

PAGAMENTOS
- Múltiplas formas de pagamento (Cartão, Boleto, PIX)
- Simulação de processamento
- Geração de códigos PIX e boletos
- Status de pagamento
- Histórico de transações

ADMINISTRAÇÃO
- Dashboard de vendas
- Relatórios por período
- Controle de estoque automático
- Estatísticas de produtos mais vendidos
- Gerenciamento por loja filiada

CONFIGURAÇÃO
BANCO DE DADOS
O sistema cria automaticamente:

Tabelas de pedidos e itens

Histórico de status

Formas de pagamento

Transações

Views para relatórios

PAGAMENTOS
Formas de pagamento padrão:

Cartão de Crédito - Simulação de processamento

Boleto Bancário - Geração de código de barras

PIX - Geração de QR Code

Transferência - Pendente de implementação

ESTOQUE
Controle automático de estoque

Atualização ao finalizar pedido

Devolução ao cancelar pedido

FLUXO DE COMPRA
1. Usuário adiciona produtos ao carrinho
2. Acessa Checkout.php
3. Preenche dados de entrega e pagamento
4. Sistema valida estoque
5. Cria pedido no banco
6. Atualiza estoque
7. Redireciona para confirmação
8. Usuário acompanha no histórico
