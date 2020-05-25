<template>
    <div class="small">
		<h6 class="text-muted font-weight-light">Suggerimenti per te</h6>
		<!-- se non ci sono suggerimenti -->
		<div class="text-secondary"  v-if="!suggested.length">
			Non ci sono suggerimenti per te
		</div>
		<!-- se ci sono suggerimenti (una row per suggerimento)-->
		<div class="row suggested-user-row mt-3" v-for="(s, i) in suggested" :key="i" v-else>
			<div class="col">
				<!-- Media con Foto profilo: dati -->
				<div class="media">
					<a :href="'/user/'+s.nome_utente"><img :src="s.foto_profilo" title="Visita profilo" 
						alt="Foto profilo" class="rounded-circle">
					</a>
					<div class="media-body mx-1 mx-md-3 my-auto">
						<a :href="'/user/'+s.nome_utente" title="Visita profilo" class="text-dark text-decoration-none"><h6 class="my-0">{{s.nome_utente}}</h6></a>
						<!-- Scritta "seguito da" -->
						<span class="text-muted font-weight-light mt-3">
							Seguito da {{s.followed_by[0].nome_utente}}
							<span v-if="s.followed_by.length > 1"> + altri {{s.followed_by.length - 1}}</span>
						</span>
					</div>
					<!-- Pulsante per seguire/seguito/inviata -->
					<button class="btn btn-link btn-sm" @click="buttonAction(s)" :disabled="s.follow_status>0"> 
						<span v-if="s.follow_status == 0">
							Segui
						</span>
						<span v-if="s.follow_status == 1">
							Inviata
						</span>
						<span v-if="s.follow_status == 2">
							Seguito
						</span>
					</button>
					<!-- fine pulsante -->
				</div>
			</div>
		</div>
		<!-- fine row suggerimento -->
    </div>
</template>

<script>
module.exports = {
	props: {
        suggested: Object,
	},
	methods: {
		buttonAction: function(user)
		{
			post("/api/follow/"+user.id).then((res) => user.follow_status = res.status);
		}
	},
}
</script>

<style scoped>
	.suggested-user-row img {
		width: 42px;
	}
</style>
