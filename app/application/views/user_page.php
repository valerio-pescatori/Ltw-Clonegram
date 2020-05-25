<div id="vue-app"  class="container-lg px-xl-5 px-md-3 px-sm-1 px-0">
    <navbar :logged_user="user_logged_info"></navbar>
    <!-- mostra le info del profilo utente -->
    <profile-info :info.sync=user_page_info :toolong="user_page_info.descrizione.length > 100" :isme="user_logged_info.id == user_page_info.id" :link-status.sync="link_status" :user_page_id=user_page_info.id :user_logged_id=user_logged_info.id></profile-info>
    <ul v-if="user_page_info.view_profile" class="nav justify-content-center border-top user-posts-head">
        <li class="nav-item mr-5">
            <span class="nav-link" @click="if(view_tagged) fetchPosts()" :class="{ 'nav-active': !view_tagged }">
                <i class="fas fa-th"></i>
                Posts
            </span>
        </li>
        <li class="nav-item">
            <span class="nav-link" @click="if(!view_tagged) fetchTaggedPosts()" :class="{ 'nav-active': view_tagged }">
                <i class="fas fa-tags"></i>
                Taggati
            </span>
        </li>
    </ul>
    <!-- mostra gli utenti tramite un v-for -->
    <section class="posts-wrapper" :class="{ 'private': !user_page_info.view_profile || rowCount < 1 }">
        <div v-if="user_page_info.view_profile">
            <div v-for="i in rowCount" class="row row-posts no-gutters mb-lg-4 mb-md-6 mb-sm-4 mb-1">
                <profile-post :post="getPost((i-1)*3)" class="mr-1 mr-sm-2 mr-md-3 mr-lg-4"></profile-post>
                <profile-post :post="getPost((i-1)*3 + 1)" class="mr-1 mr-sm-2 mr-md-3 mr-lg-4"></profile-post>
                <profile-post :post="getPost((i-1)*3 + 2)"></profile-post>
            </div>
            <div v-if="rowCount < 1">
                <p v-if="view_tagged">
                    Nessuna immagine
                </p>
                <p v-else>
                    Ancora nessun post
                </p>    
            </div>
        </div>
        <!-- se il profilo è privato non mostrare i post -->
        <div v-else>
            <p>
                <b>Questo account è privato.</b></br> 
                Segui questo account per vedere foto e video.
            </p>
        </div>
    </section>
</div>

<script type="text/javascript">
    var app = new Vue({
        el: "#vue-app",
        data() {
            return {
                rowCount: 3, /* valore di default */
                posts: [],
                tagged_posts: [],
                user_page_info: <?php echo json_encode($user_page_info); ?>,
                user_logged_info: <?php echo json_encode($user_logged_info); ?>,
                view_tagged: false,
                link_status: <?php echo $link_status; ?>
            }
        },
        components: {
            'profile-post': httpVueLoader("/js/components/profile_post.vue"),
            'profile-info': httpVueLoader("/js/components/profile_info.vue"),
            'navbar': httpVueLoader("/js/components/navbar.vue")
        },
        methods: {
            // prendi il post alla posizione i-esima dell'array posts
            getPost(i){
                if(this.view_tagged && i < this.tagged_posts.length) return this.tagged_posts[i];
                else if(!this.view_tagged && i < this.posts.length) return this.posts[i];
                return null;
            },
            // prendi i post dell'utente
            fetchPosts: function() {
                this.view_tagged=false; 
                var self = this;
                this.rowCount = 1;
                this.posts = [];
                get("/api/getuserposts/" + self.user_page_info.id).then((response) => {
                    self.posts = response;
                    self.rowCount = Math.ceil(self.posts.length / 3);
                });
            },
            // prendi i post in cui l'utente è taggato
            fetchTaggedPosts: function() {
                this.view_tagged = true;
                var self = this;
                this.rowCount = 1;
                this.tagged_posts = [];
                get("/api/getusertaggedposts/" + self.user_page_info.id).then((response) => {
                    self.tagged_posts = response;
                    self.rowCount = Math.ceil(self.tagged_posts.length / 3);
                });
            }
        },
        mounted: function() {
            this.fetchPosts();
        }
    });
</script>

<style scoped>
    .pic-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    header {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }

    .rounded-wrapper {
        height: 10rem;
        overflow: hidden;
    }

    .rounded-wrapper img {
        height: inherit;
    }

    .user-metadata {
        margin-top: 1.25rem;
    }

    .user-metadata {
        border-top: 1px solid #dbdbdb;
        border-bottom: 1px solid #dbdbdb;
    }

    .user-metadata > .row > div {
        text-align: center;
    }

    .user-metadata-button {
        cursor: pointer;
    }

    .user-nav > div {
        text-align: center;
    }

    .user-nav > div > i {
        font-size: 1.4rem;
        color: #dbdbdb;
    }

    .user-nav{
        border-bottom: 1px solid #dbdbdb;
    }

    .user-posts-head {
        border-top: 0 !important;
    }

    .user-posts-head > li > span {
        color: #8e8e8e;
        padding: 1rem 1rem;
        cursor: pointer;
        text-transform: uppercase;
        font-family: 'Roboto', sans-serif;
    }

    .user-posts-head > li:hover > span {
        color: #000;
    }

    .nav-active {
        color: #000 !important;
    }

    .private {
        min-height: 20rem;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        border-bottom: 1px solid #dee2e6!important;
    }

    .posts-wrapper p {
        text-align: center;
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) { 
        .user-metadata {
            border-top: none;
            border-bottom: none;
            margin-bottom: 1.25rem;
        }
        .user-metadata br {
            display: none;
        }
        .user-metadata > .row > div {
            text-align: initial;
        }
        .user-name > h2 {
            text-align: initial;
        }
        .pic-wrapper {
            align-items: unset;
        }
        .rounded-wrapper {
            height: 8rem;
            margin-top: 1rem;
        }
        .user-nav{
            border-top: 1px solid #dbdbdb;
            border-bottom: none;
        }
        .user-posts-head {
            margin-top: 4rem;
            border-top: 1px solid #dee2e6!important;
        }
        .private {
            border: 1px solid #dee2e6!important;
        }

        #error{
            height:44px;
            bottom:-44px; 
        }

        .foto-notifica {
            width: 44px;
        }
    }

    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) { 
        .row-posts {
            margin-bottom: 2rem;
        }
        .pic-wrapper {
            align-items: center;
        }
        .rounded-wrapper {
            height: 10rem;
        }
    }
</style>