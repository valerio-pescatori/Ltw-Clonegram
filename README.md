# Ltw-Clonegram
Clonegram è un social network dove gli utenti possono condividere i propri ricordi con i loro amici.
Nasce da un progetto per il corso di [Linguaggi e Tecnologie per il Web](http://www.dis.uniroma1.it/rosati/lw/).
**Il sito è disponibile al seguente [link](161.35.27.71/).**

## Da chi è stato sviluppato?
* [Eduardo Rinaldi](https://github.com/edu-rinaldi)
* [Gianmarco Picarella](https://github.com/gianmarcopicarella)
* [Valerio Pescatori](https://github.com/valerio-pescatori)

## Docker Setup
Il setup locale del progetto è molto semplice ed è basato su [docker](https://www.docker.com/). Dopo aver scaricato ed installato Docker ed aver estratto il pacchetto contente il progetto, è possibile runnarlo seguendo gli step qui di seguito:
1. ```cd path/to/dir```
2. ```docker-compose up -d```
3. Una volta scaricate le Docker images necessarie, sarà possibile visionare l'applicazione al [seguente indirizzo](http://localhost:80)

É possibile modificare le settings del compose tramite il file **`./docker-compose.yml`**. 

# Front-end 
Il Frontend è diviso in 3 cartelle:
* **`app/application/views`**: contiene i file **`.html`** e **`.php`**
* **`app/js`**: diviso in:
    - **`app/js/components`**: contiene i file **`.vue`** relativi ai component utilizzati nel sito
    - **`app/js/all.js`**: per le icone **fontawesome**
    - **`app/js/utils.js`**: contiene funzioni di utilità da poter riutilizzare in varie parti
* **`app/css`**: contiene i file di stile **`.css`** per la grafica del sito:
    - **`app/css/all.css`**: contiene le classi per le icone **fontawesome**
    - **`app/css/style.css`**: contiene tutte le classi css riutilizzabili su più pagine 

## Descrizione file
In questa sezione si può trovare una breve descrizione per i file principali riguardanti il front-end del progetto.

### Vue Components 
Tutti i component si trovano al seguente path: `app/js/components`.
* **`comment.vue`**: renderizza un singolo commento relativo ad un post con le relative azioni contestuali (i.e. *elimina commento*).
* **`follow_suggestions.vue`**: renderizza una di lista di max 8 utenti che l'utente loggato potrebbe voler iniziare a seguire.
* **`link_button.vue`**: renderizza il pulsante Follow/Unfollow integrando tutte le funzionalità per la comunicazione con il server
* **`loadbar.vue`**: renderizza la barra di caricamento in base alla percentuale di caricamento passata come prop
* **`modal_utenti.vue`**: renderizza una modal standard su cui mostrare una lista di utenti
* **`navbar.vue`**: renderizza la barra di navigazione importando i component:
    - search_modal
    - loadbar
* **`post_list.vue`**: renderizza una lista di post che gli viene passata come prop, gestendo il caricamento progressivo di questi a gruppi di 10
* **`post.vue`**: renderizza il post e gestisce le azioni possibili su un possibile post: (i.e. *eliminare/lasciare un like, commento, tag, ...*) 
* **`profile_info.vue`**: renderizza le informazioni riguardanti il profilo di un utente specifico
* **`profile_post.vue`**: mostra la preview di un post sottoforma di quadrato
* **`search_modal.vue`**: renderizza un input, all'inserimento di testo in questo campo viene renderizzato un piccolo riquadro con risultati caricati dinamicamente, al click su di un risultato esegue l'azione passata come prop.
* **`upload.vue`**: gestisce la pagina di upload con le sue funzionalità, gestisce inoltre il crop dell'immagine caricata, passando al back-end le informazioni relative al crop da eseguire.


### JavaScript
I file `.js` si trovano nella cartella: `app/js`.
* **`app/js/all.js`**: per le icone **fontawesome**.
* **`app/js/utils.js`**: contiene funzioni di utilità da poter riutilizzare in varie parti del sito, tra cui un wrapper che gestisce le chiamate ajax.


### Views HTML/PHP
I file si trovano nella cartella `app/application/views`
* **`templates/footer.php`**: contiene il footer del sito
* **`templates/header.php`**: contiene la parte iniziale di ogni pagina, compreso il tag `head` e l'apertura del tag `body`
* **`explore.php`**: mostra la pagina explore `{base_url}/explore`
* **`home.php`**: mostra l'home page di Clonegram all' indirizzo `{base_url}/`
* **`index.html`**: utilizzato da codeigniter per gestire l'errore `403` forbidden
* **`login_user.php`**: mostra la pagina di login e contiene il form per effettuare l'accesso `{base_url}/login` 
* **`post.php`**: mostra la pagina di un singolo post, all'indirizzo `{base_url}/post/[post_id]`
* **`register_user.php`**: mostra la pagina di registrazione contente il form per creare un nuovo utente, all'indirizzo `{base_url}/register`
* **`upload.php`**: mostra la pagina di upload, all'indirizzo `{base_url}/upload`
* **`user_page.php`**: mostra la pagina del profilo utente, all'indirizzo `{base_url}/user/[nome_utente]`
* **`user_settings.php`**: mostra la pagina delle user settings, all'indirizzo `{base_url}/user/settings`


# Back-end
Il Backend si trova nella cartella **`./app/application`** ed è basato sulla libreria [CodeIgniter 3](https://codeigniter.com/). 

Le modifiche sono state fatte principalmente su 4 cartelle:
* **`./app/application/config`**: contiene tutti i file di configurazione di CodeIgniter
* **`./app/application/controllers`**: contiene i controller utilizzati da CodeIgniter per gestire le richieste HTTP
* **`./app/application/models`**: contiene le classi con cui interfacciarsi al database nei vari controllers e libraries
* **`./app/application/libraries`**: contiene le classi di Utils utilizzabili nei vari controllers

## Descrizione file
In questa sezione si può trovare una breve descrizione per i file principali riguardanti il back-end del progetto.

### Config
* **`./app/application/config/routes.php`**: contiene gli indirizzi a cui è possibile effettuare richieste `POST` e `GET`.
* **`./app/application/config/database.php`**: contiene le settings del db, in particolare sono state modificate le seguenti righe per abilitare l'uso di Emoji nel sito  
```php
    // da
    'char_set' => 'utf8',
	'dbcollat' => 'utf8_unicode_ci'
    // a
    'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_unicode_ci' 
```
### Controllers
* **`./app/application/controllers/Api.php`**: contiene i metodi che restituiscono risposte in formato JSON 
* **`./app/application/controllers/Auth.php`**: contiene i metodi di autenticazione e registrazione di un utente
* **`./app/application/controllers/Home.php`**: contiene un metodo per la visualizzazione della home, andando a interrogare il db per ottenere i post da visualizzare, gli utenti suggeriti e le info sull'utente loggato
* **`./app/application/controllers/PostContr.php`**: contiene metodi per visualizzare la pagina di un singolo post (dato un id) e la sezione explore.
* **`./app/application/controllers/Upload.php`**: contiene un metodo per la gestione dell'upload del post e un metodo per la visualizzazione della pagina di upload
* **`./app/application/controllers/UserContr.php`**: contiene metodi per visualizzare la pagina utente, dando informazioni riguardo l'utente loggato per permettere al front-end di gestire diversamente i casi della pagina dell'utente loggato e pagina di altri utenti. Contiene inoltre metodi per visualizzare la pagina di user settings e per aggiornare le informazioni dell'utente.

### Models
* **`./app/application/models/Comment.php`**: contiene query relative ai Commenti
* **`./app/application/models/Follow.php`**: contiene query relative alle richieste/stato di Follow
* **`./app/application/models/Likes.php`**: contiene query relative ai Like
* **`./app/application/models/Media.php`**: contiene query relative ai Media
* **`./app/application/models/Notifications.php`**: contiene query relative alle Notifiche
* **`./app/application/models/Post.php`**: contiene query relative ai commenti
* **`./app/application/models/PostMedia.php`**: contiene query relative alla tabella PostMedia
* **`./app/application/models/Tag.php`**: contiene query relative ai Tag
* **`./app/application/models/User.php`**: contiene query relative ai User

### Libraries
* **`./app/application/libraries/JwtAut.php`**: contiene le funzioni necessarie all'autenticazione e creazione di un json-web-token nella fase di login e navigazione tra pagine
* **`./app/application/libraries/LoginValidator.php`**: contiene le funzioni necessarie a validare l'utente durante una richiesta HTTP
* **`./app/application/libraries/UploadManager.php`**: contiene le funzioni necessarie a caricare e/o croppare immagini e video