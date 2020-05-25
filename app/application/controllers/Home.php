<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('LoginValidator');
		$this->load->library('JwtAuth');
		$this->load->library('session');
		$this->load->model('follow');
		$this->load->model('post');
		$this->load->helper(array('form', 'url'));
		
		// se l'utente non è loggato eseguo un redirect alla pagina di login
		if(!$this->loginvalidator->isLoggedIn())
			redirect("/login", "refresh");

		//prendo lo user id
        $this->user_id = $this->jwtauth->getProperty($this->session->userdata("JWT"), 'id');
	}

	public function index()
	{
		// carica il template per l'header 
		$this->load->view('templates/header', ["titolo" => "Clonegram"]);
		// prendi i dati dell'utente attualmente loggato
		$data["user_logged_info"] = $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, nome, cognome, Media.mediafile");
		// prendi gli utenti suggeriti per l'utente attualmente loggato
		$data['suggested'] = $this->follow->getFollowSuggestions($this->user_id);
		// prendi i post da mostrare nella home
		$data['posts'] = $this->post->getPosts(['req_type' => 'HOME', 'id_utente' => $this->user_id]);
		// carica la view della home
		$this->load->view('home', $data);
		// carica il template per il footer 
		$this->load->view('templates/footer');
	}

}