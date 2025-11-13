-- Script SQL para criar as tabelas necessárias para o sistema de lojas afiliadas

-- Tabela de Lojas Afiliadas
CREATE TABLE IF NOT EXISTS lojas_afiliadas (
    id SERIAL PRIMARY KEY,
    nome_loja VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    cnpj VARCHAR(18),
    telefone VARCHAR(20),
    endereco TEXT,
    ativo BOOLEAN DEFAULT true,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP
);

-- Adicionar coluna tipo_usuario na tabela usuarios (se não existir)
-- Para identificar usuários administrativos
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS tipo_usuario VARCHAR(20) DEFAULT 'usuario';

-- Criar índice para melhor performance
CREATE INDEX IF NOT EXISTS idx_lojas_email ON lojas_afiliadas(email);
CREATE INDEX IF NOT EXISTS idx_lojas_ativo ON lojas_afiliadas(ativo);
CREATE INDEX IF NOT EXISTS idx_usuarios_tipo ON usuarios(tipo_usuario);

-- Exemplo de inserção de uma loja afiliada (senha: admin123)
-- A senha deve ser gerada com password_hash() no PHP
-- INSERT INTO lojas_afiliadas (nome_loja, email, senha_hash, cnpj, telefone, ativo) 
-- VALUES ('Loja Exemplo', 'loja@exemplo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '12.345.678/0001-90', '(49) 99999-9999', true);

-- Exemplo de atualização de um usuário para admin
-- UPDATE usuarios SET tipo_usuario = 'admin' WHERE email = 'admin@fiveanalysis.com';

