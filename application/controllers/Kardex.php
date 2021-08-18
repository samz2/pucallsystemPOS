<?php

class Kardex extends CI_Controller {

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
      'datas' => $this->Controlador_model->getAll('empresa'),
      'breads' => array(array('ruta' => 'javascript:;', 'titulo' => $this->titulo_controlador))
    );
    $this->load->view(THEME.TEMPLATE, $data);
  }

  public function autocompletar() {
    if (isset($_GET['term'])) {
      $q = strtoupper($_GET['term']);
      $this->Controlador_model->autocompletar($q);
    }
  }

  public function kardexFecha() {
    $producto = $this->input->post('producto');
    $fechainicio = $this->input->post('fechainicio');
    $fechafinal = $this->input->post('fechafinal');
    $movimientos = $this->Controlador_model->kardexFecha($producto, $fechainicio, $fechafinal);
    $saldoinicial = $this->Controlador_model->saldoinicialxFecha($producto, $fechainicio, $fechafinal);
    $ticket = '';
    $ticket .= '<table class="table table-bordered table-striped"><thead><tr><th>#</th><th>Tipo</th><th>Fecha</th><th>Nยบ Doc.</th>
    <th>Denominacion</th><th>PC/PV</th><th>Ingreso</th><th>Salida</th><th>Saldo</th></tr></thead><tbody><tr><td colspan="5"></td>
    <td>Saldo Inicial:</td><td colspan="2"></td><td>'.($saldoinicial ? $saldoinicial->stockanterior : 0).'</td></tr>';
    $saldofinal = $saldoinicial ? $saldoinicial->stockanterior : 0;
    $salida = 0;
    $ingreso = 0;
    $i = 1;
    foreach ($movimientos as $data) {
      $saldofinal = $saldofinal + ($data->tipo == 'ENTRADA' ? $data->cantidad : 0) - ($data->tipo == 'SALIDA' ? $data->cantidad : 0);
      $ingreso = $ingreso + ($data->tipo == 'ENTRADA' ? $data->cantidad : 0);
      $salida = $salida + ($data->tipo == 'SALIDA' ? $data->cantidad : 0);
      $venta = $this->Controlador_model->get($data->venta, 'venta');
      $notaingreso = $this->Controlador_model->get($data->notaingreso, 'notaingreso');
      $notasalida = $this->Controlador_model->get($data->notasalida, 'notasalida');
      if($venta) {
        $numero = isset($venta->id) ? $venta->serie.'-'.$venta->numero : '';
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $razon = $cliente->nombre.' '.$cliente->apellido;
        $fecha = $venta->created;
        $VD = $this->Controlador_model->getVDetalle($data->producto, $data->venta);
        $precio = $VD->precio;
      } else if($notaingreso) {
        $compra = $this->Controlador_model->get($notaingreso->compra, 'compra');
        if(isset($compra->proveedor)) {
          $proveedor = $this->Controlador_model->get($compra->proveedor, 'proveedor');
        }
        $razon = isset($compra->proveedor) ? $proveedor->nombre : '';
        $fecha = $notaingreso->created;
        $NID = $this->Controlador_model->getNIDetalle($data->producto, $data->notaingreso);
        $precio = $NID->precio;
        $numero = isset($notaingreso->ordencompra) ? $ordencompra->serie.'-'.$ordencompra->numero : $notaingreso->codigo;
      } else if($notasalida) {
        $numero = isset($notasalida->id) ? $notasalida->codigo : '';
        $usuario = $this->Controlador_model->get($notasalida->usuario, 'usuario');
        $razon = $usuario->nombre.' '.$usuario->apellido;
        $fecha = $notasalida->created;
        $NSD = $this->Controlador_model->getNSDetalle($data->producto, $data->notasalida);
        $precio = $NSD->precio;
      }
      $ticket .= '<tr><td>'.$i.'</td><td>'.($notaingreso ? $notaingreso->tipoingreso : '').($venta ? $venta->tipoventa : '').
      ($notasalida ? $notasalida->tiposalida : '').'</td><td>'.$fecha.'</td><td>'.$numero.'</td><td>'.$razon.'</td><td>'.$precio.'</td>
      <td>'.($data->tipo == 'ENTRADA' ? $data->cantidad : 0).'</td><td>'.($data->tipo == 'SALIDA' ? $data->cantidad : 0).'</td>
      <td>'.$saldofinal.'</td></tr>';
      $i ++;
    }
    // barcode codding type
    $ticket .= '<tr><td colspan="5"></td><td>Saldo Final:</td><td>'.$ingreso.'</td><td>'.$salida.'</td>
    <td>'.$saldofinal.'</td></tr></tbody></table>';
    echo $ticket;
  }

}

/* End of file producto.php */
/* Location: ./system/application/controllers/producto.php */
