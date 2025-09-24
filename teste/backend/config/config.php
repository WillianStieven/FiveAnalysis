<?php
/**
 * Configurações Gerais do Sistema
 * FiveAnalysis Backend
 */

// Configurações de ambiente
define('ENVIRONMENT', 'development'); // development, production
define('DEBUG', ENVIRONMENT === 'development');

// Configurações de URL
define('BASE_URL', 'http://localhost/fiveanalysis/backend');
define('API_URL', BASE_URL . '/api');

// Configurações de CORS
define('CORS_ORIGINS', [
    'http://localhost',
    'http://127.0.0.1',
    'http://localhost:3000',
    'http://localhost:8080'
]);

// Configurações de JWT
define('JWT_SECRET', 'sua_chave_secreta_jwt_aqui');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600); // 1 hora

// Configurações de upload
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Configurações de paginação
define('DEFAULT_PAGE_SIZE', 20);
define('MAX_PAGE_SIZE', 100);

// Configurações de cache
define('CACHE_ENABLED', true);
define('CACHE_TTL', 3600); // 1 hora

// Configurações de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'seu_email@gmail.com');
define('SMTP_PASSWORD', 'sua_senha_app');
define('SMTP_FROM_EMAIL', 'noreply@fiveanalysis.com');
define('SMTP_FROM_NAME', 'FiveAnalysis');

// Configurações de logs
define('LOG_PATH', __DIR__ . '/../logs/');
define('LOG_LEVEL', DEBUG ? 'DEBUG' : 'INFO');

// Configurações de rate limiting
define('RATE_LIMIT_ENABLED', true);
define('RATE_LIMIT_REQUESTS', 100); // requests por hora
define('RATE_LIMIT_WINDOW', 3600); // 1 hora

// Configurações de afiliados
define('AFFILIATE_COMMISSION_RATE', 0.05); // 5%
define('AFFILIATE_COOKIE_DURATION', 30 * 24 * 3600); // 30 dias

// Configurações de notificações
define('NOTIFICATION_EMAIL_ENABLED', true);
define('NOTIFICATION_PUSH_ENABLED', false);

// Configurações de backup
define('BACKUP_ENABLED', true);
define('BACKUP_PATH', __DIR__ . '/../backups/');
define('BACKUP_RETENTION_DAYS', 30);

// Configurações de monitoramento
define('MONITORING_ENABLED', true);
define('PERFORMANCE_TRACKING', true);

// Headers de segurança
define('SECURITY_HEADERS', [
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    'Content-Security-Policy' => "default-src 'self'"
]);

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de erro
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Auto-loader simples
spl_autoload_register(function ($class) {
    $directories = [
        __DIR__ . '/../models/',
        __DIR__ . '/../controllers/',
        __DIR__ . '/../middleware/',
        __DIR__ . '/../utils/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Função para log
function logMessage($level, $message, $context = []) {
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    $logFile = LOG_PATH . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
    
    $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Função para resposta JSON
function jsonResponse($data, $statusCode = 200, $headers = []) {
    http_response_code($statusCode);
    
    // Headers de segurança
    foreach (SECURITY_HEADERS as $header => $value) {
        header("{$header}: {$value}");
    }
    
    // Headers personalizados
    foreach ($headers as $header => $value) {
        header("{$header}: {$value}");
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// Função para validação de CORS
function handleCors() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    
    if (in_array($origin, CORS_ORIGINS)) {
        header("Access-Control-Allow-Origin: {$origin}");
    }
    
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Função para sanitização
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Função para validação de email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Função para geração de token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Função para hash de senha
function hashPassword($password) {
    return password_hash($password, PASSWORD_ARGON2ID);
}

// Função para verificação de senha
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Inicialização
handleCors();
?>
