<?php

class Perfil extends CI_Controller {

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
    $query = $this->db->order_by('id', 'desc')->get($this->controlador)->result();
    $data = [];
    foreach($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html fodr action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit('.$value->id.')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-warning" title="Menu" onclick="menu('.$value->id.')"><i class="fa fa-trello"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar('.$value->id.')"><i class="fa fa-trash"></i></a> ';
      $data[] = array(
        $key + 1,
        $value->nombre,
        $value->descripcion,
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
    $check = $this->Controlador_model->check($this->input->post('id'), $this->input->post('nombre'));

    if($this->input->post('nombre') == '') {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo es obligatorio.';
			$data['status'] = FALSE;
		}

    if($check) {
			$data['inputerror'][] = 'nombre';
			$data['error_string'][] = 'Este campo se encuentra registrado.';
			$data['status'] = FALSE;
		}

		if($data['status'] === FALSE) {
			echo json_encode($data);
			exit();
		}
	}

  public function ajax_add() {
		$this->_validate();
    $cobradorcaja = '0';
    if (!is_null($this->input->post('chkCobradorCaja'))) {
      $cobradorcaja = '1';
    }
    $data['nombre'] = $this->input->post('nombre');
    $data['cobradorcaja'] = $cobradorcaja;
    $data['descripcion'] = $this->input->post('descripcion');
		$this->Controlador_model->save($this->controlador, $data);
		echo json_encode(array("status" => TRUE));
	}

  public function ajax_edit($id) {
		$data = $this->Controlador_model->get_by_id($id, $this->controlador);
		echo json_encode($data);
	}

  public function ajax_update() {
		$this->_validate();
    $cobradorcaja = '0';
    if (!is_null($this->input->post('chkCobradorCaja'))) {
      $cobradorcaja = '1';
    }
    $data['nombre'] = $this->input->post('nombre');
    $data['cobradorcaja'] = $cobradorcaja;
    $data['descripcion'] = $this->input->post('descripcion');
		$this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
		echo json_encode(array("status" => TRUE));
	}

  public function ajax_delete($id) {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
	}

  public function listarmenu($id) {
    $datas = $this->Controlador_model->getMenu();
    $ticket = '';
    $ticket .= '<input type="hidden" class="form-control" name="perfil" id="perfil" value="'.$id.'">';
    $ticket .= '<ul class="checktree">';
    foreach ($datas as $value) {
      $perfilmenu = $this->Controlador_model->getPerfilM($id, $value->id);
      $menus = $this->Controlador_model->getSubmenu($value->id, 'menu');
      $ticket .= '<li><label>';
      $ticket .= '<input id="menu" name="menu[]" type="checkbox" value="'.$value->id.'" '.(isset($perfilmenu->id) ? 'checked' : '').'> '.$value->nombre;
      $ticket .= '</label><ul>';
      foreach ($menus as $menu) {
        $perfilmenus = $this->Controlador_model->getPerfilM($id, $menu->id);
        $ticket .= '<li><label>';
        $ticket .= '<input id="menu" name="menu[]" type="checkbox" value="'.$menu->id.'" '.(isset($perfilmenus->id) ? 'checked' : '').'> '.$menu->nombre;
        $ticket .= '</label></li>';
      }
      $ticket .= '</ul></li>';
    }
    $ticket .= '</ul>';
    echo $ticket;
  }

  public function ajax_add_menu() {
    $modulos = $this->Controlador_model->getAll('menu');
    foreach ($modulos as $modulo) {
      if($this->input->post('menu')) {
        $encontrado = false;
        foreach ($this->input->post('menu') as $menu) {
          if ($menu == $modulo->id) {
            $encontrado = true;
            $menuperfil = $this->Controlador_model->getPerfilM($this->input->post('perfil'), $menu);
            if (!$menuperfil) {
              $menus = $this->Controlador_model->get($menu, 'menu');
              $data['perfil'] = $this->input->post('perfil');
              $data['menu'] = $menu;
              if ($menus->parent_id > 0) {
                $menuperfils = $this->Controlador_model->getPerfilM($this->input->post('perfil'), $menus->parent_id);
                if ($menuperfils) {
                  $parent['is_parent'] = 1;
                  $this->Controlador_model->update(array('id' => $menuperfils->id), $parent, 'perfilmenu');
                } else {
                  $prince['perfil'] = $this->input->post('perfil');
                  $prince['menu'] = $menus->parent_id;
                  $prince['posicion'] = $menus->parent_id;
                  $prince['parent_id'] = 0;
                  $prince['is_parent'] = 1;
                  $this->Controlador_model->save('perfilmenu', $prince);
                }
                $menuperfils = $this->Controlador_model->getPerfilM($this->input->post('perfil'), $menus->parent_id);
                $data['posicion'] = 0;
                $data['parent_id'] = $menuperfils->id;
                $data['is_parent'] = 0;
              } else {
                $data['posicion'] = $menu;
                $data['parent_id'] = 0;
                $data['is_parent'] = 0;
              }
              $this->Controlador_model->save('perfilmenu', $data);
            }
            break;
          }
        }
        if ($encontrado == false){
          $this->db->where('perfil', $this->input->post('perfil'))->where('menu', $modulo->id)->delete('perfilmenu');
        }
      } else {
        $this->db->where('perfil', $this->input->post('perfil'))->delete('perfilmenu');
      }
    }
		echo json_encode(array("status" => TRUE));
	}

}

/* End of file venta_credito.php */
/* Location: ./system/application/controllers/venta_credito.php */
