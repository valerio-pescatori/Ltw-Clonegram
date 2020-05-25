<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserContr extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library(array('LoginValidator', "JwtAuth", "session"));
        $this->load->helper(array('form', 'url'));
        $this->load->model('user');
        $this->load->model('follow');

        // se l'utente non è loggato eseguo un redirect alla pagina di login
        if (!$this->loginvalidator->isLoggedIn())
            redirect('/login', 'refresh');

        // prendo l'id dell'utente attualmente loggato    
        $this->user_id = $this->jwtauth->getProperty($this->session->userdata("JWT"), 'id');
    }

    public function index($nome_utente)
    {
        // se l'utente non esiste mostra la pagina 404
        if (!$this->user->exist(array("nome_utente" => $nome_utente))) show_404();

        // prendo l'id dell'utente della pagina
        $id_nome_utente_url = $this->user->get(array("nome_utente" => $nome_utente), "id")[0]["id"];
        // prendo i dati dell'utente attualmente loggato
        $user_data = $this->user->getUserProfileInfo($id_nome_utente_url, "Utente.id, nome_utente, nome, cognome, Media.mediafile, descrizione, profilo_privato as is_private");
        // prendo il link status tra i due utenti
        $link_status = $this->follow->getLinkStatus($this->user_id, $id_nome_utente_url);
        // controllo se posso vedere il profilo
        $user_data["view_profile"] = !($id_nome_utente_url != $this->user_id && $link_status != 2 && $user_data["is_private"] == 1);

        // carica il template per l'header 
        $this->load->view('templates/header', ["titolo" => "Profilo di @".$user_data["nome_utente"]]);
        
        // carica la view
        $this->load->view('user_page', array(
            "user_page_info" => $user_data,
            "user_logged_info" => $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, Media.mediafile"),
            "link_status" => $link_status
        ));

        // carica il template per il footer 
        $this->load->view('templates/footer');
    }

    public function settings()
    {
        // prende i dati dell'utente attualmente loggato
        $user_data = $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, nome, cognome, email, data_nascita, profilo_privato, Media.mediafile, descrizione");

        // carica il template per l'header 
        $this->load->view('templates/header', ["titolo" => "Impostazioni di @".$user_data["nome_utente"]]);
        
        // carica la view
        $this->load->view('user_settings', ["user_data" => $user_data]);

        // carica il template per il footer 
        $this->load->view('templates/footer');
    }

    public function updateSettings()
    {
        //aggiorno nome utente se non esiste
        //aggiorno nome, email, data nascita
        //aggiorno immagine profilo se è settata;
        $this->load->model('media');
        $this->load->library("UploadManager");
        $user_data = array(
            "nome" => $this->input->post("nome"),
            "cognome" => $this->input->post("cognome"),
            "email" => $this->input->post("email"),
            "data_nascita" => $this->input->post("data_nascita"),
            "descrizione" => $this->input->post("descrizione"),
            "profilo_privato" => $this->input->post("profilo_privato") == "on" ? 1: 0
        );
        if (!$this->user->exist(array("nome_utente" => $this->input->post("nome_utente")))) {
            $user_data["nome_utente"] = $this->input->post("nome_utente");
        }
        $password = $this->input->post("password");
        $new_password = $this->input->post("new_password");
        if ($password != "" && $new_password != "")
            $user_data["password"] = $new_password;
        //cambia foto profilo
        //salvo l'img nel filesystem
        $upload_data = $this->uploadmanager->uploadMedia('./media/' . $this->user_id . '/', "media", "jpeg|jpg|png");
        if ($upload_data != NULL) {
            $this->uploadmanager->crop($upload_data["image_data"]);
            //carico sul db
            $media_id = $this->media->insert(array('mediafile' => $upload_data['mediafile'], 'tipo' => $upload_data['tipo']));
            $user_data["foto_profilo"] = $media_id;
        }
        $this->user->update($user_data, $this->user_id);

        redirect("/user/settings", "refresh");
    }
}
