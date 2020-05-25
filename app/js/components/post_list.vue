<template>
	<div>
		<!-- lista dei post -->
		<div :key="i" v-for="(post, i) in loaded_posts" class="row">
			<post :post="post" :logged_user_info="logged_user_info" :is_home_post="true" class="col-12 mb-5 px-0 px-sm-3">
			</post>
		</div>
		<!-- se non ci sono post, mostra la scritta "non ci sono post" -->
        <p class="small text-center text-muted" v-if="posts.length == 0">
            Non ci sono post da visionare, inizia a seguire i tuoi amici!
        </p>
	</div>
</template>

<script>
	module.exports = {
		props: { 
			// Lista dei post
			posts: Object,
			// Info sull'utente loggato
			logged_user_info: Object,
		},
		components: {'post': httpVueLoader('/js/components/post.vue')},
		data: function()
		{
			return {
				// Post inizialmente caricati
				loaded_posts: this.posts.slice(0,10),
			}
		},
		mounted: function()
		{
			var self = this;
			// per il caricamento di 10 post alla volta
			window.addEventListener('scroll', ()=> {self.loaded_posts = self.scroll(self.loaded_posts, self.posts, 10)});
		},
		methods: {
			// In utils.js
			scroll,
		}	
	}
</script>
