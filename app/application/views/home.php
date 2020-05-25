<div id="home-app" class="container-fluid container-md">
    <!-- navbar -->
    <navbar :logged_user="logged_user_info"></navbar>
    <!-- row con post a sinistra, follow suggestions a destra -->
    <div class="row justify-content-center">
        <!-- lista dei post -->
        <post-list :posts="posts" :logged_user_info="logged_user_info" :scroll_pos="scroll_pos" class="col-12 col-md-8 col-lg-6 mb-3">
        </post-list>
        <!-- follow suggestions -->
        <div class="col-md-4 d-none d-md-block">
            <span class="position-fixed mr-4">
                <follow-suggestions :suggested="suggested">
                </follow-suggestions>
            </span>
        </div>
    </div>

</div>

<script type="text/javascript">
    var home = new Vue({
        el: '#home-app',
        data: {
            // Lista dei post
            posts: <?php echo json_encode($posts); ?>,
            // Info sull'utente loggato
            logged_user_info: <?php echo json_encode($user_logged_info); ?>,
            // Posizione Y attuale nello schermo 
            scroll_pos: 0,
            // Utenti suggeriti
            suggested: <?php echo json_encode($suggested) ?>,
        },
        components: {
            'post': httpVueLoader("/js/components/post.vue"),
            'post-list': httpVueLoader("/js/components/post_list.vue"),
            'navbar': httpVueLoader("/js/components/navbar.vue"),
            'follow-suggestions': httpVueLoader("/js/components/follow_suggestions.vue"),
        },
    });
</script>