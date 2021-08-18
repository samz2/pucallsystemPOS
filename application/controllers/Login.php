<?php

class Login extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ?: FALSE;
  }

  public function index()
  {
    $password = sha1($this->input->post('password'));
    $passwordF = hash('sha256', $password);
    $existe = $this->Controlador_model->getUsuario($this->input->post('usuario'), $passwordF);
    if ($existe) {
      $this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|callback__verificar_estado');
      $this->form_validation->set_message('_verificar_estado', 'Usuario esta desactivado');
    } else {
      $this->form_validation->set_rules('usuario', 'Usuario', 'trim|required|callback__verificar_usuario');
      $this->form_validation->set_message('_verificar_usuario', 'Usuario o Contraseña incorrecta');
    }
    if ($this->form_validation->run()) {
      if (!$this->control_acceso->crearSession()) {
        redirect('login');
      } else {
        if ($this->session->userdata('perfil') == 5) {
          //redirect('cocina');
          echo "cocina";
        } else if ($this->session->userdata('perfil') == 6) {
          //redirect('venta');
          echo "venta";
        }else{
          //redirect('inicio');
          echo "inicio";
        }
      }
    } else {
      $data['empresa'] = $this->Controlador_model->getEmpresa();
      $data['usuarios'] = $this->Controlador_model->getAllActivos('usuario');
      $this->load->view('login', $data);
    }
  }

  public function _verificar_usuario()
  {
    $password = sha1($this->input->post('password'));
    $passwordF = hash('sha256', $password);
    $query = $this->db->where('usuario', $this->input->post('usuario'))->where('password', $passwordF)->get('usuario');
    if ($query->num_rows() > 0)
      return TRUE;
    return FALSE;
  }

  public function _verificar_estado()
  {
    $password = sha1($this->input->post('password'));
    $passwordF = hash('sha256', $password);
    $query = $this->db->where('usuario', $this->input->post('usuario'))->where('password', $passwordF)->where('estado', '0')->get('usuario');
    if ($query->num_rows() > 0)
      return TRUE;
    return FALSE;
  }

  public function salir()
  {
    $this->session->sess_destroy();
    redirect('login');
  }

  public function login_pattern($id)
  {
    $data = $this->Controlador_model->getUsuarioPattern($id);
    echo json_encode($data);
  }

  public function login($id)
  {
    if (!$this->control_acceso->crearSessionPattern($id)) {
      redirect('login');
    } else {
      if ($this->session->userdata('perfil') == 5) {
        redirect('cocina');
      } else {
        redirect('inicio');
      }
    }
  }
}
