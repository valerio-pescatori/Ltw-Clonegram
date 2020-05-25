<div id="app">
    <div class="container-fluid container-sm explore-container">
        <navbar :logged_user=logged_user :actual_page="'explore'" :search_bar_visible="false"></navbar>
        <div class="row mb-3">
            <search-modal class="col-12 col-sm-8 px-0"></search-modal>
        </div>
        <div class="row">
            <!-- griglia con post -->
            <div class="col-12 col-sm-8 grid-wrapper px-0">
                <div v-for="i in row_count" :class="i % 2 == 0 ? 'row-large' : 'row-normal'" class="row no-gutters-under-sm">
                    <!-- primo tipo di riga  -->
                    <div v-if="i % 2 == 0" class="col-4">
                        <div class="row left-row">
                            <profile-post stacked="true" :post="getPost((i-1)*3)"></profile-post>
                            <profile-post stacked="true" :post="getPost((i-1)*3 + 1)"></profile-post>
                        </div>
                    </div>
                    <profile-post v-if="i % 2 == 0" :post="getPost((i-1)*3 + 2)"></profile-post>

                    <!-- secondo tipo di riga -->
                    <profile-post v-if="i % 2 != 0" :post="getPost((i-1)*3)"></profile-post>
                    <profile-post v-if="i % 2 != 0" :post="getPost((i-1)*3 + 1)"></profile-post>
                    <profile-post v-if="i % 2 != 0" :post="getPost((i-1)*3 + 2)"></profile-post>
                </div>
                <p class="small text-center text-muted" v-if="posts.length == 0">
                    Non ci sono post da visionare
                </p>
            </div>
            <div class="col-sm-2 d-none d-sm-block">
                <span class="position-fixed mr-4">
                    <follow-suggestions :suggested="suggested"></follow-suggestions>
                </span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var app = new Vue({
        el: '#app',
        components: {
            'follow-suggestions': httpVueLoader('/js/components/follow_suggestions.vue'),
            'profile-post': httpVueLoader('/js/components/profile_post.vue'),
            'navbar': httpVueLoader('/js/components/navbar.vue'),
            'search-modal': httpVueLoader('/js/components/search_modal.vue')
        },
        data: function() {
            return {
                logged_user: <?php echo json_encode($logged_user) ?>,
                posts: <?php echo json_encode($posts) ?>,
                row_count: 0,
                suggested: <?php echo json_encode($suggested) ?>,
            }
        },
        methods: {
            // ritorna il post i-esimo se esiste altrimenti null
            getPost: function(i){
                return i < this.posts.length ? this.posts[i] : null;
            }
        },
        mounted: function() {
            this.row_count = Math.ceil(this.posts.length / 3);
        }
    });
</script>


<style scoped>
    .grid-wrapper>.row {
        margin-top: 30px;
    }

    .grid-wrapper>.row:first-of-type {
        margin-top: unset !important;
    }
     /* applica a tutte le row con no-gutters-under-sm */
    .row.no-gutters-under-sm {
        margin-right: 0;
        margin-left: 0;
    }

    .row.no-gutters-under-sm>.col,
    .row.no-gutters-under-sm>.col-4 {
        padding-left: 0;
        padding-right: 0;
    }

    .row-large>.col-4>div>div:last-of-type {
        margin-top: .25rem;
    }

    .row-large>.col-4>div {
        padding-right: .125rem !important;
        padding-left: .125rem !important;
    }

    .row-large>div:first-of-type,
    .row-normal>div:first-of-type {
        padding-left: .125rem;
    }

    .grid-wrapper>.row {
        margin-top: 4px;
    }

    .row-normal>div:last-of-type {
        margin-right: 0;
    }

    .explore-container {
        margin-bottom: .25rem;
    }

    .search-box {
        padding: 0.125rem !important;
    }

    @media (max-width: 991px) {
        .row-large>div:last-of-type,
        .row-normal>.post {
            padding-right: .125rem !important;
            padding-left: .125rem !important;
        }
    }

    /* Small devices (landscape phones, 576px and up) */
    @media (min-width: 576px) {}

    /* Medium devices (tablets, 768px and up) */
    @media (min-width: 768px) {}

    /* Large devices (desktops, 992px and up) */
    @media (min-width: 992px) {
        .search-box {
            padding: 0 12px !important;
        }

        .left-row>div:last-of-type {
            margin-top: 24px !important;
        }

        .row-large>div:first-of-type {
            padding: 0 !important;
        }

        .row-large>div:first-of-type>.left-row {
            padding-right: 12px !important;
            padding-left: 12px !important;
        }

        .row-large>div:last-of-type {
            padding: 0 !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .grid-wrapper>.row {
            margin-top: 24px;
        }

        .row-normal>.post {
            padding-left: 12px !important;
            padding-right: 12px !important;
            margin: 0 !important;
        }

        .explore-container {
            margin-bottom: 24px;
        }
    }

    /* Extra large devices (large desktops, 1200px and up) */
    @media (min-width: 1200px) {
        .left-row>div:last-of-type {
            margin-top: 11.85%;
        }
    }

</style>