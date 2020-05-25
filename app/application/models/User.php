<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {
    private $hash_key = "password_hash_key";

    public function insert($user_data) {
        if(isset($user_data["password"])) {
            $user_data["password"] = hash_hmac("sha256", $user_data["password"], $this->hash_key);
        }
        $this->db->insert("Utente", $user_data);
        return $this->db->insert_id();
    }

    public function exist($user_data) {
        return count($this->get($user_data, "id")) > 0;
    }

    public function update($user_data, $id)
    {
        if(isset($user_data["password"])) {
            $user_data["password"] = hash_hmac("sha256", $user_data["password"], $this->hash_key);
        };
        $this->db->set($user_data);
        $this->db->where("id", $id);
        $this->db->update("Utente");
        
    }

    public function is_private($user_id){
        $this->db->select("profilo_privato");
        $this->db->from("Utente");
        $this->db->where("id ='".$user_id."'");
        $res = $this->db->get()->row();

        if($res != NULL) return $res->profilo_privato == 1;
        return false;

    }

    public function get($user_data, $fields, $limit = NULL, $user_like_data = NULL, $like_type = "after") {
        if(isset($user_data["password"])) {
            $user_data["password"] = hash_hmac("sha256", $user_data["password"], $this->hash_key);
        }
        $this->db->select($fields);
        $this->db->from("Utente");
        $this->db->where($user_data);
        if(isset($user_like_data)) 
        {
            $this->db->group_start();
            $this->db->or_like($user_like_data, $like_type);
            $this->db->group_end();
        }
        if(isset($limit)) $this->db->limit($limit);
        return $this->db->get()->result_array();
    }


    public function getProfilePicture($id)
    {
        $this->db->select("mediafile");
        $this->db->from("Media");
        $this->db->join("Utente", "Utente.foto_profilo = Media.id");
        $this->db->where("Utente.id", $id); // <-- Questo id si può sostituire con $this->id ???

        return $this->db->get()->result_array()[0]['mediafile'];
    }
    
    public function getUserProfileInfo($id, $fields)
    {
        $arr = [];
        $this->db->from("Follow");
        $this->db->where("utente_segue", $id);
        $arr["numero_following"] = $this->db->count_all_results();
        $this->db->from("Follow");
        $this->db->where("utente_seguito", $id);
        $arr["numero_followers"] = $this->db->count_all_results();
        $this->db->from("Post");
        $this->db->where("utente", $id);
        $arr["numero_post"] = $this->db->count_all_results();
        $this->db->select($fields);
        $this->db->from("Utente");
        $this->db->join("Media", "foto_profilo = Media.id");
        $this->db->where("Utente.id", $id);
        return array_merge($arr, $this->db->get()->result_array()[0]);
    }
}