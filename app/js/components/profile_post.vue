<template>
    <!-- se il post è null allora nascondi il div -->
    <div class="post" :class="{ 'invisible' : post == null, 'col-12' : stacked, 'col' : !stacked }">
        <a :href="post != null ? post.url : ''">
            <div class="square" >
                <i v-if="post != null && post.tipo == 'video'" class="video-icon fas fa-video fa-lg"></i>
                <div class="loading-wrapper" :class="{ 'invisible': !loadingVisible }">
                    <div class="loading"></div>
                </div>
                <!-- scegli tra una foto o un immagine -->
                <img v-if="post != null && post.tipo == 'img'" v-bind:src="post != null ? post.mediafile : ''" alt="" @load="hideLoading">
                <video v-if="post != null && post.tipo == 'video'" :id="'video-'+post.id_post" preload="metadata" controlslist="nofullscreen" @loadedmetadata="hideLoading">
                    <source :src="post != null ? post.mediafile+'#t=0.5' : ''">
                </video>
                <div class="post-overlay">
                    <i class="fas fa-heart"></i> {{ post != null ? post.likes : '' }}
                    <i class="fas fa-comment"></i> {{ post != null ? post.comments : '' }}
                </div>
            </div>
        </a>
    </div>
</template>

<script>
module.exports = {
    props: {
        post: Object,
        stacked: {
            type: Boolean,
            default: false
        }
    },
    data: function() 
    {
        return {
            loadingVisible: true
        };
    },
    watch: {
        post : function(val) 
        {
            if(val == null) {
                this.loadingVisible = true;
            }
        }
    },
    methods: {
        // se la preview è stata caricata allora elimina i bordi neri dal box contenente la preview
        hideLoading(event) {
            if(this.post != null && this.post.tipo == "video"){
                this.setVideoPreviewSize(document.getElementById('video-'+this.post.id_post));
            }
            this.loadingVisible = false;
        },
        setVideoPreviewSize,
    }
};
</script>

<style>
    .video-icon {
        position: absolute;
        right: 0.8rem;
        top: 1rem;
        color: #fff;
        z-index: 1;
    }

    .square > div {
        position: absolute;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .square > div > div > img {
        width: 100%;
    }

    .user-name > h2 {
        text-align: center;
        margin-top: 0.5rem;
    }

    .post {
        background-color: unset;
    }

    .square > img {
        width: 100%;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    .square > video {
        position: absolute;
    }

    .post-overlay {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.3);
        color: #fff;
        opacity: 0;
    }

    .post-overlay > .fa-heart {
        margin-right: 0.5rem;
    }

    .post-overlay > .fa-comment {
        margin-left: 1.5rem;
        margin-right: 0.5rem;
    }

    .post-overlay:hover {
        opacity: 1;
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) { 
        .row-posts {
            margin-bottom: 0.5rem !important;
        }
    }

    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) { 
        .row-posts {
            margin-bottom: 1rem !important;
        }
    }

    /* Large devices (desktops, 992px and up) */
    @media (min-width: 992px) { 
        .row-posts {
            margin-bottom: 1.5rem !important;
        }
    }
</style>