<?php

//Nota: si renombraste la carpeta a algo diferente de "ticket" cambia el nombre en esta línea
require __DIR__ . '/ticket/autoload.php';

require 'simple_html_dom.php';

use FontLib\Table\Type\post;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use function PHPSTORM_META\map;


//? NUEVO
class Inicio extends CI_Controller
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
    $queryventa = $this->Controlador_model->totalVentasNoProcesadas($this->usuario, $this->empresa);
    $query = $this->db->where("id", $this->empresa)->get('empresa')->row();
    if ($queryventa->num_rows() == 0) {
      $data['empresa'] = $this->empresa;
      $data['usuario_creador'] = $this->usuario;
      $data['caja'] = $open_reg->id;
      $data['mesa'] = NULL;
      $data['cliente'] = 1;
      $data['tipoventa'] = $query->tipoventa;
      $data['hora'] = date("H:i:s");
      $data['created'] = date("Y-m-d");
      $this->Controlador_model->save('venta', $data);
    }
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
    <a onclick="CloseRegister()" id="boton-CloseRegister"><i class="fa fa-times" aria-hidden="true"></i></a>
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
    if ($query) {
      if ($query->tipo == "1") {
        echo json_encode(
          [
            'consulta' => ['status' => TRUE, 'idproducto' => $query->id, 'precioproducto' => $query->precioventa],
            'lote' => ['status' => $query->status_lote],
          ]
        );
        exit();
      } else if ($query->tipo == "0") {
        $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
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


  public function printfcomprobante($idventa, $metodoPago)
  {
    $venta = $this->Controlador_model->get($idventa, 'venta');
    $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');
    $htmlComprobante = "
       <h4 class='text-center'> $venta->serie - $venta->numero</h4>
          <span class='float-left'>Fecha: $venta->created</span><br>
          <span class='float-left'>Cliente: " . ($cliente ? $cliente->nombre : 'SIN DATOS') . "</span>
        <table class='table'>
        <thead>
          <tr>
            <th class='text-center'>#</th>
            <th class='text-center'>Descripcion</th>
            <th class='text-center'>Cant</th>
            <th class='text-right;' style='width:100px'>Importe S/</th>
          </tr>
        </thead>
        <tbody>";
    if ($venta->consumo == '1') {
      $htmlComprobante .=
        "<tr>
          <td class='text-center'>1</td>
          <td class='text-center'>Por consumo</td>
          <td class='text-center'>1</td>
          <td class='text-right'>" . number_format($venta->deudatotal, 2) . "</td>
        </tr>";
    } else {
      $ventadetalle = $this->Controlador_model->pedidodetalle($idventa);
      foreach ($ventadetalle as $key => $value) {
        $htmlComprobante .=
          "<tr>
            <td class='text-center'>" . ($key + 1) . "</td>
            <td class='text-center'> $value->nombre [" . ($value->variante ? ($value->cantidadvariante * $value->cantidad) : $value->cantidad) . "]</td>
            <td class='text-center'>$value->cantidad</td>
            <td class='text-right'>" . number_format($value->cantidad * $value->precio, 2) . "</td>
            </tr>";
      }
    }

    $htmlComprobante .= "
    </tbody>
    <tfoot>
      <tr>
          <td colspan='3' style='text-align:right; font-weight:bold;'>Total S/</td>
          <td style='text-align:right; font-weight:bold;'> $venta->montototal</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none'>Descuento S/</td>
        <td style='text-align:right; font-weight:bold; border:none'> $venta->descuento</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none; color:#36a229''>Pagado($metodoPago) S/</td>
        <td style='text-align:right; font-weight:bold; border:none; color:#36a229'> $venta->deudatotal</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none;'>Recibido S/</td>
        <td style='text-align:right; font-weight:bold; border:none;'>$venta->pago</td>
      </tr>
      <tr>
        <td colspan='3' style='text-align:right; font-weight:bold; border:none'>Vuelto S/</td>
        <td style='text-align:right; font-weight:bold; border:none'>$venta->vuelto</td>
      </tr>
    </tfoot>
    </table>";

    $telefono = $cliente->telefono != "" ? $cliente->telefono : "";
    $email = $cliente->correo != "" ? $cliente->correo : "";
    $htmlFotter = "
    <div class='row' style='margin-bottom:10px'>
      <div class='col-md-6'>
        <div class='input-group input-group-sm'>
          <input type='email' id='correo' class='form-control' placeholder='correo@pucallsystem.com' value='$email'>
          <span class='input-group-btn' id='span-print'>
            <button id='enviarcorreo' class='btn btn-success' onclick='sendMail($idventa)' type='button'>
            <i class='fa fa-paper-plane' aria-hidden='true'></i>
            </button>
          </span>
        </div>
      </div>
      <div class='col-md-6'>
        <div class='input-group input-group-sm'>
        <span class='input-group-addon' id='sizing-addon3'>+51</span>
        <input type='text' id='telefonoWP' class='form-control' placeholder='999999999' value='$telefono' autocomplete='off'>
        <span class='input-group-btn' id='span-print'>
          <button class='btn btn-success' onclick='sentTicketWA($idventa)' type='button'><i class='fa fa-whatsapp' aria-hidden='true'></i></button>
        </span> 
        </div>
      </div>
    </div>
   <button data-dismiss='modal' class='btn btn-default hiddenpr'>Cerrar</button>
   <button class='btn btn-add' onclick='imprimircomprobante($empresa->tipoimpresora , $idventa)' id='btncomprobante'>Imprimir</button>";

    return ["htmlComprobante" => $htmlComprobante, "htmlFotter" => $htmlFotter];
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

    if ($mail->send()) {
      echo json_encode(array('status' => TRUE));
      exit();
    } else {
      echo json_encode(array('status' => FALSE));
      exit();
    }
  }

  public function CloseRegister()
  {
    $mostrar = '
      <h2 class="text-center">
      RESUMEN DE CAJA
      <img src="' . site_url() . '/assets/adminlte/img/register.svg" alt="" style="width:100px">
      </h2>
       
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
     </div>';
    echo json_encode(array('data' => $mostrar, 'status' => TRUE));
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
    }

    $queryVentas = $this->Controlador_model->geVentastCaja($this->caja, 'venta');
    $expences = $this->Controlador_model->getCaja($this->caja, 'egreso');
    $ventasContado = 0;
    $montoCreditos = 0;
    $totalVentasCredito = 0;
    $gasto = 0;
    $totalVentasContados = 0;
    $totalGastos = 0;

    $pagosEfectivo = $this->Controlador_model->getCajaPagos($this->caja, 'ingreso', "EFECTIVO"); //VENTA
    $pagosEfectivoGenerados = $this->Controlador_model->totalGenerados($this->caja, "EFECTIVO"); // VENTA GENERADOS
    $pagosTarjeta = $this->Controlador_model->getCajaPagos($this->caja, 'ingreso', "TARJETA"); //VENTA
    $pagosTarjetaGenerados = $this->Controlador_model->totalGenerados($this->caja, "TARJETA"); // VENTA GENERADOS

    $abonosCaja =  $this->Controlador_model->abonosCaja($this->caja);
    $totalAbono = 0;
    $totalAbonoGenerado = 0;
    foreach ($abonosCaja as $abono) {
      $totalAbono += $abono->monto;
      $totalAbonoGenerado += 1;
    }
    //? esta estado 3 de una venta es una venta anulada
    $devolucionesCaja = $this->db->where("caja", $this->caja)->where("estado", "3")->get("venta")->result();
    $devolucionesGeneradas = 0;
    $devoluciones = 0;
    foreach ($devolucionesCaja as $devolucion) {
      $devoluciones += $devolucion->deudatotal;
      $devolucionesGeneradas += 1;
    }

    foreach ($queryVentas as $venta) {
      if ($venta->formapago == 'CONTADO') {
        $ventasContado += $venta->deudatotal;
        $totalVentasContados += 1;
      } else {
        $montoCreditos += $venta->deudatotal;
        $totalVentasCredito += 1;
      }
    }

    foreach ($expences as $expence) {
      $gasto += $expence->montototal;
      $totalGastos += 1;
    }



    $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
    $data['usuario_cierre'] = $this->usuario;
    $data['contado'] = $ventasContado;
    $data['contadosgenerados'] = $totalVentasContados;
    $data['credito'] = $montoCreditos;
    $data['creditosgenerados'] = $totalVentasCredito;
    $data['efectivocontado'] = $pagosEfectivo->totalpagos ? $pagosEfectivo->totalpagos : 0;
    $data['efectivogenerados'] = $pagosEfectivoGenerados;
    $data['tarjetacontado'] = $pagosTarjeta->totalpagos ? $pagosTarjeta->totalpagos : 0;
    $data['tarjetagenerados'] = $pagosTarjetaGenerados;
    $data['gasto'] = $gasto;
    $data['gastosgenerados'] = $totalGastos;
    $data['abonos'] = $totalAbono;
    $data['abonosgenerados'] = $totalAbonoGenerado;
    $data['devoluciones'] = $devoluciones;
    $data['devolucionesgeneradas'] = $devolucionesGeneradas;
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
    $idCajaAntesDeVaciar = $this->caja;
    $this->Controlador_model->save('monedero', $datamonedero);
    $CI = &get_instance();
    $CI->session->set_userdata('caja', NULL);
    echo json_encode(array("status" => TRUE, "usuarioperfil" => $this->perfil, "tipoimpresora" => $dataEmpresa->tipoimpresora, "idcaja" => $idCajaAntesDeVaciar, "datacaja" => $data));
    exit();
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

  public function imprimircierre($idcaja)
  {
    $caja = $this->Controlador_model->get($idcaja, 'caja');
    $empresa = $this->Controlador_model->get($caja->empresa, 'empresa');
    $usuario = $this->Controlador_model->get($caja->usuario, 'usuario');
    $monedero = $this->db->where('caja', $idcaja)->get('monedero')->row();
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
    $posales = $this->Controlador_model->resumenventa($idcaja);
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

  public function showcierre($idcaja)
  {
    $monedero = $this->db->where('caja', $idcaja)->get('monedero')->row();
    $data = array(
      'monedero' => $monedero,
      'montoCerrarCaja' => $monedero->montototal,
      'caja' => $caja = $this->Controlador_model->get($idcaja, 'caja'),
      'usuario' => $this->Controlador_model->get($caja->usuario, 'usuario'),
      'empresa' => $this->Controlador_model->get($caja->empresa, 'empresa'),
      'posales' => $this->Controlador_model->resumenventa($idcaja)
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

      /* if ($value->photo) {
        $html .= '<img src="' . base_url() . 'files/products/' . $value->photothumb . '" alt="' . $value->nombre . '">';
      } */
      if ($value->tipo == 02) {
        $html .= '<label style="font-size: 20px; font-weight:900; color:#00fcb8">COMBO</label>';
      }

      $botonStock = '<button class="btn btn-info btn-sm" id="verStock-' . $value->id . '-1" onclick="verstockactual(' . $value->id . ', 1)" style="padding:1px; font-size:10px" title="Ver Stock">STOCK <i class="fa fa-search" ></i></button>';
      $botonverimg = '<button class="btn btn-warning btn-sm" name ="photo "id="verFoto-' . $value->id . '" onclick="verimg(' . $value->id . ')" style="padding:1px; font-size:10px"  title="Ver Imagen"> VER <i class="fa fa-eye"></i></button>';
      $botonverimg2 = '<button class="btn  btn-warning btn-sm" name="photo" id="verFoto-' . $value->id . '" onclick="verimg(' . $value->id . ')" style="padding:1px; font-size:10px" title="Ver Imagen">VER <i class="fa fa-eye"></i></button>';
      $botonverimg3 = '<button class="btn  btn-warning btn-sm" name="photo" id="verFoto-' . $value->id . '" onclick="verimg(' . $value->id . ')" style="padding:1px; font-size:10px" title="Ver Imagen">VER <i class="fa fa-eye"></i></button>';
      $html .= '
      </div>
      </button>

      ' . ($value->tipo == '0' ? $botonStock : "") . '
      ' . ($value->tipo == '0' ? $botonverimg : "") . '
      ' . ($value->tipo == '1' ? $botonverimg : "") . '
      ' . ($value->tipo == '2' ? $botonverimg2 : "") . '
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
    $data['usuario_creador'] = $this->usuario;
    $data['hora'] = date("H:i:s");
    $data['created'] = date("Y-m-d");
    $data['caja'] = $this->caja;
    $data['mesa'] = NULL;
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
    $html = "";
    $idselec = "";
    if ($query) {
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
    echo json_encode(['datahtml' => $html, 'idselect' => $idselec]);
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
    $queryventa = $this->Controlador_model->estadoVenta($this->input->post("venta"), $this->empresa, "0");
    if ($queryventa) {
      $productos = json_decode($this->input->post("productos"), true);
      $dataEmpresa = $this->Controlador_model->get($this->empresa, "empresa");
      $dataenviar = [];
      $empresa = $this->Controlador_model->get($this->empresa, "empresa");
      foreach ($productos as $key => $value) {
        $EstadoProducto = FALSE;
        $idproducto = $value['id_producto'];
        $cantidadproducto = $value['statusvariante'] ?  ($value['cantidad_variante'] * $value['cantidad']) : $value['cantidad'];
        $cantidadTotalVerif = $cantidadproducto;
        $producto = $this->Controlador_model->get($idproducto, 'producto');
        if ($producto->tipo == '0') {
          //todo: El tipo de producto 0 es estandar
          $registro = $this->Controlador_model->get($this->caja, 'caja');
          $existenciaStock = $this->Controlador_model->existenciaStock($producto->id, $empresa->almacen, $value['lote'], $cantidadTotalVerif, $this->empresa); //todo: verificamos si el producto esta registra en stock
          if ($existenciaStock) {
            $EstadoProducto = $existenciaStock->cantidad >= $cantidadTotalVerif ? FALSE : TRUE;
          } else {
            $EstadoProducto = TRUE;
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
                $productoStock = $this->db->where('almacen', $empresa->almacen)->where('producto', $value2->item_id)->where('cantidad <', $value2->cantidad * $cantidadTotalVerif)->get('stock')->row();
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
        $dataLote = $this->Controlador_model->get($value['lote'], "lote");
        if ($EstadoProducto == TRUE) {
          $totalstock = $this->totalStock($idproducto, 0, $value['lote']);
          $dataenviar[] = ["idproducto" => $idproducto, "totalstock" => $totalstock, "key_primary" => $value['key_primary'], "nombrelote" => ($dataLote ? $dataLote->lote : "")];
        }
      }
      echo json_encode([
        "venta" => ["status" => TRUE],
        "tresPasos" => $dataEmpresa->pasos,
        "dataenviar" => $dataenviar,
      ]);
    } else {
      $dataVenta = $this->db->where("id", $this->input->post("venta"))->where("estado", "1")->get("venta")->row();
      if ($dataVenta) {
        $dataUsuario = $this->Controlador_model->get($dataVenta->usuario_proceso, "usuario");
        $hf_procesado = new DateTime($dataVenta->hf_procesado);
        $time = $hf_procesado->format("g:i:s a");
        $dataJSON = [
          "venta" => [
            "status" => FALSE,
            "msg" => "La venta ya fue procesado por el usuario " . $dataUsuario->usuario . ". hora " . $time . "",
          ]
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

  private function _validatprocesar()
  {
    $data = array();
    $data['error_string'] = array();
    $data['inputerror'] = array();
    $data['status'] = TRUE;

    $venta = $this->Controlador_model->get($this->input->post("idventa"), 'venta');
    $cliente = $this->Controlador_model->get($venta->cliente, 'cliente');

    if ($this->input->post('pago') == "") {
      $data['inputerror'][] = 'pago';
      $data['error_string'][] = 'Este campor es obligatorio';
      $data['status'] = FALSE;
    } else {
      $deudatotalVenta = $this->input->post('deuda') - $this->input->post("descuento");
      if ($deudatotalVenta > $this->input->post('pago')) {
        $data['inputerror'][] = 'pago';
        $data['error_string'][] = 'No puedes cancelar la venta con el monto isertado';
        $data['status'] = FALSE;
      }
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
      }
    }
    return $data;
  }


  function ajax_procesarVenta()
  {
    $dataPerfil = $this->Controlador_model->get($this->perfil, 'perfil');
    $dataEmpresa = $this->Controlador_model->get($this->empresa, 'empresa');
    $idventa = $this->input->post("idventa");
    $dataventa = $this->db->where("id", $idventa)->where("estado", "0")->or_where("estado", '4')->get("venta")->row();
    if ($dataventa) {
      $respuestaValidate = $this->_validatprocesar();
      if ($respuestaValidate["status"] === FALSE) {
        echo json_encode(["proceso" => ["status" => TRUE, "validate" => FALSE, "contenido" => $respuestaValidate]]);
      } else {
        $dataproductos = json_decode($this->input->post("dataproductos"));
        if ($dataPerfil->cobradorcaja == '1' and $dataEmpresa->pasos == '1') {
          $this->db->where("venta", $idventa)->delete("ventadetalle");
        }
        $CantidadItem = 0;
        $DeudaTotal = 0;
        foreach ($dataproductos as $value) {
          $producto = $value->id_producto;
          $cantidad_V = $value->cantidad_variante;
          $productodata = $this->Controlador_model->get($producto, 'producto');
          $cantidadEscogido = $value->cantidad;
          $cantidadDescontar = $value->statusvariante ? $value->cantidad_variante_total :  $value->cantidad;
          $dataVentaDetalle =  [
            "venta" => $idventa,
            'producto' => $producto,
            "variante" => ($value->statusvariante ? $value->id_variante : NULL),
            'lote' => ($value->statuslote ? $value->lote : NULL),
            'nombre' => $value->text_proudcto,
            'precio' => $value->precio_producto,
            'preciocompra' => $productodata->preciocompra,
            "cantidad" => $value->cantidad,
            "subtotal" => $value->total_pagar,
            "cantidadvariante" => ($value->statusvariante ? $cantidad_V : NULL),
            "time" => date("H:i:s"),
            "estado" => '1' // TODO Estado "1" Significa que cocina ya lo antendio
          ];
          $CantidadItem += $cantidadEscogido;
          $DeudaTotal += $value->total_pagar;
          $this->Controlador_model->save('ventadetalle', $dataVentaDetalle);
          if ($productodata->tipo == '0') {
            $cantidad = $this->Controlador_model->getStockAlmacen($producto, $dataEmpresa->almacen, $value->lote, $this->empresa);
            $movimiento['empresa'] = $this->empresa;
            $movimiento['usuario'] = $this->usuario;
            $movimiento['venta'] = $idventa;
            $movimiento['tipooperacion'] = "VENTA";
            $movimiento['producto'] = $producto;
            $movimiento['almacen'] = $dataEmpresa->almacen;
            $movimiento['lote'] =  ($value->statuslote ? $value->lote : NULL);
            if ($value->statusvariante) {
              $dataVariante = $this->Controlador_model->get($value->id_variante, "productovariante");
              $totalDescontar = $dataVariante->cantidad * $value->cantidad;
              $movimiento['medida'] =  $dataVariante->nombre;
              $movimiento['medidacantidad'] = $dataVariante->cantidad;
              $movimiento['cantidaditem'] = $dataVariante->cantidad * $value->cantidad;
              $movimiento['totalitemoperacion'] = $dataVariante->cantidad * $value->cantidad;
            } else {
              $totalDescontar = $value->cantidad;
              $movimiento['medida'] =  "UNIDAD";
              $movimiento['medidacantidad'] = 1;
              $movimiento['cantidaditem'] = $value->cantidad;
              $movimiento['totalitemoperacion'] = $value->cantidad;
            }
            $movimiento['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
            $movimiento['stockanterior'] = $cantidad ? $cantidad->cantidad : 0;
            $movimiento['tipo'] =  'SALIDA VENTA';
            $movimiento['stockactual'] = ($cantidad ? $cantidad->cantidad : 0) - $totalDescontar;
            $movimiento['created'] = date('Y-m-d');
            $movimiento['hora'] = date("H:i:s");
            $this->Controlador_model->save('movimiento', $movimiento);
          } else if ($productodata->tipo == '2') {
            $combos = $this->db->where('producto',  $producto)->get('combo')->result();
            foreach ($combos as $combo) {
              $stock = $this->Controlador_model->getStockProceso($combo->item_id, $dataEmpresa->almacen, NULL, $this->empresa);
              $movimientoCombo['empresa'] = $this->empresa;
              $movimientoCombo['usuario'] = $this->usuario;
              $movimientoCombo['venta'] = $idventa;
              $movimientoCombo['tipooperacion'] = "VENTA";
              $movimientoCombo['producto'] = $combo->item_id;
              $movimientoCombo['productocombo'] = $producto;
              $movimientoCombo['almacen'] = $dataEmpresa->almacen;
              $movimientoCombo['lote'] =  ($value->statuslote ? $value->lote : NULL);
              $movimientoCombo['medida'] =  "COMBO";
              $movimientoCombo['medidacantidad'] = $combo->cantidad;
              $movimientoCombo['cantidad'] = $value->cantidad; //? LO QUE REGISTRA
              $movimientoCombo['cantidaditem'] = $combo->cantidad * $value->cantidad;
              $movimientoCombo['totalitemoperacion'] = $combo->cantidad * $value->cantidad;
              $movimientoCombo['stockanterior'] = $stock ? $stock->cantidad : 0;
              $movimientoCombo['tipo'] =  'SALIDA VENTA COMBO';
              $movimientoCombo['stockactual'] = ($stock ? $stock->cantidad : 0) - ($combo->cantidad * $value->cantidad);
              $movimientoCombo['created'] = date('Y-m-d');
              $movimientoCombo['hora'] = date("H:i:s");
              $this->Controlador_model->save('movimiento', $movimientoCombo);
            }
          }
          $this->ajax_descontar_stock($producto, $cantidadDescontar, $value->lote, $dataEmpresa->almacen);
        }
        //? pagar venta
        $venta = $this->Controlador_model->get($idventa, 'venta');
        $empresa = $this->Controlador_model->get($venta->empresa, 'empresa');
        
        $dataventaUpdate['formapago'] = $this->input->post('formapago');
        
        $deudaTotal = $this->input->post("deuda") - $this->input->post("descuento");
        $dataventaUpdate['caja'] = $this->caja;
        
        $dataventaUpdate['usuario_proceso'] = $this->usuario;
        $dataventaUpdate['atender'] = "1";
        $dataventaUpdate['sound'] = "1";
        $dataventaUpdate['hf_procesado'] = date("Y-m-d H:i:s");
        $dataventaUpdate['vence'] = $this->input->post('vence');
        
        $dataventaUpdate['created'] = $this->input->post('fecha');
        $dataventaUpdate['totalitems'] = $CantidadItem;
        $dataventaUpdate['estado'] = '1';
        //Datos deuda de la venta
        $dataventaUpdate['montototal'] = $this->input->post("deuda");
        $dataventaUpdate['descuento'] = $this->input->post("descuento");
        $dataventaUpdate['deudatotal'] = $deudaTotal;
        if ($this->input->post('formapago') == 'CONTADO') {
          $serie = substr($venta->tipoventa, 0, 1) . substr($empresa->serie, 1, 3);
          $numero = $this->Controlador_model->codigos($venta->tipoventa, $serie);
          $numeros = $numero ? $numero->consecutivo + 1 : 1;
          $cadena = "";
          for ($i = 0; $i < 6 - strlen($numeros); $i++) {
            $cadena = $cadena . '0';
          }
          $dataventaUpdate['serie'] = $serie;
          $dataventaUpdate['numero'] = $cadena . $numeros;
          $dataventaUpdate['consecutivo'] = $numeros;
          $dataventaUpdate['montoactual'] = 0;
          $dataventaUpdate['pago'] = $this->input->post('pago');
          $dataventaUpdate['vuelto'] = $this->input->post('pago') - $deudaTotal;
          //? REGISTRO EN LA TABLA INGRESO
          $dataingreso['tipo'] = 'VENTA';
          $dataingreso['monto'] = $deudaTotal;
          $dataingreso['empresa'] = $this->empresa;
          $dataingreso['usuario'] = $this->usuario;
          //? CONCEPTO 3 ES INGRESO EN EFECTIVO
          //? CONCEPTO 23 ES INGRESO TRANSF. DINERO AREAS
          $dataingreso['concepto'] = $this->input->post('metodopago') == "EFECTIVO" ? 3 : 28;
          $dataingreso['caja'] = $this->caja;
          $dataingreso['venta'] = $idventa;
          $dataingreso['metodopago'] = $this->input->post('metodopago');
          $dataingreso['tipotarjeta'] = $this->input->post('metodopago') == "TARJETA" ? $this->input->post('tipotarjeta') : NULL;
          $dataingreso['created'] = date('Y-m-d');
          $dataingreso['hora'] = date('H:i:s');
          $this->Controlador_model->save('ingreso', $dataingreso);
        } else {
          $comprobante = $this->db->where("tipo","CREDITOS")->where("empresa",$this->empresa)->get("comprobante")->row();
          if(isset($comprobante))
          {
            $dataventaUpdate["serie"]           = $comprobante->serie;
            $dataventaUpdate["numero"]          = $this->Controlador_model->addLeadingZeros(($comprobante->correlativo+1));
            $dataventaUpdate["consecutivo"]     = (int)$comprobante->correlativo +1;
            $z["correlativo"]                   = $dataventaUpdate["consecutivo"] ;
            $this->Controlador_model->update(array('id' => $comprobante->id), $z, 'comprobante');
          }
          $dataventaUpdate['montoactual'] = $deudaTotal;
        }
        $this->Controlador_model->update(array('id' => $idventa), $dataventaUpdate, 'venta');

        $htmlComprobante = $this->input->post('formapago') == 'CONTADO' ? $this->printfcomprobante($idventa, $this->input->post('metodopago')) : "";
        $this->NewVenta();


        echo json_encode(["proceso" => ["status" => TRUE, "validate" => TRUE, "htmlcomprobante" => $htmlComprobante]]);
      }
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

  public function ajax_descontar_stock($idproducto, $cantidad, $lote, $almacenDescontar)
  {
    //todo: hacemos el descuento del stock
    $producto = $this->Controlador_model->get($idproducto, 'producto');
    if ($producto->tipo == 0) {
      //VALIDAR SI EL DESCUENTO ES POR LOTE
      $stock = $this->Controlador_model->getStockProceso($producto->id, $almacenDescontar, $lote, $this->empresa);
      $updateStock = [
        'cantidad' => ($stock->cantidad - $cantidad)
      ];
      $this->Controlador_model->update(['id' => $stock->id], $updateStock, 'stock');
    } else if ($producto->tipo == 2) {
      $combo = $this->Controlador_model->ProductoCombo($producto->id);
      foreach ($combo as $value) {
        $stock = $this->Controlador_model->getStockProceso($value->item_id, $almacenDescontar, $lote, $this->empresa);
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
  }

  function ajax_stockactual()
  {
    $idproducto = $this->input->post("idproducto");
    $queryProducto = $this->Controlador_model->get($idproducto, "producto");
    if ($queryProducto->tipo == '0') {
      $almacenes = $this->db->where("empresa", $this->empresa)->get('almacen')->result();
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
    } else {
      $stockCombo = $this->totalStock($idproducto, 0, FALSE);
      $html = "<div class='alert alert-success text-center' style='font-weight: bold;color: #8e27c1;background: #906fc5a1;border-color: #905ec5;'><h4>TOTAL DE STOCK $stockCombo </h4></div>";
    }
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
  function ajax_img($id)
  {
    $datas = $this->Controlador_model->get($id, "producto");
    //$foto= $this->Controlador_model->get($datas->categoria, 'productocategoria');
    $data = array(
      'photo' => $datas->photo
    );

    echo json_encode($data);
  }

  function ajax_tabla_pedidosEnviados()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where('empresa', $this->empresa)->where('estado', "4")->get('venta')->result();
    $data = [];
    foreach ($query as $key => $value) {
      $vendedor = $this->Controlador_model->get($value->usuario_creador, "usuario");
      $totalDeuda = $this->db->select_sum("subtotal")->where("venta", $value->id)->get("ventadetalle")->row();
      $boton = '';
      $boton .= "<a class='btn btn-success' id='cobrar-$value->id' onclick='cobrar($value->id)'><i class='fa fa-money'></i></a></td>";
      $data[] = array(
        $key + 1,
        $value->referencia,
        $vendedor->nombre,
        $totalDeuda->subtotal,
        $value->created . " / " . date("g:i a", strtotime($value->hora)),
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

  function ajax_EnviarPedidoCaja()
  {
    $idventa = $this->input->post("idventa");
    $dataproductos = json_decode($this->input->post("pedidosVentaDetalle"));
    foreach ($dataproductos as $value) {
      $producto = $value->id_producto;
      $cantidad_V = $value->cantidad_variante;
      $productodata = $this->Controlador_model->get($producto, 'producto');
      $dataVentaDetalle =  [
        "venta" => $idventa,
        'producto' => $producto,
        "variante" => ($value->statusvariante ? $value->id_variante : NULL),
        'lote' => ($value->statuslote ? $value->lote : NULL),
        'nombre' => $value->text_proudcto,
        'precio' => $value->precio_producto,
        'preciocompra' => $productodata->preciocompra,
        "cantidad" => $value->cantidad,
        "subtotal" => $value->total_pagar,
        "cantidadvariante" => ($value->statusvariante ? $cantidad_V : NULL),
        "time" => date("H:i:s"),
        "estado" => '1' // TODO Estado "1" Significa que cocina ya lo antendio
      ];
      $this->Controlador_model->save('ventadetalle', $dataVentaDetalle);
    }
    $dataUpdateVenta = ["estado" => "4", "referencia" => $this->input->post("referencia")];
    $this->Controlador_model->update(["id" => $idventa], $dataUpdateVenta, "venta");
    echo json_encode(["proceso" => ["status" => TRUE]]);
  }

  function ajax_cobrar($idventa)
  {
    $dataVentaDetalle = $this->db->where("venta", $idventa)->get("ventadetalle")->result();
    $dataVenta = $this->Controlador_model->get($idventa, "venta");
    $cliente = $this->Controlador_model->get($dataVenta->cliente, "cliente");
    $dataVenta->textCliente = $cliente->documento . " | " . $cliente->nombre . " " . $cliente->apellido;
    echo json_encode(["dataVentaDetalle" => $dataVentaDetalle, "dataVenta" => $dataVenta]);
  }

  function ajax_tableDataProductos()
  {
    $draw = intval($this->input->get("draw"));
    $start = intval($this->input->get("start"));
    $length = intval($this->input->get("length"));
    $query = $this->db->order_by('id', 'desc')->where("estado", "0")->where("categoria <>", 14)->get("producto")->result();
    $data = [];
    foreach ($query as $key => $value) {
      $dataEmpresa = $this->Controlador_model->get($this->empresa, 'empresa');
      $datacategoria = $this->Controlador_model->get($value->categoria, 'productocategoria');
      $dataAdicionales = "<input type=hidden id='tipomedida-$value->id' value=UND/>
      <input type=hidden id='cantidad-tm$value->id' value='1'/>
      <input type=hidden id='nombre-producto-$value->id' value='$value->nombre'/>
      <input type=hidden id='precio-producto-$value->id' value='$value->precioventa'/>";
      $boton = '';
      $dataextrasProducto = "";
      $dataextrasProducto .= $datacategoria->estadoextras == "1" ?  " <label class='label label-success' style='margin:0px 1px'>EXTRAS</label> " : "";
      $dataextrasProducto .= $value->status_lote == "1" ? " <label class='label label-default' style='margin:0px 1px'>LOTES</label> " : "";
      $dataextrasProducto .= $value->variante == "1" ? " <label class='label label-info' style='margin:0px 1px'>VARIANTES</label> " : "";
      if ($value->tipo == '0') {
        $textTipo = "<label class='label label-default' style='display:flex; justify-content:center; align-items:center'>NORMAL $dataextrasProducto</label>";
      } else if ($value->tipo == '1') {
        $textTipo = "<label class='label label-warning' style='display:flex; justify-content:center; align-items:center'>SERVICIO $dataextrasProducto</label>";
      } else if ($value->tipo == '2') {
        $textTipo = "<label class='label label-purple' style='display:flex; justify-content:center; align-items:center'>COMBO $dataextrasProducto</label>";
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
      $nombreProducto = $value->nombre . ' <button class="btn  btn-default btn-sm" name="photo" id="verFoto-' . $value->id . '" onclick="verimg(' . $value->id . ')"  title="Ver Imagen"><i class="fa fa-file-image-o"></i></button>';

      if ($value->tipo == '0' || $value->tipo == '2') {
        $boton .= '<button class="btn  btn-info btn-sm" id="verStock-' . $value->id . '-0" onclick="verstockactual(' . $value->id . ', 0)" title="Ver Stock">STOCK <i class="fa fa-search"></i></button> ';
      }
      $boton .= " <button  class='btn btn-sm btn-success' id='boton-producto-$value->id' title='AGREGAR' $evento><i class='fa fa-shopping-cart'></i></button>";
      $data[] = array(
        $key + 1,
        $textTipo . $nombreProducto . $dataAdicionales,
        $value->codigoBarra,
        $datacategoria ? $datacategoria->nombre : "SIN DATOS",
        $value->precioventa,
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
}
