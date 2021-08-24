<?php require 'inicioscript.php';  ?>
<!-- Page Content -->
<style>
  .opacar_div {
    cursor: no-drop;
    opacity: .3
  }

  #SubtotVenta::before {
    content: "S/ ";

  }

  #SubtotVenta {
    font-weight: bold;
  }

  .agotado {
    color: #E92525;
    font-weight: bold;
    font-size: 20px;
    position: absolute;
    top: 60px;
    left: 15px;
    cursor: no-drop;
    z-index: 400;
    transform: rotate(35deg)
  }

  .no-drop {
    cursor: no-drop !important;
  }

  .loader {
    border: 16px solid #f3f3f3;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 2s linear infinite;
    /* Safari */
    animation: spin 2s linear infinite;
    position: absolute;
    z-index: 99999;
    left: 40%;
  }

  /* Safari */
  @-webkit-keyframes spin {
    0% {
      -webkit-transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .productoIsSelected {
    background: rgb(63, 94, 251);
    background: radial-gradient(circle, rgba(63, 94, 251, 1) 0%, rgba(170, 123, 158, 1) 100%);
  }

  /* Estilos de tas nuevos */
  .Contenedortab {
    border: none;
    padding-top: 2px;
    padding-bottom: 5px;
    cursor: pointer;
    border-radius: 0;
    color: #333 !important;
    line-height: 40px;
    background-color: transparent;
    text-align: center;
  }

  /* Style the buttons that are used to open the tab content */
  .Contenedortab button {
    display: inline-block;
    color: #FFF;
    background: #33b86c;
    padding: 0px 18px;
    font-family: 'Fredoka One', sans-serif;
    border-radius: 20px;
    box-shadow: 0 8px 0 0 #4aa185;
    text-shadow: -1px 1px 2px rgb(0 0 0 / 50%);
    transition: background 0.2s ease-in-out, top 0.3s ease-in-out, box-shadow 0.1s ease-in-out;
  }

  /* Change background color of buttons on hover */
  .Contenedortab button:hover {
    background: rgb(161, 229, 108);
    box-shadow: 0 8px 0 0 #60a14a;
  }

  /* Create an active/current tablink class */
  .Contenedortab button.active {
    background-color: rgb(161, 229, 108);
  }

  /* Style the tab content */
  .tabcontent2 {
    display: none;
    padding: 6px 12px;

    border-top: none;
  }

  /*Estilos de categorias*/
  .carta__des {
    color: #adb5bd;
    font-weight: 600;
    margin: 0 0 3px;
    flex: 1;
  }

  .carta__title {
    font-size: 14px;
    line-height: 18px;
    font-weight: 600;
    color: #2d353c;
    margin: 0 0 3px;
  }

  .carta {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    width: 100%;
    border-radius: 5px;
    background-color: #fff;
    transition: all 0.2s linear;
    min-height: 200px;
    text-align: left;
  }

  .carta:hover {
    cursor: pointer;
    box-shadow: #adb5bd 0px 5px 10px 3px;
    text-decoration: none;

  }

  .carta__imagen {
    width: 100%;
    background-position: center;
    background-size: cover;
    box-sizing: border-box;
    display: block;
    border-radius: 5px 5px 0px 0px;
    background-repeat: no-repeat
  }

  .botoneliminar {
    position: relative;
    top: 0px;
    right: 0px;
    z-index: 10;
    margin-right: 0px !important;
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    background: #EEAF14;
    width: 30px;
    height: 100%
  }

  /*//? nuevo de la seleccion del producto*/
  :root {
    --sizebody: 13px;
  }

  .seleccion {
    display: flex;
    background-color: #c8f7dc;
    margin: 10px;
    flex-wrap: wrap;
    flex-direction: row;
    opacity: 0.8;
    border-radius: 15px;
  }

  .seleccion__item {
    padding: 5px;
    font-size: var(--sizebody);
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .seleccion__item--botoneliminar {
    padding: 0px;
    font-size: var(--sizebody);
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    max-width: 30px;
    justify-content: space-between;
  }

  input[type=number]::-webkit-inner-spin-button,
  input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  input[type=number] {
    -moz-appearance: textfield;
  }

  .seleccion__item--masmenos {
    padding: 2px;
    font-size: var(--sizebody);
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .seleccion__item--producto {
    padding: 3px;
    font-size: var(--sizebody);
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .seleccion__item-head {
    color: rgb(43, 43, 43);
    font-weight: bold;
    text-align: center;
  }

  .seleccion__item-body {
    text-align: center;
    margin: auto;
  }

  /* //? la parte center del div derecha estilos*/
  .partecenter {
    height: 35vh;
    overflow-y: auto;
    overflow-x: hidden
  }

  .partecenter::-webkit-scrollbar {
    width: 10px;
    background: rgb(214, 219, 223);
  }

  .partecenter::-webkit-scrollbar-thumb {
    background-color: rgb(178, 186, 187);
    border-radius: 4px;
  }

  .partecenter::-webkit-scrollbar-thumb:hover {
    background-color: rgb(127, 140, 141);
  }


  .CategriaSeleccionar::-webkit-scrollbar {
    width: 10px;
    background: rgb(214, 219, 223);
  }

  .CategriaSeleccionar::-webkit-scrollbar-thumb {
    background-color: rgb(178, 186, 187);
    border-radius: 4px;
  }

  .CategriaSeleccionar::-webkit-scrollbar-thumb:hover {
    background-color: rgb(127, 140, 141);
  }

  #Categoriaproducto::-webkit-scrollbar {
    width: 10px;
    background: rgb(214, 219, 223);
  }

  #Categoriaproducto::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background-color: rgb(178, 186, 187);
  }

  #Categoriaproducto::-webkit-scrollbar-thumb:hover {
    background-color: rgb(127, 140, 141);
  }

  .alertacantidad {
    position: absolute;
    top: -8px;
    text-align: center;
    box-sizing: content-box;
    min-width: 20px;
    min-height: 20px;
    z-index: 100;
    right: 0px;
    color: #FFF;
    border-radius: 50%;
    padding: 5px;
    font-size: 16px'

  }

  .alertacantidad__verde {
    background: #1F854B;
  }

  .alertacantidad__rojo {
    background: #d20a2b;
  }
</style>
<?php if (!$this->caja) { ?>
  <div class="container container-small">
    <?= $this->session->flashdata('mensaje') ?>
    <div class="row">
      <h1 class="text-center choose_store">ELIGE UNA TIENDA</h1>
    </div>
    <div class="row">
      <ul id="storeline">
        <?php foreach ($tiendas as $store) { ?>
          <?php $apertura = $this->Controlador_model->cajabierta('0', $store->id); ?>
          <?php $caja = $apertura ? $apertura->id : FALSE; ?>
          <?php $estado = $apertura ? 0 : 1; ?>
          <?php if ($apertura) { ?>
            <?php $disabled = ''; ?>
          <?php } else { ?>
            <?php $maximo = $this->Controlador_model->cajabierta('1', $store->id); ?>
            <?php if ($maximo && $maximo->created == date('Y-m-d')) { ?>
              <?php //$disabled = 'style="pointer-events: none; display: inline-block;opacity: 0.3;"'; 
              ?>
              <?php $disabled = ''; ?>
            <?php } else { ?>
              <?php if ($this->perfil == 1 || $this->perfil == 2 || $this->perfil == 3) { ?>
                <?php $disabled = ''; ?>
              <?php } else { ?>
                <?php $disabled = 'style="pointer-events: none; display: inline-block;opacity: 0.3;"'; ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
          <a <?= $disabled ?> onclick="OpenRegister('<?= $estado ?>', <?= $store->id ?>)">
            <li class="listing clearfix">
              <div class="image_wrapper">
                <img src="<?= base_url() . RECURSOS ?>img/store.svg" alt="store">
              </div>
              <div class="info">
                <span class="store_title"><?= $store->tipo == 0 ? $store->nombre : $store->razonsocial ?></span>
                <span class="store_info"><?= $store->telefono ?> <span>&bull;</span> <?= $store->direccion ?></span>
              </div>
              <span class=" cerrado <?= $estado == 0 ? 'store_open' : 'store_close'; ?>" style="z-index:1">

                <?= $estado == 0 ? '<div class="main-wrapper">
                          <div class="signboard-wrapper">
                            <div class="signboard-open">Abierto</div>
                            <div class="string-open"></div>
                            <div class="pin pin1"></div>
                            <div class="pin pin3"></div>
                            <div class="pin pin4"></div>
                          </div>
                        </div>' : '<div class="main-wrapper">
                          <div class="signboard-wrapper">
                            <div class="signboard">CLOSED</div>
                            <div class="string"></div>
                            <div class="pin pin1"></div>
                            <div class="pin pin2"></div>
                            <div class="pin pin3"></div>
                          </div>
                        </div>' ?>
              </span>
            </li>
          </a>
        <?php } ?>
      </ul>
    </div>
  </div>
<?php } else { ?>
  <?php
  $dataCaja = $this->Controlador_model->get($this->caja, "caja");
  $dataUsuario = $this->Controlador_model->get($dataCaja->usuario, "usuario");
  ?>
  <input id="tresPasos" type="hidden" value="<?= $empresa->pasos ?>">
  <ul class="cbp-vimenu" id="opcionmenu"></ul>
  <ul class="cbp-vimenu" id="opcionmenuPedidoVenta" style="display:none">
    <li data-toggle="tooltip" data-html="true" data-placement="left" title="Regreso">
      <a onclick="salirPedidoVenta()" id="botonSalirVenta"><i class="fa fa-arrow-left" style="font-size:35px" aria-hidden="true"></i></a>
    </li>
  </ul>
  <?php 
  $dataPerfil = $this->Controlador_model->get($this->perfil, "perfil");
  $seperar = explode(" ", $dataCaja->apertura);
  $diassemana = ["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];
  $meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
  ?>
  <?php if ($dataPerfil->cobradorcaja === '1') { ?>
    <?php $datosCaja = $this->Controlador_model->get($this->caja, "caja"); ?>
    <input id="cobradorCaja" value="1" type="hidden">
    <div id="contenedorPedidosVenta" class="container" style="margin-top:55px; margin-right:55px">
      <div class="row">
        <div class="col-lg-12">
          <div class="panel panel-border panel-border-default">
            <div class="panel-heading">
              <h3 class="panel-title text-title-panel clearfix">
                PEDIDOS DE CAJA: <?= $datosCaja->descripcion ?>
                <div class="pull-right">
                  <a onclick="datosPedidosEnviados()" class="btn btn-warning" data-toggle="tooltip">BUSCAR PEDIDOS RECIENTES <i class="fa fa-search"></i></a>
                </div>
              </h3>
            </div>
            <!-- /.box-header -->
            <div class="panel-body table-responsive">
              <table id="tabla_pedidosEnviados" class="table table-bordered table-striped">
                <thead>
                  <tr class="text-title-panel">
                    <th>#</th>
                    <th>Referencia</th>
                    <th>Vendedor</th>
                    <th>Monto</th>
                    <th>Fecha/Hora</th>
                    <th>Cobrar</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
            <!-- /.box -->
          </div>
        </div>
      </div>
    </div>

    <div class="cajon" id="contenedorProcesoPago" style="display:none">

      <div class="row">
        <div class="col-lg-8 col-md-8">
          <label class="label label-default" style="font-size: 12px;display: flex;justify-content: space-around;margin: 0px;padding: 3px;border-radius: 0px 3px;">
            <span><?= $dataCaja->descripcion ?></span>
            <span>ENCARGADO: <?= strtoupper($dataUsuario->usuario) ?></span>
            <span>APERTURADO: <?= strtoupper($diassemana[date("w", strtotime($seperar[0]))])." ".date("d", strtotime($seperar[0]))." DE ".strtoupper($meses[date("n", strtotime($seperar[0])) - 1]).". HORA: ".date("g:i A", strtotime($seperar[1])); ?></span>
          </label>
          <div class="panel panel-border-default CategriaSeleccionar" id="izquierda" style="height: 82vh;overflow:auto;margin: 0px;">
            <div class="panel-heading">
              <h3 class="panel-title text-title-panel">
                <a onclick="datosProductosVenta()" class="btn btn-warning btn-sm waves-effect waves-light " data-toggle="tooltip" style="background:#ffc107; color:#212529">Recargar productos <i class="fa fa-retweet" style="font-size:16px;"></i></a>
              </h3>
            </div>
            <div class="panel-body table-responsive">
              <table id="tableDataProductos" class="table table-striped table-bordered">
                <thead>
                  <tr class="text-title-panel">
                    <th>#</th>
                    <th>Nombre</th>
                    <th>C. Barra</th>
                    <th>Categoria</th>
                    <th>Precio</th>
                    <th> <span style="padding-right:50px">Acciones BTN</span> </th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
          <label class="label label-default" style="font-size: 15px;display: flex;justify-content: space-around;margin: 0px;padding: 3px;border-radius: 3px 0px;">
            <span>PUCALLSYSTEM</span>
          </label>
        </div>
        <div class="col-lg-4 col-md-4" id="derecha">
          <!-- ............................. -->
          <div class="row">
            <div class="col-lg-12" style="margin:10px">
              <input id="ventaseleccionada" type="hidden">
              <form action="" class="form-horizontal" id="form_principal" method="POST" role="form">
                <div class="row" id="content-referencia" style="display:none">
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="referencia" id="referencia" placeholder="Referencia..." autocomplete="off">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12" style="margin-bottom:10px">
                    <div class="input-group">
                      <span class="input-group-addon" id="busquedacodigobarra">
                        <i class="fa fa-search"></i>
                      </span>
                      <input autocomplete="off" type="text" id="codigodebarra" name="codigodebarra" class="form-control" placeholder="Codigo de barra">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12" style="margin-bottom:10px">
                    <div class="input-group">
                      <input type="text" class="form-control limpiar" placeholder="CLIENTE" name="clientes" id="clientes">
                      <span class="help-block"></span>
                      <input type="hidden" class="form-control" name="cliente" id="cliente">
                      <span class="input-group-btn">
                        <button type="button" class="btn waves-effect waves-light btn-primary" onclick="grabarcliente()"><i class="fa fa-user-plus"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-xs-12">
                    <select id="tipoventa" name="tipoventa" class="form-control" onchange="save()">
                      <option value="OTROS">TICKET</option>
                      <option value="BOLETA">BOLETA</option>
                      <option value="FACTURA">FACTURA</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- // ? Esto es la parte CENTER -->
          <div class="row partecenter">
            <div class="row">
              <div class="col-lg-12" id="pedidosventa">
              </div>
            </div>
          </div>
          <!-- ............................. -->
          <!-- // ? Esto es la parte BOTTOM -->
          <div class="row">
            <div class="col-lg-12">
              <table class="table">
                <tr>
                  <td class="active" width="40%" style="font-weight: bold;">Totales</td>
                  <td class="whiteBg" width="60%">
                    <span class="float-left">
                      <span id="ItemsNumVenta"><span></span> Item</span>
                    </span>
                    <span id="SubtotVenta"></span>
                  </td>
                </tr>
              </table>
              <button type="button" id="ProcesarVenta" class="btn btn-green col-xs-12 col-md-12" onclick="grabar()">PROCESAR VENTA</button>
            </div>
          </div>
          <!-- ............................. -->
        </div>
      </div>
    </div>

  <?php } else {  ?>
    <input id="cobradorCaja" value="0" type="hidden">
    <div class="cajon">
      <div class="row">
        <div class="col-lg-8 col-md-8" id="izquierda">
          <label class="label label-default" style="font-size: 12px;display: flex;justify-content: space-around;margin: 0px;padding: 3px;border-radius: 0px 3px;">
            <span><?= $dataCaja->descripcion ?></span>
            <span>ENCARGADO: <?= strtoupper($dataUsuario->usuario) ?></span>
            <span>APERTURADO: <?= strtoupper($diassemana[date("w", strtotime($seperar[0]))])." ".date("d", strtotime($seperar[0]))." DE ".strtoupper($meses[date("n", strtotime($seperar[0])) - 1]).". HORA: ".date("g:i A", strtotime($seperar[1])); ?></span>
          </label>
          <div class="panel panel-border-default CategriaSeleccionar" style="height: 82vh;overflow:auto;margin: 0px;">
            <div class="panel-heading">
              <h3 class="panel-title text-title-panel">
                <a onclick="datosProductosVenta()" class="btn btn-warning btn-sm waves-effect waves-light " data-toggle="tooltip" style="background:#ffc107; color:#212529">Recargar productos <i class="fa fa-retweet" style="font-size:16px;"></i></a>
              </h3>
            </div>
            <div class="panel-body table-responsive">
              <table id="tableDataProductos" class="table table-striped table-bordered">
                <thead>
                  <tr class="text-title-panel">
                    <th>#</th>
                    <th>Nombre</th>
                    <th>C. Barra</th>
                    <th>Categoria</th>
                    <th>Precio</th>
                    <th> <span style="padding-right:50px">Acciones BTN</span> </th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
          <label class="label label-default" style="font-size: 15px;display: flex;justify-content: space-around;margin: 0px;padding: 3px;border-radius: 3px 0px;">
            <span>PUCALLSYSTEM</span>
          </label>
        </div>
        <div class="col-lg-4 col-md-4" id="derecha">
          <div class="row">
            <div class="col-lg-12" style="margin:10px">
              <input id="ventaseleccionada" type="hidden">
              <div class="row">
                <div class="col-lg-2 col-md-2 col-sm-2">
                  <button type="button" id="eliminarventa" class="categories" id="eliminarventas" onclick="ELiminarVenta()"><i class="fa fa-minus"></i></button>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-8">
                  <div class="row-horizon" id="ventaspendientes">
                  </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2">
                  <button id="agregarnewventa" class="categories" onclick="agregarnewventa()"><i class="fa fa-plus"></i></button>
                </div>
              </div>
              <br>
              <form action="" class="form-horizontal" id="form_principal" method="POST" role="form">
                <div class="row">
                  <div class="col-sm-12" style="margin-bottom:10px">
                    <div class="input-group">
                      <span class="input-group-addon" id="busquedacodigobarra">
                        <i class="fa fa-search"></i>
                      </span>
                      <input autocomplete="off" type="text" id="codigodebarra" name="codigodebarra" class="form-control" placeholder="Codigo de barra">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12" style="margin-bottom:10px">
                    <div class="input-group">
                      <input type="text" class="form-control limpiar" placeholder="CLIENTE" name="clientes" id="clientes">
                      <span class="help-block"></span>
                      <input type="hidden" class="form-control" name="cliente" id="cliente">
                      <span class="input-group-btn">
                        <button type="button" class="btn waves-effect waves-light btn-primary" onclick="grabarcliente()"><i class="fa fa-user-plus"></i></button>
                      </span>
                    </div>
                  </div>
                </div>
                <div class="row" id="content-referencia" style="display:none">
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="referencia" id="referencia" placeholder="Referencia..." autocomplete="off">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12 col-xs-12">
                    <select id="tipoventa" name="tipoventa" class="form-control" onchange="save()">
                      <option value="OTROS">TICKET</option>
                      <option value="BOLETA">BOLETA</option>
                      <option value="FACTURA">FACTURA</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
              </form>
            </div>
          </div>
          <!-- // ? Esto es la parte CENTER -->
          <div class="row partecenter">
            <div class="row">
              <div class="col-lg-12" id="pedidosventa">
              </div>
            </div>
          </div>
          <!-- ............................. -->
          <!-- // ? Esto es la parte BOTTOM -->
          <div class="row">
            <div class="col-lg-12">
              <table class="table">
                <tr>
                  <td class="active" width="40%" style="font-weight: bold;">Totales</td>
                  <td class="whiteBg" width="60%">
                    <span class="float-left">
                      <span id="ItemsNumVenta"><span></span> Item</span>
                    </span>
                    <span id="SubtotVenta"></span>
                  </td>
                </tr>
              </table>
              <button type="button" id="ProcesarVenta" class="btn btn-green col-xs-12 col-md-12" onclick="grabar()">PROCESAR VENTA</button>
            </div>
          </div>
          <!-- ............................. -->
        </div>
      </div>


    </div>
  <?php } ?>
<?php } ?>

<!-- Modal Cash in Hand -->
<div class="modal fade" id="CashinHand" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">ABRIR CAJA</h4>
      </div>
      <form id="cachIH" autocomplete="off">
        <div class="modal-body">
          <div class="form-group">
            <label for="CashinHand">Fecha</label>
            <input type="date" class="form-control" value="<?= date('Y-m-d') ?>" readonly>
          </div>
          <div class="form-group">
            <label for="CashinHand">Saldo Inicial</label>
            <input type="text" class="form-control money" name="saldoinicial" id="saldoinicial" value="0">
            <input type="hidden" name="empresaIH" id="empresaIH">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
          <button id="aperturar" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" type="submit" class="btn btn-primary">ABRIR</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->


<div class="modal fade" id="comprobante" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" id="modal-dataVenta">

      </div>
      <div class="modal-footer" id="modal-fotter-cerrar">
      </div>
    </div>
  </div>
</div>


<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>

      <div class="modal-body">

      </div>

    </div>
  </div>
</div>
<!-- Modal options -->

<div class="modal fade" id="options" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" id="ticketModal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" id="modal-body">
        <form action="#" role="form" id="form_options" autocomplete="off">
          <div class="col-md-12">
            <input type="hidden" class="form-control" name="CorrelativoIdentif" id="CorrelativoIdentif">
            <input type="hidden" class="form-control" name="CantidadModalOpcion" id="CantidadModalOpcion">
            <input type="hidden" class="form-control" name="precioModalOpcion" id="precioModalOpcion">
            <input type="hidden" class="form-control" name="VentaModalOpcion" id="VentaModalOpcion">
            <input type="hidden" class="form-control" name="detalle" id="detalle">
            <textarea class="form-control" name="opcion" id="opcion"></textarea>
            <span class="help-block"></span>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default hiddenpr" data-dismiss="modal">CERRAR</button>
        <button type="submit" class="btn btn-add" onclick="addPoptions()">GRABAR</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal -->
<div class="modal fade" id="AddSale" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <form id="form_vender" action="" method="post" role="form" autocomplete="off">
        <div class="modal-body">
          <div class="form-group">
            <div class="row">
              <div class="col-lg-12">
                <h2 id="customerName" style="line-height:1">Cliente : <span></span></h2>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <h4 id="MontoPagar2">Total: <span></span></h4>
              </div>
              <div class="col-lg-6">
                <h4 id="ItemsNum2">items: <span></span></h4>
              </div>
            </div>

          </div>
          <div class="form-group">
            <h2 id="TotalModal"></h2>
          </div>
          <div class="form-group">
            <label for="paymentMethod">Forma de Pago:</label>
            <select class="form-control" name="formapago" id="formapago">
              <option value="CONTADO">CONTADO</option>
              <option value="CREDITO">CREDITO</option>
            </select>
          </div>
          <div class="form-group" id="metodo">
            <label for="paymentMethod">Metodo de Pago:</label>
            <select class="form-control" name="metodopago" id="metodopago">
              <option value="EFECTIVO">EFECTIVO</option>
              <option value="TARJETA">TARJETA</option>
            </select>
          </div>
          <div class="form-group" id="tipocard">
            <label for="tipotarjeta">Tipo Tarjetas</label>
            <i class="fa fa-cc-visa fa-2x" id="visa" aria-hidden="true"></i>
            <i class="fa fa-cc-mastercard fa-2x" id="mastercard" aria-hidden="true"></i>
            <i class="fa fa-cc-amex fa-2x" id="amex" aria-hidden="true"></i>
            <i class="fa fa-cc-discover fa-2x" id="discover" aria-hidden="true"></i>
            <select class="form-control" name="tipotarjeta" id="tipotarjeta">
              <option value="VISA">VISA</option>
              <option value="DISCOVER">DISCOVER</option>
              <option value="MASTERCARD">MASTERCARD</option>
              <option value="DINERS CLUB">DINERS CLUB</option>
              <option value="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
            </select>
          </div>
          <div class="form-group" id="descontado">
            <label>Descuento</label>
            <input type="text" class="form-control money" id="descuento" name="descuento" value="0">
            <span class="help-block"></span>
          </div>
          <div class="form-group" id="pagado">
            <label>Pagado</label>
            <input type="text" value="0" name="pago" class="form-control money" id="pago">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label>Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" readonly>
            <span class="help-block"></span>
          </div>
          <div class="form-group" id="vencimiento">
            <label>Vence</label>
            <input type="date" class="form-control" id="vence" name="vence" value="<?= date('Y-m-d') ?>">
            <span class="help-block"></span>
          </div>
          <div class="form-group ReturnChange">
            <h3 id="ReturnChange">Vuelto <span>0</span> Soles</h3>
          </div>
        </div>
        <div class="modal-footer">
          <!-- <i class='fa fa-spinner fa-spin'></i> -->
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" id="vendiendo" class="btn btn-add" onclick="saleBtn()">Procesar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal add user -->
<div class="modal fade" id="cliente_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <form action="" autocomplete="off" method="POST" id="form_cliente" role="form">
        <div class="modal-body">
          <div class="form-group">
            <label for="tipo">Tipo<span class="required">*</span></label>
            <select id="tipo" name="tipo" class="form-control">
              <option value="DNI">DNI</option>
              <option value="RUC">RUC</option>
            </select>
          </div>
          <div class="form-group">
            <label for="documento">Documento<span class="required">*</span></label>
            <div class="input-group">
              <input class="form-control" id="documento" type="number" name="documento">
              <span class="help-block"></span>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                  <span class="fa fa-search"></span>
                </button>
              </span>
            </div>
          </div>
          <div class="form-group">
            <label for="nombre">Nombre<span class="required">*</span></label>
            <input class="form-control" id="nombre" type="text" name="nombre">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="apellido">Apellido</label>
            <input class="form-control" id="apellido" type="text" name="apellido">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="direccion">Direccion</label>
            <input class="form-control" id="direccion" type="text" name="direccion">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="telefono">Telefono</label>
            <input class="form-control" id="telefono" type="number" name="telefono">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input class="form-control" id="email" type="email" name="email">
            <span class="help-block"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
          <button type="button" id="btnSavecliente" onclick="savecliente()" class="btn btn-primary">GUARDAR</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="cliente_form2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <form action="" autocomplete="off" method="POST" id="form_cliente2" role="form">
        <div class="modal-body">
          <div class="form-group">
            <label for="tipo2">Tipo<span class="required">*</span></label>
            <select id="tipo2" name="tipo2" class="form-control">
              <option value="DNI">DNI</option>
              <option value="RUC">RUC</option>
            </select>
          </div>
          <div class="form-group">
            <label for="documento2">Documento<span class="required">*</span></label>
            <div class="input-group">
              <input class="form-control" id="documento2" type="text" name="documento2">
              <span class="help-block"></span>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito2">
                  <span class="fa fa-search"></span>
                </button>
              </span>
            </div>
          </div>
          <div class="form-group">
            <label for="nombre2">Nombre<span class="required">*</span></label>
            <input class="form-control" id="nombre2" type="text" name="nombre2">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="apellido2">Apellido</label>
            <input class="form-control" id="apellido2" type="text" name="apellido2">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="direccion2">Direccion</label>
            <input class="form-control" id="direccion2" type="text" name="direccion2">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="telefono2">Telefono</label>
            <input class="form-control" id="telefono2" type="text" name="telefono2">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="email2">Email</label>
            <input class="form-control" id="email2" type="email" name="email2">
            <span class="help-block"></span>
          </div>
        </div>
        <div class="modal-footer" id="divfooter">

        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal close register -->
<div class="modal fade" id="CloseRegister" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body">
        <form action="#" role="form" id="form_cierre" autocomplete="off">
          <div id="closeregsection"></div>
        </form>
      </div>
      <div class="modal-footer">
        <a id="cerrarcaja" style="width:100%" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" onclick="SubmitRegister()" class="btn btn-red">CERRAR CAJA</a>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal Variante -->
<div class="modal fade" id="addProcesoVE" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="varianteseleccionada">
        <div class="row">
          <div class="text-center" style="padding:5px; margin:5px; display:none" id="MensajeVariante">
          </div>
          <div id="ColumnaVariante">
            <div class='panel panel-border panel-info' style='margin-bottom:5px'>
              <div class='panel-heading'>
                <h3 class='panel-title'>Variantes</h3>
              </div>
              <div class='panel-body scrollestilos' id="listaVariante" style="height: 50vh; overflow-y:auto; text-align:center; padding:0px">

              </div>
            </div>
          </div>
          <div id="ColumnaExtra">
            <div class='panel panel-border panel-success' style='margin-bottom:5px'>
              <div class='panel-heading'>
                <h3 class='panel-title'>Extras</h3>
              </div>
              <div class='panel-body scrollestilos' id="listExtras" style="height: 50vh; overflow-y:auto; text-align:center; padding:0px">
              </div>
            </div>
          </div>
          <input type="hidden" id="loteseleccionado">
          <div id="ColumnaLotes">
            <div class='panel panel-border panel-default' style='margin-bottom:5px'>
              <div class='panel-heading'>
                <h3 class='panel-title'>Lotes</h3>
              </div>
              <div class='panel-body scrollestilos' id="listLotes" style="height: 50vh; overflow-y:auto; text-align:center; padding:0px">
              </div>
            </div>
          </div>

        </div>
        <div class="row">
          <div class="col-lg-12 text-right" id="ProcesoAddVE">

          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal Producto Libre -->
<div class="modal fade" id="addproductolibre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Crear producto libre</h4>
        <hr>
      </div>
      <form action="" autocomplete="off" method="POST" id="form_libre" role="form">
        <div class="modal-body">
          <div class="form-group">
            <label for="nombre">Nombre<span class="required">*</span></label>
            <input class="form-control libre" id="nombre" type="text" name="nombre" required>
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label for="apellido">Precio</label>
            <input class="form-control" id="precioventa" type="text" name="precioventa" required>
            <span class="help-block"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
          <button type="button" id="btnSaveLibre" onclick="saveLibre()" class="btn btn-primary">GRABAR</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal add user -->
<div class="modal fade" id="stockactual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" id="datastockactual">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal mostrar imagen -->
<div class="modal fade" id="verfotoactual" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" id="datasfotoactual">

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
      </div>
    </div>
  </div>
</div>