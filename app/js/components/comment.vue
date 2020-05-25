<template>
    <div class="comment-container">
        <a :href="comment.url_user">
            <span class="font-weight-bold text-dark text-decoration-none float-left mr-1 display-6">{{comment.nome_utente}}</span>
        </a>
        <span>{{comment.commento}}</span>
        <i v-if="comment.nome_utente == logged_user_info.nome_utente" 
            class="remove-comment-btn d-md-none float-right fas fa-times" 
            @click="removeComment(comment)">
        </i>
    </div> 
</template>

<script>
module.exports = {
    props: {
        logged_user_info: Object,
        comment: Object,
        // la funzione di rimozione mi viene passata
        removeComment: Function,
    },
}
</script>

<style scoped>

    /* CSS per il pulsante per cancellare il commento */
    .remove-comment-btn {
        cursor: pointer;
    }
    /* CSS per l'hover sul commento */
    .comment-container:hover .remove-comment-btn {
        display: block !important;
    }
</style>