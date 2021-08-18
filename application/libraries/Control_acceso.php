<?php

class Control_acceso {
//TODO: Falta habilitar el acceso
  public function __construct() {
    $this->CI = & get_instance();
    $controller = $this->CI->router->fetch_class();
    $method = $this->CI->router->fetch_method();
    $is_login = $this->CI->session->userdata('is_login');
    $perfil = $this->CI->session->userdata('perfil');
    if ($controller != 'login' && !$is_login) {
      redirect('login');
    } else if ($controller === 'login' && $is_login && $method != 'salir') {
      if($perfil == 5) {
        redirect('cocina');
      } else {
        redirect('inicio');
      }
    }
  }

  public function crearSession() {
    $password = sha1($this->CI->input->post('password'));
    $passwordF = hash('sha256', $password);
    $this->CI->db->where('usuario', $this->CI->input->post('usuario'));
    $this->CI->db->where('password', $passwordF);
    $this->CI->db->where('estado', '0');
    $usuario = $this->CI->db->get('usuario')->row();
    if ($usuario) {
      $data = array(
        'is_login' => TRUE,
        'usuario' => $usuario->id,
        'perfil' => $usuario->perfil,
        'empresa' => $usuario->empresa
      );
      $this->CI->session->set_userdata($data);
      return TRUE;
    } else {
      return FALSE;
    }
  }

    public function crearSessionPattern($id) {
    $this->CI->db->where('id', $id);
    $usuario = $this->CI->db->get('usuario')->row();
    if ($usuario) {
      $data = array(
        'is_login' => TRUE,
        'usuario' => $usuario->id,
        'perfil' => $usuario->perfil,
        'empresa' => $usuario->empresa
      );
      $this->CI->session->set_userdata($data);
      return TRUE;
    } else {
      return FALSE;
    }
  }

}
