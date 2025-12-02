<?php
/**
 * VendaController.php
 * Controlador para gestão de vendas (visão administrativa)
 */
session_start();
require_once '../Model/conexao.php';
require_once '../Model/Venda.php';

class VendaController {
    private $vendaModel;
    
    public function __construct($conexao) {
        $this->vendaModel = new Venda($conexao);
    }
    
    /**
     * Listar vendas com filtros
     */
    public function listarVendas($filtros = []) {
        return $this->vendaModel->listarComFiltros($filtros);
    }
    
    /**
     * Obter estatísticas de vendas
     */
    public function obterEstatisticas($periodo = '30dias') {
        $estatisticas = [];
        
        // Vendas por período
        $estatisticas['vendas_periodo'] = $this->vendaModel->obterVendasPorPeriodo($periodo);
        
        // Produtos mais vendidos
        $estatisticas['produtos_mais_vendidos'] = $this->vendaModel->obterProdutosMaisVendidos(10);
        
        // Total de vendas
        $estatisticas['total_vendas'] = $this->vendaModel->obterTotalVendas();
        
        // Ticket médio
        $estatisticas['ticket_medio'] = $this->vendaModel->obterTicketMedio();
        
        return $estatisticas;
    }
    
    /**
     * Atualizar status de uma venda
     */
    public function atualizarStatusVenda($vendaId, $novoStatus, $observacoes = '') {
        return $this->vendaModel->atualizarStatus($vendaId, $novoStatus, $observacoes);
    }
    
    /**
     * Gerar relatório de vendas
     */
    public function gerarRelatorio($dataInicio, $dataFim, $formato = 'array') {
        $relatorio = $this->vendaModel->gerarRelatorioPeriodo($dataInicio, $dataFim);
        
        if ($formato === 'csv') {
            return $this->gerarCSV($relatorio);
        } elseif ($formato === 'json') {
            return json_encode($relatorio);
        }
        
        return $relatorio;
    }
    
    /**
     * Obter vendas por loja
     */
    public function obterVendasPorLoja($lojaId = null, $periodo = '30dias') {
        return $this->vendaModel->obterVendasPorLoja($lojaId, $periodo);
    }
    
    // ========== MÉTODOS PRIVADOS ==========
    
    private function gerarCSV($dados) {
        $output = fopen('php://temp', 'w');
        
        // Cabeçalho
        fputcsv($output, ['ID', 'Data', 'Cliente', 'Total', 'Status', 'Forma Pagamento']);
        
        // Dados
        foreach ($dados as $venda) {
            fputcsv($output, [
                $venda['numero_pedido'],
                $venda['data_criacao'],
                $venda['usuario_nome'],
                $venda['total'],
                $venda['status'],
                $venda['metodo_pagamento']
            ]);
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}

// Handler para requisições administrativas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once '../Model/conexao.php';
    
    // Verificar se é admin ou loja
    if (!isset($_SESSION['tipo_usuario']) || 
        ($_SESSION['tipo_usuario'] !== 'admin' && $_SESSION['tipo_usuario'] !== 'loja')) {
        echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
        exit;
    }
    
    $controller = new VendaController($conexao);
    
    switch ($_POST['action']) {
        case 'listar':
            $vendas = $controller->listarVendas($_POST['filtros'] ?? []);
            echo json_encode(['success' => true, 'vendas' => $vendas]);
            break;
            
        case 'estatisticas':
            $estatisticas = $controller->obterEstatisticas($_POST['periodo'] ?? '30dias');
            echo json_encode(['success' => true, 'estatisticas' => $estatisticas]);
            break;
            
        case 'atualizar_status':
            if (isset($_POST['venda_id'], $_POST['novo_status'])) {
                $result = $controller->atualizarStatusVenda(
                    $_POST['venda_id'],
                    $_POST['novo_status'],
                    $_POST['observacoes'] ?? ''
                );
                echo json_encode(['success' => $result, 'message' => 'Status atualizado']);
            }
            break;
            
        case 'relatorio':
            if (isset($_POST['data_inicio'], $_POST['data_fim'])) {
                $relatorio = $controller->gerarRelatorio(
                    $_POST['data_inicio'],
                    $_POST['data_fim'],
                    $_POST['formato'] ?? 'array'
                );
                echo json_encode(['success' => true, 'relatorio' => $relatorio]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
}
?>