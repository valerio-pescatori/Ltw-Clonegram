<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Upload extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Carico i model e le librerie che serviranno
        $this->load->helper(array('form', 'url','date'));
        $this->load->library(array('LoginValidator', 'JwtAuth', 'session','UploadManager'));
        $this->load->database();
        $this->load->model("user");
        $this->load->model('post');
        $this->load->model('tag');
        
        // se l'utente non è loggato eseguo un redirect alla pagina di login
        if(!$this->loginvalidator->isLoggedIn())
            redirect('/login', 'refresh');
            
        // prendo l'id dell'utente attualmente loggato
        $this->user_id = $this->jwtauth->getProperty($this->session->userdata("JWT"), 'id');
    }

    public function index()
    {
        // carica il template per l'header 
        $this->load->view('templates/header', ["titolo" => "Carica un post"]);
        // Prnedo le info sull'utente loggato
        $user_logged_info = $this->user->getUserProfileInfo($this->user_id, "Utente.id, nome_utente, Media.mediafile");
        // carica la view
        $this->load->view('upload', array("user_logged_info" => $user_logged_info));
        // carica il template per il footer 
        $this->load->view('templates/footer');

    }

    public function uploadPost()
    {
        // Preparo i dati del post da caricare 
        $post_data = array(
            'utente' => $this->user_id,
            'descrizione'  => $this->input->post('desc') != NULL ? $this->input->post('desc') : ""
        );
        
        // Upload del file media sul server
        $upload_data = $this->uploadmanager->uploadMedia('./media/'.$this->user_id.'/', "media", "jpeg|jpg|png|mp4");
        $id_post = 0;
        if($upload_data != NULL) {
            // Inserisco il post
            $id_post = $this->post->insert($post_data);
            // Prendo i tag
            $tags = $this->input->post("tags");
            
            // Li inserisco
            if($tags != NULL) {
                foreach ($tags as $tag) {
                    $tagged_id = $this->user->get( array( "nome_utente" => $tag) , "id")[0]["id"];
                    $this->tag->insert(array(
                        'post' => $id_post,
                        'utente_taggato' =>  $tagged_id
                    ));
                }
            }
            // Crop dell'immagine
            $this->uploadmanager->crop($upload_data["image_data"], 'crop');
            
            // Inserisco sul db
            $id_media = $this->media->insert(array('mediafile' => $upload_data['mediafile'], 'tipo' => $upload_data['tipo']));
            $this->db->insert("PostMedia", array("post" => $id_post, "media" => $id_media));
            
            $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(['id_post' => $id_post]), JSON_PRETTY_PRINT);
        }
        else {
            $this->output
            ->set_content_type("application/json")
            ->set_status_header(500);
        }
    }
}