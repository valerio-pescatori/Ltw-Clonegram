<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JwtAuth {
    private $jwt_key;
    private $last;
    public function __construct()
    {
        // inizializza una chiave per HS256 ed una durata in secondi del token
        $this->jwt_key = "jwt_test_key";
        $this->last = 600000000;
    }
    
    // encoding base64url
    protected function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // decoding base64url
    protected function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    // genera il jwt
    public function generateJwt($data)
    {
        // prepara l'header del jwt
        $header["typ"] = "JWT";
        $header["alg"] = "HS256";

        // inserisco nel payload expire ed i dati in $data
        $payload["expire"] = time() + $this->last;
        foreach($data as $key => $value) {
            $payload[$key] = $value;
        }

        // genero la stringa jwt e la restituisco
        $header = $this->base64url_encode(json_encode($header));
        $payload = $this->base64url_encode(json_encode($payload));
        $signature = $this->base64url_encode(hash_hmac("sha256", $header.".".$payload, $this->jwt_key, true));
        return $header.".".$payload.".".$signature;
    }

    // esegue il decode del jwt
    public function decodeJwt($jwt) {
        $elements = explode(".", $jwt);
        if(count($elements) != 3) return NULL;
        return array(
            "header" => json_decode($this->base64url_decode($elements[0]), true),
            "payload" => json_decode($this->base64url_decode($elements[1]), true),
            "signature" => $this->base64url_decode($elements[2])
        );
    }

    // restituisce il tempo di vita del jwt
    public function getJwtExpire($jwt) {
        $decoded = $this->decodeJwt($jwt);
        if(!isset($decoded["payload"]["expire"]) || !is_numeric($decoded["payload"]["expire"])) {
            return NULL;
        }
        return intval($decoded["payload"]["expire"]);
    }

    // verifica che il formato e l'hash sono corretti
    public function checkJwtFormatAndHash($jwt) {
        $elements = explode(".", $jwt);
        if(count($elements) != 3) return false;
        return hash_equals($elements[2], 
            $this->base64url_encode(
                hash_hmac("sha256", $elements[0].".".$elements[1], $this->jwt_key, true)
            )
        );
    }

    // restituisce true se il jwt è valido, false altrimenti
    public function isValid($jwt) {
        return $this->checkJwtFormatAndHash($jwt) && 
            $this->getJwtExpire($jwt) > time();
    }

    // restuisce una property nel payload del jwt
    public function getProperty($jwt, $prop)
    {
        return $this->decodeJwt($jwt)['payload'][$prop];
    }
}