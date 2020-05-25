<template>
	<header class="row mb-sm-5">
		<div class="col-md-4 col-sm-3 align-self-start pic-wrapper">
			<div class="row rounded-wrapper">
				<div class="loading-wrapper rounded-circle align-top" :class="{ 'invisible': !loadingVisible }">
					<div class="loading"></div>
				</div>
				<img class="rounded-circle align-top" @load="hideLoading" :src="info.mediafile" alt />
			</div>
		</div>
		<div class="col-md-8 col-sm-9">
			<div class="row header-data">
				<div class="col-12 user-head-wrapper order-1 order-sm-1">
					<h3 class="text-truncate-custom" :title="info.nome_utente" >{{ info.nome_utente }}</h3>
					<a v-if="isme" title="Impostazioni" href="/user/settings" class="ml-3 cursor-pointer">
						<i class="fal fa-user-cog fa-lg text-muted"></i>
					</a>
					<a v-if="isme" title="Esegui il logout" href="/logout" class="ml-3 cursor-pointer">
						<i class="fal fa-sign-out-alt fa-lg text-muted"></i>
					</a>
					<!-- bottone per seguire l'utente sulla sua pagina profilo che intercetta gli eventi inc e dec del bottone -->
					<link-button :isme="isme" :user-id="user_page_id" :link-status="linkStatus" @inc="onLinkChange('inc')" @dec="onLinkChange('dec', 'page-button')"></link-button>
				</div>
				<div class="col-12 user-metadata order-4 order-sm-2">
					<div class="row py-2">
						<div class="col-4">
							<b>{{ info.numero_post }}</b>
							<br />Posts
						</div>
						<div
							class="col-4 cursor-unset"
							:class="{ 'cursor-pointer': info.view_profile }"
							@click="openModal('Followers')"
						>
							<b>{{info.numero_followers }}</b>
							<br />Followers
						</div>
						<div
							class="col-4 cursor-unset"
							:class="{ 'cursor-pointer': info.view_profile }"
							@click="openModal('Following')"
						>
							<b>{{info.numero_following }}</b>
							<br />Following
						</div>
					</div>
				</div>
				<div class="col-12 order-2 order-sm-3">
					<h6>{{ info.nome }} {{info.cognome }}</h6>
				</div>
				<div v-if="toolong" class="col-12 pro-description order-3 order-sm-4">
					<div class="collapse" id="descriptionCollapse">{{ info.descrizione }}</div>

					{{ truncate(info.descrizione, 100) }}
					<a class="text-primary cursor-pointer" @click="toolong = false;">read more...</a>
				</div>
				<div v-else class="col-12 pro-description order-3 order-sm-4">
					<span style="white-space: pre-line"> {{ info.descrizione }} </span>
				</div>
			</div>
		</div>
		<!-- modal utenti seguiti/che seguono -->
		<modal-utenti :logged-user-id="user_logged_id" :users="users" :modal-title="modalTitle" :modal-id="'modal'" :link-action-loading="link_action_loading" @inc="onLinkChange('inc', 'no-action')" @dec="onLinkChange('dec', 'no-action')"></modal-utenti>
	</header>
</template>

<script>
module.exports = {
	props: {
		info: Object,
		toolong: Boolean,
		isme: Boolean,
		user_page_id: Number,
		user_logged_id: Number,
		linkStatus: Number
	},
	components: {
        'modal-utenti': httpVueLoader('/js/components/modal_utenti.vue'),
		'link-button': httpVueLoader("/js/components/link_button.vue") 
    },
	data: function() {
		return {
			modalTitle: "",
			users: [],
			loadingVisible: true,
			link_action_loading: false
		};
	},
	methods: {
		openModal: function(type) {
			if ((type != "Followers" && type != "Following") || !this.info.view_profile) return;
			if(this.modalTitle != type) this.users = [];
			this.modalTitle = type;
			$("#modal").modal("show");
			this.getUsers(type.toLowerCase());
		},
		truncate,
		onLinkChange: function(type, action){
			if(type != "inc" && type != "dec") return;

			if(this.user_page_id == this.user_logged_id){
				this.info.numero_following += (type == "inc") ? 1 : -1; 
			}
			else if(action != null && action == "no-action") return;
			else {
				this.info.numero_followers += (type == "inc") ? 1 : -1;
				if(action == "page-button" && this.info.is_private == "1") this.info.view_profile = false;
			}
		},
		changeLinkStatus: function(){
			var self = this;
			return new Promise((resolve, reject) => {
				self.linkStatus = 0;
				resolve();
			});
		},
		getUsers: function(type) {
			if (type != "followers" && type != "following") return;
			var self = this;
			get("/api/getuser" + type + "/" + self.user_page_id)
				.then(response => self.users = response);
		},
		hideLoading: function() {
			this.loadingVisible = false;
		}
	}
};
</script>

<style scoped>
	.rounded-wrapper {
		position: relative;
	}

	.header-data h6 {
		font-weight: 600;
	}

	.user-head-wrapper {
		display: flex;
		align-items: center;
	}

	.user-head-wrapper > h3 {
		margin-top: 0.5rem;
		font-weight: 200;
	}

	.user-head-wrapper > a {
		margin-left: 0.5rem;
		color: unset;
	}

	.pro-description {
		font-size: 0.875rem !important;
	}

	.user-name {
		display: flex;
		align-items: center;
	}
</style>