<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->library(['LoginValidator', 'session', 'form_validation', 'jwtAuth']);
		$this->load->helper(['form','url']);
		$this->load->model('user');
	}

	public function login()
	{
		// Se l'utente è già loggato, redirect alla home
		if ($this->loginvalidator->isLoggedIn())
			redirect('/', 'refresh');
		
		$this->load->view('templates/header', ["titolo" => "Esegui l'accesso"]);
		$this->load->view('login_user');
		$this->load->view('templates/footer');
	}

	public function register()
	{
		// Se l'utente è già loggato, redirect alla home
		if ($this->loginvalidator->isLoggedIn())
			redirect('/', 'refresh');

		$this->load->view('templates/header', ["titolo" => "Registrati"]);
		$this->load->view('register_user');
		$this->load->view('templates/footer');
	}

	public function evaluateLogin()
	{
		// Se l'utente è già loggato, redirect alla home
		if ($this->loginvalidator->isLoggedIn())
			redirect('/', 'refresh');

		// Prepara dati per la query
		$user_data = array(
			"nome_utente" => $this->input->post('nome_utente'),
			"password" => $this->input->post('password')
		);

		// Controlla se lo user esiste, se esiste crea la session
		if ($this->user->exist($user_data)) {
			$this->session->set_userdata(
				"JWT",
				$this->jwtauth->generateJwt($this->user->get($user_data, "id, nome_utente")[0])
			);
			// Login effettuato, redirect alla home
			redirect("/", "refresh");
		}

		// Altrimenti user e/o password errati
		$this->load->view('templates/header', ["titolo" => "Esegui l'accesso"]);
		$this->load->view('login_user', ["error_message" => "Nome utente e/o password errati"]);
		$this->load->view('templates/footer');
	}

	public function evaluateSignup()
	{
		if ($this->loginvalidator->isLoggedIn())
			redirect('/', 'refresh');
		
		// controlla se nome_utente ed email sono unici
		$this->form_validation->set_rules('nome_utente', 'Username', 'is_unique[Utente.nome_utente]', [ "is_unique" => "Nome utente già esistente" ]);
		$this->form_validation->set_rules('email', 'Email', 'is_unique[Utente.email]', ['is_unique' => "Email già esistente"]);

		// se non rispetta le regole, ritorna un messaggio di errore
		if ($this->form_validation->run() == FALSE) {
			$this->load->view('templates/header', ["titolo" => "Registrati"]);
			$this->load->view('register_user', ["error_message" => $this->form_validation->error_array()]);
			$this->load->view('templates/footer');
			return;
		}
		
		// prepara dati per la query di insert nel database
		$user_data = array(
			"nome_utente" => $this->input->post('nome_utente'),
			"nome" => $this->input->post('nome'),
			"cognome" => $this->input->post('cognome'),
			"email" => $this->input->post('email'),
			"password" => $this->input->post('password'),
			"data_nascita" => $this->input->post('data_nascita')
		);

		// salva utente nel database
		$user_id = $this->user->insert($user_data);

		// crea cartella utente con permessi 0777
		mkdir("./media/" . $user_id, 0777, TRUE);

		// genera jwt token
		$jwt = $this->jwtauth->generateJwt(array("id" => $user_id, "nome_utente" => $user_data["nome_utente"]));
		$this->session->set_userdata("JWT", $jwt);

		// redirect ad home
		redirect("/", "refresh");
	}

	public function logout()
	{
		// Toglie il cookie
		$this->session->unset_userdata("JWT");
		
		// fa redirect
		redirect("login", "refresh");
	}
}
