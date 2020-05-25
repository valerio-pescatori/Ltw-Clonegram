<template>
	<div>
		<!-- modal per i likes -->
		<modal-utenti
			:logged-user-id="logged_user_info.id"
			:users="post.likes"
			:modal-title="'Likes'"
			:modal-id="'modal'+post.id_post"
		></modal-utenti>
		<!-- card del post -->
		<div class="card shadow-sm">
			<div class="card-header bg-white">
				<!-- foto profilo e nome utente | classe media per vertical align-->
				<div class="media mt-1">
					<img class="rounded-circle mr-3" width="48" :src="post.foto_profilo" alt="Foto profilo" />
					<a :href="'/user/'+post.nome_utente" title="Visita profilo" class="text-dark">
						<h6 class="card-title align-self-center mt-2">{{post.nome_utente}}</h6>
					</a>
					<!-- INIZIO dropdown per togliere post o il tag -->
					<div
						class="dropdown show ml-auto align-self-center dropleft"
						v-if="im_tagged || post.nome_utente == logged_user_info.nome_utente"
					>
						<!-- icona per dropdown ( ... 3-dot ) -->
						<i data-toggle="dropdown" class="cursor-pointer fal fa-ellipsis-h fa-2x text-muted"></i>

						<!-- menu dropdown -->
						<div class="dropdown-menu small">
							<span class="dropdown-header">Azioni disponibili</span>
							<div class="dropdown-divider"></div>
							<a
								title="Rimuovi il post"
								v-if="post.nome_utente == logged_user_info.nome_utente"
								href="#"
								class="dropdown-item"
								@click.prevent="removePost"
							>
								<i class="fas fa-trash mr-3"></i>
								Elimina post
							</a>
							<div
								class="dropdown-divider"
								v-if="im_tagged && post.nome_utente == logged_user_info.nome_utente"
							></div>
							<a
								title="Rimuovi il tag"
								v-if="im_tagged"
								href="#"
								class="dropdown-item"
								@click.prevent="removeTag"
							>
								<i class="fas fa-user-minus mr-3"></i>
								Rimuovi il tag
							</a>
						</div>
					</div>
					<!-- FINE dropdown per togliere post o il tag -->
				</div>
			</div>

			<!-- immagini/video -->
			<div class="img-container square" @dblclick="leaveLike" @click="toggleMute()">
				<div class="content">
					<div class="loading-wrapper" :class="{ 'invisible': !loadingVisible }">
						<div class="loading"></div>
					</div>

					<img
						class="card-img-top rounded-0"
						:src="post.mediafile"
						v-if="post.tipo == 'img'"
						@load="loadingVisible=false"
					/>
					<div class="video-wrapper" v-else>
						<video
							:id="'video-post'+post.id_post"
							class="card-img-top h-100 w-100"
							muted
							loop="true"
							controlslist="nofullscreen"
							@timeupdate="timeUpdate"
							@volumechange="volumeChange"
							@loadedmetadata="loadingVisible=false"
						>
							<source :src="post.mediafile" type="video/mp4" />
						</video>
					</div>

					<!-- cuore animazione like -->
					<div class="card-img-overlay d-flex align-items-center justify-content-center">
						<i
							id="heart-like"
							class="fas fa-heart text-white opacity-7 position-absolute"
							:class="{'like-animation' : animated}"
						></i>
						<!-- tempo di riproduzione e icona audio -->
						<div
							class="flex-row flex-grow-1 align-self-end d-flex align-items-center justify-content-between"
						>
							<p
								class="text-light mb-0 bg-dark rounded p-2 opacity-7 font-weight-light"
								v-if="post.tipo != 'img'"
							>{{time}}</p>
							<i
								class="text-light bg-dark rounded-circle p-2"
								ref="icon"
								:class="{'fal fa-volume-up': muted, 'fal fa-volume-slash': !muted}"
								v-if="post.tipo != 'img'"
								style="transition: 1s; opacity: 0;"
							></i>
						</div>
					</div>
				</div>
			</div>
			<!-- FINE immagini/video -->
			<div class="gradient-img-bottom gradient-bg-main"></div>
			<div class="card-body small">
				<!-- Pulsante like -->
				<span class="cursor-pointer card-text">
					<i v-if="!is_liked" @click="leaveLike" class="far fa-heart h5"></i>
					<i v-else @click="removeLike" class="text-danger fa fa-heart h5"></i>
				</span>
				<!-- Piace a X persone -->
				<span class="card-subtitle mx-1 font-weight-normal" v-if="post.likes.length">
					Piace a
					<a
						:href="'/user/'+post.likes[0].nome_utente"
						title="Visita questo profilo"
						class="text-dark font-weight-bold"
					>{{post.likes[0].nome_utente}}</a>
					<span v-if="post.likes.length > 1">
						e
						<a
							:href="'/user/'+post.likes[1].nome_utente"
							v-if="post.likes.length == 2"
							title="Visita questo profilo"
							class="text-dark font-weight-bold"
						>{{post.likes[1].nome_utente}}</a>
						<a
							v-else
							href="#"
							data-toggle="modal"
							:data-target="'#modal'+post.id_post"
							class="text-dark font-weight-bold"
						>altri {{post.likes.length-1}}</a>
					</span>
				</span>
				<!-- descrizione post -->
				<span class="card-text text-dark d-block">
					<span class="font-weight-bolder text-dark">{{post.nome_utente}}</span>
					<div class="d-inline" v-if="show_more">
						<span style="white-space: pre-line">{{post.descrizione.substring(0,100)}}...</span>
						<span>
							<a
								:href="'#collapseDescription'+post.id_post"
								data-toggle="collapse"
								@click="show_more=!show_more"
							>Mostra altro</a>
						</span>
					</div>
					<div class="d-inline" v-if="!show_more">
						<span
							style="white-space: pre-line"
							:id="'#collapseDescription'+post.id_post"
						>{{post.descrizione}}</span>
					</div>
				</span>
				<!-- utenti taggati -->
				<div class="my-3">
					<span
						class="badge badge-pill badge-danger p-2 gradient-bg-el mt-2 mr-2"
						v-for="(tag, i) in post.tags"
						:key="i"
					>
						<a
							:href="'/user/'+tag.nome_utente"
							class="text-white text-decoration-none"
							title="Visita questo profilo"
						>
							<i class="fas fa-tags mr-1"></i>
							{{tag.nome_utente}}
						</a>
						<i
							class="fas fa-times ml-2 cursor-pointer"
							v-if="post.id_utente == logged_user_info.id || tag.id == logged_user_info.id"
							@click="removeTag(tag.id)"
						></i>
					</span>
				</div>
				<!-- commenti -->
				<div v-if="!is_home_post">
					<comment
						class="my-2"
						:key="i"
						v-for="(comment, i) in post.comments"
						:comment="comment"
						:logged_user_info="logged_user_info"
						:remove-comment="removeComment"
					></comment>
				</div>
				<div v-else>
					<span v-if="post.comments.length > 2">
						<a
							class="card-text"
							title="Visualizza altri commenti"
							:href="'/post/'+post.id_post"
						>Visualizza gli altri {{post.comments.length - 2}} commenti</a>
					</span>
					<comment
						class="my-2"
						v-for="(comment, i) in limitComments"
						:key="i"
						:comment="comment"
						:logged_user_info="logged_user_info"
						:remove-comment="removeComment"
					></comment>
				</div>
				<!-- FINE commenti -->
				<span class="card-text text-muted">{{dateFormat(post.data)}}</span>
			</div>
			<!-- "aggiungi commento" input field -->
			<div class="card-footer bg-white d-flex">
				<textarea
					type="text"
					class="input-comment small border-0 form-control"
					v-model="input_comment"
					@keyup.enter.exact="leaveComment"
					@keydown.enter.exact.prevent
					@keydown.enter.shift.exact="newline"
					placeholder="Aggiungi un commento..."
				></textarea>
				<!-- Pulsante pubblica -->
				<button
					class="btn btn-link btn-sm text-decoration-none"
					:disabled="!valid_comment"
					@click="leaveComment"
				>Pubblica</button>
			</div>
		</div>
	</div>
</template>

<script>

module.exports = {
	props: {
		// Post da caricare
		post: Object,
		// Info utente loggato
		logged_user_info: Object,
		// Booleano che mi dice se è un post visto dalla home
		is_home_post: Boolean,
		// Posizione Y sulla window
		scroll_pos: Number,
	},
	data: function () {
		return {
			// Commento in input
			input_comment: '',
			// Booleano usato per l'animazione del like
			animated: false,
			// Booleano per capire se mostrare tutta la descrizione o meno
			show_more: this.post.descrizione.length > 100,
			// Video da visualizzare
			video: null,
			// Se ho lasciato like
			is_liked: false,
			// Tempo trascorso dall'inizio del video da mostrare
			time: "00:00",
			// Se il video è mutato
			muted: true,
			// Per l'animazione di caricamento
			loadingVisible: true,
		}
	},
	components: {
		'comment': httpVueLoader('/js/components/comment.vue'),
		'modal-utenti': httpVueLoader('/js/components/modal_utenti.vue'),
	},
	computed: {
		// Mi dice se sono taggato in un post
		im_tagged: function () {
			return this.post.tags.map(t => t.nome_utente).includes(this.logged_user_info.nome_utente)
		},
		// Mi dice se il commento in input è valido
		valid_comment: function () {
			return this.input_comment != null && this.input_comment != '';
		},

		// test => filtra i commenti a 2
		limitComments: function() {
			return this.post.comments.slice(0,2);
		}
	},
	mounted: function () {
		// Check per vedere se ho gia messo like
		if (this.post.likes.map(like => like.nome_utente).includes(this.logged_user_info.nome_utente))
			this.is_liked = true;
		// Sullo scroll vede il video è nella viewport
		window.addEventListener('scroll', this.checkVideo);
		// setto il video
		this.video = document.getElementById('video-post' + this.post.id_post);
		this.checkVideo();
	},
	methods: {
		// Funzione che controlla se un video è nella viewport
		checkVideo: function () {
			// se non è un video
			if (!this.video) return;
			// Top e bottom del video 
			var top = this.video.getBoundingClientRect().top;
			var bottom = this.video.getBoundingClientRect().bottom;
			// se è nella viewport fai partire il video
			if (top >= 0 && bottom <= (window.innerHeight || document.documentElement.clientHeight))
				this.video.play();
			// altrimenti va in pausa
			else this.video.pause();
		},
		// disattiva/riattiva l'audio
		toggleMute: function () {
			if (!this.video) return;
			this.video.muted = !this.video.muted;
		},
		// Restituisce il nome del mese
		getMonthName,
		// Restituisce la data
		dateFormat,
		// Funzione che lascia mi piace
		leaveLike: function () {
			var self = this;
			//ajax post request al db
			post("/api/reaction/add", { "id_post": self.post.id_post })
				.then(res => {
					self.post.likes = res.likes;
					self.is_liked = true;
				});
			self.animated = true;
			setTimeout(() => self.animated = false, 1000)
		},
		// Funzione che lascia un commento
		leaveComment: function (event) {
			var self = this;
			if (!self.valid_comment) return;
			//ajax post request al db
			post("/api/comment/add", { 'id_post': self.post.id_post, 'comment': self.input_comment })
				.then(res => {
					self.post.comments = res;
					self.input_comment = '';
				})
				.catch(err => console.log(err));
		},
		// Funzione che rimuove il like dal post
		removeLike: function (event) {
			var self = this;
			// ajax post request al db
			post("/api/reaction/delete", { 'id_post': this.post.id_post })
				.then(res => {
					self.post.likes = res.likes;
					self.is_liked = false;
				});
		},
		// Funzione che rimuove uno specifico commento da un post
		removeComment: function (comment) {
			var self = this;
			post("/api/comment/delete", { 'id_post': self.post.id_post, 'data': comment['data'] })
				.then(res => {
					self.post.comments = res;
				});
		},
		// Funzione che rimuove il post
		removePost: function () {
			var self = this;
			post('/post/remove', { 'id_post': this.post.id_post })
				.then(res => window.location.href = "/user/" + self.logged_user_info.nome_utente)
		},
		// Funzione che elimina un utente "tagged_id" dai tag
		removeTag: function (tagged_id = this.logged_user_info.id) {
			var self = this;
			post('/tag/remove', { "id_post": self.post.id_post, "tagged_user": tagged_id })
				.then(res => self.post.tags = self.post.tags.filter(t => t.id != tagged_id));
		},
		// Funzione che aggiorna il tempo corrente di un video
		timeUpdate: function (e) {
			cur_time = e.target.currentTime
			this.time = (Math.floor(cur_time / 60) > 9 ? Math.floor(cur_time / 60) : "0" + Math.floor(cur_time / 60)) +
				":" + (Math.floor(cur_time % 60) > 9 ? Math.floor(cur_time % 60) : "0" + Math.floor(cur_time % 60));
		},
		volumeChange: function (e) {
			this.muted = !this.muted;
			//l'if serve altrimenti perché il primo trigger dell'evento avviene prima che inizi la riproduzione quindi l'icona apparirebbe troppo presto
			if (e.target.currentTime > 0.1);
			{
				var icon = this.$refs.icon;
				icon.style.opacity = 0.7;
				setTimeout(() => icon.style.opacity = 0, 2000);
			}
		},
	},

}


</script>


<style scoped>
/* CSS per rendere il video 1:1 */
.video-wrapper {
	position: relative;
	background-color: #000;
}

.video-wrapper:after {
	content: "";
	display: block;
	padding-bottom: 100%;
}

video {
	position: absolute;
	top: 50%;
	transform: translateY(-50%);
	width: 100%;
}

/* Container del media */
.img-container {
	position: relative;
}

/* Cuore del like inizialmente grande 0px */
#heart-like {
	font-size: 0px;
}

/* CSS per l'input del commento */
.input-comment,
.input-comment:active {
	outline: none !important;
	-webkit-appearance: none;
	box-shadow: none !important;
	resize: none;
	height: 2.5rem;
}

/* css per il gradiente sotto la foto */
.gradient-img-bottom {
	height: 4px;
}

/* Classe da aggiungere per l'animazione del cuore sul like */
.like-animation {
	animation: like 0.8s;
}

/* Animazione cuore like */
@keyframes like {
	0% {
		transform: 0px;
	}
	40% {
		font-size: 128px;
	}
	65% {
		font-size: 128px;
	}
	100% {
		font-size: 0px;
	}
}
</style>