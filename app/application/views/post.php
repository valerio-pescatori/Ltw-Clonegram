<div id="post-app">
    <!-- navbar -->
    <navbar :logged_user="logged_user_info"></navbar> 
    <!-- component del post -->
    <div class="container-fluid container-md">
        <div class="row justify-content-center mb-5">
            <post class="col-12 col-sm-10 col-md-8 col-lg-7" :post="post" :logged_user_info="logged_user_info"></post>
        </div>
    </div>
</div>


<script type="text/javascript">
    var app = new Vue({
        el: '#post-app',
        data: {
            // Info sull'utente loggato
            logged_user_info: <?php echo json_encode($user_logged_info); ?>,
            // Il post da visualizzare
            post : <?php echo json_encode($post); ?>,
        },
        components: {
            'navbar' : httpVueLoader("/js/components/navbar.vue"),
            'post' : httpVueLoader("/js/components/post.vue"),
        },
    });
</script>