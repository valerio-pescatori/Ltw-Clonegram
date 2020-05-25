<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Likes extends CI_Model{

    public function insert($likes_data) 
    {
        $this->db->insert("Likes", $likes_data);
        return $likes_data;
    }

    public function delete($likes_data){
        $this->db->delete("Likes", $likes_data);
    }

    public function exist($likes_data)
    {
        return !empty($this->get($likes_data, "post, utente_liker"));
    }

    public function get($likes_data, $fields){

        $this->db->select($fields);
        $this->db->from("Likes");
        $this->db->where($likes_data);
        return $this->db->get()->result_array();
    }

    public function getLikesFromPost($post_id) {
        $this->db->select("Utente.id, Utente.nome_utente, Utente.nome, Utente.cognome, Utente.profilo_privato, Likes.data, Media.mediafile");
        $this->db->from("Likes");
        $this->db->join("Utente", "Utente.id = Likes.utente_liker");
        $this->db->join("Media", "Media.id = Utente.foto_profilo");
        $this->db->where("Likes.post", $post_id);
        return $this->db->get()->result_array();
    }
}
