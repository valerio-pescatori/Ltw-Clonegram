<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("LoginValidator");
        // vedo se è loggato
        if (!$this->loginvalidator->isLoggedIn()) {
            $this->load->helper('url');
            redirect('login', 'refresh');
        }

        // carica i vari model
        $this->load->model('post');
        $this->load->model('user');
        $this->load->model('follow');
        $this->load->model('comment');
        $this->load->model('media');
        $this->load->model('postMedia');
        $this->load->model('tag');
        $this->load->model('notifications');

        //prendo lo user id dell'utente loggato
        $this->user_id = $this->jwtauth->getProperty($this->session->userdata("JWT"), 'id');
    }

    public function getPostHome()
    {
        // Prendo i post della home
        $posts = $this->post->getPosts(array("id_utente" => $this->user_id, "req_type" => "HOME"));

        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($posts), JSON_PRETTY_PRINT);
    }
    
    public function getSinglePost($post_id)
    {
        // Prendo un certo post con un certo id
        $post = $this->post->getPosts(array('id_post' => $post_id, 'req_type' => 'SINGLE'));
        // Se non esiste --> error 404
        if(!isset($post)) show_404();

        // Controllo se l'id dell'utente loggato è quello dell'utente che ha creato il post
        // oppure che il post non sia privato
        // oppure che lo seguo (nel caso in cui sia privato)
        $can_pass = 
            $this->user_id == $post["id_utente"] ||
            !$this->user->is_private($post["id_utente"]) || 
            $this->follow->getLinkStatus($this->user_id, $post["id_utente"]) == 2;

        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($can_pass ? $post : array()), JSON_PRETTY_PRINT);
    }

    public function getExplorePosts(){
        // prendo i post da mostrare nella pagina explore
        $result = $this->post->getPosts(array('req_type' => 'EXPLORE'));
        
        // restituisco una risposta json
        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($result != null ? $result : array()), JSON_PRETTY_PRINT);
    }

    public function getUserProfileInfo($ext_user_id)
    {
        // se l'utente ext è l'utente attualmente loggato o il profilo è privato o gli utenti si seguono allora restituisci una risposta
        $can_pass = 
            $this->user_id == $ext_user_id ||
            !$this->user->is_private($ext_user_id) || 
            $this->follow->getLinkStatus($this->user_id, $ext_user_id) == 2;
        
        // prendo i dati dell'utente che sto visionando e restituisco una risposta json
        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(
                $can_pass ? 
                $this->user->getUserProfileInfo($ext_user_id, "nome_utente, nome, cognome, Media.mediafile, descrizione") : array()
            ));
    }

    public function getUserProfilePosts($ext_user_id = NULL)
    {
        // Controllo che l'utente loggato sia lo stesso di ext_user_id (ovvero l'utente del profilo)
        // oppure che il post non sia privato
        // oppure che l'utente loggato segua l'utente del profilo
        $can_pass = 
            $this->user_id == $ext_user_id ||
            !$this->user->is_private($ext_user_id) || 
            $this->follow->getLinkStatus($this->user_id, $ext_user_id) == 2;

        // Prendo i post
        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(
                $can_pass ?
                $this->post->getPosts(array("id_utente" => $ext_user_id, "req_type" => "PROFILE")) : array()
            ), JSON_PRETTY_PRINT);
    }

    public function getUserProfileTaggedPosts($ext_user_id = NULL)
    {
        // se l'utente ext è l'utente attualmente loggato o il profilo è privato o gli utenti si seguono allora restituisci una risposta
        $can_pass = 
            $this->user_id == $ext_user_id ||
            !$this->user->is_private($ext_user_id) || 
            $this->follow->getLinkStatus($this->user_id, $ext_user_id) == 2;
        
        // prendo i post in cui l'utente è taggato e restituisco una risposta json
        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(
                $can_pass ?
                $this->post->getPosts(array("id_utente" => $ext_user_id, "req_type" => "TAGGED")) : array()
            ), JSON_PRETTY_PRINT);
    }

    public function getUserFollow($imFollower, $ext_user_id)
    {
        // se l'utente ext è l'utente attualmente loggato o il profilo è privato o gli utenti si seguono allora restituisci una risposta
        $can_pass = 
            $this->user_id == $ext_user_id ||
            !$this->user->is_private($ext_user_id) || 
            $this->follow->getLinkStatus($this->user_id, $ext_user_id) == 2;

        // prendi gli utenti che l'utente segue o che seguono l'utente e restituisco una risposta json
        return $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(
                $can_pass ? 
                $this->follow->getLinkedUsers($imFollower == "false" ? "FOLLOWER" : "FOLLOWING", $ext_user_id, $this->user_id) : array()
            ), JSON_PRETTY_PRINT);
    }

    public function putUserFollow($user_followed_id) {
        $status_header = 200;
        $status = -1;
        
        // se l'utente non esiste ritorna 404
        if(!$this->user->exist(array("id" => $user_followed_id))){
            $status_header = 404;
        }
        // se l'utente da seguire è l'utente attualmente loggato ritorna 400
        else if($user_followed_id == $this->user_id) {
            $status_header = 400;
        }
        else {
            // altrimenti inserisci il follow e ritorna lo status
            $status = 1 + $this->follow->insert($this->user_id, $user_followed_id);
        }
        
        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header($status_header)
            ->set_output(json_encode(array("status" => $status)), JSON_PRETTY_PRINT);
    }

    public function putUserFollower($user_followed_id) {
        $status_header = 200;
        $status = -1;
        
        // se l'utente non esiste ritorna 404
        if(!$this->user->exist(array("id" => $user_followed_id))){
            $status_header = 404;
        }
        // se l'utente da seguire è l'utente attualmente loggato ritorna 400
        else if($user_followed_id == $this->user_id) {
            $status_header = 400;
        }
        else {
            // altrimenti esegui un update su follow e ritorna lo status
            $this->follow->update($user_followed_id, $this->user_id);
            $status = 2;
        }

        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header($status_header)
            ->set_output(json_encode(array("status" => $status)), JSON_PRETTY_PRINT);
    }

    public function deleteUserFollow($user_followed_id, $param = false) {
        // se param è true rimuovo il following altrimenti rimuovo il follower
        if($param) 
            $this->follow->delete($user_followed_id, $this->user_id);
        else
            $this->follow->delete($this->user_id, $user_followed_id);

        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(array("status" => 0)), JSON_PRETTY_PRINT);
    }

    private function setReaction($action = "ADD") {
        // Prende il post su cui aggiungere/eliminare la reaction
        $post_id = $this->input->post('id_post');
        // Se non esiste --> 404
        if(!$this->post->exist(['id' => $post_id])) {
            $this->output
                ->set_content_type("application/json")
                ->set_status_header(404)
                ->set_output(json_encode(array("error" => "No post found")), JSON_PRETTY_PRINT);
        }
        // Mi creo l'istanza del like da cercare nel db
        $instance = array("post" => $post_id, "utente_liker" => $this->user_id);
        $reaction_status = 0;
        
        // Aggiungo o cancello il like
        switch($action) {
            case "ADD":
                if(!$this->likes->exist($instance)) {
                    $this->likes->insert($instance);
                    $reaction_status = 1;
                }
            break;
            case "DELETE":
                if($this->likes->exist($instance)) {
                    $this->likes->delete($instance);
                    $reaction_status = 1;
                }
            break;
        }

        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(array(
                "reaction_status" => $reaction_status,
                "likes" => $this->likes->getLikesFromPost($post_id)
            )), JSON_PRETTY_PRINT);

    }

    private function setComment($action = "ADD") {
        // Prendo l'id del post su cui aggiungere/rimuovere il commento
        $post_id = $this->input->post('id_post');

        // Se il post non esiste --> 404
        if(!$this->post->exist(['id' => $post_id])) {
            $this->output
                ->set_content_type("application/json")
                ->set_status_header(404)
                ->set_output(json_encode(array("error" => "No post found")), JSON_PRETTY_PRINT);
        }

        // Mi creo l'istanza da cercare nel db
        $instance = array("post" => $post_id, "utente" => $this->user_id);

        // Aggiungo o elimino il commento
        switch($action) {
            case "ADD":
                $instance["commento"] = $this->input->post('comment');
                if(strlen($instance["commento"]) < 1) {
                    $this->output
                        ->set_content_type("application/json")
                        ->set_status_header(400)
                        ->set_output(json_encode(array("error" => "Comment has no text")), JSON_PRETTY_PRINT);
                }
                $this->comment->insert($instance);
            break;
            case "DELETE":
                $instance["data"] = $this->input->post('data');
                $this->comment->delete($instance);
            break;
        }

        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($this->comment->getCommentsFromPost($post_id, true)), JSON_PRETTY_PRINT);
    }

    // aggiunge un like
    public function putReaction() { $this->setReaction("ADD"); }
    // rimuove un like
    public function deleteReaction() { $this->setReaction("DELETE"); }
    // aggiunge un commento
    public function putComment() { $this->setComment("ADD"); }
    // rimuove un commento
    public function deleteComment() {$this->setComment("DELETE"); }

    // Funzione per la ricerca di utenti
    public function getResearchedUsers($search_string) {
        $search_string = urldecode($search_string);
        $users = $this->user->get(array("id != " => $this->user_id), "nome_utente, id, nome, cognome", 20, array("nome_utente" => $search_string, "nome" => $search_string, "cognome" => $search_string));
        foreach($users as &$user) {
            $user['foto_profilo'] = $this->user->getProfilePicture($user['id']);
            $user['url'] = "/user/".$user["nome_utente"];
        }
            
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($users), JSON_PRETTY_PRINT);
    }

    // Funzione per eliminare un post
    public function removePost()
    {
        // Prendo l'id del post da eliminare
        $id_post = $this->input->post('id_post');
        $status = 400;
        // se il post esiste
        if($this->post->exist(array('id' => $id_post, 'utente' => $this->user_id)))
        {
            // Prendi media da postmedia
            $medias = $this->media->get(array(), 'Media.id, mediafile, tipo', $id_post);
            
            // cancella il post
            $this->post->remove(array('id' => $id_post));

            // cancella media dal server e dal db
            foreach($medias as $media)
            {
                $this->media->remove(array('id' => $media['id']));
                unlink('/usr/share/nginx/html'.$media['mediafile']);
            }
            $status = 200;
        }

        $this->output
            ->set_content_type("application/json")
            ->set_status_header($status)
            ->set_output(json_encode(""), JSON_PRETTY_PRINT);
        
    }

    // Funzione che elimina il tag da un post
    public function removeTag()
    {
        // Prende l'id del post e l'id dell'utente taggato
        $id_post = $this->input->post('id_post');
        $id_utente = $this->input->post('tagged_user');
        
        $status = 400;
        // il record da cercare
        $tag_data = array('post' => $id_post, 'utente_taggato' => $id_utente);
        // Se esiste un tag
        if($this->tag->exist($tag_data))
        {
            //togli il tag
            $this->tag->remove($tag_data);
            $status = 200;
        }
        
        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header($status)
            ->set_output(json_encode(""), JSON_PRETTY_PRINT);
    }

    public function getNotifications()
    {
        // prendo le notifiche da mostrare all'utente attualmente loggato
        $notifications = $this->notifications->getNotifications($this->user_id);
        
        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($notifications), JSON_PRETTY_PRINT);

    }

    public function putNotificationAsViewed()
    {
        // imposto la notifica come vista
        $this->notifications->setViewed($this->user_id);
        
        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode(""), JSON_PRETTY_PRINT);
    }

    public function getFollowSuggestions()
    {
        // prendo gli utenti suggeriti da mostrare all'utente
        $suggestions = $this->follow->getFollowSuggestions($this->user_id);
        
        // restituisco una risposta json
        $this->output
            ->set_content_type("application/json")
            ->set_status_header(200)
            ->set_output(json_encode($suggestions), JSON_PRETTY_PRINT);
    }

}
