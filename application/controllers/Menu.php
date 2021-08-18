<?php

class Menu extends CI_Controller {

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
      'menus' => $this->Controlador_model->getMenu(),
      'iconos' => $this->Controlador_model->getAll('icono'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME.TEMPLATE, $data);
  }

  public function ajax_list() {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->get($this->controlador)->result();
    $data = [];
    foreach($query as $key => $value) {
      $grupo = $this->Controlador_model->get($value->parent_id, 'menu');
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit('.$value->id.')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar('.$value->id.')"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $value->nombre,
        $value->url,
        '<i class="'.$value->icono.'"></i>',
        $grupo ? $grupo->nombre : '',
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
    //$check = $this->Controlador_model->check($this->input->post('id'), $this->input->post('nombre'));

    if($this->input->post('nombre') == '') {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}
    /*
    if($check) {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo se encuentra registrado.';
			$data['status'] = FALSE;
		}*/

		if($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

  public function ajax_add() {
		$this->_validate();
		$data['nombre'] = $this->input->post('nombre');
		$data['url'] = $this->input->post('url');
		$data['icono'] = $this->input->post('icono');
		$data['parent_id'] = $this->input->post('grupo') ? $this->input->post('grupo') : 0;
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
		$data['url'] = $this->input->post('url');
    $data['icono'] = $this->input->post('icono');
    $data['parent_id'] = $grupo = $this->input->post('grupo') ? $this->input->post('grupo') : 0;
    $menu = $this->Controlador_model->get($this->input->post('id'), 'menu');
		if($menu->parent_id <> $grupo) {
			$perfil = $this->Controlador_model->getAll('perfil');
			foreach ($perfil as $value) {
				$menu1 = $this->Controlador_model->getPerfilM($value->id, $this->input->post('id'));
        $menu2 = $this->Controlador_model->getPerfilM($value->id, $grupo);
				if($menu1) {
          $menus['parent_id'] = $menu2->id;
          $this->Controlador_model->update(array('id' => $menu1->id), $menus, 'perfilmenu');
        }
			}
		}
		$this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
		echo json_encode(array("status" => TRUE));
	}

  public function ajax_delete($id) {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
	}

}
