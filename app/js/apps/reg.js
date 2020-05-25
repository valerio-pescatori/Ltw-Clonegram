var registerForm = new Vue({
    el:'#register-form',

    data: {
        nome_utente: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        nome: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        cognome: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        email: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        password: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        password_conf: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
        data_nascita: {
            value: "",
            isValid: false,
            isInvalid: false,
            errors: [],
        },
    },

    methods:{
        // Creo un metodo validate_<name> per ogni input

        //validazione utente
        validate_nome_utente: function(event)
        {
            // Se non entra in nessun if --> è valido e 0 errori
            this.nome_utente.errors = [];
            this.nome_utente.isInvalid = false;
            this.nome_utente.isValid = true; 
            // Se è troppo lungo o troppo corto --> errore
            if(this.nome_utente.value.length > 256 || this.nome_utente.value.length <3) 
            {
               this.nome_utente.isInvalid = true;
               this.nome_utente.isValid = false;
               this.nome_utente.errors.push("Il nome utente deve essere lungo almeno 3 caratteri e massimo 256");
            }
            // Se non rispetta il pattern --> errore
            if(!/^[a-z0-9_]+$/.test(this.nome_utente.value))
            {
                this.nome_utente.errors.push("Il nome utente inserito non rispetta il range di caratteri a-z, 0-9 con il solo simbolo _");
                this.nome_utente.isInvalid = true;
                this.nome_utente.isValid = false;
            }
        
        },
        validate_nome: function(event)
        {
            // Se non entra in nessun if --> è valido e 0 errori
            this.nome.errors = [];
            this.nome.isInvalid = false;
            this.nome.isValid = true; 
            // Se è troppo lungo --> errore
            if(this.nome.value.length > 128) 
            {
               this.nome.isInvalid = true;
               this.nome.isValid = false;
               this.nome.errors.push("Il nome è troppo lungo");
            }
            // Se non rispetta il pattern --> errore
            if(!/^[a-zA-Z ]+$/.test(this.nome.value))
            {
                this.nome.errors.push("Il nome inserito non rispetta il range di caratteri a-z, 0-9 con il solo simbolo _");
                this.nome.isInvalid = true;
                this.nome.isValid = false;
            }
        },
        validate_cognome: function(event)
        {
            // Se non entra in nessun if --> è valido e 0 errori
            this.cognome.errors = [];
            this.cognome.isInvalid = false;
            this.cognome.isValid = true; 
            // Se è troppo lungo --> errore
            if(this.cognome.value.length > 128) 
            {
               this.cognome.isInvalid = true;
               this.cognome.isValid = false;
               this.cognome.errors.push("Il cognome è troppo lungo");
            }
            // Se non rispetta il pattern --> errore
            if(!/^[a-zA-Z ]+$/.test(this.cognome.value))
            {
                this.cognome.errors.push("Il cognome inserito non rispetta il range di caratteri a-z, 0-9 con il solo simbolo _");
                this.cognome.isInvalid = true;
                this.cognome.isValid = false;
            }
        },
        validate_email: function(event)
        {
            // Se non entra in nessun if --> è valido e 0 errori
            this.email.errors = [];
            this.email.isInvalid = false;
            this.email.isValid = true; 
            // Se è troppo lungo --> errore
            if(this.email.value.length > 256) 
            {
               this.email.isInvalid = true;
               this.email.isValid = false;
               this.email.errors.push("L'indirizzo email è troppo lungo");
            }
            // Se non rispetta il pattern --> errore
            if(!/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(this.email.value))
            {
                this.email.errors.push("Inserire un indirizzo email valido");
                this.email.isInvalid = true;
                this.email.isValid = false;
            }
        },
        validate_password: function(event)
        {
            this.password.errors = [];
            this.password.isInvalid = false;
            this.password.isValid = true; 
            // Se è troppo lungo
            if(this.password.value.length > 256) 
            {
               this.password.isInvalid = true;
               this.password.isValid = false;
               this.password.errors.push("La password è troppo lunga");
            }
            // Se non rispetta il pattern
            if(!/(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/.test(this.password.value))
            {
                this.password.errors.push("La password inserita non è valida. Deve contenere almeno una lettera minuscola, una maiuscola, un numero o un carattere speciale e deve essere almeno di 8 caratteri");
                this.password.isInvalid = true;
                this.password.isValid = false;
            }
        },
        validate_password_conf: function(event)
        {
            this.password_conf.errors = [];
            this.password_conf.isInvalid = false;
            this.password_conf.isValid = true; 
            if(this.password.value != "" && this.password.value != this.password_conf.value)
            {
                this.password_conf.errors.push("Le password non corrispondono");
                this.password_conf.isInvalid = true;
                this.password_conf.isValid = false;
            }
        },
        validate_form: function(event) {

            this.nome_utente.isInvalid = false;
            this.nome_utente.isValid = true;
            this.nome_utente.errors = [];

            this.nome.isInvalid = false;
            this.nome.isValid = true; 
            this.nome.errors = [];

            this.cognome.isInvalid = false;
            this.cognome.isValid = true;
            this.cognome.errors = [];

            this.email.isInvalid = false;
            this.email.isValid = true; 
            this.email.errors = [];

            this.password.isInvalid = false;
            this.password.isValid = true;
            this.password.errors = [];

            this.password_conf.isInvalid = false;
            this.password_conf.isValid = true; 
            this.password_conf.errors = [];

            this.data_nascita.isInvalid = false;
            this.data_nascita.isValid = true;
            this.data_nascita.errors = [];

             
            var initstr = "E' obbligatorio inserire ";
            if(this.nome_utente.value != "" && this.nome.value != "" && this.cognome.value != "" && this.email.value != ""
                && this.password.value != "" && this.password_conf.value != "" && this.data_nascita.value != "")
                    return true;
            if(this.nome_utente.value == "" ) 
            {
                this.nome_utente.errors.push(initstr+" il nome utente");
                this.nome_utente.isInvalid = true;
                this.nome_utente.isValid = false;
            }
            if(this.nome.value == "") 
            {
                this.nome.errors.push(initstr+" il nome");
                this.nome.isInvalid = true;
                this.nome.isValid = false;
            }
            if(this.cognome.value == "")
            {
                this.cognome.errors.push(initstr+" il cognome");
                this.cognome.isInvalid = true;
                this.cognome.isValid = false;
            }
            if(this.email.value == "")
            {
                this.email.errors.push(initstr+" l'indirizzo email");
                this.email.isInvalid = true;
                this.email.isValid = false;
            }
            if(this.password.value == "") 
            {
                this.password.errors.push(initstr+" la password");
                this.password.isInvalid = true;
                this.password.isValid = false;
            }
            if(this.password_conf.value == "") 
            {
                this.password_conf.errors.push(initstr+" la password di conferma");
                this.password_conf.isInvalid = true;
                this.password_conf.isValid = false;
            }
            if(this.data_nascita.value == "") 
            {
                this.data_nascita.errors.push(initstr+" la data di nascita");
                this.data_nascita.isInvalid = true;
                this.data_nascita.isValid = false;
            }

            event.preventDefault();
        }
    }
});