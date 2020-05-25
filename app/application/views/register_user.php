<div id="register-form" class="container-fluid container-sm small">
    <div class="row mt-2 justify-content-center">
        <!-- px-0 per fixare un problema di decentramento dovuto al padding su display troppo piccoli -->
        <div class="col-12 px-0">
            <!-- inizio card -->
            <div class="card mx-auto width-21">
                <div class="card-body text-center">
                    <!-- logo clonegram -->
                    <img src="/media/black_logo.svg" class="card-title mb-3 mt-3 width-14 "/>
                    <div class="text-muted font-weight-bold mb-3"> Registrati per vedere le foto e i video dei tuoi amici</div>
                    <!-- inizio form -->
                    <form action="/register" method="post">
                        <!-- nome utente -->
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text small" id="basic-addon1">@</span>
                            </div>
                            <input type="text" maxlength="32" class="form-control small" name="nome_utente" v-model="nome_utente.value" 
                                @change="validateNomeUtente" 
                                class="small"
                                :class="{'is-valid': nome_utente.is_valid, 'is-invalid': nome_utente.is_invalid }" 
                                placeholder="Nome utente" 
                            >    
                            <div class="invalid-feedback text-left ">
                                <ul class="mb-0"><li v-for="err in nome_utente.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- nome -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control small" name="nome" maxlength="128" v-model="nome.value" 
                                @change="validateNome" :class="{'is-valid': nome.is_valid, 'is-invalid': nome.is_invalid }" placeholder="Nome"
                            >
                            <div class="invalid-feedback text-left">
                                <ul class="mb-0"><li v-for="err in nome.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- cognome -->
                        <div class="input-group mb-3">
                            <input type="text" class="form-control small" name="cognome" maxlength="128" v-model="cognome.value" 
                                @change="validateCognome" :class="{'is-valid': cognome.is_valid, 'is-invalid': cognome.is_invalid }" 
                                placeholder="Cognome"
                            >
                            <div class="invalid-feedback text-left">
                                <ul class="mb-0"><li v-for="err in cognome.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- email -->
                        <div class="form-group mb-3">
                            <input type="text" 
                                class="form-control small" name="email" maxlength="256" v-model="email.value" @change="validateEmail" 
                                :class="{'is-valid': email.is_valid, 'is-invalid': email.is_invalid }" placeholder="Email"
                            >
                            <div class="invalid-feedback text-left">
                                <ul class="mb-0"><li v-for="err in email.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- password -->
                        <div class="form-group mb-3"> 
                            <input type="password" class="form-control small" name="password" maxlength="256" v-model="password.value" 
                                @change="validatePassword" :class="{'is-valid': password.is_valid, 'is-invalid': password.is_invalid }" 
                                placeholder="Password"
                            >
                            <div class="invalid-feedback text-left">
                                <ul class="mb-0"><li v-for="err in password.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- password di conferma -->
                        <div class="input-group mb-3"> 
                            <input type="password" class="form-control small" name="password_conf" maxlength="256" 
                                v-model="password_conf.value" @change="validatePasswordConf" 
                                :class="{'is-valid': password_conf.is_valid, 'is-invalid': password_conf.is_invalid }" 
                                placeholder="Confema password"
                            >
                            <div class="invalid-feedback text-left">
                                <ul class="mb-0"><li v-for="err in password_conf.errors">{{err}}</li></ul>
                            </div>
                        </div>
                        <!-- data di nascita -->
                        <input type="date" class="form-control text-secondary mb-3 small" v-model="data_nascita.value" 
                            @change="data_nascita.is_valid = true; data_nascita.is_invalid = false;"
                            :class="{'is-valid': data_nascita.is_valid, 'is-invalid': data_nascita.is_invalid }" name="data_nascita"
                        >
                        <!-- submit -->
                        <input type="submit" :disabled="!form_is_valid" class="btn-block btn btn-primary btn-sm" value="Registrati">
                    </form>
                    <!-- fine form -->
                </div>
            </div>
            <!-- fine prima card -->
        </div>
    </div>
    <div class="row justify-content-center mt-2">
        <div class="col-12 px-0">
            <!-- inizio card "login" -->
            <div class="card text-center width-21 mx-auto">
                <div class="card-body">
                <label>Hai già un account? <a href="login">Effettua il login!</a></label>
                </div>
            </div>
            <!-- fine card "login" -->
        </div>
    </div>
</div>
<script type="text/javascript">
    var register_form = new Vue({
    el:'#register-form',
    data: {
        nome_utente: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        nome: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        cognome: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        email: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        password: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        password_conf: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        data_nascita: {
            value: "",
            is_valid: false,
            is_invalid: false,
            errors: []
        },
        
        fields: [],
    },

    mounted: function()
    {
        // Verifica di nome_utente e/o email già esistenti (tramite php)
        var nome_utente_err = "<?php echo isset($error_message['nome_utente']) ? $error_message['nome_utente'] : '' ?>";
        var email_err = "<?php echo isset($error_message['email']) ? $error_message['email'] : '' ?>";

        // Nome utente già esistente --> non valido
        if(nome_utente_err != '') {
            this.nome_utente.errors.push(nome_utente_err);
            this.nome_utente.is_valid = false;
            this.nome_utente.is_invalid = true;
        }
        // Email già esistente --> non valido
        if(email_err != '') {
            this.email.errors.push(email_err);
            this.email.is_valid = false;
            this.email.is_invalid = true;
        }
        // Array di comodo per validation
        this.fields = [this.nome_utente , this.nome, this.cognome, this.email, this.password, this.password_conf, this.data_nascita];
    },

    methods:{
        // Creo un metodo validate_<name> per ogni input

        //validazione utente
        validateNomeUtente: function(event)
        {
            this.validateField(this.nome_utente, /^[a-z0-9_]+$/, "Il nome utente inserito non rispetta il range di caratteri a-z, 0-9 con il solo simbolo _");
        },
        validateNome: function(event)
        {
            this.validateField(this.nome, /^[a-zA-Z ]+$/, "Il nome inserito non rispetta il range di caratteri [a-z, A-Z,' ']");
        },
        validateCognome: function(event)
        {
            this.validateField(this.cognome, /^[a-zA-Z ]+$/, "Il cognome inserito non rispetta il range di caratteri [a-z, A-Z,' ']")
        },
        validateEmail: function(event)
        {
            this.validateField(this.email, /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/, "Inserire un indirizzo email valido");
        },
        validatePassword: function(event)
        {
            this.validateField(this.password, 
                /(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/,
                "La password inserita non è valida. Deve contenere almeno una lettera minuscola, una maiuscola, un numero o un carattere speciale e deve essere almeno di 8 caratteri");
        },
        validatePasswordConf: function(event)
        {
            this.password_conf.errors = [];
            this.password_conf.is_invalid = this.password.is_invalid;
            this.password_conf.is_valid = this.password.is_valid; 
            if(this.password.value != "" && this.password.value != this.password_conf.value)
            {
                this.password_conf.errors.push("Le password inserite non corrispondono");
                this.password_conf.is_invalid = true;
                this.password_conf.is_valid = false;
            }
        },

        // Validation su di un singolo campo, viene chiamata ogni volta che cambia un tag input
        validateField: function(field, pattern, errStr){
            field.errors = [];
            field.is_invalid = false;
            field.is_valid = true;
            // check pattern
            if (!pattern.test(field.value)) {
                field.errors.push(errStr);
                field.is_invalid = true;
                field.is_valid = false;
                return false;
            }
            return true;
        }
    },
    
    computed: {
        // Computed function per abilitare/disabilitare il bottone di submit
        form_is_valid: function()
        {
            // Check sui campi se sono valid e non vuoti
            return this.fields.every(b => b.is_valid && b.value != '');
        }
    }
});
</script>