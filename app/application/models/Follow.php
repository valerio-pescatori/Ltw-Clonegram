<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Follow extends CI_Model
{

    public function __construct(){
        parent::__construct();
        $this->load->model("user");
    }

    public function insert($actor_id, $followed_id) {
        // (1 - .. ) per il not
        $status = 1 - $this->user->get(array("id" => $followed_id), "profilo_privato")[0]["profilo_privato"];
        $this->db->insert("Follow", array("utente_segue" => $actor_id, "utente_seguito" => $followed_id, "accettata" => $status));
        return $status;
    }

    public function update($actor_id, $following_id) {
        $this->db->set('accettata', '1');
        $this->db->where(array('utente_segue' => $actor_id, "utente_seguito" => $following_id));
        $this->db->update('Follow');
    }

    public function getLinkStatus($actor_id, $followed_id) {
        if($actor_id == $followed_id) return 3;
        $privato = $this->user->get(array("id" => $followed_id), "profilo_privato")[0]["profilo_privato"];
        $this->db->select("accettata");
        $this->db->from("Follow");
        $this->db->where(array("utente_seguito" => $followed_id, "utente_segue" => $actor_id));
        $temp_res = $this->db->get()->row();
        if($temp_res == NULL) return 0;
        if($temp_res->accettata == 0 && $privato == 1) return 1;
        return 2;
    }

    public function getLinksStatus($actor_id, $users){
        foreach ($users as &$user) {
            $user['link_status'] = $this->getLinkStatus($actor_id, $user['id']);
        }
        return $users;
    }

    public function delete($actor_id, $followed_id) {
        $this->db->delete('Follow', array("utente_segue" => $actor_id, "utente_seguito" => $followed_id));
    }

    public function getLinkedUsers($type, $id_user, $id_user_logged) {
        if($type != "FOLLOWER" && $type != "FOLLOWING") return array();

        $name = "utente_segue";
        $name_reversed = "utente_seguito";
        
        if($type == "FOLLOWER") {
            $name = "utente_seguito";
            $name_reversed = "utente_segue";
        }

        $where_array = array("Follow.".$name => $id_user);
        //if($type == "FOLLOWER") $where_array["Follow.".$name_reversed." !="] = $id_user_logged;

        $this->db->select("Follow.".$name_reversed." as id, nome_utente, nome, cognome, Media.mediafile");
        $this->db->from("Follow");
        $this->db->join("Utente", "Utente.id = Follow.".$name_reversed);
        $this->db->join("Media", "Media.id = Utente.foto_profilo");
        $this->db->where($where_array);
        $this->db->limit(9);

        $result = $this->db->get()->result_array();
        
        foreach($result as &$user) {
            $this->db->select("accettata, profilo_privato, Utente.id");
            $this->db->from("Follow");
            $this->db->where(array("utente_seguito" => $user["id"], "utente_segue" => $id_user_logged));
            $this->db->join("Utente", "Utente.id = Follow.utente_seguito");
            $temp_res = $this->db->get()->row();
            $user["link_status"] = 0;
            
            if($temp_res == NULL) continue;

            if($temp_res->accettata == 1) {
                $user["link_status"] = 2;
            }
            else if($temp_res->profilo_privato == 1 && $temp_res->accettata == 0) {
                $user["link_status"] = 1;
            }

            if($user["id"] == $id_user_logged) {
                $user["link_status"] = -1;
            }
        }
        return $result;
    }

    public function getFollowSuggestions($user_id)
    {
        $res = $this->db->query("SELECT DISTINCT Utente.id, nome_utente, Media.mediafile as foto_profilo FROM Follow as my_follow 
        JOIN Follow as friend_follow ON my_follow.utente_seguito = friend_follow.utente_segue
        JOIN Utente ON Utente.id = friend_follow.utente_seguito
        JOIN Media ON Media.id = Utente.foto_profilo
        WHERE my_follow.utente_segue = $user_id AND
        Utente.id != $user_id AND friend_follow.utente_seguito NOT IN
        (SELECT Follow.utente_seguito FROM Follow WHERE Follow.utente_segue = $user_id) LIMIT 8")->result_array();
        
        foreach ($res as &$suggestion)
        {
            // Prendo quelli che seguo io, che seguono suggestion
            $this->db->select("Follow.utente_segue");
            $this->db->from("Follow");
            $this->db->where("Follow.utente_seguito", $suggestion['id']);
            // quelli che seguono la suggestion
            $tmp = array_column(($this->db->get()->result_array()), 'utente_segue');

            // quelli che seguo io
            $this->db->select("Utente.nome_utente");
            $this->db->from("Follow");
            $this->db->join("Utente", "Utente.id = Follow.utente_seguito");
            $this->db->where("Follow.utente_segue", $user_id);
            $this->db->where_in("Follow.utente_seguito", $tmp);
            
            $suggestion["followed_by"] = $this->db->get()->result_array();
            $suggestion['follow_status'] = $this->getLinkStatus($user_id, $suggestion["id"]);
        }

        return $res;
             
    }
}

?>