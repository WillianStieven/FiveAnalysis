# Instruções para Configurar Login de Lojas Afiliadas

## Problema Resolvido
O sistema de login de lojas afiliadas agora está funcionando corretamente. O problema era que o código estava usando validação simulada em JavaScript ao invés de verificar no banco de dados.

## Passos para Configurar

### 1. Criar a Tabela no Banco de Dados

Execute o script SQL no seu banco PostgreSQL:

```sql
-- Criar tabela de lojas afiliadas
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

-- Criar índice para melhor performance
CREATE INDEX IF NOT EXISTS idx_lojas_email ON lojas_afiliadas(email);
CREATE INDEX IF NOT EXISTS idx_lojas_ativo ON lojas_afiliadas(ativo);
```

### 2. Adicionar Coluna tipo_usuario na Tabela usuarios (Opcional)

Se quiser usar a tabela `usuarios` para administradores também:

```sql
ALTER TABLE usuarios 
ADD COLUMN IF NOT EXISTS tipo_usuario VARCHAR(20) DEFAULT 'usuario';

CREATE INDEX IF NOT EXISTS idx_usuarios_tipo ON usuarios(tipo_usuario);
```

### 3. Criar uma Loja Afiliada

#### Opção A: Usar o Script Automático (Desenvolvimento)
Acesse no navegador:
```
http://localhost/FiveAnalysis/Controller/criar_loja_afiliada.php
```

Isso criará uma loja de exemplo com:
- **Email:** loja@fiveanalysis.com
- **Senha:** admin123

#### Opção B: Criar Manualmente via SQL

```sql
-- Exemplo: criar uma loja afiliada
-- A senha será 'minhasenha123' (substitua pela senha desejada)
INSERT INTO lojas_afiliadas (nome_loja, email, senha_hash, cnpj, telefone, endereco, ativo) 
VALUES (
    'Minha Loja',
    'minhaloja@exemplo.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash da senha
    '12.345.678/0001-90',
    '(49) 99999-9999',
    'Endereço da loja',
    true
);
```

**Para gerar o hash da senha em PHP:**
```php
<?php
echo password_hash('suasenha', PASSWORD_DEFAULT);
?>
```

### 4. Testar o Login

1. Acesse: `http://localhost/FiveAnalysis/View/AdminLogin.php`
2. Use as credenciais criadas
3. O sistema agora valida no banco de dados

## Estrutura de Arquivos Criados/Modificados

- ✅ `Controller/validacaoadmin.php` - Validação real no banco de dados
- ✅ `View/AdminLogin.php` - Atualizado para usar validação PHP
- ✅ `Model/criar_tabelas.sql` - Script SQL para criar tabelas
- ✅ `Controller/criar_loja_afiliada.php` - Script para criar loja de exemplo

## Solução de Problemas

### Erro: "Tabela não existe"
Execute o script SQL do passo 1 para criar a tabela.

### Erro: "Email ou senha incorretos"
- Verifique se a loja foi criada no banco
- Verifique se o email está correto
- Verifique se a senha foi hashada corretamente

### Erro: "Erro de conexão com o banco"
- Verifique as credenciais em `Model/conexao.php`
- Verifique se o PostgreSQL está rodando
- Verifique se o banco `FiveAnalysis` existe

## Segurança

⚠️ **IMPORTANTE:**
- Remova ou proteja o arquivo `Controller/criar_loja_afiliada.php` em produção
- Use senhas fortes para contas administrativas
- Considere adicionar rate limiting no login
- Considere adicionar CAPTCHA para prevenir ataques de força bruta

