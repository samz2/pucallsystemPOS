<?php

class Usuario extends CI_Controller {

  public function __construct() {
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url().$this->controlador;
    $this->vista = $this->controlador;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
  }

  public function index() {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'perfiles' => $this->Controlador_model->getAll('perfil'),
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME.TEMPLATE, $data);
  }

  public function ajax_list() {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    if($this->perfil == 1 || $this->perfil == 2) {
      $this->db->order_by('id', 'desc');
    } else {
      $this->db->order_by('id', 'desc');
    }
    $query = $this->db->get($this->controlador)->result();
    $data = [];
    $estado['0'] = '<span class="label label-success">ACTIVO</span>';
    $estado['1'] = '<span class="label label-danger">DESABILITADO</span>';
    foreach($query as $key => $value) {
      $perfil = $this->Controlador_model->get($value->perfil, 'perfil');
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit('.$value->id.')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar('.$value->id.')"><i class="fa fa-trash"></i></a> ';
      if($value->estado == '0') {
        $boton .= '<a class="btn btn-sm btn-warning" title="Desactivar" onclick="desactivar('.$value->id.')"><i class="fa fa-power-off"></i></a> ';
      } else {
        $boton .= '<a class="btn btn-sm btn-default" title="Activar" onclick="activar('.$value->id.')"><i class="fa fa-check"></i></a> ';
      }
      $data[] = array(
        $key + 1,
        $value->documento,
        $value->nombre.' '.$value->apellido,
        $value->usuario,
        $perfil->nombre,
        $estado[$value->estado],
        $boton
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $start,
      "recordsFiltered" => $length,
      "data" => $data
    );
    //output to json format
    echo json_encode($result);
  }

  private function _validate() {
		$data = array();
		$data['error_string'] = array();
		$data['inputerror'] = array();
		$data['status'] = TRUE;

		if($this->input->post('usuario') == '') {
			$data['inputerror'][] = 'usuario';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}

    if($this->input->post('nombre') == '') {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}

    if($this->input->post('id') == '') {
      if($this->input->post('password') == '') {
  			$data['inputerror'][] = 'password';
  			$data['error_string'][] = 'Este campo es obligatorio.';
  			$data['status'] = FALSE;
  		} else {
        if($this->input->post('re_password') == '') {
    			$data['inputerror'][] = 're_password';
    			$data['error_string'][] = 'Este campo es obligatorio.';
    			$data['status'] = FALSE;
    		}
      }
    } else {
      if($this->input->post('password') != '') {
        if($this->input->post('re_password') == '') {
    			$data['inputerror'][] = 're_password';
    			$data['error_string'][] = 'Este campo es obligatorio.';
    			$data['status'] = FALSE;
    		}
  		}
    }

		if($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

  public function ajax_add() {
		$this->_validate();
    $config['upload_path'] = './files/Avatars/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $this->load->library('image_lib');
    if ($this->upload->do_upload()) {
      $datas = array('upload_data' => $this->upload->data());
      $config2['image_library'] = 'gd2';
      $config2['source_image'] = $datas['upload_data']['full_path'];
      $config2['create_thumb'] = TRUE;
      $config2['maintain_ratio'] = TRUE;
      $config2['width'] = 120;
      $config2['height'] = 120;
      $this->image_lib->clear();
      $this->image_lib->initialize($config2);
      $this->image_lib->resize();
      $image = $datas['upload_data']['file_name'];
      $image_thumb = $datas['upload_data']['raw_name'].'_thumb'.$datas['upload_data']['file_ext'];
      $data['avatar'] = $image;
    }    
    $data['password'] = hash('sha256', sha1($this->input->post('password')));
    $data['empresa'] = $this->perfil == 1 || $this->perfil == 2 ? $this->empresa : $this->input->post('empresa');
		$data['usuario'] = $this->input->post('usuario');
    $data['perfil'] = $this->input->post('perfil');
		$data['nombre'] = $this->input->post('nombre');
		$data['apellido'] = $this->input->post('apellido');
		$data['documento'] = $this->input->post('documento');
		$data['direccion'] = $this->input->post('direccion');
		$data['telefono'] = $this->input->post('telefono');
    $data['pin'] = $this->input->post('pin');
		$this->Controlador_model->save($this->controlador, $data);
    echo json_encode(array("status" => TRUE));
	}

  public function ajax_edit($id) {
		$data = $this->Controlador_model->get_by_id($id, $this->controlador);
		echo json_encode($data);
	}

  public function ajax_update() {
		$this->_validate();
    $config['upload_path'] = './files/Avatars/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $this->load->library('image_lib');
    if ($this->upload->do_upload('foto')) {
      $datas = array('upload_data' => $this->upload->data());
      $config2['image_library'] = 'gd2';
      $config2['source_image'] = $datas['upload_data']['full_path'];
      $config2['create_thumb'] = TRUE;
      $config2['maintain_ratio'] = TRUE;
      $config2['width'] = 120;
      $config2['height'] = 120;
      $this->image_lib->clear();
      $this->image_lib->initialize($config2);
      $this->image_lib->resize();
      $image = $datas['upload_data']['file_name'];
      $image_thumb = $datas['upload_data']['raw_name'].'_thumb'.$datas['upload_data']['file_ext'];
      $data['avatar'] = $image;
    }    
    $password = $this->input->post('password');
		if($password) {
			$password = sha1($password);
			$data['password'] = hash('sha256', $password);
		}
    $data['empresa'] = $this->perfil == 1 || $this->perfil == 2 ? $this->empresa : $this->input->post('empresa');
		$data['usuario'] = $this->input->post('usuario');
		$data['perfil'] = $this->input->post('perfil');
		$data['nombre'] = $this->input->post('nombre');
		$data['apellido'] = $this->input->post('apellido');
		$data['documento'] = $this->input->post('documento');
		$data['direccion'] = $this->input->post('direccion');
		$data['telefono'] = $this->input->post('telefono');
    $data['pin'] = $this->input->post('pin');
		$this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
	}

  public function ajax_delete($id) {
    if($this->Controlador_model->delete_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
	}

  public function ajax_desactivar($id) {
    if($this->Controlador_model->desactivar_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
	}

  public function ajax_activar($id) {
    if($this->Controlador_model->activar_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
	}

  public function perfil($id = FALSE) {
		if($id) {
			if ($this->form_validation->run('actualizar_usuario')) {
				if ($this->Controlador_model->perfil($this->controlador)) {
					mensaje_alerta('hecho', 'actualizar');
				} else {
					mensaje_alerta('error', 'actualizar');
				}
				redirect('/');
			} else {
				$data = array(
					'titulo' => 'Actualizar '.$this->titulo_controlador,
					'contenido' => '/clave',
					'data' => $this->Controlador_model->get($id, $this->controlador)
				);
				$this->load->view(THEME.TEMPLATE, $data);
			}
		} else {
			show_404();
		}
	}

  public function check() {
		if($this->input->is_ajax_request()) {
			if($this->Controlador_model->check($this->controlador)){
				$this->output->set_output('false');
			} else {
				$this->output->set_output('true');
			}
		} else {
			show_404();
		}
	}

}
