<template>
    <div class="modal fade" :id="modalId">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ modalTitle }}</h5>
                </div>
                <div class="modal-body">
                    <!-- esegue un for sui vari utenti generando l'html necessario -->
                    <div
                        v-for="user in users"
                        :key="user.id"
                        class="row mb-3 pb-3 align-items-center border-bottom"
                    >
                        <div class="col-3 col-sm-2 pr-1 pr-sm-2 text-center" style="height: 44px">
                            <a :href="'/user/' + user.nome_utente" title="Visita profilo">
                                <img style="width:44px" class="foto-notifica rounded-circle" :src="user.mediafile" alt />
                            </a>
                        </div>
                        <div class="px-1 px-sm-2 col-5 col-sm-6 small">
                            <a
                                class="text-break font-weight-bold text-dark text-truncate-custom"
                                :href="'/user/' + user.nome_utente"
                                title="Visita profilo"
                            >{{ user.nome_utente }}  
                            </a>
                            <p class="text-truncate-custom">{{ user.nome }} {{ user.cognome }}</p>
                        </div>
                        <div class="col-4 col-sm-4 px-1 px-sm-3 d-flex align-items-center justify-content-center">
                            <!-- Bottone per seguire/non seguire un utente presente nella lista -->
                            <link-button :isme="user.id == loggedUserId" :user-id="user.id" :link-status="user.link_status" v-on="$listeners"></link-button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
module.exports = {
    props: {
        users: Array,
        modalTitle: String,
        modalId: String,
        linkActionLoading: {
            type: Boolean,
            default: false
        },
        loggedUserId: Number
    },
    components: {
        'link-button': httpVueLoader("/js/components/link_button.vue") 
    }
};
</script>