<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PostContr extends CI_Controller
{
    public function __construct()
	{
        parent::__construct();
        // Carica i vari model e librerie da utilizzare
        $this->load->model('post');
        $this->load->model('follow');
        $this->load->model('user');
		$this->load->library('LoginValidator');
        $this->load->helper('url');
        
        // se l'utente non è loggato esegui il redirect su login
        if(!$this->loginvalidator->isLoggedIn()) 
            redirect("/login", "refresh");

        // prendo lo user id dell'utente loggato
        $this->user_id = $this->jwtauth->getProperty($this->session->userdata("JWT"), 'id');
	}
    public function index($post_id)
    {
        // Prendo le informazioni sul post con id == $post_id
        $post = $this->post->getPosts(array('id_post' => $post_id, 'req_type' => 'SINGLE'));
        
        // se non esiste il post mostra la pagina 404
        if(!isset($post)) show_404();

        // se l'utente che ha fatto il post è l'utente attualmente loggato o il profilo è privato o gli utenti si seguono allora restituisci una risposta
        if($this->user_id != $post["id_utente"] &&
            $this->user->is_private($post["id_utente"]) && 
            $this->follow->getLinkStatus($this->user_id, $post["id_utente"]) != 2) 
            redirect("/user/".$post["nome_utente"], "refresh");

        // Prendo le info sull'utente loggato
        $data["user_logged_info"] = $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, nome, cognome, Media.mediafile");
        $data["post"] = $post;

        // carica il template per l'header
        $this->load->view('templates/header', ["titolo" => $data["post"]["nome_utente"]." su Clonegram: \"".substr($data["post"]["descrizione"], 0, 15)."\""]);
        // Carico la view
        $this->load->view('post', $data);
        // carica il template per il footer 
        $this->load->view('templates/footer');
    }

    public function explore()
    {
        // carica il template per l'header 
        $this->load->view('templates/header', ['titolo' => 'Esplora']);
        // Prendo gli utenti suggeriti, i post consigliati e le info sull'utente loggato
        $suggested = $this->follow->getFollowSuggestions($this->user_id);
        $explore_post = $this->post->getPosts(['req_type' => 'EXPLORE', "id_utente" => $this->user_id]);
        $logged_user = $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, nome, cognome, Media.mediafile");
        $this->load->view('explore', ['suggested' => $suggested, 'posts' => $explore_post, 'logged_user' => $logged_user]);
        // carica il template per il footer 
        $this->load->view('templates/footer');
    }
}