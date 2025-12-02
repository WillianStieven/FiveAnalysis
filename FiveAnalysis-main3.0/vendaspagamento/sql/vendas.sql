-- ============================================
-- SISTEMA DE VENDAS E PEDIDOS - FIVEANALYSIS
-- Compatível com estrutura existente
-- ============================================

-- 1. TABELA DE PEDIDOS
CREATE TABLE IF NOT EXISTS pedidos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE SET NULL,
    numero_pedido VARCHAR(20) UNIQUE NOT NULL,
    status VARCHAR(20) DEFAULT 'pendente' 
        CHECK (status IN ('pendente', 'processando', 'enviado', 'entregue', 'cancelado')),
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    desconto DECIMAL(10,2) DEFAULT 0,
    frete DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    metodo_pagamento VARCHAR(50),
    status_pagamento VARCHAR(20) DEFAULT 'pendente'
        CHECK (status_pagamento IN ('pendente', 'aprovado', 'recusado', 'reembolsado')),
    endereco_entrega JSONB,
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. TABELA DE ITENS DO PEDIDO
CREATE TABLE IF NOT EXISTS pedido_itens (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER REFERENCES pedidos(id) ON DELETE CASCADE,
    produto_id INTEGER REFERENCES produtos(id),
    loja_afiliada_id INTEGER REFERENCES lojas_afiliadas(id),
    nome_produto VARCHAR(255) NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    quantidade INTEGER NOT NULL DEFAULT 1,
    subtotal DECIMAL(10,2) NOT NULL,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. TABELA DE HISTÓRICO DE STATUS
CREATE TABLE IF NOT EXISTS pedido_status (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER REFERENCES pedidos(id) ON DELETE CASCADE,
    status VARCHAR(50) NOT NULL,
    observacao TEXT,
    data_status TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. TABELA DE FORMAS DE PAGAMENTO
CREATE TABLE IF NOT EXISTS formas_pagamento (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    taxa DECIMAL(5,2) DEFAULT 0,
    descricao TEXT,
    configuracao JSONB
);

-- 5. TABELA DE TRANSAÇÕES DE PAGAMENTO
CREATE TABLE IF NOT EXISTS transacoes_pagamento (
    id SERIAL PRIMARY KEY,
    pedido_id INTEGER REFERENCES pedidos(id),
    metodo_pagamento VARCHAR(50),
    valor DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'pendente',
    codigo_transacao VARCHAR(100),
    dados_transacao JSONB,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ÍNDICES PARA PERFORMANCE
CREATE INDEX IF NOT EXISTS idx_pedidos_usuario ON pedidos(usuario_id);
CREATE INDEX IF NOT EXISTS idx_pedidos_status ON pedidos(status);
CREATE INDEX IF NOT EXISTS idx_pedidos_data ON pedidos(data_criacao);
CREATE INDEX IF NOT EXISTS idx_pedido_itens_pedido ON pedido_itens(pedido_id);
CREATE INDEX IF NOT EXISTS idx_pedido_status_pedido ON pedido_status(pedido_id);
CREATE INDEX IF NOT EXISTS idx_transacoes_pedido ON transacoes_pagamento(pedido_id);

-- TRIGGER PARA GERAR NÚMERO DO PEDIDO
CREATE OR REPLACE FUNCTION gerar_numero_pedido()
RETURNS TRIGGER AS $$
BEGIN
    NEW.numero_pedido := 'FA-' || 
                         TO_CHAR(CURRENT_DATE, 'YYYYMMDD-') || 
                         LPAD(NEXTVAL('pedidos_numero_seq')::TEXT, 6, '0');
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- SEQUENCE PARA NÚMEROS DE PEDIDO
CREATE SEQUENCE IF NOT EXISTS pedidos_numero_seq START 1000;

-- APLICAR TRIGGER
DO $$ 
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_trigger WHERE tgname = 'tr_gerar_numero_pedido') THEN
        CREATE TRIGGER tr_gerar_numero_pedido
            BEFORE INSERT ON pedidos
            FOR EACH ROW
            EXECUTE FUNCTION gerar_numero_pedido();
    END IF;
END $$;

-- INSERIR FORMAS DE PAGAMENTO PADRÃO
INSERT INTO formas_pagamento (nome, codigo, descricao) VALUES
    ('Cartão de Crédito', 'cartao_credito', 'Pague em até 12x sem juros'),
    ('Boleto Bancário', 'boleto', 'Pague em qualquer banco ou lotérica'),
    ('PIX', 'pix', 'Pagamento instantâneo via QR Code'),
    ('Transferência Bancária', 'transferencia', 'Transferência direta para nossa conta')
ON CONFLICT (codigo) DO NOTHING;

-- ADICIONAR CAMPOS DE CONTROLE EM PRODUTOS
ALTER TABLE produtos 
ADD COLUMN IF NOT EXISTS estoque INTEGER DEFAULT 10,
ADD COLUMN IF NOT EXISTS vendidos INTEGER DEFAULT 0;

-- PROCEDURE PARA ATUALIZAR ESTOQUE
CREATE OR REPLACE FUNCTION atualizar_estoque()
RETURNS TRIGGER AS $$
BEGIN
    -- Quando um item é adicionado ao pedido, reduzir estoque
    IF TG_OP = 'INSERT' THEN
        UPDATE produtos 
        SET estoque = estoque - NEW.quantidade,
            vendidos = vendidos + NEW.quantidade
        WHERE id = NEW.produto_id;
        
    -- Quando um pedido é cancelado, devolver estoque
    ELSIF TG_OP = 'UPDATE' AND NEW.status = 'cancelado' AND OLD.status != 'cancelado' THEN
        UPDATE produtos p
        SET estoque = estoque + pi.quantidade,
            vendidos = vendidos - pi.quantidade
        FROM pedido_itens pi
        WHERE pi.pedido_id = NEW.id
        AND p.id = pi.produto_id;
    END IF;
    
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- TRIGGER PARA ATUALIZAR ESTOQUE
CREATE TRIGGER tr_atualizar_estoque
    AFTER INSERT OR UPDATE ON pedidos
    FOR EACH ROW
    EXECUTE FUNCTION atualizar_estoque();

-- VIEW PARA RELATÓRIO DE VENDAS
CREATE OR REPLACE VIEW vw_relatorio_vendas AS
SELECT 
    p.id,
    p.numero_pedido,
    p.data_criacao,
    p.status,
    p.total,
    p.metodo_pagamento,
    u.nome as cliente_nome,
    u.email as cliente_email,
    COUNT(pi.id) as total_itens,
    SUM(pi.quantidade) as total_produtos,
    STRING_AGG(pi.nome_produto, ', ') as produtos
FROM pedidos p
JOIN usuarios u ON p.usuario_id = u.id
LEFT JOIN pedido_itens pi ON p.id = pi.pedido_id
GROUP BY p.id, u.id
ORDER BY p.data_criacao DESC;

-- VIEW PARA RELATÓRIO DE PRODUTOS MAIS VENDIDOS
CREATE OR REPLACE VIEW vw_produtos_mais_vendidos AS
SELECT 
    p.id as produto_id,
    p.nome,
    p.categoria_id,
    c.nome as categoria,
    SUM(pi.quantidade) as total_vendido,
    SUM(pi.subtotal) as total_faturado,
    AVG(pi.preco_unitario) as preco_medio,
    COUNT(DISTINCT ped.id) as total_pedidos
FROM produtos p
JOIN categorias c ON p.categoria_id = c.id
JOIN pedido_itens pi ON p.id = pi.produto_id
JOIN pedidos ped ON pi.pedido_id = ped.id
WHERE ped.status NOT IN ('cancelado')
GROUP BY p.id, p.nome, p.categoria_id, c.nome
ORDER BY total_vendido DESC;

-- FUNÇÃO PARA OBTER VENDAS DO DIA
CREATE OR REPLACE FUNCTION obter_vendas_dia(data_consulta DATE DEFAULT CURRENT_DATE)
RETURNS TABLE (
    hora TIME,
    total_vendas INTEGER,
    total_faturado DECIMAL(10,2)
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        DATE_TRUNC('hour', p.data_criacao)::TIME as hora,
        COUNT(*) as total_vendas,
        SUM(p.total) as total_faturado
    FROM pedidos p
    WHERE DATE(p.data_criacao) = data_consulta
    AND p.status NOT IN ('cancelado')
    GROUP BY DATE_TRUNC('hour', p.data_criacao)
    ORDER BY hora;
END;
$$ LANGUAGE plpgsql;

-- INSERIR DADOS DE EXEMPLO (APENAS PARA DESENVOLVIMENTO)
INSERT INTO pedidos (usuario_id, subtotal, desconto, frete, total, metodo_pagamento, status) 
SELECT 
    u.id,
    1500.00,
    75.00,
    35.00,
    1460.00,
    'cartao_credito',
    'entregue'
FROM usuarios u 
WHERE u.email = 'cliente@exemplo.com'
LIMIT 1;

-- Atualizar dados existentes para compatibilidade
DO $$
BEGIN
    -- Adicionar colunas se não existirem
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name = 'produtos' AND column_name = 'estoque') THEN
        ALTER TABLE produtos ADD COLUMN estoque INTEGER DEFAULT 10;
    END IF;
    
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns 
                   WHERE table_name = 'produtos' AND column_name = 'vendidos') THEN
        ALTER TABLE produtos ADD COLUMN vendidos INTEGER DEFAULT 0;
    END IF;
    
    RAISE NOTICE 'Banco de dados configurado com sucesso!';
END $$;