<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadManager {
    private $CI;
    private $MAX_IMG_FILE_SIZE = 5000;
    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        $this->CI->load->model('media');

    }

    public function crop($image_data, $crop_field_name = NULL)
    {
        // Se non è un'immagine --> non fare il crop
        if(!$image_data['is_image'] || !isset($image_data['is_image'])) return false;
        // Prende le info sul crop
        $crop = $this->CI->input->post($crop_field_name);
        if ($crop_field_name != NULL)
            //crop solo se faccio upload da post
            $crop_info = json_decode(strval($crop));
        
        // Dimensione minima dell'immagine
        $min_dim = intval(min($image_data["image_width"], $image_data["image_height"]));
        // Configurazione del crop
        $config_crop = array(
            "image_library" => "gd2",
            "library_path" => "gd/gd2.so",
            "source_image" => $image_data["full_path"],
            "maintain_ratio" => FALSE,
            "x_axis" => isset($crop_info->x) ? intval($crop_info->x) : 0,
            "y_axis" => isset($crop_info->y) ? intval($crop_info->y) : 0,
            "width" => isset($crop_info->width) ? intval($crop_info->width) : $min_dim,
            "height" => isset($crop_info->height) ? intval($crop_info->height) : $min_dim
        );

        // Se non gli viene passato nessun dato su come fare il crop --> croppa al centro
        if($crop_field_name == NULL)
        {
            if($config_crop["height"] == $min_dim)
                $config_crop["x_axis"] = (($image_data['image_width'] / 2) - ($min_dim/ 2));
            
            else
                $config_crop["y_axis"] = (($image_data['image_height'] / 2) - ($min_dim / 2));
        }
        $this->CI->load->library('image_lib', $config_crop);
        // croppa
        return $this->CI->image_lib->crop();
    }

    public function uploadMedia($path, $field_name, $allowed_types, $filename = NULL) 
    {
        // Config per l'upload
        $config['upload_path'] = $path;
        $config['allowed_types'] = $allowed_types;
        $config['max_size'] = $_FILES[$field_name]['type'] == "video/mp4" ? '10000' : '5000';
        $config['file_name'] = $filename != NULL ? $filename : strval(time("now"));

        // upload 
        $this->CI->load->library('upload', $config);
        if(!$this->CI->upload->do_upload($field_name)) {
            return NULL;
        }

        $image_data = $this->CI->upload->data();
        
        return array(
            "mediafile" => ltrim($path, '.').$config['file_name'].$image_data['file_ext'],
            "tipo" => $image_data['is_image'] ? 'img' : 'video',
            "image_data" => $image_data
        );
    }

}