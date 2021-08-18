<?php

class Ingreso extends CI_Controller
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
      'empresas' => $this->Controlador_model->getAll('empresa'),
      'caja' => $this->Controlador_model->getcaja(),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME . TEMPLATE, $data);
  }

  public function ajax_list($finicio, $factual)
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $usuario = $this->perfil == 7 ? $this->usuario : FALSE;
    $query = $this->db->where("created BETWEEN '" . $finicio . "' AND '" . $factual . "'")->order_by('id', 'desc')->get($this->controlador)->result();
    $data = [];
    foreach ($query as $key => $value) {
      $venta = $this->Controlador_model->get($value->venta, 'venta');
      $concepto = $this->Controlador_model->get($value->concepto, 'concepto');
      //add variables for action
      $boton = '';
      //add html fodr action
      if ($this->perfil == 1 || $this->perfil == 2) {
        $boton .= '<a class="btn btn-sm btn-danger" title="Borrar" onclick="borrar(' . $value->id . ')"><i class="fa fa-trash"></i></a>';
      }
      $data[] = array(
        $key + 1,
        $venta ? $venta->serie . '-' . $venta->numero : '',
        $concepto->concepto,
        $value->observacion,
        $value->monto,
        $value->created,
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

  private function _validate()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    if ($this->input->post('caja') == '') {
      $data['inputerror'][] = 'cajas';
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
    $data['monto'] = $this->input->post('monto');
    $data['observacion'] = $this->input->post('observacion');
    $data['created'] = date('Y-m-d');
    if ($this->Controlador_model->save($this->controlador, $data)) {
      echo json_encode(array("status" => TRUE));
    }
  }

  public function ajax_delete($id)
  {
    $this->Controlador_model->delete_by_id($id, $this->controlador);
    echo json_encode(array("status" => TRUE));
  }
}
