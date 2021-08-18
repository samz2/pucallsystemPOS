<?php

//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

require 'simple_html_dom.php';

use FontLib\Table\Type\post;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use function PHPSTORM_META\map;

class Alquiler extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(modelo(), 'Controlador_model');
        $this->load->library('Phpmailer_lib');
        $this->controlador = controlador();
        $this->titulo_controlador = humanize($this->controlador);
        $this->url = base_url() . $this->controlador;
        $this->vista = $this->controlador;
        $this->perfil = $this->session->userdata('perfil') ? $this->session->userdata('perfil') : FALSE;
        $this->caja = $this->session->userdata('caja') ? $this->session->userdata('caja') : FALSE;
        $this->venta = $this->session->userdata('venta') ? $this->session->userdata('venta') : FALSE;
        $this->usuario = $this->session->userdata('usuario') ? $this->session->userdata('usuario') : FALSE;
        $this->empresa = $this->session->userdata('empresa') ? $this->session->userdata('empresa') : FALSE;
        $this->NumberTable = $this->session->userdata('NumberTable') ? $this->session->userdata('NumberTable') : FALSE;
    }

    public function index()
    {
        $data = array(
            'contenido' => $this->vista,
            'empresa' => $this->Controlador_model->get($this->empresa, 'empresa'),
            'zonas' => $this->caja ? $this->Controlador_model->ventapendiente($this->caja) : '',
            'header' => $this->venta ? $this->Controlador_model->getmesa($this->venta) : '',
            'tiendas' => $this->Controlador_model->getAll('empresa'),
            'zonasa' => $this->Controlador_model->getZonasEmpresa($this->empresa),
            'mesotas' => $this->Controlador_model->getMesasEmpresa($this->empresa),
            'categorias' => $this->Controlador_model->getAll('productocategoria'),
        );
        $this->load->view(THEME . TEMPLATE, $data);
    }

    public function openregister()
    {
        $apertura = $this->Controlador_model->cajabierta('0', $this->input->post('empresa'));
        if ($apertura) {
            redirect($this->url);
        }
        $numero = $this->Controlador_model->maximo('caja', $this->input->post('empresa'));
        $numeros = $numero ? $numero->numero + 1 : 1;

        $cadena = "";
        for ($i = 0; $i < 5 - strlen($numeros); $i++) {
            $cadena = $cadena . '0';
        }
        $data['descripcion'] = 'CAJA N° ' . $cadena . $numeros;
        $data['numero'] = $numeros;
        $data['empresa'] = $this->input->post('empresa');
        $data['saldoinicial'] = $this->input->post('saldoinicial');
        $data['usuario'] = $this->usuario;
        $data['apertura'] = date("Y-m-d H:i:s");
        $data['created'] = date('Y-m-d');
        $registro = $this->Controlador_model->save('caja', $data);
        $CI = &get_instance();
        $CI->session->set_userdata('caja', $registro);
        $this->caja = $CI->session->userdata('caja') ? $CI->session->userdata('caja') : FALSE;
        echo json_encode(array("status" => TRUE));
    }

    public function aperturar($tienda)
    {
        $CI = &get_instance();
        $CI->session->set_userdata('empresa', $tienda);
        $apertura = $this->Controlador_model->cajabierta('0', $tienda);
        $open_reg = $apertura ? $apertura : $this->Controlador_model->maximo('caja', $tienda);
        $CI->session->set_userdata('caja', $open_reg->id);
        $query = $this->db->where("id", $this->empresa)->get('empresa')->row();
        redirect($this->url);
    }

    function ajax_CrearNewVenta()
    {
        $dataventa = [
            'empresa' => $this->empresa,
            'usuario_creador' => $this->usuario,
            'caja' => $this->caja,
            'mesa' => null,
            'cliente' => 1,
            'hora' => date("H:i:s"),
            'created' => date("Y-m-d")
        ];
        $insert = $this->Controlador_model->save('venta', $dataventa);

        if ($insert) {
            echo json_encode(["status" => TRUE]);
        }
    }


    public function eliminar($id)
    {
        $venta = $this->Controlador_model->get($id, 'venta');
        $this->Controlador_model->delete_by_id($venta->mesa, 'mesa');
        $this->Controlador_model->delete_by_venta($id, 'ventadetalle');
        $this->Controlador_model->delete_by_venta($id, 'ventatemporal');
        $this->Controlador_model->delete_by_id($id, 'venta');
        redirect($this->url);
    }


    public function switshregister()
    {
        $CI = &get_instance();
        $CI->session->set_userdata('caja', NULL);
        redirect($this->url);
    }

    public function switshtable()
    {
        $CI = &get_instance();
        $CI->session->set_userdata('venta', NULL);
        $CI->session->set_userdata('NumberTable', NULL);
        redirect($this->url);
    }

    public function habilitar()
    {
        if ($this->Controlador_model->habilitar($this->caja, 'caja')) {
            mensaje_alerta('hecho', 'habilitar');
        } else {
            mensaje_alerta('error', 'habilitar');
        }
        redirect($this->url);
    }


    function ajax_salir_caja()
    {
        $CI = &get_instance();
        $CI->session->set_userdata('caja', NULL);
        echo json_encode(array("status" => TRUE));
    }

    public function opcionmenu()
    {
        $data = '';

        $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Regreso">
    <a onclick="salircaja()"><i class="fa fa-reply" aria-hidden="true"></i></a></li>';


        $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Cerrar&nbsp;Caja">
    <a href="javascript:void(0)" onclick="CloseRegister()"><i class="fa fa-times" aria-hidden="true"></i></a>
   </li>';

        $data .= '<li data-toggle="tooltip" data-html="true" data-placement="left" title="Cargar&nbsp;Pagina">
    <a href="javascript:void(0)" onclick="location.reload()"><i class="fa fa-refresh" aria-hidden="true"></i></a></li>';

        echo json_encode(["html" => $data]);
    }

    public function autocompletar()
    {
        if (isset($_GET['term'])) {
            $q = strtoupper($_GET['term']);
            $this->Controlador_model->autocompletar($q);
        }
    }

    public function autocompleteCodigoBarra()
    {
        if (isset($_GET['term'])) {
            $q = strtoupper($_GET['term']);
            $this->Controlador_model->autocompletarcodigobarra($q);
        }
    }

    public function ajax_codigodebarra()
    {
        $codigobarra = $this->input->post("codigodebarra");
        $query = $this->Controlador_model->getCodigoBarra($codigobarra);
        $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
        if ($query) {
            if ($query->tipo == "1") {
                $dataLotes = $this->Controlador_model->dataLotes($query->id, $dataEmpresa->almacen, $this->empresa);
                echo json_encode(
                    [
                        'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa],
                        'lote' => ['status' => $query->status_lote,],
                    ]
                );
                exit();
            } else if ($query->tipo == "0") {
                if ($query->status_lote == "1") {
                    $dataLotes = $this->Controlador_model->dataLotes($query->id, $dataEmpresa->almacen, $this->empresa);
                    if ($dataLotes->num_rows() > 1) {
                        //? MODAL
                        echo json_encode(
                            [
                                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa],
                                'lote' => ['status' => $query->status_lote, 'totalotes' => $dataLotes->num_rows()]
                            ]
                        );
                    } else {
                        //? REGISTRO DIRECTO DEL LOTES
                        $dataStock = $dataLotes->row();
                        echo json_encode(
                            [
                                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa],
                                'lote' => ['status' => $query->status_lote,  'totalotes' => $dataLotes->num_rows(), 'lote' => $dataStock ? $dataStock->lote : null]
                            ]
                        );
                    }
                } else {
                    $queryStock = $this->Controlador_model->queryStock($query->id, $dataEmpresa->almacen, $this->empresa);

                    if ($queryStock) {
                        echo json_encode(
                            [
                                'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa, 'statuslote' => $query->status_lote],
                                'lote' => ['status' => $query->status_lote]
                            ]
                        );
                    } else {
                        echo json_encode(['consulta' => ["status" => FALSE, "msg" => $query->nombre . " SIN STOCK"]]);
                    }
                }
            } else {
                echo json_encode(['consulta' => ["status" => FALSE, "msg" => ""]]);
            }
        } else {
            echo json_encode(['consulta' => ["status" => FALSE, "msg" => ""]]);
        }
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('cliente') == '') {
            $data['inputerror'][] = 'clientes';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_update($idventa)
    {
        $venta = $this->Controlador_model->get($idventa, 'venta');
        if ($venta->estado == 1) {
            redirect($this->url);
        }
        $this->_validate();
        $data['tipoventa'] = $this->input->post('tipoventa');
        $data['cliente'] = $this->input->post('cliente');
        $this->Controlador_model->update(array('id' => $idventa), $data, 'venta');
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_Datos_venta()
    {
        $query = $this->Controlador_model->get($this->input->post("idventa"), "venta");
        if ($query) {
            $cliente = $this->Controlador_model->get($query->cliente, 'cliente');
            $data = [
                "tipoventa" => $query->tipoventa,
                "cliente" => $query->cliente,
                "clientes" => $cliente->documento . ' | ' . $cliente->nombre . ' | ' . $cliente->apellido
            ];
            echo json_encode($data);
        }
    }

    private function _validatecliente()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
        $detallelist = $this->Controlador_model->check($this->input->post('documento'));

        if ($this->input->post('documento') == '') {
            $data['inputerror'][] = 'documento';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nombre') == '') {
            $data['inputerror'][] = 'nombre';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($detallelist) {
            $data['inputerror'][] = 'documento';
            $data['error_string'][] = 'Este campo ya existe en la base de datos.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }



    public function ajax_addcliente()
    {
        $this->_validatecliente();
        $data['tipodocumento'] = $this->input->post('tipo');
        $data['documento'] = $this->input->post('documento');
        $data['nombre'] = $this->input->post('nombre');
        $data['apellido'] = $this->input->post('apellido');
        $data['direccion'] = $this->input->post('direccion');
        $data['telefono'] = $this->input->post('telefono');
        $data['correo'] = $this->input->post('email');
        $ultimo = $this->Controlador_model->save('cliente', $data);
        $venta['cliente'] = $ultimo;
        $this->Controlador_model->update(array('id' => $this->venta), $venta, 'venta');
        $recoverCliente = $this->Controlador_model->get($ultimo, 'cliente');
        $cliente = $recoverCliente->id;
        $clientes = $recoverCliente->documento . ' | ' . $recoverCliente->nombre . ' ' . $recoverCliente->apellido;
        echo json_encode(array("status" => TRUE, 'cliente' => $cliente, 'clientes' => $clientes));
    }

    public function _validatecliente2()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;
        $detallelist = $this->Controlador_model->check($this->input->post('documento2'));

        if ($this->input->post('documento2') == '') {
            $data['inputerror'][] = 'documento2';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($this->input->post('nombre2') == '') {
            $data['inputerror'][] = 'nombre2';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($detallelist) {
            $data['inputerror'][] = 'documento2';
            $data['error_string'][] = 'Este campo ya existe en la base de datos.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function ajax_addcliente2()
    {
        $this->_validatecliente2();
        $data['tipodocumento'] = $this->input->post('tipo2');
        $data['documento'] = $this->input->post('documento2');
        $data['nombre'] = $this->input->post('nombre2');
        $data['apellido'] = $this->input->post('apellido2');
        $data['direccion'] = $this->input->post('direccion2');
        $data['telefono'] = $this->input->post('direccion2');
        $data['correo'] = $this->input->post('email2');
        $ultimo = $this->Controlador_model->save('cliente', $data);
        $recoverCliente = $this->Controlador_model->get($ultimo, 'cliente');
        $cliente = $recoverCliente->id;
        $clientes = $recoverCliente->documento . ' | ' . $recoverCliente->nombre . ' ' . $recoverCliente->apellido;
        echo json_encode(array("status" => TRUE, 'cliente' => $cliente, 'clientes' => $clientes));
    }

    public function ajax_addproductolibre()
    {
        $data['nombre'] = $this->input->post('nombre');
        $data['precioventa'] = $this->input->post('precioventa');
        $data['categoria'] = 14;
        $data['empresa'] = $this->empresa;
        $data['tipo'] = '1';
        $data['unidad'] = 'UND';
        $producto = $this->Controlador_model->save('producto', $data);
        if ($producto > 0) {
            $productos = $this->Controlador_model->get($producto, 'producto');
            echo json_encode(array("status" => TRUE, "producto" => $productos->id, "nombre_producto" => $productos->nombre, "categoria" => 14, "precio" => $productos->precioventa));
        } else {
            echo json_encode(array("status" => FALSE));
        }
    }

    private function _validatprocesar()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        $venta = $this->Controlador_model->get($this->input->post("idventa"), 'venta');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $datapago = $this->input->post('totalpagar') - $this->input->post("descuento");

        if ($this->input->post('pago') >= $datapago) {
        } else {
            $data['inputerror'][] = 'pago';
            $data['error_string'][] = 'No puedes cancelar la venta con el monto isertado';
            $data['status'] = FALSE;
        }

        if ($this->input->post('fecha') == '') {
            $data['inputerror'][] = 'fecha';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }



        if ($venta->tipoventa == 'FACTURA' && $cliente->tipodocumento == 'DNI') {
            $data['inputerror'][] = 'pago';
            $data['error_string'][] = 'Debe agregar cliente con RUC.';
            $data['status'] = FALSE;
        }

        if ($venta->tipoventa == 'BOLETA' && $cliente->tipodocumento == 'RUC') {
            $data['inputerror'][] = 'pago';
            $data['error_string'][] = 'Debe agregar cliente con DNI.';
            $data['status'] = FALSE;
        }

        if ($this->input->post('formapago') == 'CONTADO') {
            if ($this->input->post('metodopago') == 'EFECTIVO') {
                if ($this->input->post('pago') == '') {
                    $data['inputerror'][] = 'pago';
                    $data['error_string'][] = 'Este campo es obligatorio.';
                    $data['status'] = FALSE;
                }
            } else {
                /*
        if ($this->input->post('operacion') == '') {
          $data['inputerror'][] = 'operacion';
          $data['error_string'][] = 'Este campo es obligatorio.';
          $data['status'] = FALSE;
        }
        */
            }
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function AddNewSale()
    {
        $this->_validatprocesar();
        $idventa = $this->input->post('idventa');
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $serie = substr($venta->tipoventa, 0, 1) . substr($empresa->serie, 1, 3);
        $numero = $this->Controlador_model->codigos($venta->tipoventa, $serie);
        $numeros = $numero ? $numero->consecutivo + 1 : 1;
        $cadena = "";
        $montopago = $this->input->post("pago") + $this->input->post("descuento");
        for ($i = 0; $i < 6 - strlen($numeros); $i++) {
            $cadena = $cadena . '0';
        }
        $dataventa['serie'] = $serie;
        $dataventa['numero'] = $cadena . $numeros;
        $dataventa['consecutivo'] = $numeros;
        if ($this->input->post('formapago') == 'CONTADO') {
            $deudaactual = $venta->montoactual - $this->input->post("descuento");
            if ($this->input->post('pago') >= $deudaactual) {
                $montoactual =  0;
                $dataventa['descuento'] = $venta->descuento + $this->input->post('descuento');
                $dataventa['montoactual'] = 0;
                $dataventa['estado'] = '1';
                $dataingreso['monto'] = $venta->montoactual - $this->input->post('descuento');
            } else {
                $montoactual = $venta->montoactual - $this->input->post('pago');
                $dataingreso['monto'] = $this->input->post('pago');
                $dataventa['descuento'] = $venta->descuento + $this->input->post('descuento');
                $dataventa['montoactual'] = $venta->montoactual - $this->input->post('pago');
            }
            $dataventa['pago'] = $venta->pago + $this->input->post('pago');
            $dataingreso['empresa'] = $this->empresa;
            $dataingreso['usuario'] = $this->usuario;
            $dataingreso['concepto'] = 3;
            $dataingreso['caja'] = $this->caja;
            $dataingreso['venta'] = $idventa;
            $dataingreso['metodopago'] = $this->input->post('metodopago');
            $dataingreso['tipotarjeta'] = $this->input->post('tipotarjeta');
            $dataingreso['created'] = date('Y-m-d');
            $this->Controlador_model->save('ingreso', $dataingreso);
        } else {
            $dataventa['estado'] = '1';
        }
        $dataventa['formapago'] = $this->input->post('formapago');
        $dataventa['created'] = $this->input->post('fecha');
        $dataventa['vence'] = $this->input->post('vence');
        $dataventa['hf_procesado'] = date("Y-m-d H:i:s");
        $this->Controlador_model->update(array('id' => $idventa), $dataventa, 'venta');
        $this->Controlador_model->UpdateVentaDetalleDeuda($idventa);
        $dataHtml = $this->dataMesas();
        echo json_encode(array("status" => TRUE, 'tipopago' => $this->input->post('formapago'), "dataHtml" => $dataHtml));
    }

    function NewVenta()
    {
        $queryVentas = $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $this->empresa);
        $query = $this->db->where('id', $this->empresa)->get('empresa')->row();
        if ($queryVentas->num_rows() == 0) {
            $data['empresa'] = $this->empresa;
            $data['cliente'] = 1;
            $data['hora'] = date("H:i:s");
            $data['created'] = date("Y-m-d");
            $data['usuario_creador'] = $this->usuario;
            $data['caja'] = $this->caja;
            $data['mesa'] = null;
            $data['tipoventa'] = $query->tipoventa;
            $insert = $this->Controlador_model->save('venta', $data);
        }
    }


    public function printfcomprobante()
    {
        $idventa = $this->input->post("idventa");
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $usuario = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');

        $ticket = '
    <div class="col-md-12">
    <h4 class="text-center">Venta Núm:.: ' . $venta->serie . '-' . $venta->numero . '</h4>
   <div style="clear:both;">
   </div>
   <span class="float-left">Fecha: ' . $venta->created . '</span
   ><br>
   <div style="clear:both;">
   <span class="float-left">
   Cliente: ' . ($cliente ? $cliente->nombre : '') . '</span>
   <div style="clear:both;">

   <table class="table">
   <thead>
   <tr>
   <th>#</th>
   <th>Descripcion</th>
   <th>Cant</th>
   <th>SubTotal</th>
   </tr>
   </thead>
   <tbody>';
        if ($venta->consumo == '1') {
            $ticket .= '
    <tr>
    <td style="text-align:center; width:30px;">1</td>
    <td style="text-align:left; width:180px;">Por consumo</td>
    <td style="text-align:center; width:50px;">1</td>
    <td style="text-align:right; width:70px; ">' . number_format($venta->montototal, 2) . ' Soles</td>
    </tr>';
        } else {
            $ventadetalle = $this->Controlador_model->pedidodetalle($idventa);
            foreach ($ventadetalle as $key => $value) {
                $producto = $this->Controlador_model->get($value->producto, 'producto');
                $ticket .= '
    <tr>
    <td style="text-align:center; width:30px;">' . ($key + 1) . '</td>
    <td style="text-align:left; width:180px;">' . $value->nombre . ' [' . ($value->variante ? ($value->cantidadvariante * $value->cantidad) : $value->cantidad) . ']</td>
    <td style="text-align:center; width:50px;">' .  $value->cantidad . '</td>
    <td style="text-align:right; width:70px; ">' . number_format($value->cantidad * $value->precio, 2) . ' Soles</td>
    </tr>';
            }
        }

        $ticket .= '</tbody>
    </table>
    <table class="table" cellspacing="0" border="0" style="margin-bottom:8px;">
   <tbody>
   <tr>
   <td style="text-align:left;">Total Items</td>
   <td style="text-align:right; padding-right:1.5%;">' . $venta->totalitems . '</td>
   <td style="text-align:left; padding-left:1.5%;">Total</td>
   <td style="text-align:right;font-weight:bold;">' . $venta->montototal . ' Soles</td>
   </tr>';
        $ticket .= '<tr>
    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Grand Total</td>
   <td colspan="2" style="border-top:1px dashed #000; padding-top:5px; text-align:right; font-weight:bold;">' . $venta->montototal . ' Soles</td>
   </tr>
   <tr>';
        $ticket .= '<tr>
    <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Descuento</td>
   <td colspan="2" style="border-top:1px dashed #000; padding-top:5px; text-align:right; font-weight:bold;">' . $venta->descuento . ' Soles</td>
   </tr>
   <tr>';
        $pagos = $this->Controlador_model->pagos($idventa);
        $vuelto = 0;
        foreach ($pagos as $value) {
            if ($value->metodopago == 'EFECTIVO') {
                $vuelto = $venta->pago - $value->monto;
            }
            $ticket .= '
      <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Pagado (' . strtolower($value->metodopago) . ')</td>
     <td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format($value->monto, 2) . ' Soles</td>
     </tr>';
        }
        $ticket .= '
      <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Recibio</td>
      <td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format($venta->pago, 2) . ' Soles</td></tr>';

        $ticket .= '
    <tr>
      <td colspan="2" style="text-align:left; font-weight:bold; padding-top:5px;">Vuelto</td>
      <td colspan="2" style="padding-top:5px; text-align:right; font-weight:bold;">' . number_format($vuelto, 2) . ' Soles</td>
   </tr>

   </tbody>
   </table>';

        $telefono = $cliente->telefono != "" ? $cliente->telefono : "";
        $email = $cliente->correo != "" ? $cliente->correo : "";
        $correo = '<div class="input-group input-group-sm">';
        $correo .= '<input type="email" id="correo" class="form-control" placeholder="correo@pucallsystem.com" value="' . $email . '">';
        $correo .= '<span class="input-group-btn" id="span-print">';
        $correo .= '<button id="enviarcorreo" class="btn btn-success" onclick="sendMail(' . $idventa . ')" type="button">';
        $correo .= '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
        $correo .= '</button>';
        $correo .= '</span>';
        $correo .= ' </div>';
        $WP = '
    <div class="input-group input-group-sm">
    <span class="input-group-addon" id="sizing-addon3">+51</span>
    <input type="text" id="telefonoWP" class="form-control" placeholder="999999999" value="' . $telefono . '" autocomplete="off">
    <span class="input-group-btn" id="span-print">
      <button class="btn btn-success" onclick="sentTicketWA(' . $idventa . ')" type="button"><i class="fa fa-whatsapp" aria-hidden="true"></i></button>
    </span> 
    </div>';

        $fotter = '
  <button data-dismiss="modal" class="btn btn-default hiddenpr">Cerrar</button>
  <button type="submit" class="btn btn-add" onclick="imprimircomprobante(' . $empresa->tipoimpresora . ',' . $idventa . ' )" id="btncomprobante">Imprimir</button>';


        if ($venta->montoactual == 0) {
            echo  json_encode(['html' => $ticket, 'fotter' => $fotter, "email" => $correo, "whatsapp" => $WP]);
        }
    }

    function sendemail()
    {
        $idventa = $this->input->post('idventa');
        $this->crearPDF($idventa);
        $deta = $this->Controlador_model->get($idventa, 'venta');
        $cliente = $this->Controlador_model->get($deta->cliente, 'cliente');
        $nombre_pdf = $deta->serie . "-" . $deta->numero . "-" . $cliente->documento . "-" . $deta->created . ".pdf";

        $correoTo = $this->input->post('correo');
        $path = base_url() . "CPETemp/" . $nombre_pdf;
        $asunto = "Documento electronico";
        $correo = "fel@pucallsystem.com";
        $password = "=ACn*SH^QYh6";
        $name = "PucallSystem";
        $mail = $this->phpmailer_lib->load();
        //$mail->isSMTP();                      // Establecer el correo electrónico para utilizar SMTP
        $mail->Host = 'mail.pucallsystem.com';  // Especificar el servidor de correo a utilizar 
        $mail->SMTPAuth = true;                 // Habilitar la autenticacion con SMTP
        $mail->Username = $correo;              // Correo electronico saliente ejemplo: tucorreo@gmail.com
        $mail->Password = $password;            // Tu contraseña de gmail
        $mail->SMTPSecure = 'tls';              // Habilitar encriptacion, `ssl` es aceptada
        $mail->Port = 465;                      // Puerto TCP  para conectarse 
        $mail->setFrom($correo, $name);         //Introduzca la dirección de la que debe aparecer el correo electrónico. Puede utilizar cualquier dirección que el servidor SMTP acepte como válida. El segundo parámetro opcional para esta función es el nombre que se mostrará como el remitente en lugar de la dirección de correo electrónico en sí.
        $mail->addReplyTo($correo, "HOLA");         //Introduzca la dirección de la que debe responder. El segundo parámetro opcional para esta función es el nombre que se mostrará para responder
        $mail->addAddress($correoTo);   // Agregar quien recibe el e-mail enviado
        /* $message = file_get_contents($template);
    $message = str_replace('{{first_name}}', $name, $message);
    $message = str_replace('{{message}}', $txt_message, $message);
    $message = str_replace('{{customer_email}}', $correo, $message); */

        $mail->isHTML(true);  // Establecer el formato de correo electrónico en HTML
        $template = '<!doctype html>
    <html lang="es">
    <head>
    <meta charset="UTF-8">
    <title>Aloha!</title>
    <style type="text/css">
        * {
            font-family: Verdana, Arial, sans-serif;
        }
    </style>
    </head>
    <body>
    <p>Hola!</p>
    <p>Adjuntamos el comprobante electrónico de su compra.</p>
    </body>
    </html>';
        $mail->Subject = $asunto;
        $mail->msgHTML($template);
        $archivo = 'comprobante.pdf';
        //if (!$mail->AddAttachment($path, $archivo)) {
        $url = $path;
        $fichero = file_get_contents($url);
        $mail->addStringAttachment($fichero, $archivo);
        // }

        if (!$mail->send()) {
            echo json_encode(array('status' => FALSE));
        } else {
            echo json_encode(array('status' => TRUE));
        }
    }

    public function CloseRegister()
    {
        $c = 0;
        $mesasAbiertas = '';
        $queryVentasSinGenerar = $this->db->where("caja", $this->caja)->where("estado", "0")->get("venta")->result();

        if ($c == 0) {
            $empresa = $this->Controlador_model->get($this->empresa, 'empresa');
            $registro = $this->Controlador_model->get($this->caja, 'caja');
            $usuario = $this->Controlador_model->get($registro->usuario, 'usuario');
            $sales = $this->Controlador_model->getCaja($this->caja, 'venta');
            $payaments = $this->Controlador_model->getCaja($this->caja, 'ingreso');
            $expences = $this->Controlador_model->getCaja($this->caja, 'egreso');
            $desc = 0;
            $cash = 0;
            $cc = 0;
            $cont = 0;
            $cred = 0;
            $gasto = 0;
            foreach ($payaments as $payament) {
                if ($payament->metodopago == 'EFECTIVO') {
                    $cash += $payament->monto;
                } else {
                    $cc += $payament->monto;
                }
            }
            foreach ($sales as $sale) {
                if ($sale->estado == '1') {
                    if ($sale->formapago == 'CONTADO') {
                        $cont += $sale->montototal;
                        $desc += $sale->descuento;
                    } else {
                        $cred += $sale->montototal;
                    }
                }
            }
            foreach ($expences as $expence) {
                $gasto += $expence->montototal;
            }

            $data = '<div class="col-md-3"><blockquote><footer>ENCARGADO</footer><p>' . $usuario->nombre . '</p></blockquote></div>
      <div class="col-md-3"><blockquote><footer>SALDO EFECTIVO</footer><p>' . number_format($cash, 2) . ' SOLES</p></blockquote></div>
      <div class="col-md-4"><blockquote><footer>FECHA DE APERTURA</footer><p>' . $registro->apertura . '</p></blockquote></div>
      <div class="col-md-2"><img src="' . site_url() . '/assets/adminlte/img/register.svg" alt=""></div>
      <h2 class="text-center">RESUMEN DE CAJA DIARIO ' . strtoupper($registro->descripcion) . '</h2>
      <table class="table table-striped"><tr><th width="25%">TIPO DE PAGO</th><th width="25%">MONTOS (Soles)</th></tr>
      <tr><td>SALDO INICIAL</td><td>' . number_format($registro->saldoinicial, 2) . '</td></tr>
      <tr><td>VENTA DIARIO</td><td>' . number_format($cont + $cred, 2) . '</td></tr>
      <tr><td>EFECTIVO</td><td>' . number_format($cash, 2) . '</td></tr>
      <tr><td>CREDITO</td><td>' . number_format($cred, 2) . '</td></tr>
      <tr><td>TARJETA</td><td>' . number_format($cc, 2) . '</td></tr>
      <tr><td>GASTO</td><td>' . number_format($gasto, 2) . '</td></tr>
      <tr class="warning"><td>TOTAL</td><td>' . number_format(($registro->saldoinicial + $cont + $cred - $gasto), 2) . '</td></tr></table>
      <input type="hidden" name="contado" id="contado" value="' . $cont . '">
      <input type="hidden" name="descuento" id="descuento" value="' . $desc . '">
      <input type="hidden" name="credito" id="credito" value="' . $cred . '">
      <input type="hidden" name="efectivo" id="efectivo" value="' . $cash . '">
      <input type="hidden" name="tarjeta" id="tarjeta" value="' . $cc . '">
      <input type="hidden" name="gasto" id="gasto" value="' . $gasto . '">';

            $mostrar = '
         <div class="col-md-3"><blockquote><footer>ENCARGADO</footer><p>' . $usuario->nombre . '</p></blockquote></div>
      <div class="col-md-3"><blockquote><footer>SALDO EFECTIVO</footer><p>0.00 SOLES</p></blockquote></div>
      <div class="col-md-4"><blockquote><footer>FECHA DE APERTURA</footer><p>' . $registro->apertura . '</p></blockquote></div>
      <div class="col-md-2"><img src="' . site_url() . '/assets/adminlte/img/register.svg" alt=""></div>
      <h2 class="text-center">RESUMEN DE CAJA DIARIO ' . $registro->descripcion . '</h2>
       
     <input type="hidden" value="1" name="tipoproceso" id="tipoproceso">
   
     <div class="col-lg-12">
       <div class="tabs-vertical-env">
         <ul class="nav tabs-vertical">
           <li class="active">
             <a href="#procesoone" data-toggle="tab" aria-expanded="true" style="font-size:16px; font-weight:bold" onclick="cambiarproceso(1)">Proceso 1</a>
           </li>
           <li class="">
             <a href="#procesotwo" data-toggle="tab" aria-expanded="false" style="font-size:16px;font-weight:bold" onclick="cambiarproceso(0)">Proceso 2</a>
           </li>
         </ul>
   
         <div class="tab-content" style="width:100%">

           <div class="tab-pane active" id="procesoone">
             <div class="form-group text-center">
                <label>Inserte el monto total de efectivo</label>
                <input type="number" name="montototalcaja" class="form-control" id="montototalcaja" placeholder="Digitar">
                <span class="help-block"></span>
             </div>
             
         </div>
           <div class="tab-pane" id="procesotwo">
             
             <table class="table table-striped" >
             <tr>
               <th>TIPO DE MONEDA (SOLES)</th>
               <th>CANTIDAD MONEDA</th>
             </tr>
             <tr>
               <td>0.10</td>
               <td><input type="text" class="form-control" name="diezcentimos" id="diezcentimos" value="0"></td>
             </tr>
             <tr>
             <td>0.20</td>
             <td><input type="text" class="form-control" name="veintecentimos" id="veintecentimos" value="0"></td>
             </tr>
             <tr>
             <td>0.50</td>
             <td><input type="text" class="form-control" name="cincuentacentimos" id="cincuentacentimos" value="0">
             </td>
             </tr>
             <tr><td>1.00</td>
             <td><input type="text" class="form-control" name="unsol" id="unsol" value="0"></td></tr>
             <tr><td>2.00</td>
             <td><input type="text" class="form-control" name="dossoles" id="dossoles" value="0"></td></tr>
             <tr><td>5.00</td>
             <td><input type="text" class="form-control" name="cincosoles" id="cincosoles" value="0"></td></tr>
             <tr><td>10.00</td>
             <td><input type="text" class="form-control" name="diezsoles" id="diezsoles" value="0"></td></tr>
             <tr><td>20.00</td><td><input type="text" class="form-control" name="veintesoles" id="veintesoles" value="0"></td></tr>
             <tr><td>50.00</td><td><input type="text" class="form-control" name="cincuentasoles" id="cincuentasoles" value="0"></td></tr>
             <tr><td>100.00</td><td><input type="text" class="form-control" name="ciensoles" id="ciensoles" value="0"></td></tr>
             <tr><td>200.00</td><td><input type="text" class="form-control" name="doscientossoles" id="doscientossoles" value="0"></td></tr>
           </table>
   
   
   
           </div>
         </div>
       </div>
     </div>
      
      <input type="hidden" class="form-control" name="contado" id="contado" value="' . $cont . '">
      <input type="hidden" class="form-control" name="descuento" id="descuento" value="' . $desc . '">
      <input type="hidden" class="form-control" name="credito" id="credito" value="' . $cred . '">
      <input type="hidden" class="form-control" name="efectivo" id="efectivo" value="' . $cash . '">
      <input type="hidden" class="form-control" name="tarjeta" id="tarjeta" value="' . $cc . '">
      <input type="hidden" class="form-control" name="gasto" id="gasto" value="' . $gasto . '">';




            echo json_encode(array('data' => $mostrar, 'status' => TRUE));
        } else {
            echo json_encode(array('data' => $mesasAbiertas, 'status' => FALSE));
        }
    }

    private function _validateCierre()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        if ($this->input->post('montototalcaja') == '') {
            $data['inputerror'][] = 'montototalcaja';
            $data['error_string'][] = 'Este campo es obligatorio.';
            $data['status'] = FALSE;
        }

        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function SubmitRegister()
    {
        if ($this->input->post('tipoproceso') == 1) {
            $this->_validateCierre();
        } else {
        }
        $data['contado'] = $this->input->post('contado');
        $data['descuento'] = $this->input->post('descuento');
        $data['credito'] = $this->input->post('credito');
        $data['efectivo'] = $this->input->post('efectivo');
        $data['tarjeta'] = $this->input->post('tarjeta');
        $data['gasto'] = $this->input->post('gasto');
        $data['estado'] = '1';

        $this->Controlador_model->update(array('id' => $this->caja), $data, 'caja');


        $datamonedero['empresa'] = $this->empresa;
        $datamonedero['usuario'] = $this->usuario;
        $datamonedero['caja'] = $this->caja;
        $datamonedero['status'] = $this->input->post('tipoproceso');

        if ($this->input->post('tipoproceso') == "0") {
            $diezcentimos = $this->input->post('diezcentimos');
            $veintecentimos = $this->input->post('veintecentimos');
            $cincuentacentimos = $this->input->post('cincuentacentimos');
            $unsol = $this->input->post('unsol');
            $dossoles = $this->input->post('dossoles');
            $cincosoles = $this->input->post('cincosoles');
            $diezsoles = $this->input->post('diezsoles');
            $veintesoles = $this->input->post('veintesoles');
            $cincuentasoles = $this->input->post('cincuentasoles');
            $ciensoles = $this->input->post('ciensoles');
            $doscientossoles = $this->input->post('doscientossoles');

            $datamonedero['diezcentimos'] = $diezcentimos;
            $datamonedero['veintecentimos'] = $veintecentimos;
            $datamonedero['cincuentacentimos'] = $cincuentacentimos;
            $datamonedero['unsol'] = $unsol;
            $datamonedero['dossoles'] = $dossoles;
            $datamonedero['cincosoles'] = $cincosoles;
            $datamonedero['diezsoles'] = $diezsoles;
            $datamonedero['veintesoles'] = $veintesoles;
            $datamonedero['cincuentasoles'] = $cincuentasoles;
            $datamonedero['ciensoles'] = $ciensoles;
            $datamonedero['doscientossoles'] = $doscientossoles;
            $total =  $diezcentimos + $veintecentimos + $cincuentacentimos + $unsol + $dossoles + $cincosoles + $diezsoles + $veintesoles + $cincuentasoles + $ciensoles + $doscientossoles;
            $datamonedero['montototal'] = $total;
        } else {
            $datamonedero['montototal'] = $this->input->post('montototalcaja');
        }


        $this->Controlador_model->save('monedero', $datamonedero);


        $this->deleteCPETemp();
        //perfil 3 = caja
        echo json_encode(array("status" => TRUE, "usuarioperfil" => $this->perfil));
        exit();
        $data['contado'] = $this->input->post('contado');
        $data['descuento'] = $this->input->post('descuento');
        $data['credito'] = $this->input->post('credito');
        $data['efectivo'] = $this->input->post('efectivo');
        $data['tarjeta'] = $this->input->post('tarjeta');
        $data['gasto'] = $this->input->post('gasto');
        $data['estado'] = '1';
        $this->Controlador_model->update(array('id' => $this->caja), $data, 'caja');
        $datamonedero['empresa'] = $this->empresa;
        $datamonedero['usuario'] = $this->usuario;
        $datamonedero['caja'] = $this->caja;
        $datamonedero['diezcentimos'] = $this->input->post('diezcentimos');
        $datamonedero['veintecentimos'] = $this->input->post('veintecentimos');
        $datamonedero['cincuentacentimos'] = $this->input->post('cincuentacentimos');
        $datamonedero['unsol'] = $this->input->post('unsol');
        $datamonedero['dossoles'] = $this->input->post('dossoles');
        $datamonedero['cincosoles'] = $this->input->post('cincosoles');
        $datamonedero['diezsoles'] = $this->input->post('diezsoles');
        $datamonedero['veintesoles'] = $this->input->post('veintesoles');
        $datamonedero['cincuentasoles'] = $this->input->post('cincuentasoles');
        $datamonedero['ciensoles'] = $this->input->post('ciensoles');
        $datamonedero['doscientossoles'] = $this->input->post('doscientossoles');
        $this->Controlador_model->save('monedero', $datamonedero);
        $this->deleteCPETemp();
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_updatecaja()
    {
        $datas = $this->Controlador_model->get($this->caja, 'caja');
        $empresa = $this->Controlador_model->get($datas->empresa, 'empresa');
        $data['tipoimpresora'] = $empresa->tipoimpresora;
        echo json_encode($data);
    }

    public function imprimircomanda()
    {
        $venta = $this->Controlador_model->get($this->venta, 'venta');
        $mesa = $this->Controlador_model->get($venta->mesa, 'mesa');
        $usuario = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        if ($venta->estado == '0') {
            $pedidotemporal = $this->Controlador_model->pedidotemporal($this->venta);
            $numerobebida = 0;
            $numerocomida = 0;
            foreach ($pedidotemporal as $value) {
                $producto = $this->Controlador_model->get($value->producto, 'producto');
                if ($producto->categoria == 5) {
                    $numerobebida++;
                } else {
                    $numerocomida++;
                }
            }
            if ($numerobebida > 1000) {
                $nombre_impresora = $empresa->nombreimpresora;
                $connector = new WindowsPrintConnector($nombre_impresora);
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setTextSize(2, 2);
                $printer->text("------------------------------------------------" . "\n");
                $printer->text("CAJA --> BEBIDAS" . "\n");
                $printer->text(date("Y-m-d H:i:s") . "\n");
                $printer->text("------------------------------------------------" . "\n");
                $printer->text("Mesa: " . ($mesa) ? $mesa->nombre : "MESA TEMPORAL" . "\n");
                $printer->text("Camarera: " . $usuario->nombre . "\n");
                $printer->text("------------------------------------------------" . "\n");
                /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $total = 0;
                foreach ($pedidotemporal as $value) {
                    $total += $value->precio * $value->cantidad;
                    $producto = $this->Controlador_model->get($value->producto, 'producto');
                    if ($producto->categoria == 5) {
                        $printer->text($value->cantidad . " UND. " . $value->nombre . " " . $value->opcion . "      \n");
                    }
                }
                /* Terminamos de imprimir los productos, ahora va el total */
                $printer->text("------------------------------------------------" . "\n");
                /* Alimentamos el papel 3 veces */
                $printer->feed(3);
                /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
                $printer->cut();
                /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
                $printer->pulse();
                /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
                $printer->close();
            }
            if ($numerocomida > 1000) {
                $nombre_impresora = $empresa->nombreimpresora;
                $connector = new WindowsPrintConnector($nombre_impresora);
                $printer = new Printer($connector);
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->setTextSize(2, 2);
                // $printer->text("------------------------------------------------"."\n");
                // $printer->text("------------------------"."\n");
                $printer->text("------------------------" . "\n");
                $printer->text("CAJA --> COMIDAS" . "\n");
                $printer->text(date("Y-m-d H:i:s") . "\n");
                $printer->text("------------------------" . "\n");
                $printer->text("Mesa: " . ($mesa) ? $mesa->nombre : "MESA TEMPORAL" . "\n");
                $printer->text("Camarera: " . $usuario->nombre . "\n");
                $printer->text("------------------------" . "\n");
                /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $total = 0;
                foreach ($pedidotemporal as $value) {
                    $total += $value->precio * $value->cantidad;
                    $producto = $this->Controlador_model->get($value->producto, 'producto');
                    if ($producto->categoria <> 5) {
                        $printer->text($value->cantidad . " UND. " . $value->nombre . " " . $value->opcion . " " . $value->precio . "      \n");
                    }
                }
                /* Terminamos de imprimir los productos, ahora va el total */
                $printer->text("------------------------" . "\n");
                /* Alimentamos el papel 3 veces */
                $printer->feed(3);
                /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
                $printer->cut();
                /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
                $printer->pulse();
                /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
                $printer->close();
            }
        }
    }

    public function showcomanda()
    {
        $venta = $this->Controlador_model->get($this->venta, 'venta');
        $pedidotemporal = $this->Controlador_model->pedidotemporal($this->venta);
        $numerobebida = 0;
        $numerocomida = 0;
        foreach ($pedidotemporal as $value) {
            $producto = $this->Controlador_model->get($value->producto, 'producto');
            if ($producto->categoria == 5) {
                $numerobebida++;
            } else {
                $numerocomida++;
            }
        }
        $mesa = $this->Controlador_model->get($venta->mesa, 'mesa');
        $data = array(
            'venta' => $venta,
            'numerobebida' => $numerobebida,
            'numerocomida' => $numerocomida,
            'ventadetalle' => $pedidotemporal,
            'mesa' => $mesa,
            'zona' => $this->Controlador_model->get($mesa->zona, 'zona'),
            'cliente' => $this->Controlador_model->get($venta->cliente, 'cliente'),
            'usuario' => $this->Controlador_model->get($venta->usuario, 'usuario'),
            'empresa' => $this->Controlador_model->get($venta->empresa, 'empresa')
        );
        $this->load->view('imprimircomanda', $data);
    }


    public function imprimircomprobante()
    {
        $venta = $this->Controlador_model->get($this->input->post("venta"), 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $mesa = $this->Controlador_model->get($venta->mesa, 'mesa');
        $usuario = $this->Controlador_model->get($venta->usuario_creador, 'usuario');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $nombre_impresora = $empresa->nombreimpresora;
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        $comprobante = $venta->serie . '|' . $venta->numero;
        $numerocomprobante = $venta->serie . '-' . $venta->numero;
        $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
        $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
        $url_base = 'archivos_xml_sunat/imgqr/';
        $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
        $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
        $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
        $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
        $params['level'] = 'H';
        $params['size'] = 3;
        $params['savename'] = $url_base . $numerocomprobante . '.png';
        $this->ciqrcode->generate($params);
        # Mando un numero de respuesta para saber que se conecto correctamente.
        # Vamos a alinear al centro lo próximo que imprimamos
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        /* Intentaremos cargar e imprimir el logo */
        try {
            $url = 'files/Setting/' . $empresa->ruc . '.png';
            $logo = EscposImage::load($url, false);
            // $printer->bitImage($logo);
        } catch (Exception $e) {
            /*No hacemos nada si hay error*/
        }
        /* Ahora vamos a imprimir un encabezado */
        if ($venta->tipoventa == 'OTROS') {
            $printer->text("\n" . "NOTA DE VENTA" . "\n");
        } else {
            if ($empresa->tipo == 1) {
                $printer->text("\n" . $empresa->razonsocial . "\n");
            } else {
                $printer->text("\n" . $empresa->nombre . "\n");
                $printer->text($empresa->razonsocial . "\n");
            }
            $printer->text($empresa->direccion . "\n");
            $printer->text($empresa->departamento . ' ' . $empresa->provincia . ' ' . $empresa->distrito . "\n");
            $printer->text("RUC: " . $empresa->ruc . "\n");
            $printer->text("DELIVERY " . $empresa->telefono . "\n");
            #La fecha también
            $printer->text("------------------------------------------------" . "\n");
            $printer->text($venta->tipoventa . " DE VENTA ELECTRONICA" . "\n");
            $printer->text($venta->serie . "-" . $venta->numero . "\n");
        }
        $printer->text("------------------------------------------------" . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text($cliente->tipodocumento . ": " . $cliente->documento . "\n");
        $printer->text("CLIENTE: " . $cliente->nombre . ' ' . $cliente->apellido . "\n");
        $printer->text("DIRECCION: " . $cliente->direccion . "\n");
        $printer->text("FECHA EMISION: " . $venta->created . " " . $venta->hora . "\n");
        $printer->text("FECHA VENCIMIENTO: " . $venta->created . "\n");
        $printer->text("MODEDA: SOLES\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("------------------------------------------------\n");
        $printer->text("[CANT.]    DESCRIPCION          P/U       TOTAL\n");
        $printer->text("------------------------------------------------" . "\n");
        /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $total = 0;
        if ($venta->consumo == '1') {
            $total = $venta->montototal;
            $printer->text("Por consumosssssss" . "\n");
            $printer->text("[1]                    " . $venta->montototal . "    " . number_format($venta->montototal, 2) . " \n");
        } else {
            $ventadetalle = $this->Controlador_model->pedidodetalle($this->venta);
            foreach ($ventadetalle as $value) {
                $total += $value->precio * $value->cantidad;
                $producto = $this->Controlador_model->get($value->producto, 'producto');
                $printer->text($producto->codigo . " " . $value->nombre . "\n");
                $printer->text("[" . $value->cantidad . "ssssssssss]                    " . $value->precio . "    " . number_format($value->precio * $value->cantidad, 2) . " \n");
            }
        }





        /* Terminamos de imprimir los productos, ahora va el total */
        $printer->text("------------------------------------------------" . "\n");
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        if ($venta->tipoventa == 'OTROS') {
            $printer->text("TOTAL:   S/   " . number_format($total, 2) . "\n");
        } else {
            $printer->text("OP. EXONERADA:   S/   " . number_format($total, 2) . "\n");
            $printer->text("IGV:   S/   " . number_format(0, 2) . "\n");
            $printer->text("TOTAL:   S/   " . number_format($total, 2) . "\n");
        }
        $pago = 0;
        $vuelto = 0;
        $pagos = $this->Controlador_model->getDetalle($this->venta, 'ingreso');
        foreach ($pagos as $value) {
            if ($value->metodopago == 'EFECTIVO') {
                $vuelto = $venta->pago - $value->monto;
            }
            $printer->text("PAGO ($value->metodopago):   S/   " . number_format($value->monto, 2) . "\n");
        }
        $printer->text("RECIBIO:   S/   " . number_format($venta->pago, 2) . "\n");
        $printer->text("VUELTO:   S/   " . number_format($vuelto, 2) . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("------------------------------------------------" . "\n");
        $printer->text("SON: " . num_to_letras($total) . "\n");
        $printer->text("VENDEDOR: " . $usuario->usuario . "\n");
        $printer->text("MESA: " . ($mesa) ? $mesa->nombre : "MESA TEMPORAL" . "\n");
        $printer->text("------------------------------------------------" . "\n");
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        if ($venta->tipoventa == 'OTROS') {
            $printer->text("GRACIAS POR SU COMPRA" . "\n");
        } else {
            $printer->text($codigohash . "\n");
            $printer->text("------------------------------------------------" . "\n");
            /* Intentaremos cargar e imprimir el logo */
            try {
                $codeurl = $url_base . $numerocomprobante . '.png';
                $codeqr = EscposImage::load($codeurl, false);
                $printer->bitImage($codeqr);
            } catch (Exception $e) {
                /*No hacemos nada si hay error*/
            }
            $printer->text("NO SE ACEPTAN DEVOLUCIONES Y/O\n");
            $printer->text("CAMBIO DESPUES DE LAS 24 HORAS\n");
            $printer->text("------------------------------------------------\n");
            $printer->text("REPRESENTACION IMPRESA DE\n");
            $printer->text("COMPROBANTE ELECTRONICO\n");
            $printer->text("AUTORIZADO MEDIANTE LA RESOLUCION DE\n");
            $printer->text("INTENDENCIA N°. 034-005-0005315\n");
        }
        /* Alimentamos el papel 3 veces */
        $printer->feed(3);
        /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
        $printer->cut();
        /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
        $printer->pulse();
        /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
        $printer->close();
    }

    public function showcomprobante($ventaid)
    {
        $venta = $this->Controlador_model->get($ventaid, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $comprobante = $venta->serie . '|' . $venta->numero;
        $numerocomprobante = $venta->serie . '-' . $venta->numero;
        $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
        $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
        $url_base = 'archivos_xml_sunat/imgqr/';
        $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
        $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
        $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
        $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
        $params['level'] = 'H';
        $params['size'] = 5;
        $params['savename'] = $url_base . $numerocomprobante . '.png';
        $this->ciqrcode->generate($params);
        $data = array(
            'venta' => $venta,
            'empresa' => $empresa,
            'cliente' => $cliente,
            'codigohash' => $codigohash,
            'qrcode' => '../../' . $url_base . $numerocomprobante . '.png',
            'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
            'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
            'ingresos' => $this->Controlador_model->getDetalle($ventaid, 'ingreso'),
            'ventadetalle' => $this->Controlador_model->comanda($ventaid)
        );
        $this->load->view('imprimircomprobante', $data);
    }

    public function getimprimir()
    {
        $venta = $this->Controlador_model->get($this->venta, 'venta');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $comprobante = $venta->serie . '|' . $venta->numero;
        $numerocomprobante = $venta->serie . '-' . $venta->numero;
        $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
        $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
        $url_base = 'archivos_xml_sunat/imgqr/';
        $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
        $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
        $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
        $data = array(
            'ventas' => $venta,
            'empresa' => $empresa,
            'cliente' => $cliente,
            'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
            'vuelto' => number_format($venta->pago - $venta->total, 2),
            'codigoqr' => $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|',
            'importeletra' => num_to_letras($venta->total),
            'pagos' => $this->Controlador_model->getDetalle($this->venta, 'ingreso'),
            'ventadetalle' => $this->Controlador_model->getDetalle($this->venta, 'ventadetalle')
        );
        echo json_encode($data);
    }

    public function imprimircierre()
    {
        $caja = $this->Controlador_model->get($this->caja, 'caja');
        $empresa = $this->Controlador_model->get($caja->empresa, 'empresa');
        $usuario = $this->Controlador_model->get($caja->usuario, 'usuario');
        $monedero = $this->db->where('caja', $this->caja)->get('monedero')->row();
        $diezcentimos = $monedero->diezcentimos * 0.10;
        $veintecentimos = $monedero->veintecentimos * 0.20;
        $cincuentacentimos = $monedero->cincuentacentimos * 0.50;
        $unsol = $monedero->unsol * 1;
        $dossoles = $monedero->dossoles * 2;
        $cincosoles = $monedero->cincosoles * 5;
        $diezsoles = $monedero->diezsoles * 10;
        $veintesoles = $monedero->veintesoles * 20;
        $cincuentasoles = $monedero->cincuentasoles * 50;
        $ciensoles = $monedero->ciensoles * 100;
        $doscientossoles = $monedero->doscientossoles * 200;
        $montototal = $diezcentimos + $veintecentimos + $cincuentacentimos + $unsol + $dossoles + $cincosoles + $diezsoles + $veintesoles + $cincuentasoles + $ciensoles + $doscientossoles;
        $nombre_impresora = $empresa->nombreimpresora;
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        # Mando un numero de respuesta para saber que se conecto correctamente.
        # Vamos a alinear al centro lo próximo que imprimamos
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        if ($empresa->tipo == 0) {
            $printer->text($empresa->nombre . "\n");
            $printer->text($empresa->razonsocial . "\n");
        } else {
            $printer->text($empresa->razonsocial . "\n");
        }
        $printer->text($empresa->departamento . ' ' . $empresa->provincia . ' ' . $empresa->distrito . "\n");
        $printer->text("RUC: " . $empresa->ruc . "\n");
        $printer->text("Telf: " . $empresa->telefono . "\n");
        /* Ahora vamos a imprimir un encabezado */
        $printer->text("CIERRRE CAJA DIARIO \n");
        $printer->text("------------------------------------------------" . "\n");
        #La fecha también
        $printer->text(strtoupper($caja->descripcion) . "\n");
        $printer->text("FECHA DE APERTURA: " . $caja->apertura . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("ENCARGADO: " . $usuario->nombre . "\n");
        $printer->text("SALDO INICIAL: " . $caja->saldoinicial . "\n");
        $printer->text("------------------------------------------------" . "\n");
        $printer->text("TIPO DE MONEDA                 CANTIDAD DE MONEDA\n");
        $printer->text("------------------------------------------------" . "\n");
        /* Ahora vamos a imprimir los productos. Alinear a la izquierda para la cantidad y el nombre */
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("0.10                                   " . $monedero->diezcentimos . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("0.20                                   " . $monedero->veintecentimos . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("0.50                                   " . $monedero->cincuentacentimos . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("1.00                                   " . $monedero->unsol . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("2.00                                   " . $monedero->dossoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("5.00                                   " . $monedero->cincosoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("10.00                                  " . $monedero->diezsoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("20.00                                  " . $monedero->veintesoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("50.00                                  " . $monedero->cincuentasoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("100.00                                 " . $monedero->ciensoles . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("200.00                                 " . $monedero->doscientossoles . "\n");
        /* Terminamos de imprimir los productos, ahora va el total */
        $printer->text("------------------------------------------------" . "\n");
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->text("TOTAL VENTA:                          " . number_format($montototal, 2) . "\n");
        /* Podemos poner también un pie de página */
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------------------" . "\n");
        $printer->text("PRODUCTOS VENDIDOS\n");
        $posales = $this->Controlador_model->resumenventa($this->caja);
        $totalventas = 0;
        foreach ($posales as $value) {
            $producto = $this->Controlador_model->get($value->producto, 'producto');
            $sumacantidad = $this->Controlador_model->ventaresumencantidad($caja->id, $value->producto);
            $sumasubtotal = $this->Controlador_model->ventaresumensubtotal($caja->id, $value->producto);
            $totalventas += $sumasubtotal->subtotal;
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text(ucwords(strtolower($producto->nombre)) . "\n");
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text($sumacantidad->cantidad . " UND\n");
        }
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------------------" . "\n");
        /* Podemos poner también un pie de página */
        $printer->text("EL MUNDO ES DE QUIEN SE ATREVE\n");
        /* Alimentamos el papel 3 veces */
        $printer->feed(3);
        /* Cortamos el papel. Si nuestra impresora no tiene soporte para ello, no generará ningún error */
        $printer->cut();
        /* Por medio de la impresora mandamos un pulso. Esto es útil cuando la tenemos conectada por ejemplo a un cajón */
        $printer->pulse();
        /* Para imprimir realmente, tenemos que "cerrar" la conexión con la impresora. Recuerda incluir esto al final de todos los archivos */
        $printer->close();
    }

    public function showcierre()
    {
        $monedero = $this->db->where('caja', $this->caja)->get('monedero')->row();
        $diezcentimos = $monedero->diezcentimos * 0.10;
        $veintecentimos = $monedero->veintecentimos * 0.20;
        $cincuentacentimos = $monedero->cincuentacentimos * 0.50;
        $unsol = $monedero->unsol * 1;
        $dossoles = $monedero->dossoles * 2;
        $cincosoles = $monedero->cincosoles * 5;
        $diezsoles = $monedero->diezsoles * 10;
        $veintesoles = $monedero->veintesoles * 20;
        $cincuentasoles = $monedero->cincuentasoles * 50;
        $ciensoles = $monedero->ciensoles * 100;
        $doscientossoles = $monedero->doscientossoles * 200;
        if ($monedero->status == '1') {
            $montototal = $monedero->montototal;
        } else {
            $montototal = $diezcentimos + $veintecentimos + $cincuentacentimos + $unsol + $dossoles + $cincosoles + $diezsoles + $veintesoles + $cincuentasoles + $ciensoles + $doscientossoles;
        }
        $data = array(
            'monedero' => $monedero,
            'montototal' => $montototal,
            'caja' => $caja = $this->Controlador_model->get($this->caja, 'caja'),
            'usuario' => $this->Controlador_model->get($caja->usuario, 'usuario'),
            'empresa' => $this->Controlador_model->get($caja->empresa, 'empresa'),
            'posales' => $this->Controlador_model->resumenventa($this->caja)
        );
        $this->load->view('imprimircierre', $data);
    }

    public function backup()
    {
        /*
    $this->Controlador_model->backup();
    mensaje_alerta('hecho', 'crear');
    */
        $CI = &get_instance();
        $CI->session->set_userdata('caja', NULL);
        redirect($this->url);
    }

    public function alertaStock()
    {
        $stocks = $this->Controlador_model->getAlertaStock();
        $html = "";
        $numeroStock = 0;
        foreach ($stocks as $stock) {
            $dataproducto = $this->Controlador_model->get($stock->idproducto, "producto");
            $html .= "<li class='list-group-item '>
      <h4 class='list-group-item-heading'>"
                . $stock->nombre . "</h4>" . "
      <p class='list-group-item-text'>Cantidad minima: " . $dataproducto->alertqt . "</p>
      <p class='list-group-item-text'>Cantidad actual: " . $stock->cantidad . "</p>
      </li>";
            $numeroStock++;
        }
        $caducidad = $this->db->where("fechacaducidad <>", "0000-00-00")->where("fechacaducidad <", date("Y-m-d"))->get("producto")->result();
        foreach ($caducidad as $value) {

            $hoy = new DateTime("NOW");
            $caducado = new DateTime($value->fechacaducidad);
            $direferencia = $hoy->diff($caducado);
            $html .= "
      <li class='list-group-item '>
      <h4 class='list-group-item-heading'>" . $value->nombre . "</h4>" . "<p class='list-group-item-text'> <span class='label label-danger'>CADUCADO HACE " . $direferencia->d . ($direferencia->d == 1 ? ' DIA' : ' DIAS') . "</span></p></li>";
            $numeroStock++;
        }

        echo json_encode(array('data' => $html, 'numeroStock' => $numeroStock));
    }


    public function ajax_agregarAdicionales($producto)
    {
        $dataProducto = $this->Controlador_model->get($producto, "producto");
        $columna = 0;
        //TODO: VARIANTES
        if ($dataProducto->variante == "1") {
            $columna += 1;
            $statusvariante = TRUE;
            $datavariantes = $this->Controlador_model->getVariante($producto);
            $htmlVariantes = "";
            foreach ($datavariantes->result() as $variante) {
                $htmlVariantes .= '
            <div style="display:inline-block">
              <label id="label-variante-' . $variante->id . '" for="input-variante-' . $variante->id . '" class="content-animalista" >
                <input class="variantes" type="hidden" id="variante-' . $variante->id . '" value="' . $variante->id . '">
                <input type="hidden" id="precioproducto-' . $variante->id . '"  value="' . $variante->precio . '">
                <input type="hidden" id="text-variante-' . $variante->id . '"  value="' . $variante->nombre . '">
                <input type="hidden" id="variante-cantidad-' . $variante->id . '"  value="' . $variante->cantidad . '">
                <div >
                <input onclick="seleccionarVariante(' . $variante->id . ')" class="radio" type="radio" id="input-variante-' . $variante->id . '" name="variante">
                </div>
                <div class="dataradio">
                  <div>' . $variante->nombre . '</div>
                  <div>' . $variante->precio . '</div>
                </div> 
              </label>
            </div> 
            ';
            }
        } else {
            $htmlVariantes = "";
            $statusvariante = 0;
        }
        //TODO: EXTRAS 
        $statusExtras =  $this->Controlador_model->queryCategoria($dataProducto->categoria);
        if ($statusExtras) {
            $dataextras = $this->Controlador_model->getExtras($dataProducto->categoria);
            $htmlExtras = "";
            $statusextra = TRUE;
            $columna += 1;
            foreach ($dataextras->result() as $value) {
                $htmlExtras .= '
            <div style="display: inline-block">
            <div class="main-extra" id="extra-' . $value->id . '" style="position:relative">
            <input type="hidden" class="ExtraIdenti" value="' . $value->id . '">
            <input type="hidden" id="PrecioExtra-' . $value->id . '" value="' . $value->precio . '">
            <input type="hidden" id="NombreExtra-' . $value->id . '" value="' . $value->nombre . '">
                <span id="sinextra-' . $value->id . '" class="sin-extra" onclick="sinextraope(' . $value->id . ')"><i class="fa fa-minus-circle"></i></span>
                <div class="main-extra__uno">
                    <input type="checkbox" onclick="seleccionarExtraChecked(' . $value->id . ')" class="check_extra" id="checkep_extra-' . $value->id . '">
                </div>
                <label class="main-extra__two" onclick="seleccionarExtra(' . $value->id . ')">
                    <span id="extraname-' . $value->id . '">' . $value->nombre . '</span>
                    <span id="extraprecio-' . $value->id . '">S/. ' . $value->precio . '</span>
                </label>
            </div>
            </div>
              ';
            }
        } else {
            $htmlExtras = "";
            $statusextra = FALSE;
        }
        //TODO: LOTES
        $empresa = $this->Controlador_model->get($this->empresa, "empresa");
        $loteAlmacen = $this->db->where("producto", $dataProducto->id)->where("almacen", $empresa->almacen)->where("lote IS NOT NULL")->get("stock");
        if ($dataProducto->status_lote == '1' and $loteAlmacen->num_rows() > 1) {
            $statuslote = TRUE;
            $htmlLotes = "";
            $columna += 1;
            foreach ($loteAlmacen->result() as $value) {
                $lote = $this->Controlador_model->get($value->lote, "lote");
                $htmlLotes .= '
          <div style="display:inline-block">
            <label id="label-lote-' . $lote->id . '" for="input-lote-' . $lote->id . '" class="content-animalista" >
            <input class="lotes" type="hidden" id="lote-' . $lote->id . '" value="' . $lote->id . '">
              <div>
              <input onclick="seleccionarLote(' . $lote->id . ')" class="radio" type="radio" id="input-lote-' . $lote->id . '" name="lote">
              </div>
              <div class="dataradio">
                <div><label style="display:block">' . $lote->lote . '</label><label style="display:block">' . $lote->vencimiento . '</label></div>
              </div> 
            </label>
          </div> 
          ';
            }
        } else {
            if ($dataProducto->status_lote == '1') {
                $dataLote = $loteAlmacen->row();
                $statuslote = 1;
                $htmlLotes = $dataLote->lote;
            } else {
                $statuslote = 0;
                $htmlLotes = "";
            }
        }

        $htmlBoton = '
    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
    <button class="btn btn-primary" id="btnAgregarVenta"  onclick="procesoAgregarVenta({idproducto : ' . $producto . ', statusvariante : ' . $statusvariante . ', statuslote : ' . $statuslote . '})">Agregar</button>';
        $totalcolumnas = 12 / ($columna == 0 ? 1 : $columna);
        echo json_encode([
            "variantes" => ["status" => $statusvariante, "html" => $htmlVariantes],
            "extras" => ["status" => $statusextra, "html" => $htmlExtras],
            "lotes" => ["status" => $statuslote, "html" => $htmlLotes, "totalLotes" => $loteAlmacen->num_rows()],
            "boton" => $htmlBoton,
            "totalcolumnas" => $totalcolumnas
        ]);
    }

    public function showcomprobanteSaved($url, $phone, $idventa)
    {
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $comprobante = $venta->serie . '|' . $venta->numero;
        $numerocomprobante = $venta->serie . '-' . $venta->numero;
        $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
        $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
        $url_base = 'archivos_xml_sunat/imgqr/';
        $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
        $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
        $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
        $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
        $params['level'] = 'H';
        $params['size'] = 5;
        $params['savename'] = $url_base . $numerocomprobante . '.png';
        $this->ciqrcode->generate($params);
        $data = array(
            'venta' => $venta,
            'empresa' => $empresa,
            'cliente' => $cliente,
            'codigohash' => $codigohash,
            'qrcode' => base_url() . $url_base . $numerocomprobante . '.png',
            'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
            'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
            'ingresos' => $this->Controlador_model->getDetalle($idventa, 'ingreso'),
            'ventadetalle' => $this->Controlador_model->comanda($idventa)
        );
        $this->load->view('imprimircomprobante', $data);
        $filename = $url . "/" . $venta->serie . "-" . $venta->numero . "-" . $cliente->documento . "-" . date('Y-m-d') . ".pdf";
        $html = $this->output->get_output();
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        // $this->dompdf->setPaper(array(0, 0, 100, 100));
        $this->dompdf->render();
        $output = $this->dompdf->output();
        file_put_contents($filename, $output);
        $this->sendCPToNumber($filename, $phone);
        //$this->switshtable();
        // $this->dompdf->stream($filename, array("Attachment"=>1));
    }

    public function sentTicketWA($phone, $venta)
    {
        $url = "CPETemp";
        if (!file_exists($url)) {
            mkdir($url, 0777, true);
            // var_dump("entro");
        }
        $this->showcomprobanteSaved($url, $phone, $venta);
    }

    public function crearPDF($idventa)
    {
        $url = "CPETemp";
        if (!file_exists($url)) {
            mkdir($url, 0777, true);
            // var_dump("entro");
        }
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
        $comprobante = $venta->serie . '|' . $venta->numero;
        $numerocomprobante = $venta->serie . '-' . $venta->numero;
        $tipo = $venta->tipoventa == 'FACTURA' ? 6 : 1;
        $tipocom = $venta->tipoventa == 'FACTURA' ? "01" : "03";
        $url_base = 'archivos_xml_sunat/imgqr/';
        $encriptado = password_hash($venta->numero, PASSWORD_BCRYPT);
        $encriptadohash = substr(str_replace("$2y$10$", "", $encriptado), 0, 27) . '=';
        $codigohash = $venta->hash ? $venta->hash : $encriptadohash;
        $params['data'] = $empresa->ruc . '|' . $tipocom . '|' . $comprobante . '|0.00|' . $venta->montototal . '|' . date('d/m/Y', strtotime($venta->created)) . '|' . $tipo . '|' . $cliente->documento . '|' . $codigohash . '|';
        $params['level'] = 'H';
        $params['size'] = 5;
        $params['savename'] = $url_base . $numerocomprobante . '.png';
        $this->ciqrcode->generate($params);
        $data = array(
            'venta' => $venta,
            'empresa' => $empresa,
            'cliente' => $cliente,
            'codigohash' => $codigohash,
            'qrcode' => base_url() . $url_base . $numerocomprobante . '.png',
            'mesa' => $this->Controlador_model->get($venta->mesa, 'mesa'),
            'usuario' => $this->Controlador_model->get($venta->usuario_creador, 'usuario'),
            'ingresos' => $this->Controlador_model->getDetalle($idventa, 'ingreso'),
            'ventadetalle' => $this->Controlador_model->comanda($idventa)
        );
        $this->load->view('imprimircomprobante', $data);
        $filename = $url . "/" . $venta->serie . "-" . $venta->numero . "-" . $cliente->documento . "-" . date('Y-m-d') . ".pdf";
        $html = $this->output->get_output();
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        // $this->dompdf->setPaper(array(0, 0, 100, 100));
        $this->dompdf->render();
        $output = $this->dompdf->output();
        file_put_contents($filename, $output);
        //$this->switshtable();
        // $this->dompdf->stream($filename, array("Attachment"=>1));

    }

    public function sendCPToNumber($uri, $phone)
    {
        $texto = "Hola, gracias por tu consumo. Puedes descargar tu Comprobante Electronico en el siguiente enlace! " . urlencode(base_url() . $uri);
        redirect("https://wa.me/51" . $phone . "?text=" . $texto);
    }

    public function deleteCPETemp()
    {
        $url = "CPETemp" . DIRECTORY_SEPARATOR;
        $this->rmdir_recursive($url);
    }

    public function rmdir_recursive($dir)
    {
        if (is_dir($dir)) {
            foreach (scandir($dir) as $file) {
                if ('.' === $file || '..' === $file) continue;
                if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
                else unlink("$dir/$file");
            }
            rmdir($dir);
        }
    }
    public function consulta_reniec()
    {
        consuta_reniec_helper();
    }

    public function load_CategriaSeleccionar()
    {
        $html = "";
        $categoria = $this->db->where('estado', '0')->get('productocategoria')->result();
        foreach ($categoria as $key => $category) {
            $html .= '<div class="contenedor">
                  <a class="tap" onclick="categoria(' . $category->id . ')">
                    <div class="card-wrap">
                      <div class="card" style="transform: rotateY(0deg) rotateX(0deg);">';
            if ($category->photo) {
                $html .= '<div class="card-bg" style="background-image: url(' . base_url() . 'files/productocategoria/' . $category->photothumb . '" alt="' . $category->nombre . ';);transform: translateX(0px) translateY(0px);"></div>';
            }
            $html .= '
      <div class="card-info"><h1>' . $category->nombre . '</h1>
      <p>' . ($category->descripcion != '' ? $category->descripcion : "S/D") . '</p>
      </div>
                      </div>
                    </div>
                  </a>
                </div>';
        }
        $html .= '<div class="contenedor">
      <a class="tap" onclick="productolibre()">
        <div class="card-wrap">';
        $html .= '<div class="card" style="transform: rotateY(0deg) rotateX(0deg);text-align: center;">
           <h1 style="text-align:center"><i class="fa fa-plus-circle fa-3x" aria-hidden="true"></i></h1>
           <p>Producto Libre</p>
            </div>
            </div>
            </a>
            </div>';
        echo $html;
    }

    public function load_CategriaSeleccionarAlquiler()
    {
        $html = "";
        $categoria = $this->db->where('estado', '0')->get('productocategoria')->result();
        foreach ($categoria as $key => $category) {
            $html .= '<div class="contenedor">
                  <a class="tap" onclick="categoriaAlquiler(' . $category->id . ')">
                    <div class="card-wrap">
                      <div class="card" style="transform: rotateY(0deg) rotateX(0deg);">';
            if ($category->photo) {
                $html .= '<div class="card-bg" style="background-image: url(' . base_url() . 'files/productocategoria/' . $category->photothumb . '" alt="' . $category->nombre . ';);transform: translateX(0px) translateY(0px);"></div>';
            }
            $html .= '
      <div class="card-info"><h1>' . $category->nombre . '</h1>
      <p>' . ($category->descripcion != '' ? $category->descripcion : "S/D") . '</p>
      </div>
                      </div>
                    </div>
                  </a>
                </div>';
        }
        $html .= '<div class="contenedor">
      <a class="tap" onclick="productolibre()">
        <div class="card-wrap">';
        $html .= '<div class="card" style="transform: rotateY(0deg) rotateX(0deg);text-align: center;">
           <h1 style="text-align:center"><i class="fa fa-plus-circle fa-3x" aria-hidden="true"></i></h1>
           <p>Producto Libre</p>
            </div>
            </div>
            </a>
            </div>';
        echo $html;
    }

    function ajax_TodosProductos()
    {
        $html = "";
        $productos = $this->Controlador_model->getAll('producto');
        $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
        foreach ($productos as $key => $value) {
            $EstadoProducto = FALSE;
            $datacategoria = $this->Controlador_model->get($value->categoria, "productocategoria");
            if ($value->estado == '1' or $value->categoria == 14) {
                //todo: Ignora los productos desactivados
                continue;
            }
            if ($value->tipo == '0') {
                //todo: El tipo de producto 0 es estandar
                $registro = $this->Controlador_model->get($this->caja, 'caja');
                if ($value->status_lote == '1') {
                    $totalLote = $this->Controlador_model->dataLotes($value->id, $dataEmpresa->almacen, $this->empresa);
                    if ($totalLote->num_rows() > 1) {
                        $existenciaStock = TRUE;
                    } else {
                        $dataproductolote = $totalLote->row();
                        if ($dataproductolote) {
                            $existenciaStock = TRUE;
                        } else {
                            $existenciaStock = FALSE;
                        }
                    }
                } else {
                    $existenciaStock = $this->db->where('empresa',  $this->empresa)->where('producto',  $value->id)->where('almacen', $dataEmpresa->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                }
                if ($existenciaStock) {
                    if ($value->status_lote == '1') {
                        $totalLote2 = $this->Controlador_model->dataLotes($value->id, $dataEmpresa->almacen, $this->empresa);
                        if ($totalLote2->num_rows() > 1) {
                            $stok_D = FALSE; //? PARA QUE NO SE BLOQUEE Y MUESTRE EL MODAL DONDE ESTAN TODOS SUS LOTES
                        } else {
                            $stok_D = $this->db->where('lote IS NOT NULL')->where('empresa', $registro->empresa)->where('almacen', $dataEmpresa->almacen)->where('producto', $value->id)->where("cantidad <=", 0)->get('stock')->row();
                        }
                    } else {
                        $stok_D = $this->db->where('empresa', $registro->empresa)->where('almacen', $dataEmpresa->almacen)->where('producto', $value->id)->where("cantidad <=", 0)->get('stock')->row();
                    }
                    if ($stok_D) {
                        $EstadoProducto = TRUE;
                    } else {
                        $EstadoProducto = FALSE;
                    }
                } else {
                    $EstadoProducto = TRUE;
                }
            } else if ($value->tipo == '2') {
                //todo: El tipo de producto 2 es combo
                $combo = $this->db->where('producto',  $value->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
                if ($combo) {
                    foreach ($combo as $key => $value2) {
                        //Si no cuenta con sufuciente STOCK el como saldra como agotado
                        $existenciaStockCombo = $this->db->where('producto',  $value2->item_id)->where('almacen', $dataEmpresa->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                        if ($existenciaStockCombo) {
                            $productoStock = $this->db->where('producto', $value2->item_id)->where('cantidad <', $value2->cantidad)->where('almacen', $dataEmpresa->almacen)->get('stock')->row();
                            if ($productoStock) {
                                $EstadoProducto = TRUE; //cuando $EstadoProducto es TRUE ara que el div/boton este desahabilitado
                                break;
                            } else {
                                $EstadoProducto = FALSE;
                            }
                        } else {
                            $EstadoProducto = TRUE;
                            break;
                        }
                    }
                } else {
                    //todo: si no tine que productos en el combo se desahanilita el producto es tipo combo
                    $EstadoProducto = TRUE;
                }
            } else {
                $EstadoProducto = FALSE;
            }
            $queryLotes = $this->Controlador_model->queryLotes($this->empresa, $value->id, $dataEmpresa->almacen);
            if ($value->variante == "1" or $datacategoria->estadoextras == "1" or ($value->status_lote == "1" and $queryLotes->num_rows() > 1)) {
                $evento = ' onclick="agregarAdicionales(' . $value->id . ')" ';
            } else {
                if ($value->status_lote == "1" and $queryLotes->num_rows() == 1) {
                    $dataLote = $queryLotes->row();
                    $lote = $dataLote ? $dataLote->lote : null;
                    $evento = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : ' . $lote . ' ,statuslote : ' . ($lote ? true : false) . '} ,\'\')"';
                } else {
                    $evento = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : null, statuslote: false} ,\'\')"';
                }
            }
            if ($value->variante == "1") {
                $dataMask = '<span class="label label-info" style="display:block">' . ($value->variante == TRUE ? "variante" : "") . '</span>';
            } else {
                $dataMask = '<div class="mask" style="color:#D6D6D6">' . number_format($value->precioventa, 2, '.', '') . ' Soles </div>';
            }

            if ($datacategoria->estadoextras == "1") {
                $textextras = "<label class='label label-success' style='display:block'>Extras</label>";
            } else {
                $textextras = "";
            }

            if ($value->status_lote == "1") {
                $textLotes = "<label class='label label-default' style='display:block'>Lotes</label>";
            } else {
                $textLotes = "";
            }

            if ($EstadoProducto) {
                $disabled = "disabled";
            } else {
                $disabled = "";
            }

            $html .= '
      <div class="col-sm-2 col-xs-4 text-center" id="content-producto-' . $value->id . '">

      <input type="hidden" id="tipomedida-' . $value->id . '" value="UND"/>
      <input type="hidden" id="cantidad-tm' . $value->id . '" value="1"/>
      <input type="hidden" id="nombre-producto-' . $value->id . '" value="' . $value->nombre . '"/>
      <input type="hidden" id="precio-producto-' . $value->id . '" value="' . $value->precioventa . '"/>

      <button ' . $disabled . ' 
      style="display:inline-block; border-width:0px; position:relative" href="javascript:void(0)" 
      class="addPct" 
      id="boton-product-' . $value->id . '"  ' . $evento . ' >';

            if ($value->tipo == 2) {
                $EstiloCajaCombo = "border: 0px solid #00fcb8; box-shadow:0px 0px 30px 5px #00fcb8";
            } else {
                $EstiloCajaCombo = '';
            }

            if ($EstadoProducto) {
                $html .= '<label id="Agotado' . $value->id . '" class="agotado" >AGOTADO</label>';
            }

            if ($EstadoProducto) {
                $EstadoProductoData = "opacar_div";
            } else {
                $EstadoProductoData = "";
            }

            //TODO: Esta clase da color cuando seleccina algo .productoIsSelected
            $html .= '
          <div class="product ' . $value->color . ' flat-box  ' . $EstadoProductoData . ' " style="' . $EstiloCajaCombo . ' "     id="ProductoLista' . $value->id . '">
            <h3 id="proname">
            ' . $value->nombre . '
            ' . $dataMask . '
            ' . $textLotes . '
            ' . $textextras . '
            </h3>';

            if ($value->photo) {
                $html .= '<img src="' . base_url() . 'files/products/' . $value->photothumb . '" alt="' . $value->nombre . '">';
            }

            $botonStock = '<button class="btn btn-block btn-info btn-sm" id="verStock-' . $value->id . '-0" onclick="verstockactual(' . $value->id . ', 0)" style="padding:0px">STOCK <i class="fa fa-search"></i></button>';

            if ($value->tipo == 02) {
                $html .= '<label style="font-size: 20px; font-weight:900; color:#00fcb8">COMBO</label>';
            }
            $html .= '
      </div>
      </button>
      ' . ($value->tipo == "0" ? $botonStock : "") . '
      </div>';
        }
        echo $html;
    }

    public function ajax_productos_categoria()
    {
        $html = "";
        $productos = $this->db->where("estado", "0")->where('categoria', $this->input->post('categoria'))->get("producto")->result();
        $dataEmpresa = $this->Controlador_model->get($this->empresa, 'empresa');
        foreach ($productos as $key => $value) {
            $EstadoProducto = FALSE;
            $datacategoria = $this->Controlador_model->get($value->categoria, "productocategoria");
            if ($value->tipo == '0') {
                //todo: El tipo estandar
                $registro = $this->Controlador_model->get($this->caja, 'caja');
                if ($value->status_lote == '1') {
                    $totalLote = $this->Controlador_model->dataLotes($value->id, $dataEmpresa->almacen, $this->empresa);
                    if ($totalLote->num_rows() > 1) {
                        $existenciaStock = TRUE;
                    } else {
                        $dataproductolote = $totalLote->row();
                        if ($dataproductolote) {
                            $existenciaStock = TRUE;
                        } else {
                            $existenciaStock = FALSE;
                        }
                    }
                } else {
                    $existenciaStock = $this->db->where('empresa',  $this->empresa)->where('producto',  $value->id)->where('almacen', $dataEmpresa->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                }

                if ($existenciaStock) {
                    if ($value->status_lote == '1') {
                        $totalLote2 = $this->Controlador_model->dataLotes($value->id, $dataEmpresa->almacen, $this->empresa);
                        if ($totalLote2->num_rows() > 1) {
                            $stok_D = FALSE; //? PARA QUE NO SE BLOQUEE Y MUESTRE EL MODAL DONDE ESTAN TODOS SUS LOTES
                        } else {
                            $stok_D = $this->db->where('lote IS NOT NULL')->where('empresa', $registro->empresa)->where('almacen', $dataEmpresa->almacen)->where('producto', $value->id)->where("cantidad <=", 0)->get('stock')->row();
                        }
                    } else {
                        $stok_D = $this->db->where('empresa', $registro->empresa)->where('almacen', $dataEmpresa->almacen)->where('producto', $value->id)->where("cantidad <=", 0)->get('stock')->row();
                    }
                    if ($stok_D) {
                        $EstadoProducto = TRUE; //? SE BLOQUEA EL PRODUCTO
                    } else {
                        $EstadoProducto = FALSE; //? NO SE BLOQUEA
                    }
                } else {
                    $EstadoProducto = TRUE; //? SE BLOQUEA EL PRODUCTO
                }
            } else if ($value->tipo == '2') {
                //todo: El tipo de producto 2 es combo
                $combo = $this->db->where('producto',  $value->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
                if ($combo) {
                    foreach ($combo as $key => $value2) {
                        $ProductoCombo = $this->Controlador_model->get($value2->item_id, 'combo');
                        //Si no cuenta con sufuciente STOCK el como saldra como agotado
                        $existenciaStockCombo = $this->db->where('producto',  $value2->item_id)->where('almacen', $dataEmpresa->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                        if ($existenciaStockCombo) {
                            $productoStock = $this->db->where('producto', $value2->item_id)->where('almacen', $dataEmpresa->almacen)->where('cantidad <', $value2->cantidad)->get('stock')->row();
                            if ($productoStock) {
                                $EstadoProducto = TRUE; //cuando $EstadoProducto es TRUE ara que el div/boton este desahabilitado
                                break;
                            } else {
                                $EstadoProducto = FALSE;
                            }
                        } else {
                            $EstadoProducto = TRUE;
                            break;
                        }
                    }
                } else {
                    //todo: si no tine que productos en el combo se desahanilita el producto es tipo combo
                    $EstadoProducto = TRUE;
                }
            } else {
                $EstadoProducto = FALSE;
            }

            $queryLotes = $this->Controlador_model->queryLotes($this->empresa, $value->id, $dataEmpresa->almacen);
            if ($value->variante == "1" or $datacategoria->estadoextras == "1" or ($value->status_lote == "1" and $queryLotes->num_rows() > 1)) {
                $evento = ' onclick="agregarAdicionales(' . $value->id . ')" ';
            } else {
                if ($value->status_lote == "1" and $queryLotes->num_rows() == 1) {
                    $dataLote = $queryLotes->row();
                    $lote = $dataLote ? $dataLote->lote : null;
                    $statuslote = $lote ? true : false;
                    $evento = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : ' . $lote . ' ,statuslote : ' . $statuslote . '} ,\'\')"';
                } else {
                    $evento = 'onclick="agregaarventa(' . $value->id . ', ' . $value->precioventa . ',{statusvariante : false, lote : null, statuslote: false} ,\'\')"';
                }
            }

            if ($value->variante == TRUE or $value->variante == 1 or $value->variante == "1") {
                $dataMask = '<label class="label label-info" style="display:block">' . ($value->variante == TRUE ? "variante" : "") . '</label>';
            } else {
                $dataMask = '<div class="mask" style="color:#D6D6D6">' . number_format($value->precioventa, 2, '.', '') . ' Soles </div>';
            }

            if ($datacategoria->estadoextras == "1") {
                $textextras = "<label class='label label-success' style='display:block'>Extras</label>";
            } else {
                $textextras = "";
            }

            if ($value->status_lote == "1") {
                $textLotes = "<label class='label label-default' style='display:block'>Lotes</label>";
            } else {
                $textLotes = "";
            }


            if ($EstadoProducto) {
                $disabled = "disabled";
            } else {
                $disabled = "";
            }

            $html .= '
      <div class="col-sm-2 col-xs-4 text-center" id="content-producto-' . $value->id . '">
      <input type="hidden" id="tipomedida-' . $value->id . '" value="UND"/>
      <input type="hidden" id="cantidad-tm' . $value->id . '" value="1"/>
      <input type="hidden" id="nombre-producto-' . $value->id . '" value="' . $value->nombre . '"/>
      <input type="hidden" id="precio-producto-' . $value->id . '" value="' . $value->precioventa . '"/>

      <button ' . $disabled . ' 
      style="display:inline-block; border-width:0px; position:relative" href="javascript:void(0)" 
      class="addPct" 
      id="boton-product-' . $value->id . '"  ' . $evento . ' >';

            if ($value->tipo == 2) {
                $EstiloCajaCombo = "border: 0px solid #00fcb8; box-shadow:0px 0px 30px 5px #00fcb8";
            } else {
                $EstiloCajaCombo = '';
            }
            if ($EstadoProducto) {
                $html .= '<label id="Agotado' . $value->id . '" class="agotado"  >AGOTADO</label>';
            }

            if ($EstadoProducto) {
                $EstadoProductoData = "opacar_div";
            } else {
                $EstadoProductoData = "";
            }


            //TODO: Esta clase da color cuando seleccina algo .productoIsSelected
            $html .= '
          <div class="product ' . $value->color . ' flat-box  ' . $EstadoProductoData . '" style="' . $EstiloCajaCombo . '" id="ProductoLista' . $value->id . '">
            <h3 id="proname">
            ' . $value->nombre . '
            ' . $dataMask . '
            ' . $textLotes . '
            ' . $textextras . '
            
            </h3>
            ';

            if ($value->photo) {
                $html .= '<img src="' . base_url() . 'files/products/' . $value->photothumb . '" alt="' . $value->nombre . '">';
            }
            if ($value->tipo == 02) {
                $html .= '<label style="font-size: 20px; font-weight:900; color:#00fcb8">COMBO</label>';
            }

            $botonStock = '<button class="btn btn-block btn-info btn-sm" id="verStock-' . $value->id . '-1" onclick="verstockactual(' . $value->id . ', 1)" style="padding:0px">STOCK <i class="fa fa-search"></i></button>';

            $html .= '
      </div>
      </button>
      ' . ($value->tipo == '0' ? $botonStock : "") . '
      </div>';
        }
        echo $html;
    }

    function ajax_total_ventas()
    {
        $query = $this->db->where("estado", "0")->where("caja", $this->caja)->where("usuario_creador", $this->usuario)->get("venta")->result();
        $data = [];

        if ($query) {
            foreach ($query as $value) {
                $data[] = $value->id;
            }
        }

        echo json_encode($data);
    }

    function ajax_newventa()
    {
        $query = $this->db->where('id', $this->empresa)->get('empresa')->row();
        $data['empresa'] = $this->empresa;
        $data['cliente'] = 1;
        $data['tipozona'] = $this->input->post("tipozona");
        $data['usuario_creador'] = $this->usuario;
        $data['hora'] = date("H:i:s");
        $data['created'] = date("Y-m-d");
        $data['caja'] = $this->caja;
        $data['mesa'] = null;
        $data['tipoventa'] = $query->tipoventa;
        $insert = $this->Controlador_model->save('venta', $data);
        $ventainsertada = $this->Controlador_model->get($insert, 'venta');
        $cliente = $this->Controlador_model->get($ventainsertada->cliente, 'cliente');
        $querynNum =  $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $this->empresa);
        if ($insert) {
            echo json_encode(
                [
                    'idventa' => $insert,
                    'totalventa' => $querynNum->num_rows(),
                    'cliente' => $ventainsertada->cliente,
                    'clientes' => $cliente->documento . " " . $cliente->nombre . " " . $cliente->apellido, // ,

                ]
            );
        }
    }

    function ajax_ventasReload()
    {
        $query =  $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $this->empresa);
        //$query = $this->db->where('usuario_creador', $this->usuario)->where('empresa', $this->empresa)->where('estado', '0')->where('formapago', 'CONTADO')->get("venta");
        $html = "";
        $idselec = "";
        if ($query) {
            $total = 0;
            foreach ($query->result() as $key => $value) {
                if ($key == 0) {
                    $idselec = $value->id;
                }
                $html .= ' <button style="border-top:0px; border-left:0px; border-right:0px" class="categories ' . ($key == 0 ? "selectedGat" : "") . ' " id="venta_' . $value->id . '" onclick="traerpedidosventa(' . $value->id . ')">
            <input value="' . $value->id . '" type="hidden">
            VENTA ' . ($key + 1) . '
            </button> ';
            }
        }
        echo json_encode(['datahtml' => $html, 'idselect' => $idselec, "dataquery" => $query]);
    }

    function ajax_ProcesoELiminarVenta()
    {
        $delete = $this->Controlador_model->delete_by_id($this->input->post("idventa"), "venta");
        if ($delete) {
            echo json_encode(["status" => TRUE]);
        }
    }

    function ajax_verif_stock()
    {
        $queryventa = $this->Controlador_model->estadoVenta($this->input->post("idventa"), $this->empresa, "0");
        if ($queryventa) {
            $productos = $this->Controlador_model->getVentaDetalle($this->input->post("idventa"));
            $dataenviar = [];
            $empresa = $this->Controlador_model->get($this->empresa, "empresa");
            foreach ($productos as $key => $value) {
                if ($value->tipo == '0') {
                    $EstadoProducto = FALSE;
                    $idproducto = $value->producto;
                    $cantidadproducto = $value->variante ?  ($value->cantidadvariante * $value->cantidad) : $value->cantidad;
                    $cantidadTotalVerif = $cantidadproducto;
                    $producto = $this->Controlador_model->get($idproducto, 'producto');
                    if ($producto->tipo == '0') {
                        //todo: El tipo de producto 0 es estandar
                        $registro = $this->Controlador_model->get($this->caja, 'caja');
                        $existenciaStock = $this->Controlador_model->existenciaStock($producto->id, $empresa->almacen, $value->lote, $cantidadTotalVerif, $this->empresa); //todo: verificamos si el producto esta registra en stock
                        if ($existenciaStock) {
                            $EstadoProducto = TRUE;
                        } else {
                            $EstadoProducto = FALSE;
                        }
                    } else if ($producto->tipo == '2') {
                        //todo: El tipo de producto 2 es combo
                        $combo = $this->db->where('producto', $producto->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
                        if ($combo) {
                            foreach ($combo as $key => $value2) {
                                $ProductoCombo = $this->Controlador_model->get($value2->item_id, 'combo');
                                //Si no cuenta con sufuciente STOCK el como saldra como agotado
                                $existenciaStockCombo = $this->db->where('almacen', $empresa->almacen)->where('empresa', $this->empresa)->where('producto',  $value2->item_id)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                                if ($existenciaStockCombo) {
                                    $productoStock = $this->db->where('almacen', $empresa->almacen)->where('producto', $value2->item_id)->where('cantidad <', $value2->cantidad)->get('stock')->row();
                                    if ($productoStock) {
                                        $EstadoProducto = TRUE; //cuando $EstadoProducto es TRUE ara que el div/boton este desahabilitado
                                        break;
                                    } else {
                                        $EstadoProducto = FALSE;
                                    }
                                } else {
                                    $EstadoProducto = TRUE;
                                    break;
                                }
                            }
                        } else {
                            //todo: si no tine que productos en el combo se desahanilita el producto es tipo combo
                            $EstadoProducto = TRUE;
                        }
                    } else {
                        $EstadoProducto = FALSE;
                    }
                    $dataLote = $this->Controlador_model->get($value->lote, "lote");
                    if ($EstadoProducto == TRUE) {
                        $totalstock = $this->totalStock($idproducto, 0, $value->lote);
                        $dataenviar[] = ["idproducto" => $idproducto, "totalstock" => $totalstock, "idventadetalle" => $value->id, "nombrelote" => ($dataLote ? $dataLote->lote : "")];
                    }
                }
            }
            echo json_encode(["dataenviar" => $dataenviar, "venta" => ["status" => TRUE]]);
        } else {
            $dataVenta = $this->db->where("id", $this->input->post("idventa"))->where("estado", "1")->get("venta")->row();
            if ($dataVenta) {
                $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
                $hf_procesado = new DateTime($dataVenta->hf_procesado);
                $time = $hf_procesado->format("g:i:s a");
                $dataJSON = [
                    "venta" => ["status" => FALSE, "msg" => "La venta ya fue procesado por el usuario " . $dataUsuario->usuario . ". hora " . $time . ""]
                ];
            } else {
                $dataJSON = [
                    "venta" => ["status" => FALSE, "msg" => "La venta fue eliminada :( "]
                ];
            }
            echo json_encode($dataJSON);
        }
    }


    public function totalStock($idproducto, $cantidadvariante, $lote)
    {
        //? contamos para cuantas selecciones de ese producto le resta
        $queryproducto = $this->Controlador_model->get($idproducto, "producto");
        $dataempresa = $this->Controlador_model->get($this->empresa, 'empresa');
        $cantidadRestante = [];

        if ($queryproducto->tipo == '0') {
            //todo: El tipo de producto 0 es estandar
            $registro = $this->Controlador_model->get($this->caja, 'caja');
            $stockproducto = $this->Controlador_model->stockproducto($queryproducto->id, $registro->empresa, $dataempresa->almacen, $lote); //todo: verificamos si el producto esta registra en stock
            if ($stockproducto) {
                $cantidadRestante[] =  $cantidadvariante > 0 ? $stockproducto->cantidad / $cantidadvariante : $stockproducto->cantidad;
            } else {
                $cantidadRestante[] = 0;
            }
        } else if ($queryproducto->tipo == '2') {
            //todo: El tipo de producto 2 es combo
            $combo = $this->db->where('producto',  $queryproducto->id)->get('combo')->result(); //todo: verificamos si el tiene registro en la tabal combo
            if ($combo) {
                foreach ($combo as $key => $value2) {
                    $existenciaStockCombo = $this->db->where('producto',  $value2->item_id)->where("almacen", $dataempresa->almacen)->get('stock')->row(); //todo: verificamos si el producto esta registra en stock
                    if ($existenciaStockCombo) {
                        $verificarCantidad = $this->db->where('producto',  $value2->item_id)->where('cantidad >=', $value2->cantidad)->where('almacen', $dataempresa->almacen)->get('stock')->row();
                        if ($verificarCantidad) {
                            $cantidadTotalStock =  $verificarCantidad->cantidad;
                            $cantidadCombo = $value2->cantidad;
                            $totalSelecciones = ($cantidadTotalStock /  $cantidadCombo);
                            $cantidadRestante[] = $totalSelecciones;
                        } else {
                            $cantidadRestante[] = 0;
                            break;
                        }
                    } else {
                        $cantidadRestante[] = 0;
                    }
                }
            } else {
                $cantidadRestante[] = 0;
            }
        } else {
            $cantidadRestante[] = "Varios";
        }

        return min($cantidadRestante);
    }

    function ajax_EnviarPedido()
    {
        $idventa = $this->input->post("idventa");
        $dataventa = $this->db->where("id", $idventa)->where("estado", "0")->get("venta")->row();
        if ($dataventa) {
            $dataproductos = $this->Controlador_model->getVentaDetalle($idventa);
            $data = "";
            $CantidadItem = 0;
            $DeudaTotal = 0;
            foreach ($dataproductos as $value) {
                $cantidadDescontar = $value->variante ? ($value->cantidadvariante * $value->cantidad) : $value->cantidad;
                $CantidadItem += $cantidadDescontar;
                $DeudaTotal += $value->subtotal;
                if ($value->tipo == '0') {
                    $this->ajax_descontar_stock($value->producto, $cantidadDescontar, $value->lote);
                }
            }
            $this->Actualiar_venta($CantidadItem, $DeudaTotal, $idventa);
            //? actualizar datos de la mesa
            $updateMesa = ["estado" => "0", "totalalquiler" => 0.00, "time" => "00:00:00", "checked" => "0000-00-00 00:00:00", "open_by" => "0"];
            $this->Controlador_model->update(["id" => $dataventa->mesa], $updateMesa, "mesa");
            echo json_encode(["proceso" => ["status" => TRUE]]);
        } else {
            $dataVenta = $this->db->where("id", $idventa)->where("estado", "1")->get("venta")->row();
            if ($dataVenta) {
                $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
                $hf_procesado = new DateTime($dataVenta->hf_procesado);
                $time = $hf_procesado->format("g:i:s a");
                $dataJSON = [
                    "proceso" => ["status" => FALSE, "msg" => "La venta ya fue procesado por el usuario " . $dataUsuario->usuario . ". hora " . $time . ""]
                ];
            } else {
                $dataJSON = [
                    "proceso" => ["status" => FALSE, "msg" => "La venta fue eliminada :( "]
                ];
            }
            echo json_encode($dataJSON);
        }
    }

    public function ajax_descontar_stock($idproducto, $cantidad, $lote)
    {
        $dataEmpresa = $this->Controlador_model->get($this->empresa, 'empresa');
        //todo: hacemos el descuento del stock
        $producto = $this->Controlador_model->get($idproducto, 'producto');
        if ($producto->tipo == 0) {
            //VALIDAR SI EL DESCUENTO ES POR LOTE
            $stock = $this->Controlador_model->getStockProceso($producto->id, $dataEmpresa->almacen, $lote, $this->empresa);
            $updateStock = [
                'cantidad' => ($stock->cantidad - $cantidad)
            ];
            $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
        } else if ($producto->tipo == 2) {
            $combo = $this->Controlador_model->ProductoCombo($producto->id);
            foreach ($combo as $value) {
                $stock = $this->Controlador_model->getStockProceso($value->item_id, $dataEmpresa->almacen, $lote, $this->empresa);
                $updateStock = [
                    'cantidad' => $stock->cantidad - ($value->cantidad * $cantidad)
                ];
                $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
            }
        } else {
            // el producto no maneja el stock
        }
    }

    function Actualiar_venta($CantidadItem, $DeudaTotal, $idventa)
    {
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $dataVenta = [
            'usuario_proceso' => $this->usuario,
            'montototal' => $venta->montototal + $DeudaTotal,
            'montoactual' => $venta->montoactual + $DeudaTotal,
            'totalitems' => $venta->totalitems + $CantidadItem,
            'atender' => '1',
            'sound' => '1',
            'created' => date("Y-m-d")
        ];
        $updateVenta = $this->Controlador_model->update(['id' => $venta->id],  $dataVenta, 'venta');
    }

    function ajax_stockactual()
    {
        $idproducto = $this->input->post("idproducto");
        $almacenes = $this->Controlador_model->getAll('almacen');
        $html = '
    <div class="row">
     <div class="col-lg-12">
     <table class="table table-bordered">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Almacen</th>
            <th>Cantidad</th>
          </tr>
        </thead>
     ';
        foreach ($almacenes as $value) {
            $dataProducto = $this->Controlador_model->get($idproducto, "producto");
            $dataStock = $this->Controlador_model->stockAlmacen($idproducto, $value->id, $this->empresa);
            $cantidadStock =  $dataStock->cantidad == "" ? 0 : $dataStock->cantidad;
            $html .= '
          <tbody>
            <tr class="' . ($cantidadStock == 0 ? "danger" : "success") . '">
              <td>' . $dataProducto->nombre . '</td>
              <td>' . $value->nombre . '</td>
              <td style="text-align:right" >' . $cantidadStock . '</td>
            </tr>
          </tbody>';
        }
        $html .= '
      </table>
     </div>
    </div>';

        echo json_encode(['datahtml' => $html]);
    }

    function ajax_alertComprobantes()
    {
        $queryCPBoletas = $this->Controlador_model->queryCPBoletas();
        $queryCPFacturas = $this->Controlador_model->queryCPFacturas();
        $html = '<div class="col-lg-12">';
        $totalBoletas = 0;
        if ($queryCPBoletas->num_rows() > 0) {

            $html .= '
      <div style="display:flex; justify-content:space-between; align-items:center">
      <h5>BOLETAS</h5>
      <h5 class="badgetotalesCP" id="subtotalCPBoletas"></h5>
      </div>
      <ul class="list-group">
      ';
            foreach ($queryCPBoletas->result() as $value) {
                $totalBoletas += $value->totalfecha;
                $html .= '
        <li class="list-group-item" style="border:none; padding-top:5px; padding-bottom:5px; padding-left:25px; padding-right:0px">
          ' . $value->created . '
          <span class="badge badge-danger" style="background:#777">' . $value->totalfecha . '</span>
        </li>';
            }
            $html .= '</ul>';
        }
        $totalFacturas = 0;
        if ($queryCPFacturas->num_rows() > 0) {
            $html .= '
      <div style="display:flex; justify-content:space-between; align-items:center">
      <h5>FACTURAS</h5>
      <h5 class="badgetotalesCP"  id="subtotalCPFacturas"></h5>
      </div>
      <ul class="list-group">
      ';
            foreach ($queryCPFacturas->result() as $valueF) {
                $totalFacturas += $valueF->cantidadFactura;
                $html .= '
        <li class="list-group-item" style="border:none; padding-top:5px; padding-bottom:5px; padding-left:25px; padding-right:0px">
          ' . $valueF->created . '
          <span class="badge badge-danger" style="background:#777">' . $valueF->cantidadFactura . '</span>
        </li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div';
        $totalCP = $totalBoletas + $totalFacturas;
        echo json_encode(["dataCP" => $html, "subtotalBoletas" => $totalBoletas, "subtotalFacturas" => $totalFacturas, "totalCP" => $totalCP]);
    }

    private function ventaDetalleMesa($idventa)
    {
        $dataVenta = $this->Controlador_model->get($idventa, "venta");
        $queryVentaDetalle = $this->db->where("tipo", "1")->where("venta", $idventa)->where("mesa", $dataVenta->mesa)->get("ventadetalle")->row();
        if ($queryVentaDetalle) {
            //? Actualizacion
            $dataMesa = $this->Controlador_model->get($queryVentaDetalle->mesa, "mesa");
            $deudaAlquilerUpdate = $this->Controlador_model->calcularmontoalquiler($dataMesa) == 0 ? "0.00" : $this->Controlador_model->calcularmontoalquiler($dataMesa);
            $tiempoTranscurridoUpdate =  $this->Controlador_model->calculartiempo($dataMesa->time);
            $ventaDetalleUpdate = [
                "tiempo_mesa" => $tiempoTranscurridoUpdate,
                "precio" => $deudaAlquilerUpdate,
                "subtotal" => $deudaAlquilerUpdate
            ];
            $this->Controlador_model->update(["id" => $queryVentaDetalle->id], $ventaDetalleUpdate, "ventadetalle");
            return "Actualizacion";
        } else {
            //? Registro
            $dataMesaActualizado = $this->Controlador_model->get($dataVenta->mesa, 'mesa');
            $tiempoTranscurrido =  $this->Controlador_model->calculartiempo($dataMesaActualizado->time);
            $deudaAlquiler = $this->Controlador_model->calcularmontoalquiler($dataMesaActualizado) == 0 ? "0.00" : $this->Controlador_model->calcularmontoalquiler($dataMesaActualizado);
            $dataVentaDetalle = [
                "venta" => $dataVenta->id,
                "tipo" => "1",
                "mesa" => $dataVenta->mesa,
                "nombre" => "ALQUILER DE " . $dataMesaActualizado->nombre,
                "tiempo_mesa" => $tiempoTranscurrido,
                "precio" => $deudaAlquiler,
                "cantidad" => 1,
                "subtotal" => $deudaAlquiler,
            ];
            $register = $this->Controlador_model->save("ventadetalle", $dataVentaDetalle);
            if($register){
                return "se registro";
            }else{
                return "No se pudo registrar";
            }
        }
    }

    function ajax_crearpedido()
    {
        $idmesa = $this->input->post("idmesa");
        $datas = $this->db->where('mesa', $idmesa)->where('estado', '0')->get('venta')->row();
        $dataEmprea = $this->Controlador_model->get($this->empresa, "empresa");
        if ($datas) {
            $dataCliente = $this->Controlador_model->get($datas->cliente, "cliente");
            $dataMesa = $this->Controlador_model->get($idmesa, "mesa");
            $datas->textcliente = $dataCliente->documento . " | " . $dataCliente->nombre . " | " . $dataCliente->apellido;
            //? Actualizamos los datos de la tabla ventadetalle con el registro de tipo = '1'
            $repuestaMesa = $this->ventaDetalleMesa($datas->id);
            $dataHtml = $this->dataVentaDetalle($datas->id);
            echo json_encode(["ocupado" => ["status" => TRUE, "dataventa" =>  $datas, "datamesa" => $dataMesa, "respuestamesa" => $repuestaMesa, "dataHtml" =>  $dataHtml]]);
        } else {
            $mesa = $this->Controlador_model->get($idmesa, 'mesa');
            //? Registrar la venta
            $data['cliente'] = 1;
            $data['empresa'] = $this->empresa;
            $data['usuario_creador'] = $this->usuario;
            $data['caja'] = $this->caja;
            $data['mesa'] = $idmesa;
            $data['hora'] = date("H:i:s");
            $data['created'] = date("Y-m-d");
            $data['tipoventa'] = $dataEmprea->tipoventa;
            $idventa = $this->Controlador_model->save('venta', $data);
            //? actualizar datos de la mesa
            $dataMesa['estado'] = '1';
            $dataMesa['time'] = date("Y-m-d H:i:s");
            $dataMesa['open_by'] = $this->usuario;
            $dataCliente2 = $this->Controlador_model->get(1, "cliente");
            $data['textcliente'] = $dataCliente2->documento . " | " . $dataCliente2->nombre . " | " . $dataCliente2->apellido;
            $this->Controlador_model->update(array('id' => $idmesa), $dataMesa, 'mesa');
            $respuestaMesa = $this->ventaDetalleMesa($idventa);
            $dataHtml = $this->dataVentaDetalle($idventa);
            echo json_encode(["ocupado" => ["status" => FALSE, "dataventa" => $data, "datamesa" => $mesa, "idventaseleccionada" => $idventa, "respuestamesa" => $respuestaMesa, "dataHtml" => $dataHtml]]);
        }
    }

    function ajax_datosDeMesa()
    {
        $dataVenta = $this->Controlador_model->get($this->input->post("idventa"), "venta");
        $dataCliente = $this->Controlador_model->get($dataVenta->cliente, "cliente");
        $dataVenta->textcliente = $dataCliente->documento . " | " . $dataCliente->nombre . " " . $dataCliente->apellido;
        $this->ventaDetalleMesa($dataVenta->id);
        $dataHtml = $this->dataVentaDetalle($this->input->post("idventa"));
        echo json_encode(["dataventa" => ["status" => TRUE, "venta" => $dataVenta, 'dataHtml' => $dataHtml]]);
    }

    private function dataMesas()
    {
        $zonasa = $this->Controlador_model->getZonasEmpresa($this->empresa);
        $empresa = $this->Controlador_model->get($this->empresa, "empresa");
        $dataHtml = '';
        $dataHtml .= "<ul class='nav nav-tabs tabs'>";
        foreach ($zonasa as $key => $value) {
            $active = $key == 0 ? 'active' : '';
            $dataHtml .= "
                <li class='$active  tab' style='margin:inherit;'>
                    <a href='#$value->nombre ' data-toggle='tab' aria-expanded='false'>
                        <span class='visible-xs'><i class='$value->icono'></i></span>
                        <span class='user-name hidden-xs'>$value->nombre</span>
                    </a>
                </li>";
        }
        $dataHtml .= '</ul>';
        $dataHtml .= '<div class="tab-content" style="padding:0px">';
        $dataHtml .= '<div class="row" style="margin:17px" id="msjmesas">
            <div class="col-xs-12">
                <div class="input-group text-center">
                    <div class="input-group-btn">
                        <label class="btn btn-success btn-sm" style="padding:0px 5px; font-size:14px;border-top-left-radius: 8px; border-bottom-left-radius: 8px;"><small>Libre</small></label>
                        <label class="btn btn-danger  btn-sm" style="padding:0px 5px; font-size:14px;border-top-right-radius: 8px; border-bottom-right-radius: 8px;"><small>Ocupado</small></label>
                    </div>
                </div>
            </div>
            </div>';
        foreach ($zonasa as $key2 => $value) {
            $active2 = $key2 == 0 ? 'active' : '';
            $dataHtml .= '
               <div class="tab-pane  ' . $active2 . '   dale" id="' . $value->nombre . '" style="padding:0px; text-align:center">
                    <div style="display:flex; justify-content:center; align-items:center;">';
            $mesasas = $this->Controlador_model->zonamesas($value->id, $empresa->id);
            foreach ($mesasas as $key => $table) {
                if ($table->estado == 0) {
                    $dataHtml .= '
                        <div id="content-boton-mesa-'.$table->id.'" style="position:relative; margin:10px">
                        <button class="btn btn-success waves-effect" id="botonMesa-'.$table->id.'" m-b-5" onclick="crearpedido(' . $table->id . ')">
                            <img style="width:100px" src="' . base_url() . RECURSOS . 'img/mesaLibre'.rand(1,5).'.svg">
                            <h4 class="titlemesa">' . $table->nombre . ' </h4>
                        </button>
                        </div>';
                } else {
                    $venta = $this->db->where('mesa', $table->id)->where('estado', '0')->get('venta')->row();
                    $totalVentaDetalle = $this->Controlador_model->totalVentaDetalle($venta->id);
                    $usuario = $this->Controlador_model->get($table->open_by, 'usuario');
                    $datamesa = $this->Controlador_model->get($table->id, "mesa");
                    $tiempoTranscurrido =  $this->Controlador_model->calculartiempo($datamesa->time);
                    $deudaAlquiler = $this->Controlador_model->calcularmontoalquiler($datamesa) == 0 ? "0.00" : $this->Controlador_model->calcularmontoalquiler($datamesa);
                    $deudaExtras = ($totalVentaDetalle->subtotal == "" ? "0.00" : $totalVentaDetalle->subtotal);
                    $totalDeuda = number_format(floatval($deudaAlquiler) + floatval($deudaExtras), 2, ".", "");
                    $estado['1'] = "danger";
                    $estado['2'] = "warning";
                    $estado['3'] = "info";
                    $dataHtml .= '
                            <div class="btn-group" style="margin:10px">
                                    <div class="row">
                                        <div class="col-lg-12 text-center">
                                            <span class="label label-info" style="border-radius: 10px 10px 0px 0px">USUARIO:  ' . ($usuario ? $usuario->nombre : '') . '</span>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12 text-center" style="display: flex; flex-direction: column";>
                                            <div id="content-boton-mesa-'.$venta->id.'" style="position:relative; ">
                                                <button id="botonMesa-'.$venta->id.'" class="btn btn-' . $estado[$table->estado] . '" onclick="datosDeMesa(' . $venta->id . ')" style="border-bottom-left-radius:0px; border-bottom-right-radius:0px">
                                                    <div class="label label-default iniciotiempo">
                                                        <span style="display:block; margin-bottom:3px">INICIO: ' . date('h:i:s A', strtotime($table->time)) . '</span>
                                                        <span style="display:block; margin-bottom:3px">TIEMPO: ' . $tiempoTranscurrido . '</span>
                                                    </div>
                                                    <img style="width:100px" src="' . base_url() . RECURSOS . 'img/mesaOcupado' . rand(1, 5) . '.svg">
                                                    <h4 class="titlemesa">' . $table->nombre . '</h4>
                                                    <div class="label label-default" style="display:flex; flex-direction:column">
                                                        <div style="display:flex; justify-content:space-between"><span>ALQUILER:  </span><span>S/ ' . $deudaAlquiler . '</span></div>
                                                        <div style="display:flex; justify-content:space-between"><span>EXTRAS:  </span><span>S/ ' . $deudaExtras . '</span></div>
                                                        <div style="display:flex; justify-content:space-between"><span>DEUDA:  </span><span>S/ ' . $totalDeuda . '</span></div>
                                                    </div>
                                                </button>
                                            </div>
                                            <div>
                                            <button class="btn btn-warning btn-sm btn-limpiar" onclick="limpiarmesa(' . $venta->id . ')">LIMPIAR</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                }
            }
            $dataHtml .= '</div>
                </div>';
        }
        $dataHtml .= '</div>';
        return $dataHtml;
    }

    function load_zonaMesa(){
        $dataHtml = $this->dataMesas();
        echo $dataHtml;
    }



    function ajax_salirdatamesa()
    {
        $dataHtml = $this->dataMesas();
        echo json_encode(["dataHtml" => $dataHtml]);
    }

    function ajax_agregarPedido()
    {
        $VariExtrData = json_decode($this->input->post("VariExtrData"), true);
        $idventa = $this->input->post("idventa");
        $idvariante = $this->input->post("idvariante");
        $idproducto = $this->input->post("idproducto");
        $precioinsert = $this->input->post("precioinsert");
        $statusregister = TRUE;
        $dataProductoProduco = $this->Controlador_model->get($idproducto, "producto");
        if ($VariExtrData["statusvariante"]) {
            $queryVariante = $this->db->where("venta", $idventa)->where("variante", $idvariante)->get("ventadetalle")->row();
            if ($queryVariante) {
                $cantidad = $queryVariante->cantidad + 1;
                $total = $queryVariante->precio * $cantidad;
                $dataVentaDetalle["cantidad"] = $cantidad;
                $dataVentaDetalle["subtotal"] = $total;
                $this->Controlador_model->update(["id" => $queryVariante->id], $dataVentaDetalle, "ventadetalle");
            } else {
                $statusregister = FALSE;
            }
        } else {
            $queryVentaDetalleProducto = $this->db->where("venta", $idventa)->where("producto", $idproducto)->get("ventadetalle")->row();
            if ($queryVentaDetalleProducto) {
                $cantidadP = $queryVentaDetalleProducto->cantidad + 1;
                $totalP = $queryVentaDetalleProducto->precio * $cantidadP;
                $dataVentaDetalleProducto["cantidad"] = $cantidadP;
                $dataVentaDetalleProducto["subtotal"] = $totalP;
                $this->Controlador_model->update(["id" => $queryVentaDetalleProducto->id], $dataVentaDetalleProducto, "ventadetalle");
            } else {
                $statusregister = FALSE;
            }
        }
        if ($statusregister == FALSE) {
            $dataRegister = [
                "venta" => $idventa,
                "producto" => $idproducto,
                "precio" => $precioinsert,
                "preciocompra" => $dataProductoProduco->preciocompra,
                "variante" => $VariExtrData["statusvariante"] ? $this->input->post("idvariante")  : NULL,
                "nombre" => $this->input->post("textoproducto"),
                "cantidadvariante" =>  $VariExtrData["statusvariante"] ? $this->input->post("cantidadvariante") : NULL,
                "subtotal" => $precioinsert,
                "cantidad" => 1,
                "lote" => $VariExtrData["statuslote"] ? $VariExtrData["lote"] : NULL,
            ];
            $insert = $this->Controlador_model->save("ventadetalle", $dataRegister);
        }
        $dataHtml = $this->dataVentaDetalle($idventa);
        echo json_encode(["proceso" => ["status" => TRUE, "contenido" => $dataHtml]]);
    }

    function dataVentaDetalle($idventa)
    {
        $dataHtml = "";
        $dataVentaDetalle = $this->db->where("venta", $idventa)->get("ventadetalle")->result();
        foreach ($dataVentaDetalle as $value) {
            if ($value->tipo == "0") {
                $dataproducto = $this->Controlador_model->get($value->producto, "producto");
                $dataHtml .= "<div class='seleccion' id='content-padre-$value->id'>
                    <input  value='$value->id' type='hidden'>
                    <div class='seleccion__item--producto'>
                        <div class='seleccion__item-head'>
                        $dataproducto->nombre " . ($value->variante ? "[<span id='total_variante-$value->id'>$value->cantidadvariante</span>]" : "") . "
                        </div>
                        <div class='seleccion__item-body'>
                        <span style='color:#DDA433'>P/U: </span><span>S/</span> <span id='CostoProducto-$value->id'>$value->precio</span> 
                        </div> 
                    </div>
                    <div class='seleccion__item'>
                        <div class='seleccion__item-head'>
                          Cantidad 
                        </div>
                        <div class='seleccion__item-body'>
                          <div class='input-group' style='width:130px;'>
                              <div class='spinner-buttons input-group-btn'>
                                  <button type='button' onclick='MasMenos(0, $value->id)' class='btn spinner-up btn-inverse waves-effect waves-light btn-sm' id='BotonMenos-$value->id'>
                                    <i class='fa fa-minus'></i>
                                  </button>
                            </div>
                              <input onkeyup='CantidadProducto($value->id)' class='spinner-input form-control' readonly id='CantidadProducto-$value->id' type='number' value='$value->cantidad'>
                              <div class='spinner-buttons input-group-btn'>
                                    <button type='button' onclick='MasMenos(1, $value->id)' class='btn spinner-up btn-inverse waves-effect waves-light btn-sm' id='BotonMas-$value->id'>
                                        <i class='fa fa-plus'></i>
                                    </button>
                              </div>
                        </div>
                        </div> 
                    </div>
                    <div class='seleccion__item'>
                        <div class='seleccion__item-head'>
                          Total 
                        </div>
                        <div class='seleccion__item-body'>
                          <div class='input-group'>
                            <span class='input-group-addon'>S/.</span>
                            <input onkeyup='TotalPagarServicio($value->id)' readonly step='0.1' type='number' id='TotalPagar-$value->id' value='$value->subtotal'  class='form-control'>                                                  
                          </div>
                        </div> 
                    </div>
                    <div class='seleccion__item--botoneliminar'>
                    <button class='botoneliminar btn btn-warning btn-sm'   id='BTN-EliminaProducto-$value->id' title='CANCELAR' onclick='EliminaProducto($value->id, $value->producto)'>
                    <i class='fa fa-minus-circle'></i>
                    </button>
                    </div>
                    <div style='display:flex; justify-content:center; width:100%' >
                        <div class='alert alert-danger alert-dismissable' style='margin:3px; padding:2px; display:none' id='ContenedorMensajeStock-$value->id'>
                        </div>
                    </div>
                </div>";
            } else {
                $dataVenta = $this->Controlador_model->get($idventa, "venta");
                $dataMesa = $this->Controlador_model->get($dataVenta->mesa, "mesa");
                $dataHtml .= "<div class='seleccion' id='content-padre-$dataMesa->id'>
                        <input  value='$dataMesa->id' type='hidden'>
                        <div class='seleccion__item--producto'>
                            <div class='seleccion__item-head'>
                            ALQUILER DE $dataMesa->nombre
                            </div>
                            <div class='seleccion__item-body'>
                        <span>S/</span> <span id='CostoProducto-$dataMesa->id'>$dataMesa->precioalquiler</span> 
                            </div> 
                        </div>
                        <div class='seleccion__item'>
                            <div class='seleccion__item-head'>
                            Tiempo 
                            </div>
                            <div class='seleccion__item-body'>
                            <div class='input-group' style='width:130px;'>
                               $value->tiempo_mesa
                                <input class='spinner-input form-control' readonly id='CantidadProducto-$dataMesa->id' type='hidden' value='1'>
                            </div>
                            </div> 
                        </div>
                        <div class='seleccion__item'>
                            <div class='seleccion__item-head'>
                            Total 
                            </div>
                            <div class='seleccion__item-body'>
                            <div class='input-group'>
                                <span class='input-group-addon'>S/.</span>
                                <input readonly step='0.1' type='number' id='TotalPagar-$dataMesa->id' value='$value->subtotal'  class='form-control'>                                                  
                            </div>
                            </div> 
                        </div>
                    </div>";
            }
        }
        return $dataHtml;
    }

    function ajax_eliminarproducto()
    {
        $this->Controlador_model->delete_by_id($this->input->post("idventadetalle"), "ventadetalle");
        echo json_encode(["status" => TRUE]);
    }

    function ajax_menos()
    {
        $ventadetalle = $this->Controlador_model->get($this->input->post("idventadetalle"), "ventadetalle");
        $cantidad = $ventadetalle->cantidad - 1;
        $dataVentaDetalle = [
            "cantidad" => $cantidad,
            "subtotal" => $ventadetalle->precio * $cantidad
        ];
        $this->Controlador_model->update(["id" => $ventadetalle->id], $dataVentaDetalle, "ventadetalle");
        $dataHtml = $this->dataVentaDetalle($ventadetalle->venta);
        echo json_encode(["proceso" => ["status" => TRUE, "dataHtml" => $dataHtml]]);
    }

    function ajax_mas()
    {
        $ventadetalle = $this->Controlador_model->get($this->input->post("idventadetalle"), "ventadetalle");
        $cantidad = $ventadetalle->cantidad + 1;
        $dataVentaDetalle = [
            "cantidad" => $cantidad,
            "subtotal" => $ventadetalle->precio * $cantidad
        ];
        $this->Controlador_model->update(["id" => $ventadetalle->id], $dataVentaDetalle, "ventadetalle");
        $dataHtml = $this->dataVentaDetalle($ventadetalle->venta);
        echo json_encode(["proceso" => ["status" => TRUE, "dataHtml" => $dataHtml]]);
    }

    function ajax_limpiarmesa($idventa)
    {
        $dataventa = $this->db->where("id", $idventa)->where("estado", "0")->get("venta")->row();

        if ($dataventa) {
            $updateMesa = ["estado" => "0", "totalalquiler" => 0.00, "time" => "00:00:00", "checked" => "0000-00-00 00:00:00", "open_by" => "0"];
            $this->Controlador_model->update(["id" => $dataventa->mesa], $updateMesa, "mesa");
            $this->db->where("venta", $dataventa->id)->delete("ventadetalle");
            $this->Controlador_model->delete_by_id($dataventa->id, "venta");
            $dataHtml = $this->dataMesas();
            $dataJSON = [
                "proceso" => ["status" => TRUE, "dataHtml" => $dataHtml]
            ];
        } else {
            $dataVenta = $this->db->where("id", $idventa)->where("estado", "1")->get("venta")->row();
            $dataHtml = $this->dataMesas();
            if ($dataVenta) {
                $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
                $hf_procesado = new DateTime($dataVenta->hf_procesado);
                $time = $hf_procesado->format("g:i:s a");
                $dataJSON = [
                    "proceso" => ["status" => FALSE, "msg" => "La mesa ya fue cobrada por el usuario " . $dataUsuario->usuario . ". hora " . $time . "", "dataHtml" => $dataHtml]
                ];
            } else {
                $dataJSON = [
                    "proceso" => ["status" => FALSE, "msg" => "La mesa ya fue limpiada :(", "dataHtml" => $dataHtml]
                ];
            }
        }
        echo json_encode($dataJSON);
    }
}
