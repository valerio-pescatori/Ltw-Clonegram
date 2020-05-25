<template>
    <div>
        <div class="row">
            <!-- errori -->
            <div class="col alert alert-danger" role="alert" v-if="errors.length">
                <ul>
                    <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                </ul>
            </div>
        </div>
        <div class="row my-3">
            <!-- div per il drop immagini -->
            <div class="col-12 col-lg-7 my-3 my-lg-0">
                <div
                    class="border border-dashed border-md rounded h-100
                            d-flex flex-row align-items-center justify-content-center text-center p-3"
                    @dragenter.prevent
                    @dragleave.prevent
                    @dragover.prevent
                    @drop="drop"
                    v-if="!file_img"
                >
                    <div>
                        <i class="fas fa-cloud-upload-alt fa-4x opacity-2"></i>
                        <div class="file-input">
                            <!-- upload manuale del file -->
                            <label for="file-input"><span class="font-weight-bold"> Seleziona un file</span> oppure trascinalo</label>
                            <input type="file" id="file-input" accept=".jpeg,.jpg,.png,.mp4" @change="inputChange" />
                        </div>
                    </div>
                    
                </div>
                <!-- preview per il video (se carico un video) -->
                <video id="video-preview" 
                    :src="image_url"  
                    v-if="file_img != null && file_img.type == 'video/mp4'" 
                    autoplay controls
                    class="w-100"
                >
                </video>  
                <!-- preview per l'image -->
                <img id="cropper-image"/>
                <!-- icona per rimuovere il media caricato -->
                <span class="badge badge-danger gradient-bg-el mt-3 p-2 rounded-circle float-right" style="cursor: pointer" v-if="file_img" @click="resetMedia">
                    <i class="far fa-trash-alt"></i>
                </span>
            </div>
            <!-- dati del post -->
            <div class="col-12 offset-lg-1 col-lg-4 my-3 my-lg-0">
                <h4 class="text-muted">Carica un post</h4>
                <!-- descrizione -->
                <textarea
                    name="descrizione"
                    class="form-control my-3 small"
                    maxlength="256"
                    v-model="descrizione"
                    style="resize:none;"
                    placeholder="Scrivi una descrizione"
                ></textarea>
                <!-- search per i tag -->
                <search-modal
                    :click-function="(user)=> {tags.push(user.nome_utente);}"
                    :filter-function="res => res.filter(u => !tags.includes(u.nome_utente))"
                    :placeholder="'Tags'"
                    class="mt-3 "
                ></search-modal>
                <!-- div con persone taggate -->
                <div class="row mt-3">
                    <div class="col">
                        <span class="badge badge-pill badge-danger gradient-bg-el p-2 mr-2 mt-2" v-for="(tag, index) in tags" :key="index">
                            @{{ tag }}
                            <!-- icona per annullare il tag -->
                            <i class="delete-tag-btn fas fa-times ml-1" @click="tags.splice(tags.indexOf(tag), 1)"></i>
                        </span>
                    </div>
                </div>
                <input
                    type="submit"
                    class="btn btn-primary btn my-3 btn-block"
                    name="submit"
                    value="Carica post"
                    @click="submit_form"
                />
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: {
        id: null
    },

    components: {
        "search-modal": httpVueLoader("/js/components/search_modal.vue"),
    },

    data: function() {
        return {
            image_url: null,
            crop_info: "",
            cropper: null,
            errors: [],
            tags: [],
            file_is_valid: false,
            descrizione: "",
            file_img: null,
            file_type: ""
        };
    },
    methods: {
        resetMedia: function(){
            // rimuovo il file caricato
            var type = this.file_img.type;
            this.file_img = null;
            this.file_is_valid = false;
            this.image_url = null;
            if(type != 'video/mp4')
            {
                document.getElementById("cropper-image").src = "";
                this.cropper.destroy();
                this.cropper = null;
            }
        },

        inputChange: function(e) {
            // carico un file
            this.file_img = e.target.files[0];
            this.validate_file();
        },
        drop: function(e) {
            e.preventDefault();
            e.stopPropagation();
            this.file_img = e.dataTransfer.files[0];
            this.validate_file();
        },

        validate_file: function() {
            this.onFileChange();
            this.file_is_valid =
                this.file_img.name != "" && this.file_img.name.length > 0;
            if (!this.file_is_valid) this.errors.push("File obbligatorio");
        },

        submit_form: function(e) {
            e.preventDefault();
            var self = this;
            if (!this.file_is_valid) {
                if (!this.errors.includes("File Obbligatorio")) this.errors.push("File Obbligatorio");
                return false;
            }
            // creo un form data a mano
            var formData = new FormData();
            // Nome campo - Valore campo - Nome file
            formData.append("media", this.file_img, this.file_img.value);
            formData.append("desc", this.descrizione);
            this.tags.forEach(tag => {
                formData.append("tags[]", tag);
            });
            formData.append("crop", this.crop_info);
            // richiesta post multipart (per passare file tramite richiesta post)
            post_multipart({
                url: "/upload/post",
                xhr: function() 
                {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.onprogress = function(e) {
                        self.$emit("upload", Math.floor(e.loaded / e.total *100));
                    };
                    return xhr;
                },
                data: formData,
                contentType: false,
                processData: false,
            })
            .then(response => window.location.href = "/post/" + response.id_post )
            .catch(err => self.errors.push("Non è stato possibile caricare il tuo file"));
        },

        onFileChange: function() {
            // carico il cropper
            var self = this;
            if (this.cropper != null) {
                this.cropper.destroy();
            }
            this.image_url = URL.createObjectURL(this.file_img);
            
            if(this.file_img.type == "video/mp4") {
                return;
            }
            
            var img = document.getElementById("cropper-image");
            img.src = this.image_url;

            // salvo i dati sul crop
            // il crop effettivo avviene in back-end
            this.cropper = new Cropper(img, {
                viewMode: 2,
                initialAspectRatio: 1,
                zoomable: false,
                scalable: false,
                aspectRatio: 1,
                autoCropArea: 1,
                movable: false,
                background: false,
                crop: () => { self.crop_info = JSON.stringify(self.cropper.getData()); }
            });
        }
    }
};
</script>

<style scoped>
/* Ensure the size of the image fit the container perfectly */
img {
    display: block;

    /* This rule is very important, please don't ignore this */
    max-width: 100%;
}

.file-input {
    width: 200px;
    margin: auto;
    height: 68px;
    position: relative;
}

.file-input > input {
    width: 100%;
    position: absolute;
    left: 0;
    top: 0;
    padding: 10px;
    border-radius: 4px;
    margin-top: 7px;
    cursor: pointer;
    opacity: 0;
}

.file-input > label {
    background: rgba(0,0,0,0);
    /*color: #2196f3;*/
    width: 100%;
    position: absolute;
    left: 0;
    top: 0;
    padding: 10px;
    border-radius: 4px;
    margin-top: 7px;
}

.delete-tag-btn {
    cursor: pointer;
}

.gradient-bg-el {
		background: rgb(131,58,180);
		background: linear-gradient(90deg, rgba(131,58,180,1) 0%, rgba(182,33,33,1) 100%, rgba(252,176,69,1) 100%);
	}

</style>