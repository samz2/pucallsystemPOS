<?php

class Productocategoria extends CI_Controller
{

  var $controlador;
  var $titulo_controlador;
  var $url;
  var $vista;

  public function __construct()
  {
    $this->folder = 'files/';
    parent::__construct();
    $this->load->model(modelo(), 'Controlador_model');
    $this->controlador = controlador();
    $this->titulo_controlador = humanize($this->controlador);
    $this->url = base_url() . $this->controlador;
    $this->vista = $this->controlador;
    $this->venta = $this->session->userdata('venta') ? $this->session->userdata('venta') : FALSE;
    $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
    $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
    $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_list()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->get($this->controlador)->result();
    $data = [];
    foreach ($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      if ($value->estadoextras == '1') {
        $boton .= '<a class="btn btn-sm btn-info" title="Extras" onclick="extrascategoria(' . $value->id . ')"><i class="fa fa-plus-circle"></i></a> ';
      } 
      // if ($value->estado == '1') {
      //   $estaboCategoria = "<label class='label label-danger'>DESACTIVADO</label>";
      //   $boton .= '<a class="btn btn-sm btn-default" title="Activar" onclick="activar(' . $value->id . ')"><i class="fa fa-check"></i></a> ';
      // } else {
      //   $estaboCategoria = "<label class='label label-success'>ACTIVADO</label>";
      //   $boton .= '<a class="btn btn-sm btn-warning" title="Desactivar" onclick="desactivar(' . $value->id . ')"><i class="fa fa-power-off"></i></a> ';
      // }

      if ($value->estado == '1') {
        $estaboCategoria = '
        <div style="display: flex;justify-content: center;align-items: center;">
        <div class="material-switch">
              <input id="estado' . $value->id . '" name="estado' . $value->id . '"  type="checkbox" value="' . $value->id . '"  onchange="activa(event, ' . $value->id . ')"/>
              <label for="estado' . $value->id . '"  class="label-success" value="' . $value->id . '"></label>
            </div>
        </div>
            ';
      } elseif ($value->estado == '0') {
        $estaboCategoria = '
        <div style="display: flex;justify-content: center;align-items: center;">
        <div class="material-switch">
           <input id="estado' . $value->id . '" name="estado' . $value->id . '" type="checkbox" checked="true" onchange="desactiva(event,' . $value->id . ')"/>
             <label for="estado' . $value->id . '" class="label-success"></label>
        </div>
        </div>
        ';
      }

      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
      
      $data[] = array(
        $key + 1,
        $value->nombre,
        $value->codigo,
        $estaboCategoria,
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
  function ajax_eliminar_extras($idextras)
  {
    $data["statusdelete"] = "1";
    $this->Controlador_model->update(["id" => $idextras], $data, "productomodificador");
    echo json_encode(["status" => TRUE]);
  }

  function ajax_activar_extras($idextras)
  {
    $data["estado"] = "0";
    $this->Controlador_model->update(["id" => $idextras], $data, "productomodificador");
    echo json_encode(["status" => TRUE]);
  }

  function ajax_desactivar_extras($idextras)
  { 
    $data["estado"] = "1";
    $this->Controlador_model->update(["id" => $idextras], $data, "productomodificador");
    echo json_encode(["status" => TRUE]);
  }

  function ajax_update_saveextras()
  {
    $data['nombre'] = $this->input->post('nombreextra');
    $data['precio'] = $this->input->post('precioextra');
    $this->Controlador_model->update(array('id' => $this->input->post('idextras')), $data, 'productomodificador');
    echo json_encode(array("status" => TRUE));
  }

  function ajax_list_extras($idcategoria)
  {

    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->where('empresa', $this->empresa)->where("categoria", $idcategoria)->order_by('id', 'desc')->get("productomodificador")->result();
    $data = [];
    foreach ($query as $key => $value) {
      if ($value->statusdelete == 0) {
        //add variables for action
        $boton = '';
        $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="editExtras(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
       
        if ($value->estado == '0') {
          $estado = "<label class='label label-success'>ACTIVO</label>";
        } else {
          $estado = "<label class='label label-danger'>INACTIVO</label>";
        }

        if ($value->estado == '1') {
          $boton .= '<a class="btn btn-sm btn-default" title="Activar" onclick="activarExtras(' . $value->id . ')"><i class="fa fa-check"></i></a> ';
        } else {
          $boton .= '<a class="btn btn-sm btn-warning" title="Desactivar" onclick="desactivarExtras(' . $value->id . ')"><i class="fa fa-power-off"></i></a> ';
        }

        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrarExtras(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';

        $data[] = array(
          $key + 1,
          $value->nombre,
          $value->precio,
          $estado,
          $boton
        );
      }
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

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $categoria = $this->Controlador_model->check($this->input->post('id'), $this->input->post('nombre'));

    if ($categoria) {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este nombre ya se encuentra registrado.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('nombre') == '') {
      $data['inputerror'][] = 'nombre';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_add()
  {
    $this->_validate();
    if (!is_null($this->input->post('chkCocina'))) {
      $EstadoCocina = $this->input->post('chkCocina');
    } else {
      $EstadoCocina = '0';
    }

    if (!is_null($this->input->post('chkExtras'))) {
      $EstadoExtras = "1";
    } else {
      $EstadoExtras = "0";
    }

    $config['upload_path'] = './files/productocategoria/';
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
      $image_thumb = $datas['upload_data']['raw_name'] . '_thumb' . $datas['upload_data']['file_ext'];
      $data['photo'] = $image;
      $data['photothumb'] = $image_thumb;
    }
    $numero = $this->Controlador_model->codigos();
    $numeros = $numero ? $numero->numero + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 2 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $data['estadococina'] =  $EstadoCocina;
    $data['estadoextras'] =  $EstadoExtras;
    $data['nombre'] = $this->input->post('nombre');
    $data['codigo'] = $cadena . $numeros;
    $data['numero'] = $numeros;
    $data['descripcion'] = $this->input->post('descripcion');
    $data['empresa'] = $this->empresa;
   
    if ($this->Controlador_model->save($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }
  public function ajax_saveextras()
  {
    $this->_validateExtras();
    $data['empresa'] = $this->empresa;
    $data['categoria'] = $this->input->post('idcategoria');
    $data['nombre'] = $this->input->post('nombreextra');
    $data['precio'] = $this->input->post('precioextra');
    $data['created_at'] = date("Y-m-d H:i:s");
    $data['estado'] = "0";
    $insert = $this->Controlador_model->save('productomodificador', $data);
    if ($insert) {
      echo json_encode(array("status" => TRUE));
    }
  }
  function ajax_edit_extras($idextras)
  {
    $dataExtras = $this->Controlador_model->get($idextras, "productomodificador");
    echo json_encode($dataExtras);
  }

  public function ajax_edit($id)
  {
    $data = $this->Controlador_model->get($id, $this->controlador);
    echo json_encode($data);
  }

  public function ajax_desactivar($id)
  {
    $this->Controlador_model->desactivar_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_activar($id)
  {
    $this->Controlador_model->activar_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_update()
  {
    $this->_validate();
    if ($this->input->post('chkCocina') == '1') {
      $EstadoCocina = $this->input->post('chkCocina');
    } else {
      $EstadoCocina = '0';
    }
    if (!is_null($this->input->post('chkExtras'))) {
      $EstadoExtras = "1";
    } else {
      $EstadoExtras = "0";
    }
    $config['upload_path'] = './files/productocategoria/';
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
      $image_thumb = $datas['upload_data']['raw_name'] . '_thumb' . $datas['upload_data']['file_ext'];
      $data['photo'] = $image;
      $data['photothumb'] = $image_thumb;
    }
    $data['nombre'] = $this->input->post('nombre');
    $data['descripcion'] = $this->input->post('descripcion');
    $data['estadococina'] = $EstadoCocina;
    $data['estadoextras'] = $EstadoExtras;
    $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_delete($id)
  {
    if ($this->Controlador_model->delete_by_id($id, $this->controlador)) {
      echo json_encode(array("status" => TRUE));
    }
  }
  private function _validateExtras()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('nombreextra') == '') {
      $data['inputerror'][] = 'nombreextra';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('precioextra') == '') {
      $data['inputerror'][] = 'precioextra';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }
}
