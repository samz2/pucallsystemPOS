<?php

class Empresa extends CI_Controller
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
      'zonamesa' => $this->Controlador_model->getAll('zona'),
      'almacenes' => $this->Controlador_model->getAll('almacen')
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
    $tipo['0'] = 'NATURAL';
    $tipo['1'] = 'JURIDICA';
    foreach ($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html for action
      if ($this->perfil == 1) {
        $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="edit(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
        $boton .= '<a class="btn btn-sm btn-info" title="Zona" onclick="zona(' . $value->id . ')"><i class="fa fa-suitcase"></i></a> ';
        $boton .= '<a class="btn btn-sm btn-default" title="Mesa" onclick="mesa(' . $value->id . ')"><i class="fa fa-suitcase"></i></a> ';
        $boton .= '<a class="btn btn-sm btn-success" title="Almacenes" onclick="almacen(' . $value->id . ')"><i class="fa fa-server"></i></a> ';
        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a> ';
        //$boton .= '<a class="btn btn-sm btn-warning" title="Sucursales" onclick="sucursal('.$value->id.')"><i class="fa fa-th-large"></i></a> ';
      };
      $data[] = array(
        $key + 1,
        $value->ruc,
        $value->razonsocial,
        substr($value->nombre, 0, 20),
        substr($value->direccion, 0, 30),
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

  public function ajax_listzona($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('empresa', $id)->get('zona')->result();
    $data = [];
    foreach ($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="editzona(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrarzona(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $key + 1,
        $value->nombre,
        $value->icono,
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

  private function _validate($estadoProceso)
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('ruc') == '') {
      $data['inputerror'][] = 'ruc';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('razonsocial') == '') {
      $data['inputerror'][] = 'razonsocial';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if($estadoProceso = 'update'){
      if ($this->input->post('almacen') == '0') {
        $data['inputerror'][] = 'almacen';
        $data['error_string'][] = 'Este campo es obligatorio.';
        $data['status'] = FALSE;
      }
    }
    

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_add($estadoProceso)
  {
    $this->_validate($estadoProceso);
    $pasos = 0;
    if (!is_null($this->input->post('pasos'))) {
      $pasos = $this->input->post('pasos');
    }
    $config['upload_path'] = './files/Setting/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    if ($this->upload->do_upload('logo')) {
      $datas = array('upload_data' => $this->upload->data());
      $image = $datas['upload_data']['file_name'];
    } else {
      $image = '5dda8da7287e07792b4ce7094f1270c6.png';
    }
    $data['pasos'] = $pasos;
    $data['ruc'] = $this->input->post('ruc');
    $data['razonsocial'] = $this->input->post('razonsocial');
    $data['nombre'] = $this->input->post('nombre');
    $data['direccion'] = $this->input->post('direccion');
    $data['telefono'] = $this->input->post('telefono');
    $data['serie'] = $this->input->post('serie');
    $data['ubigeo'] = $this->input->post('ubigeo');
    $data['departamento'] = $this->input->post('departamento');
    $data['provincia'] = $this->input->post('provincia');
    $data['distrito'] = $this->input->post('distrito');
    $data['usuariosol'] = $this->input->post('usuariosol');
    $data['clavesol'] = $this->input->post('clavesol');
    $data['logo'] = $image;
    $data['tipoproceso'] = $this->input->post('tipoproceso');
    $data['tipoventa'] = $this->input->post('tipoventa'); //adc
    $data['tipo'] = $this->input->post('tipo');
    $data['almacen'] = $this->input->post('almacen');
    $data['tipoimpresora'] = $this->input->post('tipoimpresora');
    $data['nombreimpresora'] = $this->input->post('nombreimpresora');
    $data['color_menu'] = $this->input->post('color_menu');
    if ($this->Controlador_model->save($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_edit($id)
  {
    $data = $this->Controlador_model->get_by_id($id, $this->controlador);
    $data->dataAlamcenes = $this->db->where("empresa",$id)->get("almacen")->result();
    echo json_encode($data);
  }

  public function ajax_update($estadoProceso)
  {
    $this->_validate($estadoProceso);
    $pasos = 0;
    if (!is_null($this->input->post('pasos'))) {
      $pasos = $this->input->post('pasos');
    }
    $config['upload_path'] = './files/Setting/';
    $config['encrypt_name'] = TRUE;
    $config['allowed_types'] = 'gif|jpg|jpeg|png';
    $config['max_width'] = '10000';
    $config['max_height'] = '10000';
    $this->load->library('upload', $config);
    $usuario = $this->Controlador_model->get_by_id($this->input->post('id'), $this->controlador);
    if ($this->upload->do_upload('logo')) {
      if ($usuario->logo <> '5dda8da7287e07792b4ce7094f1270c6.png') {
        unlink('./files/Setting/' . $usuario->logo);
      }
      $datas = array('upload_data' => $this->upload->data());
      $image = $datas['upload_data']['file_name'];
    } else {
      $image = $usuario->logo;
    }
    $data['pasos'] = $pasos;
    $data['ruc'] = $this->input->post('ruc');
    $data['razonsocial'] = $this->input->post('razonsocial');
    $data['nombre'] = $this->input->post('nombre');
    $data['direccion'] = $this->input->post('direccion');
    $data['telefono'] = $this->input->post('telefono');
    $data['serie'] = $this->input->post('serie');
    $data['ubigeo'] = $this->input->post('ubigeo');
    $data['departamento'] = $this->input->post('departamento');
    $data['provincia'] = $this->input->post('provincia');
    $data['distrito'] = $this->input->post('distrito');
    $data['usuariosol'] = $this->input->post('usuariosol');
    $data['clavesol'] = $this->input->post('clavesol');
    $data['almacen'] = $this->input->post('almacen');
    $data['logo'] = $image;
    $data['tipoproceso'] = $this->input->post('tipoproceso');
    $data['tipo'] = $this->input->post('tipo');
    $data['tipoimpresora'] = $this->input->post('tipoimpresora'); //adc
    $data['tipoventa'] = $this->input->post('tipoventa');
    $data['nombreimpresora'] = $this->input->post('nombreimpresora');
    $data['color_menu'] = $this->input->post('color_menu');
    $this->Controlador_model->update(array('id' => $this->input->post('id')), $data, $this->controlador);
    $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
    echo json_encode(array("status" => TRUE, "dataEmpresa" => $dataEmpresa));
  }

  public function ajax_delete($id)
  {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }

  public function cambiar($id)
  {
    if ($id) {
      $CI = &get_instance();
      $CI->session->set_userdata('empresa', $id);
      redirect('/');
    } else {
      show_404();
    }
  }

  public function ajax_listalmacen($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('empresa', $id)->get('almacen')->result();
    $data = [];
    foreach ($query as $key => $value) {
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="editalmacen(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borraralmacen(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $key + 1,
        $value->nombre,
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

  private function _validatealmacen()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $nombre = $this->input->post('nombrealmacen');
    /*
    $empresa = $this->input->post('idempresa');
    $id = $this->input->post('idzona');
    $zonalist = $this->Controlador_model->duplicadozona($id, $empresa, $nombre);*/

    if ($nombre == '') {
      $data['inputerror'][] = 'nombrealmacen';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    /*
    if($zonalist) {
      $data['inputerror'][] = 'nombrezona';
      $data['error_string'][] = 'Este campo ya existe en la base de datos.';
      $data['status'] = FALSE;
    }
    */

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addalmacen()
  {
    $this->_validatealmacen();
    $data['empresa'] = $this->input->post('idempresa');
    $data['nombre'] = $this->input->post('nombrealmacen');
    $numero = $this->Controlador_model->maxcodigo("almacen");
    $numeros = $numero ? $numero->numero + 1 : 1;
    $cadena = "";
    for ($i = 0; $i < 2 - strlen($numeros); $i++) {
      $cadena = $cadena . '0';
    }
    $data['codigo'] = $cadena . $numeros;
    $data['numero'] = $numeros;
    $insert = $this->Controlador_model->save('almacen', $data);
    if ($insert) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_editalmacen($id)
  {
    $data = $this->Controlador_model->get_by_id($id, 'almacen');
    echo json_encode($data);
  }

  public function ajax_updatealmacen()
  {
    $this->_validatealmacen();
    $data['nombre'] = $this->input->post('nombrealmacen');
    $this->Controlador_model->update(array('id' => $this->input->post('idalmacen')), $data, 'almacen');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_deletealmacen($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'almacen')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_listmesa($id)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('empresa', $id)->get('mesa')->result();
    $data = [];
    foreach ($query as $key => $value) {
      $zona = $this->Controlador_model->get($value->zona, 'zona');
      //add variables for action
      $boton = '';
      //add html for action
      $boton .= '<a class="btn btn-sm btn-primary" title="Modificar" onclick="editmesa(' . $value->id . ')"><i class="fa fa-pencil"></i></a> ';
      $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrarmesa(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      $data[] = array(
        $key + 1,
        $value->nombre,
        $zona ? $zona->nombre : "",
        $value->tipo,
        $value->precioalquiler,
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

  private function _validatemesa($estadoRegister)
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $empresa = $this->input->post('empresamesa');
    $nombre = $this->input->post('nombremesa');
    $zona = $this->input->post('zonamesa');

    if ($estadoRegister == 'add') {
      $zonalist = $this->Controlador_model->duplicadomesa($empresa, $nombre, $this->input->post("zonamesa"));
      if ($zonalist) {
        $data['inputerror'][] = 'nombremesa';
        $data['error_string'][] = 'Este nombre ya se encuentra registrado';
        $data['status'] = FALSE;
      }
    }

    if ($nombre == '') {
      $data['inputerror'][] = 'nombremesa';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post("tipomesa") == '') {
      $data['inputerror'][] = 'tipomesa';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post("precioalquiler") == '') {
      $data['inputerror'][] = 'precioalquiler';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_addmesa($estadoRegister)
  {
    $this->_validatemesa($estadoRegister);
    $data['empresa'] = $this->input->post('empresamesa');
    $data['zona'] = $this->input->post('zonamesa');
    $data['nombre'] = $this->input->post('nombremesa');
    $data['tipo'] = $this->input->post('tipomesa');
    $data['precioalquiler'] = $this->input->post('precioalquiler');
    $insert = $this->Controlador_model->save('mesa', $data);
    if ($insert) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_editmesa($id)
  {
    $data = $this->Controlador_model->get_by_id($id, 'mesa');
    echo json_encode($data);
  }

  public function ajax_updatemesa($estadoRegister)
  {
    $this->_validatemesa($estadoRegister);
    $data['empresa'] = $this->input->post('empresamesa');
    $data['zona'] = $this->input->post('zonamesa');
    $data['nombre'] = $this->input->post('nombremesa');
    $data['precioalquiler'] = $this->input->post('precioalquiler');
    
    $this->Controlador_model->update(array('id' => $this->input->post('idmesa')), $data, 'mesa');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_deletemesa($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'mesa')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  private function _validatezona($tiporegister)
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;
    $id = $this->input->post('idzona');
    $empresa = $this->input->post('empresazona');
    $nombre = $this->input->post('nombrezona');

    if ($nombre == '') {
      $data['inputerror'][] = 'nombrezona';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($tiporegister == 'add') {
      $zonalist = $this->Controlador_model->duplicadozona($empresa, $nombre);
      if ($zonalist) {
        $data['inputerror'][] = 'nombrezona';
        $data['error_string'][] = 'Este campo ya existe en la base de datos.';
        $data['status'] = FALSE;
      }
    }

    if ($data['status'] === FALSE) {
      echo json_encode($data);
      exit();
    }
  }

  public function ajax_updatezona($tiporegister)
  {
    $this->_validatezona($tiporegister);
    $data['empresa'] = $this->input->post('empresazona');
    $data['nombre'] = $this->input->post('nombrezona');
    $data['icono'] = $this->input->post('iconozona');
    $this->Controlador_model->update(array('id' => $this->input->post('idzona')), $data, 'zona');
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_addzona($tiporegister)
  {
    $this->_validatezona($tiporegister);
    $data['empresa'] = $this->input->post('empresazona');
    $data['nombre'] = $this->input->post('nombrezona');
    $data['icono'] = $this->input->post('iconozona');
    $insert = $this->Controlador_model->save('zona', $data);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_editzona($id)
  {
    $data = $this->Controlador_model->get_by_id($id, 'zona');
    echo json_encode($data);
  }

  public function ajax_deletezona($id)
  {
    if ($this->Controlador_model->delete_by_id($id, 'zona')) {
      echo json_encode(array("status" => TRUE));
    }
  }

  function ajax_datazona()
  {
    $idempresa = $this->input->post("idempresa");
    $datazona = $this->db->where("empresa", $idempresa)->get("zona")->result();
    echo json_encode(["datazona" => $datazona]);
  }
}
