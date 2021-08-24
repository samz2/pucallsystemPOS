<?php

class Egreso extends CI_Controller
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
    $this->caja = $this->session->userdata('caja') ? $this->session->userdata('caja') : FALSE;
  }

  public function index()
  {
    $data = array(
      'titulo' => $this->titulo_controlador,
      'contenido' => $this->vista,
      'conceptos' => $this->Controlador_model->getConcepto(),
      'caja' => $this->Controlador_model->getcaja(),
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_list($finicio, $factual, $tipoegreso)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $data = [];
    if ($tipoegreso == "CAJA") {
      $query = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->where("caja IS NOT NULL")->order_by('id', 'desc')->get($this->controlador)->result();
      foreach ($query as $key => $value) {
        $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
        $caja = $this->Controlador_model->get($value->caja, 'caja');
        $compra = $this->Controlador_model->get($value->compra, 'compra');
        $empleado = $this->Controlador_model->get($value->usuario, 'usuario');
        $concepto = $this->Controlador_model->get($value->concepto, 'concepto');
        $boton = '';
        if ($this->perfil == 1 || $this->perfil == 2) {
          $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
        }
        $data[] = array(
          $key + 1,
          $empresa->ruc . " | " . $empresa->razonsocial . " | " . $empresa->nombre,
          ($empleado ? $empleado->nombre . " " . $empleado->apellido : "SIN DATOS"),
          $caja->descripcion,
          $concepto->concepto,
          $value->observacion,
          $value->montototal,
          $value->created,
          $boton
        );
      }
    } else {
      $query = $this->Controlador_model->getEgresoEmpresa($finicio, $factual, "egreso");
      foreach ($query as $key => $value) {
        $empresa = $this->Controlador_model->get($value->empresa, 'empresa');
        $caja = $this->Controlador_model->get($value->empresa, 'caja');
        $compra = $this->Controlador_model->get($value->compra, 'compra');
        $empleado = $this->Controlador_model->get($value->usuario, 'usuario');
        $concepto = $this->Controlador_model->get($value->concepto, 'concepto');
        $proveedor = $this->Controlador_model->get($compra->proveedor, 'proveedor');
        $boton = '';
        /* 
        LOS EGRESO POR EMPRESA SE GENERA CUANDO HACES UNA COMPRA Y POR ESO NO SE PUEDE ELIMINAR ALMENOS QUE 
        QUE ANULES LA COMPRA.
        if ($this->perfil == 1 || $this->perfil == 2) {
          $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
        }
        */
        $data[] = array(
          $key + 1,
          $empresa->ruc . " | " . $empresa->razonsocial . " | " . $empresa->nombre,
          ($empleado ? $empleado->nombre . " " . $empleado->apellido : "SIN DATOS"),
          $proveedor ? $proveedor->ruc." | ".$proveedor->nombre : "SIN DATOS",
          $concepto->concepto,
          $value->observacion,
          $value->montototal,
          $value->created,
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

    if ($this->input->post('caja') == '') {
      $data['inputerror'][] = 'caja';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('concepto') == '') {
      $data['inputerror'][] = 'concepto';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('monto') == '') {
      $data['inputerror'][] = 'monto';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('observacion') == '') {
      $data['inputerror'][] = 'observacion';
      $data['error_string'][] = 'Este campo es obligatorio.';
      $data['status'] = FALSE;
    }

    if ($this->input->post('monto') <= 0) {
      $data['inputerror'][] = 'monto';
      $data['error_string'][] = 'Este campo debe ser mayor a cero.';
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
    $data['empresa'] = $this->empresa;
    $data['usuario'] = $this->usuario;
    $data['caja'] = $this->input->post('caja');
    $data['concepto'] = $this->input->post('concepto');
    $data['montototal'] = $this->input->post('monto');
    $data['observacion'] = $this->input->post('observacion');
    $data['created'] = $this->input->post('fecha');
    $data['hora'] = date("H:i:s");
    $insert = $this->Controlador_model->save($this->controlador, $data);
    echo json_encode(array("status" => TRUE));
  }

  public function ajax_delete($id)
  {
    $query = $this->Controlador_model->get($id, "egreso");
    $statusCaja = $this->Controlador_model->get($query->caja, "caja");
    if($statusCaja->estado == '0'){
      $this->Controlador_model->delete_by_id($id, $this->controlador);
      $respuesta = array("status" => TRUE);
    }else{
      $respuesta = array("status" => FALSE);
    }
    echo json_encode($respuesta);
  }
}
