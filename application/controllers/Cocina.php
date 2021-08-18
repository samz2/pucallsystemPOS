<?php

class Cocina extends CI_Controller {

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

  public function load_pedido() {
    $this->db->where('estado', '0')->where('atender', '0');
    $ventas = $this->db->order_by('hora', 'asc')->get('venta')->result();
    $row = '';
    if($ventas) {
     
      foreach ($ventas as $hold) {

        $ventasound = $this->db->where('sound', '0')->where('totalitems >', '0')->where('id', $hold->id)->get('venta')->row();
        
        if($ventasound){
            $row .= '<embed src="'.base_url().'assets/sonidos/beep-02.wav" type="audio/wav" controller="true" height="0px" loop="true" autostart="true"></embed>';
            $dataventa = ['sound' => '1'];
            $this->Controlador_model->update(['id' => $ventasound->id], $dataventa, 'venta');
        }
        
        $posales = $this->db->where('venta', $hold->id)->where('estado', '0')->get('ventadetalle')->result();
        $table = $this->Controlador_model->get($hold->mesa, 'mesa');
        $usuario = $this->Controlador_model->get($hold->usuario, 'usuario');
        $empresa = $this->Controlador_model->get($hold->empresa, 'empresa');
        if($hold->atender == '0' && $posales) {
          $row .= '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
          <div class="panel panel-warning">
          <div class="panel-heading text-center">
          <div  style="display: flex; align-items:center; justify-content:space-between">
              <div class="" >
              <h4>Mesa: '.$table->nombre.' '.date('h:i A', strtotime($table->time)).'</h4>
              <h4>Camarero: '.$usuario->nombre.' '.$usuario->apellido.'</h4>
              </div>
              <div class="col-lg-4 text-center">
              <button onclick="preparados('.$hold->id.')" style="font-size:40px" type="button" class="btn btn-block btn-sm btn-success btn-lg"><i class="fa fa-check-square-o"></i></button>
             </div>
              </div>
          </div>
          <div class="panel-body"><table class="table table-striped table-bordered table-wrapper-scroll-y my-custom-scrollbar">
          <thead>
          <tr>
          <th>#</th>
          <th>Descripcion</th>
          <th>Cant</th>
          <th></th>
          </tr>
          </thead>
          <tbody>';
          $i = 1;
          foreach ($posales as $posale) {
            $d1 = new DateTime($posale->time);
            $d2 = new DateTime($table->checked);
            if($d1 < $d2) {
              $time = 'y';
            } else {
              $time = 'n';
            }
            $producto = $this->Controlador_model->get($posale->producto, 'producto');
            $productocategoria = $this->Controlador_model->get($producto->categoria, 'productocategoria');

      			if($productocategoria->estadococina == '1') { }
      				$row .= '<tr style="'.($time == "n" ? 'background-color:#FFC0CB;' : '').'">
              <td style="text-align:center; width:30px;">'.$i.'</td>
              <td>'.$posale->nombre.'<br>
              <span style="font-size:16px;color:#666">'.$posale->opcion.'</span></td>
              <td>'.$posale->cantidad.'</td>
              <td><div class="checkbox checkbox-primary">
              <input type="checkbox" onclick="preparado('.$posale->id.')" id="checkbox'.$posale->id.'">
              <label for="checkbox'.$posale->id.'"></label></div></td></tr>';
              $i ++;
      			
          }
          $row .= '</tbody></table></div></div></div>';
        }
      }
    }
    echo json_encode($row);
  }

  function preparados($venta){
    $ventadetalle = $this->db->where('estado', '0')->where('venta', $venta)->get('ventadetalle');
    foreach($ventadetalle->result() as $detalle){
      $ventaConsult = $this->Controlador_model->get($detalle->venta, 'venta');
      $mesaData['estado'] = '3';
  		$mesaData['checked'] = date('Y-m-d H:i:s');
      $this->Controlador_model->update(array('id' => $ventaConsult->mesa), $mesaData, 'mesa');
  		$ventaData['atender'] = '1';
      $this->Controlador_model->update(array('id' => $detalle->venta), $ventaData, 'venta');
      $ventadetalleData['estado'] = '1';
      $this->Controlador_model->update(array('id' => $detalle->id), $ventadetalleData, 'ventadetalle');
    }
    echo json_encode(['status' => TRUE]);
  }

  public function preparado($detalle) {
    $detalles = $this->Controlador_model->get($detalle, 'ventadetalle');
    $ventas = $this->Controlador_model->get($detalles->venta, 'venta');
    $ventadetalle = $this->db->where('estado', '0')->where('venta', $detalles->venta)->from('ventadetalle')->count_all_results();
    if($ventadetalle == 1) {
      $mesa['estado'] = '3';
  		$mesa['checked'] = date('Y-m-d H:i:s');
      $this->Controlador_model->update(array('id' => $ventas->mesa), $mesa, 'mesa');
  		$venta['atender'] = '1';
      $this->Controlador_model->update(array('id' => $detalles->venta), $venta, 'venta');
    }
    $data['estado'] = '1';
    $this->Controlador_model->update(array('id' => $detalle), $data, 'ventadetalle');
  }

}
