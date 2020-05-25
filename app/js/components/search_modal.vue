<template>
    <div class="search-box">
        <!-- input field -->
        <input
            @keyUp="searchUser"
            :class="'search-bar form-control '+getControlSize()"
            type="search"
            :placeholder="'&#xf002;   '+placeholder"
            v-model="search_input"
        />
        <!-- modal results -->
        <div :class="{'invisible': search_input == '', 'visible': search_input != ''}" class="small">
			<!-- card dei result -->
            <div class="card results-card position-absolute">
                <ul class="list-group list-group-flush overflow-auto">
                    <!-- list di user trovati -->
                    <li
                        :key="i"
                        v-for="(user,i) in results"
                        class="list-group-item cursor-pointer"
                        @click="clickFunction(user, search_input); search_input=''"
                    >
                        <div class="media">
                            <img class="rounded-circle mr-3" :src="user.foto_profilo" width="44px" />
                            <div class="media-body">
                                <div class="font-weight-normal">{{ user.nome_utente }}</div>
                                <div class="font-weight-light text-muted">{{ user.nome }} {{ user.cognome }}</div>
                            </div>
                        </div>
                    </li>
                    <!-- nessun risultato trovato -->
                    <li v-if="results.length == 0 && !searching" class="list-group-item">
                        <div class="media">
                            <div class="media-body">
                                <div class="text-muted font-weight-normal">Nessun risultato trovato per "{{search_input}}"</div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- fine card dei result -->
        </div>
        <!-- end modal -->
    </div>
</template>

<script>
module.exports = {
	props: {
        // Funzione che viene chiamata quando si clicca su un risultato
        clickFunction: {
            type: Function,
            default: (user)=> window.location.href = user.url,
        },
        // Funzione di filtering sui risultati
        filterFunction: null,
        // Placeholder della searchbar
        placeholder: {
            default: "Cerca",
        },
        // Grandezza della searchbar
        controlSize: '',
    },
    data: function() 
    {
        return {
            // Input della searchbox
            search_input : "",
            // Flag che indica se stiamo cerando (viene settato a true prima della richiesta ajax e a false quando ha finito)
            searching: false,
            // Risultati della ricerca
			results: [],
        }
    },
    methods: {
        searchUser: function() 
        {
            // Se l'input è vuoto non fa nulla
            if (this.search_input == '') return;
            this.searching = true;
            var self = this;
            get('/api/search/' + self.search_input)
                .then(res => {
                    self.searching = false;
					self.results = res;
                    if(self.filterFunction) 
                        self.results = self.filterFunction(self.results);
                });
        },
        // Restituisce la size della search-bar
        getControlSize: function()
        {
            switch(this.controlSize)
            {
                // small
                case 'sm':
                    return 'form-control-sm';
                break;
                // large
                case 'lg':
                    return 'form-control-lg';
                break;
                // default or case 1 => normal
                default:
                    return '';
            }
        }
    }
}
</script>

<style scoped>

    /* CSS per il positioning della card */
	.search-box {
        position: relative;
    }

    .results-card {
        width: 18rem;
        max-height: 20rem;
        top: 2.2rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
    }

    /* Colore search-bar input */
    .search-bar {
        background: #fafafa;
    }

    /* Per l'icona vicino il placeholder */
    input[type="search"]::placeholder {
        font-family: "Font Awesome 5 Pro", "Montserrat", sans-serif;
    }

    .search-bar::placeholder{
        text-align: center;
        transform: scale(0.9);
        font-weight: lighter;
    }

</style>