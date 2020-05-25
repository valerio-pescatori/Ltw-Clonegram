<template>
    <div>
		<div class="nav-root fixed-top">
			<nav class="navbar border-bottom navbar-expand bg-white">
				<div class="container-fluid container-md">
					<a class="navbar-brand" href="/" title="Home di Clonegram">
						<img id="logo" src="/media/black_logo.svg" alt="">
					</a>
					<!-- barra di ricerca -->
					<div class="mx-auto">
						<search-modal control-size="sm" class="d-none d-sm-block" v-if="search_bar_visible">
                        </search-modal>
					</div>
					<!-- menu a destra -->
					<ul class="nav navbar-nav">
						<!-- explore -->
						<li class="nav-item">
							<a id="search-btn-navbar" class="nav-link" href="/explore/" title="Esplora, trova nuovi amici">
                                <i v-if="actual_page == 'explore'" class="fas fa-compass"></i>
                                <i v-else class="fal fa-compass"></i>
                            </a>
						</li>
						<!-- fine explore -->
						<!-- upload -->
						<li class="nav-item">
							<a class="nav-link"  href="/upload/" title="Carica un nuovo post">
                                <i v-if="actual_page == 'upload'" class="fas fa-cloud-upload"></i>
                                <i v-else class="fal fa-cloud-upload"></i>
                            </a>
						</li>
						<!-- fine upload -->
						<!-- notifiche -->
						<li class="nav-item notification-btn">
							<a class="nav-link " data-toggle="modal" data-target="#modal-notifiche" 
                                href="#" @click.prevent="setNotificationViewed" title="Le tue notifiche"
                            >
                                <i class="fa-heart fa-md" :class="{'fas' : nuove_notifiche, 'fal': !nuove_notifiche, 'text-danger': nuove_notifiche}"></i>
                            </a>
						</li>
						<!-- fine notifiche -->
						<!-- profilo -->
						<li class="nav-item">
							<a class="nav-link" :href="'/user/'+logged_user.nome_utente" title="Il tuo profilo">
								<img :src="logged_user.mediafile" class="rounded-circle border shadow-sm">                       
							</a>
						</li>
						<!-- fine profilo -->
					</ul>
				</div>
			</nav>
            <!-- barra di caricamento -->
			<loadbar :percentage="percentage" :height="2"></loadbar> 
		</div>
        <!-- modal notifiche -->
        <div class="modal fade small" id="modal-notifiche">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Notifiche</h5>
                    </div>
					<!--
						like    => 	type = 0
					 	comment => 	type = 1
						tag     =>	type = 2
						follow  =>	type = 3 
					-->
                    <div class="modal-body">
                        <p class="small text-center text-muted" v-if="notifications.length == 0">
                            Non ci sono notifiche da visionare
                        </p>
                        <!-- v-for per le righe -->
                        <div class="row mb-3 pb-3 align-items-center border-bottom" 
                            v-for="(notification, index) in notifications" :key="index">
                            <div class="col-2 pr-1 pr-sm-2 text-center">
                                <a :href="notification.url_user" title="Visita il profilo">
                                    <img :src="notification.foto_profilo" class="foto-notifica rounded-circle" alt="Foto profilo">
                                </a>
                            </div>
                            <!-- v-bind per ridurre di una col in caso di notifica nuova -->
                            <div class="px-1 px-sm-2" :class="{'col-7 col-sm-8': notification.visto == 1, 'col-6 col-sm-7': notification.visto != 1}">
                                <!-- vari tipi di notifiche -->
                                <!-- d-inline perchè vogliamo un formato "testo_notifica data" e p di default è block -->
                                <p class="d-inline m-0" >
                                    <a :href="notification.url_user" class="text-break font-weight-bold text-dark" title="Visita il profilo">
                                        {{ notification.nome_utente }}
                                    </a>
                                    <!-- like  -->
                                    <span v-if="notification.tipo == 0">ha messo mi piace ad un tuo post</span>
                                    <!-- commento -->
                                    <span v-else-if="notification.tipo == 1">ha commentato il tuo post: "{{notification.commento}}"</span>
                                    <!-- tag  -->
                                    <span v-else-if="notification.tipo == 2">ti ha taggato in un post</span>
                                    <!-- segui privato -->
                                    <span v-else-if="notification.tipo == 3 && notification.accettata == 0">ha richiesto di seguirti</span>
                                    <!-- segui pubblico  -->
                                    <span v-else-if="notification.tipo == 3 && notification.accettata == 1">ha iniziato a seguirti</span>
                                </p>
                                <!-- data -->
                                <span class="text-muted font-weight-light"> {{dateFormat(notification.data, true)}}</span>
                            </div>
                            <!-- notifica non vista => pallino blu -->
                            <div class="px-1 px-sm-2 col-1 text-center" v-if="notification.visto!=1">
                                <i class="fas fa-dot-circle text-primary"></i>
                            </div>
                            <!-- img o accetta/rifiuta o vuoto -->
                            <div class="col-3 col-sm-2  px-1 px-sm-3 d-flex align-items-center justify-content-center">
                                <a :href=notification.url_post v-if="notification.tipo != 3">
                                    <img v-if="notification.tipo_media == 'img'" :src=notification.foto_post class="foto-notifica" alt="foto del post">
                                    <div v-else class="foto-notifica video-notification-wrapper mx-auto">
                                        <video :src="notification.foto_post+'#t=0.5'"></video>
                                    </div>
                                </a>
                                <div v-else-if="notification.accettata == 0"> 
                                    <!-- btn accetta follow -->
                                    <i class="btn-follow-req fal fa-check text-success mr-2" 
                                        @click="replyToFollowRequest(true, notification, index)">
                                    </i>
                                    <!-- btn rifiuta follow -->
                                    <i class="btn-follow-req fal fa-times text-danger" 
                                        @click="replyToFollowRequest(false, notification, index)">
                                    </i>
                                </div>
                            </div>
                            <hr />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- fine modal notifiche -->
	</div>
</template>

<script>
module.exports = {
    props: {
        // Utente loggato
        logged_user: Object,
        // Percentuale barra di caricamento
        percentage: {
            default: 0,
        },
        // La searchbar di default viene sempre vista
        search_bar_visible: {
            default: true,
        },
        // Pagina attualmente visibile
        actual_page: {
            default: "home"
        }
    },
    data: function()
    {
        return {
            // Lista delle notifiche
            notifications: [],
            // Viene utilizzato come flag per indicare se ci sono nuove notifiche
            nuove_notifiche: false
        };
    },
    components: {
        "search-modal": httpVueLoader('/js/components/search_modal.vue'),
        'loadbar': httpVueLoader('/js/components/loadbar.vue'),
    },
    methods: {
        // Richiede le notifiche via ajax
        getNotifications: function()
        {
            var self = this;
            get("/api/getnotifications").then((res) => {
                self.notifications = res.notifiche;
                self.nuove_notifiche = res.nuove_notifiche;
            });
        },
        // Setta le notifiche come viste tramite richiesta post
        setNotificationViewed: function()
        {
            var self = this;
            post('/api/putnotificationasviewed').then(res => self.nuove_notifiche = false);
        },
        // Risponde a una follow request
        replyToFollowRequest: function(type, notification, index)
        {
            var self = this;
            var route = (type ? 'acceptfollow' : 'refusefollow')+"/"+notification.utente_segue;
            post('/api/' + route).then(res => {
                if(res.status == 2){
                    self.notifications[index].accettata = 1;
                }
                else {
                    self.notifications.splice(index, 1);
                }
            });
        },
        // Per la preview dei video
        setVideoPreviewSize,
        // Per il formato delle date
        dateFormat,
    },
    mounted: function()
    {
        // Inizializza le notifiche
        this.getNotifications();
    }
}
</script>

<style scoped>

    /* Animazione cuore che pulsa */
    .fa-heart.fas {
        animation: 1.5s pulse infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }
		15% {
			transform: scale(1.25);
		}
        25% {
            transform: scale(1);
        }
		35% {
			transform: scale(1.25);
		}
        45% {
            transform: scale(1);
        }
    }
    /* Fine animazione cuore che pulsa */


    .fas.fa-dot-circle.text-primary {
        animation: 1.5s pulse infinite;
    }

    /* Ridimensiona il pulsante accetta rifiuta follow */
    .btn-follow-req {
        font-size: 1.4rem;
        cursor: pointer;
    }

    /* Per non far collapsare le icone sulla navbar */
    .nav-root {
        min-width: 340px;
    }

    /* Per non avere le decoration dei tag a sui link della nav */
    .nav-link{
        text-decoration: none !important; 
    }

    /* sfondo nero per video, position relative per il child in position absolute*/
    .video-notification-wrapper {
        background-color: #000;
        position: relative;
        overflow: hidden;
    }

    /* pseudo-element per rendere il div quadrato (responsive) => padding-bottom prende la larghezza */
    .video-notification-wrapper:after {
        content: "";
        display: block;
        padding-bottom: 100%;
    }

    /* position absolute per rimanere fisso in alto a sx del padre */
    .video-notification-wrapper video {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    }

    /* Size per le scritte dei nav item e colore nero */
    .nav-item > a {
        color: black !important;
        font-size: 1.2rem !important;
    }

    .nav-item  img {
        width: 1.6rem !important;
    }
    
    /* Setta la larghezza del logo */
    #logo {
        width: 103px;
    }

	.modal-dialog,
	.modal-content {
    	/* 80% of window height */
    	height: 80%;
	}	

	.modal-body {
    	/* 100% = dialog height, 120px = header + footer */
    	max-height: calc(100% - 120px);
    	overflow-y: scroll;
	}	

</style>