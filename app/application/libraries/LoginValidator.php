<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginValidator {
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->library('JwtAuth');
        $this->CI->load->library('session');
        $this->CI->load->model('user');
    }
    // restituisce true se l'utente è loggato altrimenti false
    public function isLoggedIn() {
        $jwt = $this->CI->session->userdata("JWT");
        // se non esiste il jwt ritorna false
        if($jwt == NULL) return false;
        // se il jwt non è valido ritorna false
        if(!$this->CI->jwtauth->isValid($jwt)) return false;
        // decodifico il jwt
        $decoded_jwt = $this->CI->jwtauth->decodeJwt($jwt);
        // se non esiste il campo id ritorna false
        if(!isset($decoded_jwt["payload"]["id"])) return false;
        // controllo se esiste un utente con l'id nel jwt
        return $this->CI->user->exist(array("id" => $decoded_jwt["payload"]["id"]));
    }

}