# FiveAnalysis Backend

Backend em PHP para o sistema FiveAnalysis - Plataforma de montagem de computadores.

## 🚀 Características

- **API RESTful** completa
- **Autenticação JWT** segura
- **Banco PostgreSQL** com schema otimizado
- **Arquitetura MVC** limpa e organizada
- **Middleware** de segurança e autenticação
- **Sistema de logs** integrado
- **CORS** configurado
- **Rate limiting** (configurável)

## 📁 Estrutura do Projeto

```
backend/
├── api/
│   └── index.php              # Router principal da API
├── config/
│   ├── config.php             # Configurações gerais
│   ├── database.php           # Configuração do banco
│   └── database.example.php   # Exemplo de configuração
├── controllers/
│   ├── AuthController.php     # Autenticação
│   ├── ProductController.php  # Produtos
│   └── BuildController.php    # Montagens de PC
├── models/
│   ├── User.php              # Model de usuários
│   ├── Product.php           # Model de produtos
│   └── Build.php             # Model de montagens
├── middleware/
│   └── (middlewares futuros)
├── utils/
│   └── JWT.php               # Utilitário JWT
├── public/
│   └── uploads/              # Arquivos enviados
├── logs/                     # Logs do sistema
├── .htaccess                 # Configuração Apache
└── README.md                 # Este arquivo
```

## 🛠️ Instalação

### 1. Pré-requisitos

- PHP 7.4+ com extensões:
  - PDO PostgreSQL
  - JSON
  - OpenSSL
  - cURL
- PostgreSQL 12+
- Apache/Nginx com mod_rewrite

### 2. Configuração do Banco

1. Crie o banco PostgreSQL:
```sql
CREATE DATABASE fiveanalysis;
```

2. Execute o script SQL fornecido para criar as tabelas

3. Configure as credenciais:
```bash
cp config/database.example.php config/database.php
```

4. Edite `config/database.php` com suas credenciais:
```php
private $host = 'localhost';
private $port = '5432';
private $dbname = 'fiveanalysis';
private $username = 'seu_usuario';
private $password = 'sua_senha';
```

### 3. Configuração do Servidor Web

#### Apache
Certifique-se de que o mod_rewrite está habilitado:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx
Adicione ao seu virtual host:
```nginx
location /backend {
    try_files $uri $uri/ /backend/api/index.php?$query_string;
}
```

### 4. Permissões

```bash
chmod 755 backend/
chmod 777 backend/logs/
chmod 777 backend/public/uploads/
```

## 🔧 Configuração

### Variáveis de Ambiente

Edite `config/config.php` para ajustar:

- **JWT_SECRET**: Chave secreta para tokens JWT
- **CORS_ORIGINS**: URLs permitidas para CORS
- **UPLOAD_PATH**: Diretório de uploads
- **SMTP_***: Configurações de email

### Configurações de Segurança

- Headers de segurança automáticos
- Validação de entrada
- Sanitização de dados
- Rate limiting configurável

## 📚 API Endpoints

### Autenticação
- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Registro
- `POST /api/auth/logout` - Logout
- `POST /api/auth/refresh` - Renovar token
- `GET /api/auth/verify` - Verificar token
- `POST /api/auth/change-password` - Alterar senha
- `POST /api/auth/forgot-password` - Reset de senha

### Produtos
- `GET /api/products` - Listar produtos
- `GET /api/products/{id}` - Buscar produto
- `GET /api/products/categories` - Listar categorias
- `GET /api/products/brands` - Listar marcas
- `GET /api/products/promotional` - Produtos em promoção
- `GET /api/products/top-rated` - Mais avaliados
- `GET /api/products/best-selling` - Mais vendidos
- `GET /api/products/{categoria}` - Por categoria

### Montagens
- `GET /api/builds` - Listar montagens do usuário
- `POST /api/builds` - Criar montagem
- `GET /api/builds/{id}` - Buscar montagem
- `PUT /api/builds/{id}` - Atualizar montagem
- `DELETE /api/builds/{id}` - Deletar montagem
- `POST /api/builds/{id}/components` - Adicionar componente
- `DELETE /api/builds/{id}/components` - Remover componente
- `GET /api/builds/{id}/compatibility` - Verificar compatibilidade
- `POST /api/builds/{id}/duplicate` - Duplicar montagem
- `GET /api/builds/public` - Montagens públicas
- `GET /api/builds/popular` - Montagens populares

### Sistema
- `GET /api/health` - Status da API

## 🔐 Autenticação

### Login
```bash
curl -X POST http://localhost/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@exemplo.com","password":"senha123"}'
```

### Usar Token
```bash
curl -X GET http://localhost/backend/api/builds \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## 📊 Exemplos de Uso

### Criar Montagem
```bash
curl -X POST http://localhost/backend/api/builds \
  -H "Authorization: Bearer SEU_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nome_montagem": "PC Gamer 2024",
    "componentes": [
      {"produto_id": 1},
      {"produto_id": 7},
      {"produto_id": 20}
    ]
  }'
```

### Buscar Produtos
```bash
curl -X GET "http://localhost/backend/api/products?categoria=Processador&page=1&limit=10"
```

## 🐛 Logs

Os logs são salvos em `backend/logs/` com formato:
```
[2024-01-15 10:30:45] [INFO] Usuário logado {"user_id": 1, "email": "usuario@exemplo.com"}
```

## 🔒 Segurança

- **JWT** para autenticação
- **HTTPS** recomendado em produção
- **Validação** de entrada em todos os endpoints
- **Sanitização** de dados
- **Headers** de segurança automáticos
- **Rate limiting** configurável

## 🚀 Deploy

### Produção

1. Configure `ENVIRONMENT = 'production'` em `config.php`
2. Desabilite `DEBUG = false`
3. Configure HTTPS
4. Ajuste `CORS_ORIGINS` para seu domínio
5. Configure backup automático do banco

### Docker (Futuro)

```dockerfile
FROM php:8.1-apache
# Configuração Docker será adicionada
```

## 📈 Monitoramento

- Logs estruturados
- Métricas de performance (configurável)
- Health check endpoint
- Monitoramento de erros

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT.

## 🆘 Suporte

Para suporte, abra uma issue no repositório ou entre em contato.

---

**FiveAnalysis Backend** - Desenvolvido com ❤️ para a comunidade gamer
