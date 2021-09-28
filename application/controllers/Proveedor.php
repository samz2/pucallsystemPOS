<?php

class Proveedor extends CI_Controller {

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
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME.TEMPLATE, $data);
  }

  public function ajax_list() {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->get($this->controlador);
    $data = [];
    $no = 0;
    foreach($query->result() as $value) {
      $no++;
      //add variables for action
      $boton1 = '';
      $boton2 = '';
      //add html for action
      $boton1 = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Modificar" onclick="edit('."'".$value->id."'".')"><i class="fa fa-pencil"></i></a> ';
      $boton2 = '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="borrar('."'".$value->id."'".')"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $no,
        $value->ruc,
        substr($value->nombre, 0, 50),
        substr($value->direccion, 0, 50),
        $boton1.$boton2
      );
    }
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $query->num_rows(),
      "recordsFiltered" => $query->num_rows(),
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

		if($this->input->post('nombre') == '') {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}
    
    if($this->input->post('ruc') == '') {
			$data['inputerror'][] = 'ruc';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

  public function ajax_add() {
		$this->_validate();
		$data['nombre'] = $this->input->post('nombre');
		$data['ruc'] = $this->input->post('ruc');
    $data['direccion'] = $this->input->post('direccion');
    $data['referencia'] = $this->input->post('referencia');
    $data['telefono'] = $this->input->post('telefono');
    $data['celular'] = $this->input->post('celular');
    $data['correo'] = $this->input->post('correo');
    $data['contacto'] = $this->input->post('contacto');
		$this->Controlador_model->save($this->controlador, $data);
		echo json_encode(array("status" => TRUE));
	}

  public function ajax_edit($id) {
		$data = $this->Controlador_model->get_by_id($id, $this->controlador);
		echo json_encode($data);
	}

  public function ajax_update() {
		$this->_validate();
    $data['nombre'] = $this->input->post('nombre');
		$data['ruc'] = $this->input->post('ruc');
    $data['direccion'] = $this->input->post('direccion');
    $data['referencia'] = $this->input->post('referencia');
    $data['telefono'] = $this->input->post('telefono');
    $data['celular'] = $this->input->post('celular');
    $data['correo'] = $this->input->post('correo');
    $data['contacto'] = $this->input->post('contacto');
		$this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
		echo json_encode(array("status" => TRUE));
	}

  public function ajax_delete($id) {
    if($this->Controlador_model->delete_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
	}

  public function consulta_reniec() {
    if($this->input->is_ajax_request()) {
      $ruc = $this->input->post('ruc');
      $data = file_get_contents("https://api.sunat.cloud/ruc/".$ruc);
      $info = json_decode($data, true);
      if($data === '[]' || $info['fecha_inscripcion'] === '--') {
        $output = array('nombre' => 'NADA');
      } else {
        $output = array('nombre' => $info['razon_social'], 'direccion' => $info['domicilio_fiscal']);
      }
      $this->output->set_content_type('application/json')->set_output(json_encode($output));
    } else {
      show_404();
    }
  }

}
