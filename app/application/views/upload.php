<div id="upload-app">
    <!-- navbar con percentage per la progressbar -->
    <navbar :percentage="percentage" :actual_page="'upload'" :logged_user="logged_user_info"></navbar>
    <!-- page upload (in upload.vue) -->
    <div class="container-fluid container-sm small">
        <!-- onUpload aggiorna la progressBar -->
        <upload v-on:upload="new_percentage => percentage = new_percentage" :id="logged_user_info.id"></upload>
    </div>  
</div>

<script>
var upload = new Vue({
    el: "#upload-app",
    components: {
        "upload": httpVueLoader('/js/components/upload.vue'),
        "navbar": httpVueLoader('/js/components/navbar.vue')
    },
    data: function() 
    {
        return {
            logged_user_info: <?php echo json_encode($user_logged_info); ?>,
            percentage: 0
        }
    },
});
</script>