-- Script de inicialização do banco de dados FiveAnalysis
-- Este script é executado automaticamente quando o container PostgreSQL é criado

-- Criar extensões necessárias
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha_hash VARCHAR(255) NOT NULL,
    tipo_usuario VARCHAR(20) DEFAULT 'usuario' CHECK (tipo_usuario IN ('usuario', 'admin', 'loja_afiliada')),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de lojas afiliadas
CREATE TABLE IF NOT EXISTS lojas_afiliadas (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id) ON DELETE CASCADE,
    nome_loja VARCHAR(200) NOT NULL,
    cnpj VARCHAR(18) UNIQUE,
    endereco TEXT,
    telefone VARCHAR(20),
    status VARCHAR(20) DEFAULT 'ativo' CHECK (status IN ('ativo', 'inativo', 'suspenso')),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de categorias de produtos
CREATE TABLE IF NOT EXISTS categorias (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    icone VARCHAR(50)
);

-- Tabela de marcas
CREATE TABLE IF NOT EXISTS marcas (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    logo_url VARCHAR(255)
);

-- Tabela de produtos
CREATE TABLE IF NOT EXISTS produtos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(200) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    imagem_url VARCHAR(255),
    categoria_id INTEGER REFERENCES categorias(id),
    marca_id INTEGER REFERENCES marcas(id),
    loja_afiliada_id INTEGER REFERENCES lojas_afiliadas(id),
    especificacoes JSONB,
    compatibilidade JSONB,
    estoque INTEGER DEFAULT 0,
    ativo BOOLEAN DEFAULT true,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de montagens
CREATE TABLE IF NOT EXISTS montagens (
    id SERIAL PRIMARY KEY,
    nome_montagem VARCHAR(200) NOT NULL,
    usuario_id INTEGER REFERENCES usuarios(id),
    descricao TEXT,
    preco_total DECIMAL(10,2),
    compatibilidade_verificada BOOLEAN DEFAULT false,
    publica BOOLEAN DEFAULT false,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de componentes das montagens
CREATE TABLE IF NOT EXISTS montagem_componentes (
    id SERIAL PRIMARY KEY,
    montagem_id INTEGER REFERENCES montagens(id) ON DELETE CASCADE,
    produto_id INTEGER REFERENCES produtos(id),
    quantidade INTEGER DEFAULT 1,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de avaliações de produtos
CREATE TABLE IF NOT EXISTS avaliacoes (
    id SERIAL PRIMARY KEY,
    produto_id INTEGER REFERENCES produtos(id),
    usuario_id INTEGER REFERENCES usuarios(id),
    nota INTEGER CHECK (nota >= 1 AND nota <= 5),
    comentario TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(produto_id, usuario_id)
);

-- Inserir categorias padrão
INSERT INTO categorias (nome, descricao, icone) VALUES
('Processador', 'Unidade Central de Processamento (CPU)', 'cpu'),
('Placa Mãe', 'Motherboard - conecta todos os componentes', 'motherboard'),
('Memória RAM', 'Memória de Acesso Randômico', 'memory'),
('Placa de Vídeo', 'Unidade de Processamento Gráfico (GPU)', 'gpu'),
('Armazenamento', 'HDDs e SSDs para armazenamento de dados', 'storage'),
('Fonte', 'Fonte de Alimentação', 'power'),
('Gabinete', 'Case para montagem do computador', 'case'),
('Cooler', 'Sistema de refrigeração', 'cooler'),
('Monitor', 'Dispositivos de exibição', 'monitor'),
('Periféricos', 'Teclados, mouses e outros acessórios', 'peripherals')
ON CONFLICT (nome) DO NOTHING;

-- Inserir marcas padrão
INSERT INTO marcas (nome, logo_url) VALUES
('Intel', '/assets/img/brands/intel.png'),
('AMD', '/assets/img/brands/amd.png'),
('ASUS', '/assets/img/brands/asus.png'),
('MSI', '/assets/img/brands/msi.png'),
('Gigabyte', '/assets/img/brands/gigabyte.png'),
('Corsair', '/assets/img/brands/corsair.png'),
('Kingston', '/assets/img/brands/kingston.png'),
('Samsung', '/assets/img/brands/samsung.png'),
('Western Digital', '/assets/img/brands/wd.png'),
('Seagate', '/assets/img/brands/seagate.png'),
('NVIDIA', '/assets/img/brands/nvidia.png'),
('EVGA', '/assets/img/brands/evga.png'),
('Cooler Master', '/assets/img/brands/coolermaster.png'),
('Thermaltake', '/assets/img/brands/thermaltake.png'),
('BenQ', '/assets/img/brands/benq.png'),
('LG', '/assets/img/brands/lg.png'),
('Dell', '/assets/img/brands/dell.png'),
('HP', '/assets/img/brands/hp.png')
ON CONFLICT (nome) DO NOTHING;

-- Inserir produtos de exemplo
INSERT INTO produtos (nome, descricao, preco, imagem_url, categoria_id, marca_id, especificacoes, compatibilidade, estoque) VALUES
('Intel Core i5-12400F', 'Processador Intel Core i5 de 12ª geração com 6 núcleos', 899.90, '../Assets/img/processador.jpeg', 1, 1, 
 '{"nucleos": 6, "threads": 12, "frequencia_base": "2.5GHz", "frequencia_turbo": "4.4GHz", "cache": "18MB", "tdp": "65W", "socket": "LGA1700"}',
 '{"socket": "LGA1700", "chipset": ["H610", "B660", "H670", "Z690"]}', 15),

('AMD Ryzen 5 5600X', 'Processador AMD Ryzen 5 com arquitetura Zen 3', 799.90, '../Assets/img/R57600.jpeg', 1, 2,
 '{"nucleos": 6, "threads": 12, "frequencia_base": "3.7GHz", "frequencia_turbo": "4.6GHz", "cache": "35MB", "tdp": "65W", "socket": "AM4"}',
 '{"socket": "AM4", "chipset": ["A520", "B450", "B550", "X470", "X570"]}', 12),

('AMD Ryzen 5 5600GT', 'Processador AMD Ryzen 5 5600GT com 6 núcleos', 839.90, '../Assets/img/5600gt.jpeg', 1, 2,
 '{"nucleos": 6, "threads": 12, "frequencia_base": "3.9GHz", "frequencia_turbo": "4.4GHz", "cache": "35MB", "tdp": "65W", "socket": "AM4"}',
 '{"socket": "AM4", "chipset": ["A520", "B450", "B550", "X470", "X570"]}', 10),

('AMD Ryzen 5 7600', 'Processador AMD Ryzen 5 7600 com arquitetura Zen 4', 1439.90, '../Assets/img/a7.jpeg', 1, 2,
 '{"nucleos": 6, "threads": 12, "frequencia_base": "3.8GHz", "frequencia_turbo": "5.1GHz", "cache": "38MB", "tdp": "65W", "socket": "AM5"}',
 '{"socket": "AM5", "chipset": ["A620", "B650", "X670"]}', 8),

('AMD Ryzen 3', 'Processador AMD Ryzen 3 com 4 núcleos', 599.90, '../Assets/img/ryzen3.jpeg', 1, 2,
 '{"nucleos": 4, "threads": 8, "frequencia_base": "3.5GHz", "frequencia_turbo": "4.1GHz", "cache": "20MB", "tdp": "65W", "socket": "AM4"}',
 '{"socket": "AM4", "chipset": ["A520", "B450", "B550", "X470", "X570"]}', 12),

('ASUS Prime B660M-A', 'Placa mãe Intel B660 micro-ATX', 599.90, '../Assets/img/placamae.webp', 2, 3,
 '{"socket": "LGA1700", "chipset": "B660", "formato": "micro-ATX", "memoria_max": "128GB", "slots_pcie": 2, "slots_ram": 4}',
 '{"socket": "LGA1700", "memoria_tipo": "DDR4", "formato": "micro-ATX"}', 8),

('MSI B450M PRO-VDH MAX', 'Placa mãe AMD B450 micro-ATX', 549.90, '../Assets/img/placamae2.jpeg', 2, 4,
 '{"socket": "AM4", "chipset": "B450", "formato": "micro-ATX", "memoria_max": "64GB", "slots_pcie": 2, "slots_ram": 4}',
 '{"socket": "AM4", "memoria_tipo": "DDR4", "formato": "micro-ATX"}', 10),

('Corsair Vengeance LPX 16GB', 'Kit de memória DDR4 3200MHz 16GB (2x8GB)', 399.90, '../Assets/img/a1.jpg', 3, 6,
 '{"capacidade": "16GB", "tipo": "DDR4", "frequencia": "3200MHz", "latencia": "CL16", "voltagem": "1.35V", "formato": "DIMM"}',
 '{"tipo": "DDR4", "frequencia_max": "3200MHz"}', 20),

('Kingston Fury Beast 32GB', 'Kit de memória DDR4 3200MHz 32GB (2x16GB)', 699.90, '../Assets/img/a2.jpg', 3, 7,
 '{"capacidade": "32GB", "tipo": "DDR4", "frequencia": "3200MHz", "latencia": "CL16", "voltagem": "1.35V", "formato": "DIMM"}',
 '{"tipo": "DDR4", "frequencia_max": "3200MHz"}', 15),

('NVIDIA GeForce RTX 3060', 'Placa de vídeo RTX 3060 com 12GB GDDR6', 1899.90, '../Assets/img/Placadevideo.webp', 4, 11,
 '{"memoria": "12GB", "tipo_memoria": "GDDR6", "interface": "PCIe 4.0", "consumo": "170W", "conectores": ["8-pin"]}',
 '{"interface": "PCIe", "consumo_max": "170W"}', 5),

('NVIDIA GeForce RTX 4060', 'Placa de vídeo RTX 4060 com 8GB GDDR6', 2199.90, '../Assets/img/A6.jpeg', 4, 11,
 '{"memoria": "8GB", "tipo_memoria": "GDDR6", "interface": "PCIe 4.0", "consumo": "115W", "conectores": ["8-pin"]}',
 '{"interface": "PCIe", "consumo_max": "115W"}', 8),

('Samsung 980 PRO 1TB', 'SSD NVMe M.2 1TB com PCIe 4.0', 699.90, '../Assets/img/a3.jpg', 5, 8,
 '{"capacidade": "1TB", "interface": "PCIe 4.0", "formato": "M.2", "velocidade_leitura": "7000MB/s", "velocidade_escrita": "5000MB/s"}',
 '{"interface": "PCIe", "formato": "M.2"}', 10),

('Western Digital Blue SSD 500GB', 'SSD SATA 500GB para notebooks e desktops', 249.90, '../Assets/img/a4.jpg', 5, 9,
 '{"capacidade": "500GB", "interface": "SATA", "formato": "2.5\"", "velocidade_leitura": "560MB/s", "velocidade_escrita": "530MB/s"}',
 '{"interface": "SATA", "formato": "2.5\""}', 15),

('Corsair CV650', 'Fonte de alimentação 650W 80 Plus Bronze', 299.90, '../Assets/img/a5.jpg', 6, 6,
 '{"potencia": "650W", "certificacao": "80 Plus Bronze", "modular": false, "conectores": ["24-pin", "8-pin CPU", "6+2-pin PCIe"]}',
 '{"potencia_min": "650W", "certificacao": "80 Plus Bronze"}', 15),

('Cooler Master MasterBox Q300L', 'Gabinete micro-ATX com janela lateral', 199.90, '../Assets/img/a8.jpeg', 7, 13,
 '{"formato": "micro-ATX", "janela_lateral": true, "fans_inclusos": 1, "slots_expansao": 4, "baias_hdd": 2}',
 '{"formato": "micro-ATX", "altura_max_gpu": "360mm"}', 8),

('Thermaltake View 21', 'Gabinete Mid-Tower com janela lateral', 299.90, '../Assets/img/a9.jpeg', 7, 14,
 '{"formato": "Mid-Tower", "janela_lateral": true, "fans_inclusos": 2, "slots_expansao": 7, "baias_hdd": 3}',
 '{"formato": "ATX", "altura_max_gpu": "400mm"}', 6),

('BenQ Mobiuz EX271', 'Monitor gaming 27" Full HD 144Hz', 1299.90, '../Assets/img/monitorP.webp', 9, 15,
 '{"tamanho": "27\"", "resolucao": "1920x1080", "taxa_atualizacao": "144Hz", "tempo_resposta": "1ms", "conectores": ["HDMI", "DisplayPort"]}',
 '{"resolucao": "1920x1080", "taxa_atualizacao": "144Hz"}', 6),

('LG Ultrawide 29"', 'Monitor LG Ultrawide 29 polegadas', 1099.90, '../Assets/img/monitorP.webp', 9, 16,
 '{"tamanho": "29\"", "resolucao": "2560x1080", "taxa_atualizacao": "75Hz", "tempo_resposta": "5ms", "conectores": ["HDMI", "DisplayPort"]}',
 '{"resolucao": "2560x1080", "taxa_atualizacao": "75Hz"}', 5),

('Headset Gamer HyperX', 'Headset gaming com som surround 7.1', 399.90, '../Assets/img/headset.jpeg', 10, 17,
 '{"tipo": "Gaming", "driver": "50mm", "frequencia": "20Hz-20kHz", "impedancia": "32 Ohm", "microfone": true}',
 '{"conectividade": "USB", "compatibilidade": ["PC", "PS4", "Xbox One"]}', 8),

('Cadeira Gamer TGIF T0', 'Cadeira ergonômica para gaming', 3699.90, '../Assets/img/Cadeira.jpeg', 10, 13,
 '{"tipo": "Gaming", "material": "Couro sintético", "altura_ajustavel": true, "peso_max": "120kg", "garantia": "2 anos"}',
 '{"peso_max": "120kg", "altura_ajustavel": true}', 3)
ON CONFLICT DO NOTHING;

-- Criar índices para melhor performance
CREATE INDEX IF NOT EXISTS idx_produtos_categoria ON produtos(categoria_id);
CREATE INDEX IF NOT EXISTS idx_produtos_marca ON produtos(marca_id);
CREATE INDEX IF NOT EXISTS idx_produtos_preco ON produtos(preco);
CREATE INDEX IF NOT EXISTS idx_montagens_usuario ON montagens(usuario_id);
CREATE INDEX IF NOT EXISTS idx_montagem_componentes_montagem ON montagem_componentes(montagem_id);
CREATE INDEX IF NOT EXISTS idx_avaliacoes_produto ON avaliacoes(produto_id);

-- Criar usuário administrador padrão
INSERT INTO usuarios (nome, email, senha_hash, tipo_usuario) VALUES
('Administrador', 'admin@fiveanalysis.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Criar usuário de loja afiliada de exemplo
INSERT INTO usuarios (nome, email, senha_hash, tipo_usuario) VALUES
('Loja TechStore', 'loja@techstore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'loja_afiliada')
ON CONFLICT (email) DO NOTHING;

-- Inserir dados da loja afiliada
INSERT INTO lojas_afiliadas (usuario_id, nome_loja, cnpj, endereco, telefone, status) 
SELECT u.id, 'TechStore', '12.345.678/0001-90', 'Rua das Tecnologias, 123', '(11) 99999-9999', 'ativo'
FROM usuarios u WHERE u.email = 'loja@techstore.com'
ON CONFLICT (cnpj) DO NOTHING;

-- Comentários finais
COMMENT ON TABLE usuarios IS 'Tabela de usuários do sistema';
COMMENT ON TABLE lojas_afiliadas IS 'Tabela de lojas afiliadas do sistema';
COMMENT ON TABLE produtos IS 'Catálogo de produtos de hardware';
COMMENT ON TABLE montagens IS 'Montagens de computadores criadas pelos usuários';
COMMENT ON TABLE montagem_componentes IS 'Componentes que fazem parte de cada montagem';
COMMENT ON TABLE avaliacoes IS 'Avaliações e comentários dos usuários sobre produtos';

