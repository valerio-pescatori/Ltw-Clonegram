<div id="login-form" class="container-fluid small">
    <!-- riga wrapper -->
    <div class="row mt-md-5">
        <!-- px-0 per fixare un problema di decentramento dovuto al padding su display troppo piccoli -->
        <div class="col-12 d-flex justify-content-center align-items-center px-0">
            <!-- img preview -->
            <img class="d-none d-md-block mr-md-4 mr-lg-5" src="/media/login_picture.png" alt="Foto login">
            <!-- wrapper form -->
            <div class="width-21">
                <!-- card del form -->
                <div class="card text-center">
                    <div class="card-body">
                        <img src="/media/black_logo.svg" class="card-title mb-4 mt-3 width-14"/>
                        <!-- form -->
                        <form action="/login" method="post">
                            <!-- nome utente -->
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text small">@</span>
                                </div>
                                <input class="form-control small" type="text" name="nome_utente" placeholder="Nome utente" 
                                    v-model="nome_utente" 
                                />
                            </div>
                            <!-- password -->
                            <div class="input-group mb-3">
                                <input class="form-control small" :type="password_eye ? 'password' : 'text' " 
                                    name="password" placeholder="Password" v-model="password" 
                                />
                                <div class="input-group-append">
                                    <span class="input-group-text" @mouseup="toggleEye" @mousedown="toggleEye">
                                        <i :class="{'fa': true, 'fa-eye' : password_eye, 'fa-eye-slash' : !password_eye}"></i>
                                    </span>
                                </div>
                            </div>
                            <input class="btn btn-primary btn-sm btn-block" :disabled="!nome_utente || !password || password.length<8" type="submit" value="Login" />
                            <!-- messaggio d'errore (post submit) -->
                            <div class="text-danger font-weight-light mt-3"> {{error}}</div>
                        </form>
                        <!-- fine form -->
                    </div>
                </div>
                <!-- card sign up -->
                <div class="card text-center mt-2">
                    <div class="card-body">
                        <span for="">Non hai un account? <a href="register">Registrati!</a></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var auth = new Vue({
        el: '#login-form',

        data: {
            error: '<?php echo isset($error_message) ? $error_message : '' ?>',
            nome_utente: null,
            password: null,
            password_eye: true,
            disabled: true,
        },

        methods: {
            toggleEye: function(event) 
            {
                this.password_eye = !this.password_eye;
            }
        }
    });
</script>