<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notifications extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->model("likes");    // type = 0
        $this->load->model("comment");  // type = 1
        $this->load->model("tag");      // type = 2
        $this->load->model("follow");   // type = 3
    }

    /*array(
        [0] => array(
            nome_utente => ""
            nome => ""
            cognome => ""
            foto_profilo => ""
            foto_post => "" (se è un commento / like / tag)
            data => ""
            type => ""
        )
    )
    */

    public function setViewed($user_id){
        
        // Setta 'visto' a 1 su Follow
        $this->db->set('visto', '1');
        $this->db->where('utente_seguito', $user_id);
        $this->db->update('Follow');
        
        // Setta 'visto' a 1 su Tag
        $this->db->set('visto', '1');
        $this->db->where('utente_taggato', $user_id);
        $this->db->update('Tag');

        // Setta 'visto' a 1 su Commento
        $this->db->query("UPDATE Commento JOIN Post ON Post.id = Commento.post SET Commento.visto = '1' WHERE Post.utente = '".$user_id."'");

        // Setta 'visto' a 1 su Likes
        $this->db->query("UPDATE Likes JOIN Post ON Post.id = Likes.post SET Likes.visto = '1' WHERE Post.utente = '".$user_id."'");
    }

    public function getNotifications($user_id)
    {
        $result = array();

        // likes
        $this->db->select("(0) as tipo, Utente.nome_utente, (CONCAT('/user/', nome_utente)) as url_user,
            (CONCAT('/post/', Post.id)) as url_post , m2.mediafile as foto_profilo, m1.mediafile as foto_post,
            Likes.data as data, Likes.visto, m1.tipo as tipo_media");
        $this->db->from("Post");
        $this->db->where("Post.utente", $user_id);
        $this->db->join("PostMedia", "Post.id = PostMedia.post");
        //join con media per foto post
        $this->db->join("Media as m1", "PostMedia.media = m1.id");
        $this->db->join("Likes", "Likes.post = Post.id");
        $this->db->join("Utente", "Likes.utente_liker = Utente.id");
        //join con media per foto profilo
        $this->db->join("Media as m2", "Utente.foto_profilo = m2.id");
        $this->db->where("Likes.utente_liker !=", $user_id);
        $this->db->order_by("Likes.data", "DESC");
        $this->db->limit(100);
        $result = $this->db->get()->result_array();

        //commenti
        $this->db->select("(1) as tipo, Utente.nome_utente, (CONCAT('/user/', nome_utente)) as url_user,
            (CONCAT('/post/', Post.id)) as url_post, m2.mediafile as foto_profilo, m1.mediafile as foto_post,
            Commento.data as data, SUBSTRING(Commento.commento, 1, 50) as commento, Commento.visto, m1.tipo as tipo_media");
        $this->db->from("Post");
        $this->db->where("Post.utente", $user_id);
        $this->db->join("PostMedia", "Post.id = PostMedia.post");
        //join con media per foto post
        $this->db->join("Media as m1", "PostMedia.media = m1.id");
        $this->db->join("Commento", "Commento.post = Post.id");
        $this->db->join("Utente", "Commento.utente = Utente.id");
        //join con media per foto profilo
        $this->db->join("Media as m2", "Utente.foto_profilo = m2.id");
        $this->db->where("Commento.utente !=", $user_id);
        $this->db->order_by("Commento.data", "DESC");
        $this->db->limit(100);
        
        $result = array_merge($result, $this->db->get()->result_array());

        //tags
        $this->db->select("(2) as tipo, Tag.data as data, Utente.nome_utente, (CONCAT('/post/', Post.id)) as url_post, 
            (CONCAT('/user/', nome_utente)) as url_user, m1.mediafile as foto_post, m2.mediafile as foto_profilo, Tag.visto, m1.tipo as tipo_media");
        $this->db->from("Tag");
        $this->db->where("utente_taggato", $user_id);
        $this->db->join("Post", "Post.id = Tag.post");
        $this->db->join("PostMedia", "Post.id = PostMedia.post");
        $this->db->join("Media as m1", "PostMedia.media = m1.id");
        $this->db->join("Utente", "Post.utente = Utente.id");
        $this->db->join("Media as m2", "Utente.foto_profilo = m2.id");
        $this->db->order_by("Tag.data", "DESC");
        $this->db->limit(100);

        $result = array_merge($result, $this->db->get()->result_array());

        //follow
        $this->db->select("(3) as tipo, Follow.accettata, nome_utente, (CONCAT('/user/', nome_utente)) as url_user, 
            mediafile as foto_profilo, Utente.id as utente_segue, Follow.data as data, Follow.visto");
        $this->db->from("Follow");
        $this->db->where("Follow.utente_seguito", $user_id);
        $this->db->join("Utente", "Utente.id=Follow.utente_segue");
        $this->db->join("Media", "Media.id = Utente.foto_profilo");
        $this->db->order_by("Follow.data", "DESC");
        $this->db->limit(100);
        
        $result = array_merge($result, $this->db->get()->result_array());

        // Controllo per vedere se ci sono notifiche non viste e convertiamo la data da str a time
        foreach ($result as &$notifica) {
            $notifica["data"] = strtotime($notifica["data"]) * 1000;
        }

        array_multisort(array_column($result, 'data'), SORT_DESC, $result);
        $result = array_slice($result, 0, 100);

        $new = FALSE;
        foreach ($result as &$notifica) {
            if($notifica["visto"] == 0)
            {
                $new = TRUE;
                break;
            }
        }

        return array( "nuove_notifiche" => $new, "notifiche" => $result);
    }
};
