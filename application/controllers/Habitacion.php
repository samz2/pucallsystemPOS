<?php

class Habitacion extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'iconos' => $this->Controlador_model->getAll('icono'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador)),
      'habitaciones' => $this->Controlador_model->getAll('habitacion'),
      'data' => $this->Controlador_model->getAllActives("habitacion"),
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }
  public function lista()
  {
    $data = $this->Controlador_model->getAllActives("habitacion");
    echo json_encode($data);
  }
}
