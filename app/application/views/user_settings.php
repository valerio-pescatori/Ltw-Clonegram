<div id="app">
    <div class="container-fluid small">
        <navbar :logged_user="profile_info"></navbar>
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                <!-- inizio card -->
                <div class="card mb-5">
                    <div class="card-header font-weight-bolder">
                        Modifica Profilo
                    </div>
                    <div class="card-body">
                        <form action="/user/settings" method="post" enctype="multipart/form-data" @submit="validateForm">
                            <!-- propic -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-2 text-left text-md-right">
                                    <img :src="fileImg" class="rounded-circle cursor-pointer" style="width:38px; height:38px" @click.prevent="triggerInput" />
                                </div>
                                <div class="col col-md-6">
                                    <h4 class="card-text font-weight-bold"> {{profile_info.nome_utente}} </h4>
                                    <button class="btn btn-link btn-sm font-weight-light" @click.prevent="triggerInput"> Cambia immagine profilo</button>
                                    <input type="file" ref="input" name="media" class="d-none" accept=".jpg,.png" @change="fileImg = URL.createObjectURL($event.srcElement.files[0])">
                                </div>
                            </div>
                            <!-- nome -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2" style="overflow-x: visible">
                                    <label class="font-weight-bold" for="nome">Nome</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <!-- v-model per two-way data-binding -->
                                    <input type="text" id="nome" v-model="nome.value" name="nome" class="form-control">
                                </div>
                            </div>
                            <!-- cognome -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2" style="overflow-x: visible">
                                    <label class="font-weight-bold" for="cognome"> Cognome</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="text" id="cognome" v-model="cognome.value" name="cognome" class="form-control small">
                                </div>
                            </div>
                            <!-- username -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="username"> Username</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text small">@</span>
                                        </div>
                                        <input type="text" id="username" v-model="nome_utente.value" name="nome_utente" class="form-control small">
                                    </div>
                                </div>
                            </div>
                            <!-- vecchia password -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="oldp"> Vecchia password</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="password" id="oldp" v-model="old_password.value" name="password" class="form-control small">
                                </div>
                            </div>
                            <!-- nuova password -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="newp"> Nuova password</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="password" id="newp" v-model="password.value" name="new_password" class="form-control small">
                                </div>
                            </div>
                            <!-- conferma password -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="confp"> Conferma password</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="password" id="confp" v-model="password_conf.value" name="password_confirm" class="form-control small">
                                </div>
                            </div>
                            <!-- descrizione -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="bio"> Descrizione</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <textarea id="bio" name="descrizione" v-model="descrizione.value" maxlength="256" style="resize:none" class="form-control small"></textarea>
                                </div>
                            </div>
                            <!-- email -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="email"> Email</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="email" id="email" v-model="email.value" name="email" class="form-control small">
                                </div>
                            </div>
                            <!-- data nascita -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold" for="data"> Data di nascita</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="date" id="data" v-model="data_nascita.value" name="data_nascita" class="form-control small">
                                </div>
                            </div>
                            <!-- privato -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                    <label class="font-weight-bold"> Profilo Privato</label>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" name="profilo_privato" id="private" :checked="profile_info.profilo_privato == 1">
                                        <label class="custom-control-label text-hidden" for="private"></label>
                                    </div>
                                </div>
                            </div>
                            <!-- submit -->
                            <div class="row justify-content-center mb-3">
                                <div class="col-12 text-left text-md-right col-md-2">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="submit" class="btn btn-primary btn-sm" value="Salva modifiche">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- error bar nascosta che sale al submit -->
    <div class="fixed-bottom my-0 bg-secondary text-light" id="error" style="transition: 1s; bottom: -300px">
        <label class="ml-2 mt-2">{{ errors[0] }}</label>
    </div>
</div>

<script type="text/javascript">
    var app = new Vue({
        el: "#app",

        data: function() {
            return {
                profile_info: <?php echo json_encode($user_data); ?>,

                // fileImg per mostrare dinamicamente una preview della nuova immagine profilo
                fileImg: null,

                errors: [],

                nome_utente: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "il nome utente"
                },
                nome: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "il nome"
                },
                cognome: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "il cognome"
                },
                email: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "l'email"
                },

                old_password: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "la password attuale"
                },

                password: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "la nuova password"
                },
                password_conf: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "la password di conferma"
                },

                descrizione: {
                    value: "",
                },

                data_nascita: {
                    value: "",
                    is_valid: false,
                    is_invalid: false,
                    field_name: "la data di nascita"
                },

                fields: [],
            }
        },

        mounted: function() {
            // assegnamento iniziale di valori per i v-model
            this.nome_utente.value = this.profile_info.nome_utente;
            this.nome.value = this.profile_info.nome;
            this.cognome.value = this.profile_info.cognome;
            this.email.value = this.profile_info.email;
            this.descrizione.value = this.profile_info.descrizione;
            this.data_nascita.value = this.dateFormat(this.profile_info.data_nascita);
            this.fileImg = this.profile_info.mediafile;
            // array di comodo per la validation
            this.fields = [this.nome_utente, this.nome, this.cognome, this.email, this.old_password, this.password, this.password_conf, this.data_nascita];
        },

        components: {
            'navbar': httpVueLoader("/js/components/navbar.vue"),
            'errorbar': httpVueLoader("/js/components/errorbar.vue"),
        },

        methods: {
            // metodo per trasformare la data in formato html
            dateFormat: function(data) {
                var date_format = new Date(data);
                var month = date_format.getMonth() + 1 > 9 ? (date_format.getMonth() + 1) : "0" + (date_format.getMonth() + 1);
                var day = date_format.getDate() > 9 ? date_format.getDate() : "0" + date_format.getDate();
                return date_format.getFullYear() + "-" + month + "-" + day;
            },

            // trigger dell' input file nascosto
            triggerInput() {
                this.$refs.input.click();
            },

            // metodi di validazione
            validateNome() {
                return this.validateField(this.nome, /^[a-zA-Z ]+$/, "Il nome inserito non rispetta il range di caratteri [a-z, A-Z, ' ']");
            },

            validateCognome: function() {
                return this.validateField(this.cognome, /^[a-zA-Z ]+$/, "Il cognome inserito non rispetta il range di caratteri [a-z, A-Z, ' ']");
            },

            validateUsername: function() {
                return this.validateField(this.nome_utente, /^[a-z0-9_]+$/, "Il nome utente inserto non riespetta il range di caratteri [a-z, 0-9, '_'")
            },

            validateEmail: function() {
                return this.validateField(this.email, /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/, "Inserire un indirizzo email valido");
            },

            validateOldPassword: function() {
                return this.validateField(this.old_password, /((?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$)/,
                    "La password deve essere lunga almeno 8 caratteri, contenere  una lettera minuscola, una maiuscola e un numero o un carattere speciale", true);
            },

            validatePassword: function() {
                return this.validateField(this.password, /((?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$)/,
                    "La password deve essere lunga almeno 8 caratteri, contenere  una lettera minuscola, una maiuscola e un numero o un carattere speciale", true);
            },

            validatePasswordConf: function() {
                this.password_conf.is_invalid = this.password.is_invalid;
                this.password_conf.is_valid = this.password.is_valid;
                if (this.password.value != this.password_conf.value) {
                    this.errors.push("Le password non corrispondono");
                    this.password_conf.is_invalid = true;
                    this.password_conf.is_valid = false;
                    return false;
                }
                return true;
            },

            validateDataNascita() {
                this.data_nascita.is_valid = this.data_nascita.value != "";
                this.data_nascita.is_invalid = !this.data_nascita.is_valid;
                if (this.data_nascita.is_invalid)
                    errors.push("Inserire una data di nascita");
            },

            // riuso del codice
            validateField(field, pattern, err_str, accept_empty = false) {
                field.is_invalid = false;
                field.is_valid = true;
                if (!pattern.test(field.value) && !(accept_empty && field.value == "")) {
                    this.errors.push(err_str);
                    field.is_invalid = true;
                    field.is_valid = false;
                    return false;
                }
                return true;
            },

            checkAllFields() {
                // promise per effettuare prima il controllo sugli errori e poi attivare la barra d'errore
                // altrimenti la barra non prende l'altezza giusta
                return new Promise((resolve, reject) => {
                    this.errors = [];
                    this.validateNome();
                    this.validateCognome();
                    this.validateUsername();
                    this.validateEmail();
                    this.validateOldPassword();
                    this.validatePassword();
                    this.validatePasswordConf();
                    this.validateDataNascita();

                    if (this.fields.every(b => b.is_valid))
                        resolve();
                    else reject();
                });
            },

            //onsubmit
            validateForm: function(event) {
                //validate su tutti i campi
                this.checkAllFields().then(() => true).catch(() => {
                    event.preventDefault();
                    var error_bar = document.getElementById("error");

                    error_bar.style.bottom = "0px";
                    // outerHeight per prendere l'altezza complessiva (margin incluso)
                    error_bar.style.height = $(error_bar.firstChild).outerHeight(true) + "px";
                    // la barra d'errore torna giù dopo 8 secondi
                    setTimeout(() => {
                        error_bar.style.bottom = -300 + "px";
                    }, 8000);
                });
            },
        }
    });
</script>