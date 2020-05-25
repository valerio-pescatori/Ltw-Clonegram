<template>
    <button
        v-if="!isme"
        class="btn btn-primary ml-2 btn-sm"
        :class="{ 'disabled': link_action_loading }"
        @click="buttonAction()"
    >
        <nav v-if="link_status == 0">SEGUI</nav>
        <nav v-else-if="link_status == 1">INVIATA</nav>
        <nav v-else-if="link_status == 2">SEGUITO</nav>
    </button>
</template>

<script>
module.exports = {
    props: {
        isme: {
			type: Boolean,
			default: false
		},
		userId: Number,
		linkStatus: Number
    },
	mounted: function(){
		// assegna a link_status il valore passato tramite prop
		this.link_status = this.linkStatus;
	},
    data: function(){
        return {
			// se true, indica che è in corso una richiesta asincrona al server
            link_action_loading: false,
            link_status: 0
        };
    },
    methods: {
		// ritorna una promise con la risposta dal server
        linkButtonAction: function(user_id, type) {
			if (type != "follow" && type != "unfollow") return -1;
			var self = this;
			self.link_action_loading = true;
			return post("/api/" + type + "/" + user_id).then(response => {
				self.link_action_loading = false;
				return response.status;
			});
		},
		// emette gli eventi necessari ai component parent per eseguire le azioni necessarie
		buttonAction: function() {
			var self = this;

			this.linkButtonAction(this.userId, this.link_status > 0 ? "unfollow" : "follow")
			.then(status => {
				switch(status){
					case -1: break;
					case 0:
						if(self.link_status == 2) self.$emit("dec");
					break;
					case 2:
						self.$emit("inc");
					break;
				}
				self.link_status = status;
			});
		},
    }
}
</script>