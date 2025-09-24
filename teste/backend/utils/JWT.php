<?php
/**
 * Utilitário para JWT (JSON Web Token)
 * FiveAnalysis Backend
 */

class JWT {
    private static $secret = JWT_SECRET;
    private static $algorithm = JWT_ALGORITHM;
    
    /**
     * Gerar token JWT
     */
    public static function encode($payload, $expiration = JWT_EXPIRATION) {
        $header = [
            'typ' => 'JWT',
            'alg' => self::$algorithm
        ];
        
        $payload['iat'] = time();
        $payload['exp'] = time() + $expiration;
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        $signature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . '.' . $payloadEncoded . '.' . $signatureEncoded;
    }
    
    /**
     * Decodificar token JWT
     */
    public static function decode($token) {
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Token inválido');
        }
        
        list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
        
        // Verificar assinatura
        $signature = self::base64UrlDecode($signatureEncoded);
        $expectedSignature = hash_hmac('sha256', $headerEncoded . '.' . $payloadEncoded, self::$secret, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            throw new Exception('Assinatura inválida');
        }
        
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        
        // Verificar expiração
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new Exception('Token expirado');
        }
        
        return $payload;
    }
    
    /**
     * Verificar se token é válido
     */
    public static function validate($token) {
        try {
            self::decode($token);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Obter payload do token
     */
    public static function getPayload($token) {
        try {
            return self::decode($token);
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Codificar em Base64 URL-safe
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Decodificar Base64 URL-safe
     */
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
    
    /**
     * Gerar refresh token
     */
    public static function generateRefreshToken() {
        return bin2hex(random_bytes(32));
    }
    
    /**
     * Extrair token do header Authorization
     */
    public static function extractFromHeader() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return null;
        }
        
        $authHeader = $headers['Authorization'];
        
        if (strpos($authHeader, 'Bearer ') !== 0) {
            return null;
        }
        
        return substr($authHeader, 7);
    }
}
?>
