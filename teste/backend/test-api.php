<?php
/**
 * Script de Teste da API FiveAnalysis
 * Execute este arquivo para testar os endpoints
 */

require_once __DIR__ . '/config/config.php';

// Configurações
$baseUrl = 'http://localhost/backend/api';
$testEmail = 'teste@exemplo.com';
$testPassword = 'senha123';

echo "🧪 Testando API FiveAnalysis\n";
echo "============================\n\n";

// Função para fazer requisições
function makeRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($token) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $token
        ]);
    }
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true)
    ];
}

// Teste 1: Health Check
echo "1. 🏥 Testando Health Check...\n";
$response = makeRequest($baseUrl . '/health');
if ($response['code'] === 200) {
    echo "   ✅ API funcionando\n";
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 2: Listar Produtos
echo "2. 📦 Testando listagem de produtos...\n";
$response = makeRequest($baseUrl . '/products?limit=5');
if ($response['code'] === 200) {
    echo "   ✅ Produtos listados: " . count($response['body']['data']) . " itens\n";
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 3: Buscar Categorias
echo "3. 🏷️ Testando categorias...\n";
$response = makeRequest($baseUrl . '/products/categories');
if ($response['code'] === 200) {
    echo "   ✅ Categorias encontradas: " . count($response['body']['data']) . "\n";
    foreach ($response['body']['data'] as $category) {
        echo "      - " . $category['categoria'] . " (" . $category['total_produtos'] . " produtos)\n";
    }
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 4: Buscar Marcas
echo "4. 🏭 Testando marcas...\n";
$response = makeRequest($baseUrl . '/products/brands');
if ($response['code'] === 200) {
    echo "   ✅ Marcas encontradas: " . count($response['body']['data']) . "\n";
    foreach (array_slice($response['body']['data'], 0, 5) as $brand) {
        echo "      - " . $brand['marca'] . " (" . $brand['total_produtos'] . " produtos)\n";
    }
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 5: Buscar Produto por ID
echo "5. 🔍 Testando busca de produto por ID...\n";
$response = makeRequest($baseUrl . '/products/1');
if ($response['code'] === 200) {
    $product = $response['body']['data']['product'];
    echo "   ✅ Produto encontrado: " . $product['nome'] . "\n";
    echo "      Preço: R$ " . number_format($product['preco'], 2, ',', '.') . "\n";
    echo "      Categoria: " . $product['categoria'] . "\n";
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 6: Registro de Usuário
echo "6. 👤 Testando registro de usuário...\n";
$userData = [
    'nome' => 'Usuário Teste',
    'email' => $testEmail,
    'password' => $testPassword
];
$response = makeRequest($baseUrl . '/auth/register', 'POST', $userData);
if ($response['code'] === 201) {
    echo "   ✅ Usuário registrado com sucesso\n";
    $accessToken = $response['body']['data']['access_token'];
} else {
    echo "   ⚠️ Usuário já existe ou erro: " . $response['code'] . "\n";
    // Tentar fazer login
    echo "   🔐 Tentando fazer login...\n";
    $loginData = [
        'email' => $testEmail,
        'password' => $testPassword
    ];
    $response = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
    if ($response['code'] === 200) {
        echo "   ✅ Login realizado com sucesso\n";
        $accessToken = $response['body']['data']['access_token'];
    } else {
        echo "   ❌ Erro no login: " . $response['code'] . "\n";
        $accessToken = null;
    }
}
echo "\n";

// Teste 7: Verificar Token
if ($accessToken) {
    echo "7. 🔑 Testando verificação de token...\n";
    $response = makeRequest($baseUrl . '/auth/verify', 'GET', null, $accessToken);
    if ($response['code'] === 200) {
        echo "   ✅ Token válido\n";
        echo "      Usuário: " . $response['body']['data']['user']['nome'] . "\n";
        echo "      Email: " . $response['body']['data']['user']['email'] . "\n";
    } else {
        echo "   ❌ Token inválido: " . $response['code'] . "\n";
    }
    echo "\n";
    
    // Teste 8: Listar Montagens do Usuário
    echo "8. 🖥️ Testando listagem de montagens...\n";
    $response = makeRequest($baseUrl . '/builds', 'GET', null, $accessToken);
    if ($response['code'] === 200) {
        echo "   ✅ Montagens listadas: " . count($response['body']['data']) . " itens\n";
    } else {
        echo "   ❌ Erro: " . $response['code'] . "\n";
    }
    echo "\n";
    
    // Teste 9: Criar Montagem
    echo "9. 🔧 Testando criação de montagem...\n";
    $buildData = [
        'nome_montagem' => 'PC Teste API',
        'componentes' => [
            ['produto_id' => 1], // Processador
            ['produto_id' => 7]  // Placa mãe
        ]
    ];
    $response = makeRequest($baseUrl . '/builds', 'POST', $buildData, $accessToken);
    if ($response['code'] === 201) {
        echo "   ✅ Montagem criada com sucesso\n";
        $buildId = $response['body']['data']['id'];
        echo "      ID: " . $buildId . "\n";
    } else {
        echo "   ❌ Erro: " . $response['code'] . "\n";
        if (isset($response['body']['message'])) {
            echo "      Mensagem: " . $response['body']['message'] . "\n";
        }
        $buildId = null;
    }
    echo "\n";
    
    // Teste 10: Buscar Montagem
    if ($buildId) {
        echo "10. 🔍 Testando busca de montagem...\n";
        $response = makeRequest($baseUrl . '/builds/' . $buildId, 'GET', null, $accessToken);
        if ($response['code'] === 200) {
            echo "   ✅ Montagem encontrada\n";
            echo "      Nome: " . $response['body']['data']['build']['nome_montagem'] . "\n";
            echo "      Componentes: " . count($response['body']['data']['components']) . "\n";
        } else {
            echo "   ❌ Erro: " . $response['code'] . "\n";
        }
        echo "\n";
    }
}

// Teste 11: Produtos Promocionais
echo "11. 🏷️ Testando produtos promocionais...\n";
$response = makeRequest($baseUrl . '/products/promotional?limit=3');
if ($response['code'] === 200) {
    echo "   ✅ Produtos promocionais: " . count($response['body']['data']) . " itens\n";
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

// Teste 12: Montagens Públicas
echo "12. 🌐 Testando montagens públicas...\n";
$response = makeRequest($baseUrl . '/builds/public?limit=3');
if ($response['code'] === 200) {
    echo "   ✅ Montagens públicas: " . count($response['body']['data']) . " itens\n";
} else {
    echo "   ❌ Erro: " . $response['code'] . "\n";
}
echo "\n";

echo "🎉 Testes concluídos!\n";
echo "=====================\n";
echo "Para mais testes, consulte a documentação da API.\n";
?>
