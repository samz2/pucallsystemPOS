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

    .iniciotiempo {
        display: block;
        margin-bottom: 3px;
        background: #2b2a2a7a
    }
    .btn-limpiar {
        background:#ffc107;
        color:#212529 !important;
        font-weight:bold;
        width: 100% !important;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;

    }
    .titlemesa {
        background: #2b2a2a7a;
        color: #fff;
        padding: 0px 0px;
        font-size: 11px;
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

    .content-spinner {
        font-size: 40px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #00474e;
        z-index: 2;
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
        height: 40vh;
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
        <div class="row">
            <h1 class="text-center choose_store">ELIGE UNA TIENDA PARA ALQUILAR</h1>
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
    <ul class="cbp-vimenu" id="opcionmenu"></ul>

    <ul class="cbp-vimenu" id="opcionmenumesa" style="display:none">
        <li data-toggle="tooltip" data-html="true" data-placement="left" title="Regreso">
            <a onclick="salirdatamesa()" id="botonSalirMesa"><i class="fa fa-arrow-left" style="font-size:35px" aria-hidden="true"></i></a>
        </li>
    </ul>

    <input id="ventaseleccionada" type="hidden">
    <input id="tipozona" type="hidden">
    <input id="idzona" type="hidden">

    <div class="container" style="margin-top: 55px; margin-right:25px" id="contenedor-mesa">

    </div>

    <div class="container" style="margin-top: 55px; margin-right:25px; display: none;" id="datos-mesa">
        <div class="row">
            <div class="col-lg-8 col-md-8" id="izquierda">
                <div class="row" id="topbuscador">
                    <div class="col-sm-6">
                        <div id="searchContaner">
                            <div class="input-group stylish-input-group">
                                <input type="text" id="searchProd" class="form-control" placeholder="Buscar" autocomplete="off">
                                <span class="input-group-addon">
                                    <button type="submit"><span class="glyphicon glyphicon-search"></span></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6 text-right" id="retrocederFila">
                        <button class="btn btn-amarillo col-xs-12 col-md-12" style="margin:5px 0px;" onclick="retroceder()">Regresar</button>
                    </div>
                </div>
                <div id="CategriaSeleccionar" class="CategriaSeleccionar" style="height:80vh; overflow:auto">
                </div>
                <div id="Categoriaproducto" style="height:79vh; overflow-y:auto; overflow-x:hidden">
                </div>
                <div id="CategoriaproductoTodos" class="CategriaSeleccionar" style="display:none; height:80vh; overflow:auto">
                </div>
            </div>
            <div class="col-lg-4 col-md-4" id="derecha">
                <div class="row">
                    <div class="col-lg-12" style="margin:10px">
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
                                        <input type="text" class="form-control limpiar" placeholder="BUSCAR CLIENTE..." name="clientes" id="clientes" autocomplete="off">
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
                <div class="row partecenter">
                    <div class="row">
                        <div class="col-lg-12" id="pedidosventa">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table">
                            <tr>
                                <td class="active" width="40%" style="font-weight: bold;">Totales</td>
                                <td class="whiteBg" width="60%">
                                    <span class="float-left">
                                        <span id="ItemsNumVenta"><span></span> Item</span>
                                    </span>
                                    <span id="SubtotVenta" class="pull-right"></span>
                                    <div class="clearfix"></div>
                                </td>
                            </tr>
                        </table>
                        <button type="button" id="ProcesarVenta" class="btn btn-green col-xs-12 col-md-12" onclick="grabar()">PROCESAR VENTA</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="dataventadetalle"></div>
                <div class="col-sm-8" id="SendMail">
                </div>
                <div class="col-sm-4" id="SendWP">
                </div>
            </div>
            <div class="modal-footer" id="fotter-cerrar">
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
                    <!--
          <div class="form-group" id="numberoperacion">
            <label>Número de operacion</label>
            <input type="text" name="operacion" class="form-control" id="operacion">
            <span class="help-block"></span>
          </div>
          -->
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>">
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
                    <button type="button" id="btnSavecliente" onclick="savecliente()" class="btn btn-primary">GRABAR</button>
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
<div class="modal fade" id="CloseRegister" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cerrar&nbsp;registrarse</h4>
            </div>
            <div class="modal-body">
                <form action="#" role="form" id="form_cierre" autocomplete="off">
                    <div id="closeregsection"></div>
                </form>
            </div>
            <div class="modal-footer">
                <a id="cerrarcaja" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" onclick="SubmitRegister()" class="btn btn-red col-xs-12 col-md-12 flat-box-btn">Cerrar&nbsp;registrarse</a>
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

<script type="text/javascript">
    $(document).ready(function() {
        var clientesCD = [];
        ProductosArr = [];
        <?php if ($this->caja) { ?>
            $("#contenedor-mesa").load("<?= $this->url ?>/load_zonaMesa");
            categoriaSeleccionar(); //todo: trae todas las categorias
            opcionesmenu();
            $("#CategoriaproductoTodos").load("<?= $this->url ?>/ajax_TodosProductos"); //todo: con esto funciona el buscador
        <?php } ?>
        var down = false;
        var scrollLeft = 0;
        var x = 0;
        $('#categoriasP').mousedown(function(e) {
            down = true;
            scrollLeft = this.scrollLeft;
            x = e.clientX;
        }).mouseup(function() {
            down = false;
        }).mousemove(function(e) {
            if (down) {
                this.scrollLeft = scrollLeft + x - e.clientX;
            }
        }).mouseleave(function() {
            down = false;
        });

        $(".limpiar").addClear();
        $('.money').number(true, 2);

        $("#clientes").autocomplete({
            source: "<?= $this->url ?>/autocompletar",
            minLength: 2,
            select: function(event, ui) {
                $("#cliente").val(ui.item.cliente);
                save();
            }
        });

        $("#tipoventa").change(() => {
            $("#tipoventa").parent().removeClass("has-error");
            $("#tipoventa").next().empty();
        });

        $("#codigodebarra").focus();

        /*
        $("#codigodebarra").autocomplete({
          source: "<?= $this->url ?>/autocompleteCodigoBarra",
          minLength: 2,
          select: function(event, ui) {
            if (ui.item.status) {
              $("#busquedacodigobarra").html(`<i class="fa fa-spin fa-spinner"></i>`);
              agregaarventa(ui.item.idproducto, ui.item.idcategoria, ui.item.precioproducto)
            } else {}

          }
        });
        */

        $("#codigodebarra").keypress(function() {
            let datacodigobarra = $("#codigodebarra").val();
            if (datacodigobarra.length > 1) {
                $("#busquedacodigobarra").html(`<i class="fa fa-spin fa-spinner"></i>`);
                $.ajax({
                    url: "<?= $this->url ?>/ajax_codigodebarra",
                    type: "POST",
                    data: {
                        codigodebarra: datacodigobarra,
                    },
                    dataType: "JSON",
                    success: function(data) {
                        if (data.consulta.status) {
                            if (data.lote.status == "1") {
                                if (data.lote.totalotes > 1) {
                                    //? MODAL
                                    agregarAdicionales(data.consulta.idproducto);
                                } else {
                                    //? REGISTRO DIRECTO DE LOTE
                                    agregaarventa(data.consulta.idproducto, data.consulta.precioproducto, {
                                        "statusvariante": false,
                                        "lote": data.lote.lote,
                                        "statuslote": true
                                    }, "");
                                }
                            } else {
                                agregaarventa(data.consulta.idproducto, data.consulta.precioproducto, {
                                    "statuslote": false,
                                    "lote": false,
                                    "statusvariante": false
                                }, "");
                            }
                        } else {
                            if (data.consulta.msg != "") {
                                Lobibox.alert("info", {
                                    title: "Informacion",
                                    msg: data.consulta.msg
                                })
                            }
                            $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
                            $("#codigodebarra").val("");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'Codigo de error: codigodebarra. comunicarse con el administrador'
                        });
                    }
                });
            } else {
                $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
            }
        });

        $("#formapago").change(() => {
            if ($('#formapago').val() == 'CONTADO') {
                $('#metodo').show();
                $('#pagado').show();
                $("#descontado").show();
                $("#vencimiento").hide();
                $('.ReturnChange').show();
            } else {
                $('#metodo').hide();
                $('#pagado').hide();
                $("#descontado").hide();
                $("#vencimiento").show();
                $('.ReturnChange').hide();
            }
        })

        $('#descuento').on('keyup', function() {
            var change = parseFloat($('#MontoPagar2 span').text()) - parseFloat($(this).val());
            if (change > 0) {
                $('#pago').val(change.toFixed(2));
                $('#pago').removeClass("red");
                $('#pago').addClass("light-blue");
            } else {
                $('#pago').val('0.00');
                $('#pago').addClass("red");
                $('#pago').removeClass("light-blue");
            }
        });

        $('#pago').on('keyup', function() {
            var change = parseFloat($(this).val()) - (parseFloat($('#MontoPagar2 span').text()) - parseFloat($('#descuento').val()));
            if (change > 0) {
                $('#ReturnChange span').text(change.toFixed(2));
                $('#ReturnChange span').removeClass("red");
                $('#ReturnChange span').addClass("light-blue");
            } else {
                $('#ReturnChange span').text('0.00');
                $('#ReturnChange span').addClass("red");
                $('#ReturnChange span').removeClass("light-blue");
            }
        });

        $("#searchProd").keyup(function() {
            let valor = $("#searchProd").val();
            if (valor == "") {
                $("#CategriaSeleccionar").show();
                $("#Categoriaproducto").hide();
                $("#CategoriaproductoTodos").hide();
            } else {
                $("#CategriaSeleccionar").hide();
                $("#Categoriaproducto").hide();
                $("#CategoriaproductoTodos").show();
                //! Busquedad
                var filter = $(this).val();
                $("#CategoriaproductoTodos #proname").each(function() {
                    // If the list item does not contain the text phrase fade it out
                    if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                        // Show the list item if the phrase matches
                        $(this).parent().parent().parent().hide();
                    } else {
                        $(this).parent().parent().parent().show();
                    }
                });
            }
        });

        $('#documento').attr('minLength', 8);
        $('#documento').attr('maxlength', 8);
        $("#nombre").mayusculassintildes();
        $(".libre").mayusculassintildes();
        $("#apellido").mayusculassintildes();
        $("#direccion").mayusculassintildes();
        $('#documento').numeric();
        $("#documento").prop("placeholder", "BUSCAR POR RENIEC");
        $('#tipo').change(function(e) {
            $("#documento").val("");
            $("#nombre").val("");
            $("#apellido").val("");
            $("#direccion").val("");
            $("#telefono").val("");
            $("#email").val("");
            if ($('#tipo').val() == 'DNI') { //muestro el div1 y oculto los demas
                $('#documento').attr('minLength', 8);
                $('#documento').attr('maxlength', 8);
                $('#nombre').siblings('label').text("Nombre");
                $('#apellido').parent().show();
                $("#documento").prop("placeholder", "BUSCAR POR RENIEC");
            } else if ($('#tipo').val() == 'RUC') { //muestro el div2 y oculto los demas
                $('#documento').attr('minLength', 11);
                $('#documento').attr('maxlength', 11);
                $('#nombre').siblings('label').text("Razón social");
                $('#apellido').parent().hide();
                $("#documento").prop("placeholder", "BUSCAR POR SUNAT");
            }
        });

        $('#cachIH').submit(function(event) {
            event.preventDefault();
            $('#aperturar').button('loading');
            $.ajax({
                url: "<?= $this->url ?>/openregister",
                type: "POST",
                data: {
                    saldoinicial: $('#saldoinicial').val(),
                    empresa: $("#empresaIH").val()
                },
                success: function(data) {
                    window.location.href = "<?= $this->url ?>/aperturar/" + $("#empresaIH").val();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#aperturar').button('reset');
                    toast.error("Algo inesperado ha sucedido");
                    // Lobibox.notify('error', {
                    //   size: 'mini',
                    //   position: 'top center',
                    //   msg: 'Error al obtener datos de ajax.'
                    // });
                }
            });
        });


        $('#botoncito').on('click', function() {
            $(this).button('loading');
            let tipo = $("#tipo").val() == "DNI" ? "dni" : "ruc";
            $.ajax({
                method: 'GET',
                url: `https://apiperu.dev/api/${tipo}/${$('#documento').val()}?api_token=7460d2fa0d1d01c5fe9c96448ea0c3a1d800bae62461f6c27bfd48914e466e14`,
                beforeSend: function() {
                    $('[name="nombre"]').val("");
                    $('[name="direccion"]').val("");
                    $('[name="apellido"]').val("");
                },
                success: function(data) {
                    $('#botoncito').button('reset');
                    if (data.success === true) {
                        if ($("#tipo").val() == "DNI") {
                            $("#nombre").val(data.data.nombres);
                            $("#apellido").val(data.data.apellido_paterno + " " + data.data.apellido_materno);
                        } else {
                            $("#nombre").val(data.data.nombre_o_razon_social);
                            $("#direccion").val(data.data.direccion_completa);
                        }
                    } else {
                        let dataMSG = $("#tipo").val() == "DNI" ? "El DNI EXISTE" : "El RUC NO EXISTE";
                        Lobibox.notify('warning', {
                            size: 'mini',
                            position: "top right",
                            msg: dataMSG
                        });
                    }
                },
                error: function(data) {
                    $('#botoncito').button('reset');
                    Lobibox.notify('error', {
                        size: 'mini',
                        position: "top right",
                        msg: 'Error al obtener datos de ajax.'
                    });
                }
            });


        });

        $('#botoncito2').on('click', function() {
            $(this).button('loading');
            $.ajax({
                method: 'post',
                url: "<?= $this->url ?>/consulta_reniec",
                data: {
                    tipo: $('#tipo2').val(),
                    documento: $('#documento2').val()
                },
                beforeSend: function() {
                    $('[name="nombre2"]').val("");
                    $('[name="apellido2"]').val("");
                    $('[name="direccion2"]').val("");
                },
                success: function(data) {
                    $('#botoncito2').button('reset');
                    nombre = data.nombre;
                    apellido = data.apellido;
                    direccion = data.direccion;
                    $('[name="nombre2"]').val(nombre);
                    $('[name="apellido2"]').val(apellido);
                    $('[name="direccion2"]').val(direccion);
                },
                error: function(data) {
                    $('#botoncito2').button('reset');
                    //toast.error("Algo inesperado ha sucedido");
                    Lobibox.notify('warning', {
                        size: 'mini',
                        position: "top right",
                        msg: 'Nuestros servidores estan en mantenimiento.'
                    });
                }
            });
        });


    });

    function OpenRegister(estado, tienda) {
        if (estado == 1) {
            $("#empresaIH").val(tienda);
            $('#CashinHand').modal('show');
        } else {
            window.location.href = "<?= $this->url ?>/aperturar/" + tienda;
        }
    };



    function grabarcliente() {
        $('#cliente_form').modal('show');
        $('.modal-title').text('CREAR CLIENTE');
    };

    function savecliente() {
        $('#btnSavecliente').text('guardando...'); //change button text
        $('#btnSavecliente').attr('disabled', true); //set button disable
        // ajax adding data to database
        $.ajax({
            url: '<?= $this->url ?>/ajax_addcliente',
            type: "POST",
            data: $('#form_cliente').serialize(),
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                if (data.status) {
                    $('#cliente_form').modal('hide');
                    $('#form_cliente')[0].reset();
                    $("#cliente").val(data.cliente);
                    $("#clientes").val(data.clientes);
                    //toast.success("El registro fue creado exitosamente");
                    save();
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: 'El registro fue creado exitosamente.'
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSavecliente').text('GRABAR'); //change button text
                $('#btnSavecliente').attr('disabled', false); //set button enable
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toast.error("Algo inesperado ha sucedido");
                // Lobibox.notify('error', {
                //   size: 'mini',
                //   msg: 'El registro no se pudo crear verifique las validaciones.'
                // });
                $('#btnSavecliente').text('GRABAR'); //change button text
                $('#btnSavecliente').attr('disabled', false); //set button enable
            }
        });
    };

    function cambiarproceso(tipoproceso) {
        $("#tipoproceso").val(tipoproceso);
        if (tipoproceso == "1") {
            document.getElementById("montototalcaja").focus();
        } else {}

    }


    function savecliente2(identificador, idventa) {
        $('#btnSavecliente2').text('guardando...'); //change button text
        $('#btnSavecliente2').attr('disabled', true); //set button disable
        // ajax adding data to database
        $.ajax({
            url: '<?= $this->url ?>/ajax_addcliente2',
            type: "POST",
            data: $('#form_cliente2').serialize(),
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                if (data.status) {
                    $('#cliente_form2').modal('hide');
                    $('#form_cliente2')[0].reset();
                    $(`#cliente${identificador}`).val(data.cliente);
                    $(`#clientes${identificador}`).val(data.clientes);
                    //TODO: Insertando al localStorage
                    let clientesCD = JSON.parse(localStorage.getItem(`cuentadividida_${idventa}`));
                    let dataselect = document.querySelector(`#tipoventa${identificador}`);
                    clientesCD.forEach(function(value, index) {
                        if (value.indice == identificador) {
                            value.cliente = data.cliente;
                            value.textocliente = data.clientes
                        }
                    });
                    localStorage.setItem(`cuentadividida_${idventa}`, JSON.stringify(clientesCD));
                    //TODO: ------END-----
                    //toast.success("El registro fue creado exitosamente");
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: 'El registro fue creado exitosamente.'
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSavecliente2').text('GRABAR'); //change button text
                $('#btnSavecliente2').attr('disabled', false); //set button enable
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toast.error("Algo inesperado ha sucedido");
                // Lobibox.notify('error', {
                //   size: 'mini',
                //   msg: 'El registro no se pudo crear verifique las validaciones.'
                // });
                $('#btnSavecliente2').text('GRABAR'); //change button text
                $('#btnSavecliente2').attr('disabled', false); //set button enable
            }
        });
    };

    function inicio() {
        $.ajax({
            url: "<?= $this->url ?>/ajax_inicio",
            type: "POST",
            success: function(data) {
                if (data.status) {

                }
                location.reload(true);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });
    }

    function comandar() {
        $.ajax({
            url: "<?= $this->url ?>/GenerarPedido",
            type: "POST",
            success: function(data) {
                $('#listaproducto').load("<?= $this->url ?>/listaproducto");
                $('#generarpedido').button('reset');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toast.error("Algo inesperado ha sucedido");
                // Lobibox.notify('error', {
                //   size: 'mini',
                //   msg: 'Error al obtener datos de ajax.'
                // });
                $('#generarpedido').button('reset');
            }
        });
    };

    function delete_posale(id) {
        // ajax delete data to database
        $.ajax({
            url: "<?= $this->url ?>/delete/" + id,
            type: "POST",
            dataType: "JSON",
            success: function(data) {},
            error: function(jqXHR, textStatus, errorThrown) {
                toast.error("Algo inesperado ha sucedido");
                // Lobibox.notify('error', {
                //   size: 'mini',
                //   msg: 'Error al obtener datos de ajax.'
                // });
            }
        });
    };

    //TODO -----------------------------------------------------------------------------------------------------------
    //!                  RECORDAR EN LOCALSTORAGE SE GUARDA LA SIGUIENTE IFORMACION EN EL SIGUIENTE ORDEN           -
    //!                   [IDRODUCTO, CANDTIDA DEL PRODUCTO A LLEVADR, OBSERVACION DEL PRODUCTO/OPCION ]            -
    //TODO -----------------------------------------------------------------------------------------------------------
    function addoptions(producto, venta, correlativo) {
        let dataStorage = JSON.parse(localStorage.getItem(`${venta}_${correlativo}`));
        $('#form_options')[0].reset(); // reset form on modals
        $('[name="detalle"]').val(producto);
        $(`[name="CorrelativoIdentif"]`).val(correlativo);
        $(`[name="VentaModalOpcion"]`).val(venta);
        $('[name="CantidadModalOpcion"]').val(dataStorage[1]);
        $('[name="precioModalOpcion"]').val(dataStorage[3]);
        $('[name="opcion"]').val(dataStorage[2]);
        $('#options').modal('show');
        $('.modal-title').text('AGREGAR OPCION');
    };

    function CantidadProductokeyup(event, key_primary) {

        let TeclaPresionado = event.keyCode || event.which;

        if (TeclaPresionado === 8) {
            let StockRestablecer = $(`#CantidadProductoAnterior-${key_primary}`).val() - ($(`#CantidadProductoActual-${key_primary}`).val() == "" ? 0 : $(`#CantidadProductoActual-${key_primary}`).val());

            console.log("restablecer " + StockRestablecer);

        } else if ((TeclaPresionado >= 48 && TeclaPresionado <= 57) || (TeclaPresionado >= 96 && TeclaPresionado <= 105)) {

            let StockDescontar = $(`#CantidadProductoActual-${key_primary}`).val() - $(`#CantidadProductoAnterior-${key_primary}`).val();
            console.log("Descontar Stock " + StockDescontar)

        } else {

        }


    }

    function CantidadProductoDown(event, key_primary) {

        let TeclaPresionado = event.keyCode || event.which;
        if ((TeclaPresionado >= 48 && TeclaPresionado <= 57) || (TeclaPresionado >= 96 && TeclaPresionado <= 105)) {

            //? guardamos la cantidad en cantidadanteriror antes que sea modificado
            let cantidadactual = $(`#CantidadProductoActual-${key_primary}`).val() == "" ? 0 : $(`#CantidadProductoActual-${key_primary}`).val();
            $(`#CantidadProductoAnterior-${key_primary}`).val(cantidadactual);

        } else if (TeclaPresionado === 8) {
            //? guardamos la cantidad en cantidadanteriror antes que sea eliminado
            let cantidadactual = $(`#CantidadProductoActual-${key_primary}`).val();
            $(`#CantidadProductoAnterior-${key_primary}`).val(cantidadactual);

        }


    }

    function addPoptions() {
        //TODO: En el eleemnto "detalle" va el id del producto
        let opcion = $("#opcion").val();
        let idproducto = $("#detalle").val();
        let idventa = $("#VentaModalOpcion").val();
        let cantidadProducto = $("#CantidadModalOpcion").val();
        let precioModalOpcion = $("#precioModalOpcion").val();
        let correlativo = $("#CorrelativoIdentif").val();
        let DatasStorage = JSON.parse(localStorage.getItem(`${idventa}_${correlativo}`));
        let DataAddStorage = JSON.stringify([idproducto, cantidadProducto, opcion, precioModalOpcion]);
        localStorage.setItem(`${idventa}_${correlativo}`, DataAddStorage);
        let DatasStorageUpdate = JSON.parse(localStorage.getItem(`${idventa}_${correlativo}`));
        $(`#pooptions${idproducto}`).text(DatasStorageUpdate[2]);
        $('#options').modal('hide');
        $('#form_options')[0].reset();
    };

    function saleBtn() {
        let idventa = $("#ventaseleccionada").val();
        $('#vendiendo').html("<i class='fa fa-spinner fa-spin'></i>");
        $('#vendiendo').attr("disabled", true);
        var subtotal = parseFloat($(`#MontoPagar2 span`).text() - $(`#descuento`).val());
        var pago = parseFloat($('#pago').val(), 10);
        if (pago < subtotal) {
            $("#pago").closest('div').addClass("has-error");
            $('#pago').siblings('span').text("Debe colocar un monto mayor o igual que el total");
            $('#vendiendo').html("Enviar");
            $('#vendiendo').attr("disabled", false);
        } else {
            $("#pago").closest('div').removeClass("has-error");
            $('#pago').siblings('span').text("");
            $('#vendiendo').html("<i class='fa fa-spinner fa-spin'></i>");
            $('#vendiendo').attr("disabled", true);
            $('#clientes').val("00000000 | CLIENTES VARIOS | ");
            EnviarPedido(idventa); // TODO: Registra en ventadetalle
        }
    };


    function EnviarPedido(idventa) {
        var DeudaTotal = 0;
        var CantidadItem = 0;
        //TODO:-------------------------------Guardamos los produtos en ventadetalle------------------------------------------------
        $.ajax({
            url: "<?= $this->url ?>/ajax_EnviarPedido",
            type: "POST",
            data: {
                "idventa": idventa,
            },
            dataType: "JSON",
            success: function(data) {
                if (data.proceso.status) {
                    DatosSecundariosPedido(); //TODO: Cuenta todos los datos de la parte de bottom de la venta
                    pagarSoloUno(idventa); //TODO: Cancela la venta
                    $("#addPct").removeClass("productoIsSelected");
                } else {
                    //? Votale en la parte donde esta todas las mesas
                    $("#opcionmenu").show(); //?menu de las mesas
                    $("#opcionmenumesa").hide(); //? menu datos mesa
                    $("#ventaseleccionada").val("");
                    $("#datos-mesa").hide();
                    $("#contenedor-mesa").show();
                    $("#contenedor-mesa").html(data.proceso.dataHtml);
                    $("#AddSale").modal("hide");
                    Lobibox.alert("info", {
                        title: "Informacion",
                        msg: data.proceso.msg,
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("error ajax_EnviarPedido");
            }
        });
    }

    function pagarSoloUno(idventas) {
        $.ajax({
            url: '<?= $this->url ?>/AddNewSale',
            type: "POST",
            data: {
                'formapago': $(`#formapago`).val(),
                'metodopago': $(`[name="metodopago"]`).val(),
                'descuento': parseFloat($(`[name="descuento"]`).val()),
                'pago': parseFloat($(`[name="pago"]`).val()),
                'tipotarjeta': $(`[name="tipotarjeta"]`).val(),
                'operacion': $(`[name="operacion"]`).val(),
                'vence': $(`[name="vence"]`).val(),
                'fecha': $(`[name="fecha"]`).val(),
                'idventa': idventas,
                'totalpagar': parseFloat($("#MontoPagar2 span").text())
            },
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $("#opcionmenu").show(); //?menu de las mesas
                    $("#opcionmenumesa").hide(); //? menu datos mesa
                    $("#ventaseleccionada").val("");
                    $("#datos-mesa").hide();
                    $("#contenedor-mesa").show();
                    $("#contenedor-mesa").html(data.dataHtml);
                    if (data.tipopago == "CREDITO") {
                        Lobibox.notify('success', {
                            size: 'mini',
                            position: "top right",
                            msg: 'El credito fue creado correctamente.'
                        });
                    } else {
                        printfcomprobante(idventas);
                    }
                    $(".product").removeClass(".productoIsSelected");
                    alertComprobantes(); //Busca si hay mas Boletas/Facturas sin emitir
                    $('#form_vender')[0].reset();
                    $('#AddSale').modal('hide');
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#vendiendo').text('Enviar');
                $('#vendiendo').attr("disabled", false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'El registro no se pudo crear verifique las validaciones'
                });
            }
        });

    }


    function CrearNewVenta() {
        $.ajax({
            url: "<?= $this->url ?>/ajax_CrearNewVenta",
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                ventasReload(); //? recargamos todas las ventas
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });
    }


    function printfcomprobante(idventas) {
        $('#comprobante').modal('show');
        $('.modal-title').text('COMPROBANTE');
        $.ajax({
            url: "<?= $this->url ?>/printfcomprobante",
            data: {
                "idventa": idventas
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $('#dataventadetalle').html(data.html);
                $('#fotter-cerrar').html(data.fotter);
                $("#SendMail").html(data.email);
                $("#SendWP").html(data.whatsapp);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax'
                });
            }
        });

    };

    function sendMail(idventas) {
        var correo = $('#correo').val();
        $("#enviarcorreo").attr("disabled", true);
        $("#enviarcorreo").html("<i class='fa fa-spinner fa-spin'></i>");
        $.ajax({
            url: "<?= $this->url ?>/sendemail",
            data: {
                "idventa": idventas,
                "correo": correo
            },
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $("#enviarcorreo").attr("disabled", false);
                $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $("#enviarcorreo").attr("disabled", false);
                $("#enviarcorreo").html("<i class='fa fa-paper-plane'></i>");
                Lobibox.notify('success', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Correo enviado'
                });
            }
        });
    };

    function imprimircomprobante(tipoimpresora, venta) {
        if (tipoimpresora === 0) {
            $.ajax({
                url: '<?= $this->url ?>/imprimircomprobante',
                type: 'POST',
                data: {
                    'venta': venta
                }
            });
        }
        if (tipoimpresora === 1) {
            var Url = '<?= $this->url ?>/showcomprobante/' + venta;
            window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
        }
    };

    function opcionesmenu() {
        $.ajax({
            url: "<?= $this->url ?>/opcionmenu",
            type: "POST",
            dataType: "json",
            success: function(data) {
                $(`#opcionmenu`).html(data.html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //toast.error("Ocurrió algo inesperado");
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });


    }

    function salircaja() {
        $.ajax({
            url: "<?= $this->url ?>/ajax_salir_caja",
            type: "POST",
            dataType: "json",
            success: function(data) {
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax caja.'
                });
            }
        });
    }

    function CloseRegister() {
        let totalventa = $("#ventaspendientes").children().length; // le ponemos meenos uno por que el boton + tiene tambien esa clase
        if (totalventa == 0) {
            $.ajax({
                url: "<?= $this->url ?>/CloseRegister",
                type: "POST",
                dataType: "json",
                success: function(data) {
                    if (data.status) {
                        $('#closeregsection').html(data.data);
                        $('#CloseRegister').modal('show');
                    } else {
                        swal("Mesas Abiertas !!", "" + data.data, "error");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    toast.error("Ocurrió algo inesperado");
                    // Lobibox.notify('error', {
                    //   size: 'mini',
                    //   msg: 'Error al obtener datos de ajax.'
                    // });
                }
            });

        } else {
            Lobibox.alert("info", {
                title: "Informacion",
                msg: "No tiene que haber ventas"
            })
        }
    };

    function SubmitRegister() {

        $("#montototalcaja").keyup(function() {
            if ($(this).val() != "") {
                $(this).parent().removeClass("has-error");
                $(this).next().text("");
            }
        });

        $('#cerrarcaja').attr('disabled', true);
        $('#cerrarcaja').html('<i class="fa fa-spinner fa-spin"></i>');

        swal({
                title: 'Estas seguro ?',
                text: 'Usted no será capaz de recuperar las bodegas más tarde!',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: 'Sí, Ciérralo!',
                closeOnConfirm: false
            },
            function() {

                $.when(
                        $.ajax({
                            url: "<?= $this->url ?>/SubmitRegister",
                            type: "POST",
                            data: $('#form_cierre').serialize(),
                            dataType: "JSON",
                        })
                        /*
                        $.ajax({
                          url: '<?= base_url() ?>venta/enviomasivo/<?= date('Y-m-d') ?>/<?= date('Y-m-d') ?>',
                          method: 'POST',
                          dataType: "JSON",
                        })
                        */
                    ).then(function(response1) {
                        if (response1.status) {

                            //toast.success("El registro fue creado exitosamente");

                            /*
                            Lobibox.notify('success', {
                              size: 'mini',
                              msg: 'El registro fue creado exitosamente.'
                            });*/

                            /*
                            if (response2[0].respuesta == 'ok') {
                              swal({
                                title: 'Resultado',
                                text: 'Su comprobante se ha procesado correctamente!',
                                html: true,
                                type: "success",
                                confirmButtonText: "Ok",
                                confirmButtonColor: "#2196F3"
                              });
                            } else {
                              swal({
                                title: 'ERROR',
                                text: 'No Existen comprobantes pendientes de envio',
                                html: true,
                                type: "error",
                                confirmButtonText: "Ok",
                                confirmButtonColor: "#2196F3"
                              });
                            }
                            */

                            //? El perfil de 3 es caja
                            if (response1.usuarioperfil == 1 || response1.usuarioperfil == 2) {
                                printfcierre(); //? le muestra el reporte al cerrar caja
                            }


                            setTimeout("location.href='<?= $this->url ?>/backup'", 1000);
                        } else {
                            $('#cerrarcaja').attr('disabled', false);
                            $('#cerrarcaja').text('Cerrar registrarse');
                            for (var i = 0; i < response1.inputerror.length; i++) {
                                $('[name="' + response1.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                                $('[name="' + response1.inputerror[i] + '"]').next().text(response1.error_string[i]); //select span help-block class set text error string
                            }
                        }
                    })
                    .fail(function(err) {
                        console.log('Something went wrong', err);

                    });
                swal.close();
                $('#cerrarcaja').attr('disabled', false);
                $('#cerrarcaja').text('Cerrar registrarse');
            });


    };

    function printfcierre() {
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= $this->url ?>/ajax_updatecaja",
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data.tipoimpresora === '0') {
                    $.ajax({
                        url: '<?= $this->url ?>/imprimircierre',
                        type: 'POST',
                    });
                }
                if (data.tipoimpresora === '1') {
                    var Url = '<?= $this->url ?>/showcierre';
                    window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toast.error("Ocurrió algo inesperado");
                // Lobibox.notify('error', {
                //   size: 'mini',
                //   msg: 'Error al obtener datos de ajax.'
                // });
            }
        });
    };

    //TODO----------------------------------------------------------------------------------------------------------------------------------------
    function agregarnewventa() {
        $("#agregarnewventa").attr("disabled", true);
        $("#ProcesarVenta").attr("disabled", true);
        $("#agregarnewventa").html("<i class='fa fa-spinner fa-spin'></i>");
        QuitarResalteDeVentaOld();
        $.ajax({
            url: "<?= $this->url ?>/ajax_newventa",
            type: "POST",
            data: {
                "tipozona": $("#tipozona").val(),
                "idzona": $("#idzona").val(),
            },
            dataType: "JSON",
            success: function(data) {
                $("#cliente").val(data.cliente);
                $("#clientes").val(data.clientes);
                $("#ventaspendientes").append(drawNewVenta(data.idventa, data.totalventa));
                $("#ventaseleccionada").val(data.idventa);
                drawDataProductoLS(null);
                DatosVenta();
                $("#agregarnewventa").attr("disabled", false);
                $("#agregarnewventa").html("<i class='fa fa-plus'></i>");
                resaltar();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Error al obtener datos de ajax.'
                });
                $("#agregarnewventa").attr("disabled", false);
                $("#agregarnewventa").html("<i class='fa fa-plus'></i>");
            }
        });
    }

    function drawNewVenta(idventa, totalventa) {

        $("#ventaspendientes").children().removeClass("selectedGat");
        $("#ventaseleccionada").val(idventa);
        let datahtml = ` <button style="border-top:0px; border-left:0px; border-right:0px" id="venta_${idventa}" class="categories selectedGat" onclick="traerpedidosventa(${idventa})">
    <input value="${idventa}" type="hidden">
    VENTA ${totalventa}
    </button> `;
        return datahtml;

    }

    function resaltar() {
        $('.help-block').empty(); // clear error string
        $("#tipoventa").closest('div').removeClass("has-error"); //clear error 

    }

    function traerpedidosventa(idventa) {
        QuitarResalteDeVentaOld(); //Antese que se cambie el id de la venta Demarcamos los productos de esa venta
        $("#ventaspendientes").children().removeClass("selectedGat");
        $("#ventaseleccionada").val(idventa);
        $(`#venta_${idventa}`).addClass("selectedGat");
        DatosVenta(); //traemos los datos de laventa
        ResalteSeleccionados(); // marcamos los productos de la venta actual seleccionada
        DatosSecundariosPedido();
        drawDataProductoLS(null);
        resaltar();
    }


    function DatosVenta() {
        let idventa = $("#ventaseleccionada").val();
        if (idventa != "") {
            $.ajax({
                url: "<?= $this->url ?>/ajax_Datos_venta",
                data: {
                    "idventa": idventa
                },
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    $("#tipoventa").val(data.tipoventa);
                    $("#cliente").val(data.cliente);
                    $("#clientes").val(data.clientes);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Lobibox.notify('error', {
                        size: 'mini',
                        position: 'top right',
                        msg: 'Error! Codigo de error: ajax_Datos_venta'
                    });
                }
            });
        }
    }

    function QuitarResalteDeVentaOld() {
        let idventa = $("#ventaseleccionada").val();
        let dataPLS = localStorage.getItem(`productos_${idventa}`);
        if (dataPLS != null) {
            let array = JSON.parse(dataPLS);
            for (value of array) {
                $(`#ProductoLista${value.id_producto}`).removeClass("productoIsSelected");
            }
        }
    }


    //? traemos todas las ventas
    function ventasReload() {
        $("#ventaspendientes").empty();
        // $("#ventaspendientes").html(`<button id="agregarnewventa" style="border-top:0px; border-left:0px" class="categories" onclick="agregarnewventa()"><i class="fa fa-plus"></i></button>`);
        $("#ventaspendientes").children().removeClass("selectedGat");
        $.ajax({
            url: "<?= $this->url ?>/ajax_ventasReload",
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                console.log(data.dataquery);
                $("#ventaspendientes").append(data.datahtml);
                $("#ventaseleccionada").val(data.idselect); //insertamos el id de la venta para saber de quien sera el producto
                drawDataProductoLS(null);
                DatosVenta(); //traemos los datos de laventa
                DatosSecundariosPedido();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax'
                });
            }
        });

    }

    function ELiminarVenta() {
        $("#eliminarventa").attr("disabled", true);
        $("#eliminarventa").html("<i class='fa fa-spinner fa-spin'></i>");
        let idventa = $("#ventaseleccionada").val();
        let key = localStorage.getItem(`productos_${idventa}`);
        if (idventa != "") {
            if (key != null) {
                let arrayLS = JSON.parse(key);
                if (arrayLS.length > 0) {
                    Lobibox.alert("info", {
                        title: "Informacion",
                        position: "top right",
                        msg: "Tienes que eliminar los productos seleccionados",
                    });
                    $("#eliminarventa").attr("disabled", false);
                    $("#eliminarventa").html("<i class='fa fa-minus'></i>");
                } else {
                    ProcesoELiminarVenta(idventa);
                }
            } else {
                ProcesoELiminarVenta(idventa);
            }
        } else {
            Lobibox.alert("info", {
                title: "Informacion",
                position: "top right",
                msg: "Tienes que seleccionar una venta",
            });
            $("#eliminarventa").attr("disabled", false);
            $("#eliminarventa").html("<i class='fa fa-minus'></i>");
        }


    }

    function ProcesoELiminarVenta(idventa) {

        $.ajax({
            url: "<?= $this->url ?>/ajax_ProcesoELiminarVenta",
            type: "POST",
            data: {
                'idventa': idventa
            },
            dataType: "JSON",
            success: function(data) {

                $(`#venta_${idventa}`).removeClass("selectedGat");
                $(`#venta_${idventa}`).prev().addClass("selectedGat");

                if (localStorage.getItem(`productos_${idventa}`) != null) {
                    localStorage.removeItem(`productos_${idventa}`);
                }


                let idventaPREV = $(`.selectedGat`).children().val();
                $("ventaseleccionada").val(idventaPREV);

                $(`#venta_${idventa}`).remove();
                traerpedidosventa(idventaPREV);

                $("#eliminarventa").attr("disabled", false);
                $("#eliminarventa").html("<i class='fa fa-minus'></i>");

            },
            error: function(jqXHR, textStatus, errorThrown) {
                //toast.error("Ha ocurrido algo inesperado");
                Lobibox.notify('error', {
                    size: 'mini',
                    position: "top right",
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });

    }

    function sentTicketWA(venta) {
        const phone = $("#telefonoWP").val();
        window.open("<?= $this->url ?>/sentTicketWA/" + phone + "/" + venta);
    }

    function enviomasivo_documento_electronico() {
        var slider = document.createElement("input");
        slider.type = "range";
        swal({
            content: slider,
        });
        $.ajax({
            url: '<?= base_url() ?>venta/enviomasivo/' + <?= date('Y-m-d') ?> + '/' + <?= date('Y-m-d') ?>,
            method: 'POST',
            dataType: "JSON",
        }).then(function(data) {
            if (data.respuesta == 'ok') {
                swal({
                    title: 'Resultado',
                    text: 'Su comprobante se ha procesado correctamente!',
                    html: true,
                    type: "success",
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#2196F3"
                });
            } else {
                swal({
                    title: 'ERROR',
                    text: 'No Existen comprobantes pendientes de envio',
                    html: true,
                    type: "error",
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#2196F3"
                });
            }
        }, function(reason) {
            console.log(reason);
        });
    };

    // TODO: --------------------------------Esto es el codigo nuevo-----------------------------
    function categoriaSeleccionar() {
        $("#retrocederFila").hide();
        $("#Categoriaproducto").hide();
        $("#CategriaSeleccionar").load('<?= $this->url ?>/load_CategriaSeleccionar');
    }


    function EliminaProducto(idventadetalle, idproducto) {
        $(`#BTN-EliminaProducto-${idventadetalle}`).attr("disabled", true);
        $(`#BTN-EliminaProducto-${idventadetalle}`).html(`<i class="fa fa-spin fa-spinner"></i>`);
        $.ajax({
            url: "<?= $this->url ?>/ajax_eliminarproducto",
            type: "POST",
            data: {
                'idventadetalle': idventadetalle
            },
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    $(`#ProductoLista${idproducto}`).removeClass("productoIsSelected");
                    $(`#BTN-EliminaProducto-${idventadetalle}`).attr("disabled", false);
                    $(`#BTN-EliminaProducto-${idventadetalle}`).html(`<i class="fa fa-minus-circle"></i>`);
                    $(`#content-padre-${idventadetalle}`).remove();
                    DatosSecundariosPedido();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("error: ajax_eliminarproducto");
            }
        });
    }

    function EliminarProductoArray(index, id_producto) {

        let venta = $("#ventaseleccionada").val();
        ProductosArr.splice(index, 1);
        localStorage.setItem(`productos_${venta}`, JSON.stringify(ProductosArr));

        //? si elimina todos los productos del array tambien eliminamos del LS
        let dataLsProductos = JSON.parse(localStorage.getItem(`productos_${venta}`));
        if (dataLsProductos.length <= 0) {
            localStorage.removeItem(`productos_${venta}`);
        }
        drawDataProductoLS(null);
    }

    function categoria(categoria) {
        $.ajax({
            url: "<?= $this->url ?>/ajax_productos_categoria",
            type: "POST",
            data: {
                'categoria': categoria
            },
            success: function(data) {
                $("#CategriaSeleccionar").hide();
                $("#retrocederFila").show();
                $("#Categoriaproducto").show();
                $("#CategoriaproductoTodos").hide();
                $("#Categoriaproducto").html(data);
                ResalteSeleccionados();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("error");
            }
        });
    }

    function ResalteSeleccionados() {
        let idventa = $("#ventaseleccionada").val();
        let dataPLS = localStorage.getItem(`productos_${idventa}`);
        if (dataPLS != null) {
            let array = JSON.parse(dataPLS);
            for (value of array) {
                $(`#ProductoLista${value.id_producto}`).addClass("productoIsSelected");
            }
        }
    }

    function productolibre() {
        $("#addproductolibre").modal('show');
    }

    function saveLibre() {
        $('#btnSaveLibre').text('guardando...'); //change button text
        $('#btnSaveLibre').attr('disabled', true); //set button disable
        let ventaseleccionada = $("#ventaseleccionada").val();
        if (ventaseleccionada != "") {
            $.ajax({
                url: '<?= $this->url ?>/ajax_addproductolibre',
                type: "POST",
                data: $('#form_libre').serialize(),
                dataType: "JSON",
                success: function(data) {
                    if (data.status) {
                        $('#addproductolibre').modal('hide');
                        $('#form_libre')[0].reset();
                        agregaarventa(data.producto, data.precio, {
                            statusvariante: false,
                            statuslote: false,
                            lote: false
                        }, data.nombre_producto);
                        DatosSecundariosPedido();
                    } else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                        }
                    }
                    $('#btnSaveLibre').text('GRABAR'); //change button text
                    $('#btnSaveLibre').attr('disabled', false); //set button enable
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#btnSaveLibre').text('GRABAR'); //change button text
                    $('#btnSaveLibre').attr('disabled', false); //set button enable
                }
            });
        } else {
            $('#addproductolibre').modal('hide');
            $('#btnSaveLibre').text('GRABAR'); //change button text
            $('#btnSaveLibre').attr('disabled', false); //set button enable
            Lobibox.alert("info", {
                title: 'Informacion',
                msg: 'Tiene que haber una venta selecionada'
            });
        }

    };

    function retroceder(zona) {
        if (zona == "ALQUILER") {} else {
            $('#Categoriaproducto').hide();
            $("#CategriaSeleccionar").show();
        }
        categoriaSeleccionar();
    }

    function asignarindicePro(idventa) {
        let dataproductoLS = JSON.parse(localStorage.getItem(`productos_${idventa}`));
        let indiceinsert;
        if (dataproductoLS == null) {
            indiceinsert = 0;
        } else {
            if (dataproductoLS.length > 0) {
                let indiceEncont = dataproductoLS.length - 1; //obtenemos el ultimo arreglo insertado
                let dataArreglo = dataproductoLS[indiceEncont];
                indiceinsert = dataArreglo.key_primary + 1;
            } else {
                indiceinsert = 0;
            }
        }
        return indiceinsert;
    }

    function cantidadPro(idventa, idproducto) {
        let dataproductoLS = JSON.parse(localStorage.getItem(`productos_${idventa}`));
        let cantidadP;

        if (dataproductoLS != null) {
            for (value of dataproductoLS) {
                if (value.id_producto == idproducto) {
                    cantidadP = value.cantidad + 1;
                    break;
                } else {
                    continue;
                }
            }

            if (cantidadP > 1) {
                return cantidadP;
            } else {
                return 1;
            }
        } else {
            return 1;
        }

    }

    function datainsert(idproducto, precioproducto, idventa, nameproducto, cantidad_variante, idvariante, objectParametros) {
        let key = asignarindicePro(idventa);
        let data = {
            key_primary: key,
            id_producto: idproducto,
            text_proudcto: nameproducto,
            id_variante: idvariante,
            statusvariante: objectParametros.statusvariante,
            total_pagar: precioproducto,
            precio_producto: precioproducto,
            cantidad: 1,
            cantidad_variante: cantidad_variante,
            cantidad_variante_total: cantidad_variante,
            lote: objectParametros.lote,
            statuslote: objectParametros.statuslote,
            EstadoMas: false,
            EstadoMenos: true,
        };

        ProductosArr.push(data);

    }

    function guardarProductoLS(idventa, idproducto) {
        localStorage.setItem(`productos_${idventa}`, JSON.stringify(ProductosArr));
        $(`#ProductoLista${idproducto}`).addClass("productoIsSelected");
    }

    function drawDataProductoLS(idproducto) {
        $("#pedidosventa").html("");
        $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
        $("#codigodebarra").val("");
        $(`#boton-product-${idproducto}`).attr("disabled", false);
        $("#addProcesoVE").modal("hide");
        $("#btnAgregarVenta").attr("disabled", false);

        let idventa = $("#ventaseleccionada").val();
        if (idventa != "") {
            ProductosArr = JSON.parse(localStorage.getItem(`productos_${idventa}`));
            if (ProductosArr) {
                ProductosArr.forEach(function(value, index) {
                    $("#pedidosventa").append(drawaHtmlProd(value));
                })
            } else {
                ProductosArr = [];
            }
        }
    }

    //? Antiguo modelo
    function drawaHtmlProd(value) {

        let html = `
      <div class="seleccion" id="content-padre-${value.id_producto}">
                <input id="Productos" value="" type="hidden">
              <div class="seleccion__item--producto">
                
                  <div class="seleccion__item-head">
                  ${value.text_proudcto} ${(value.statusvariante ? `[<span id="total_variante-${value.key_primary}">${value.cantidad_variante_total}</span>]` : "")}
                  </div>
                  <div class="seleccion__item-body">
                  <span style="color:#DDA433">P/U: </span><span>S/</span> <span id="CostoProducto-${value.key_primary}">${value.precio_producto}</span> 
                  </div> 
              </div>

              <div class="seleccion__item">

                  <div class="seleccion__item-head">
                    Cantidad 
                  </div>
                  <div class="seleccion__item-body">

                    <div class="input-group" style="width:130px;">
                        <div class="spinner-buttons input-group-btn">
                            <button type="button" onclick="MasMenos(0, ${value.id_producto}, ${value.key_primary})" class="btn spinner-up btn-inverse waves-effect waves-light btn-sm" id="BotonMas-${value.key_primary}">
                              <i class="fa fa-minus"></i>
                            </button>
                      </div>
                        <input onkeyup="CantidadProducto(${value.key_primary})" class="spinner-input form-control" id="CantidadProducto-${value.key_primary}" type="number" value="${value.cantidad}">
                        <div class="spinner-buttons input-group-btn">
                              <button type="button" onclick="MasMenos(1, ${value.id_producto}, ${value.key_primary})" class="btn spinner-up btn-inverse waves-effect waves-light btn-sm">
                                  <i class="fa fa-plus"></i>
                              </button>
                        </div>
                  </div>
                  </div> 
              </div>
              <div class="seleccion__item">
                  <div class="seleccion__item-head">
                    Total 
                  </div>
                  <div class="seleccion__item-body">
                    <div class="input-group">
                      <span class="input-group-addon">S/.</span>
                      <input onkeyup="TotalPagarServicio(${value.key_primary})" step="0.1" type="number" id="TotalPagar-${value.key_primary}" value="${value.total_pagar}"  class="form-control">                                                  
                    </div>
                  </div> 
              </div>
              <div class="seleccion__item--botoneliminar">
              <button class="botoneliminar btn btn-warning btn-sm"   id="BTN-EliminaProducto-${value.key_primary}" title="CANCELAR" onclick="EliminaProducto(${value.id_producto}, ${value.key_primary})">
              <i class="fa fa-minus-circle"></i>
              </button>
              </div>
              <div style="display:flex; justify-content:center; width:100%" >
                  <div class="alert alert-danger alert-dismissable" style="margin:3px; padding:2px; display:none" id="ContenedorMensajeStock-${value.key_primary}" >
                    
                  </div>
              </div>
          </div>
      `;
        return html;
    }



    function TotalPagarServicio(datakey_primary) {

        let ventaseleccionada = $(`#ventaseleccionada`).val();

        let costoproducto = parseFloat($(`#CostoProducto-${datakey_primary}`).text());
        let TotalPagar = parseFloat($(`#TotalPagar-${datakey_primary}`).val());

        let ResultCantidad = isNaN(parseFloat(TotalPagar / costoproducto)) ? 0.00 : parseFloat(TotalPagar / costoproducto);

        $(`#CantidadProducto-${datakey_primary}`).val(ResultCantidad.toFixed(2));

        for (value of ProductosArr) {
            if (value.key_primary == datakey_primary) {
                value.cantidad = ResultCantidad.toFixed(2);

                value.total_pagar = isNaN(TotalPagar.toFixed(2)) ? 0 : TotalPagar.toFixed(2);
                let cantidad_variante_total = parseFloat(value.cantidad_variante * ResultCantidad);
                value.cantidad_variante_total = cantidad_variante_total;
                $(`#total_variante-${datakey_primary}`).text(cantidad_variante_total.toFixed(2))
                //? Guardamos en el localStorage.
                localStorage.setItem(`productos_${ventaseleccionada}`, JSON.stringify(ProductosArr));
                //? actulizamos los datos secundatos
                DatosSecundariosPedido();
                break;
            } else {
                continue;
            }
        }
    }

    function CantidadProducto(key_primary) {

        let ventaseleccionada = $(`#ventaseleccionada`).val();

        let costoproducto = parseFloat($(`#CostoProducto-${key_primary}`).text());
        let CantidadProducto = parseInt($(`#CantidadProducto-${key_primary}`).val());

        let operacion = isNaN(parseFloat(costoproducto * CantidadProducto)) ? 0 : parseFloat(costoproducto * CantidadProducto);
        $(`#TotalPagar-${key_primary}`).val(operacion.toFixed(2));

        for (index in ProductosArr) {

            if (ProductosArr[index].key_primary == key_primary) {

                let cantidadproducto = isNaN(CantidadProducto.toFixed(2)) ? 0 : CantidadProducto.toFixed(2);
                let totalvariante = parseInt(ProductosArr[index].cantidad_variante * cantidadproducto);
                ProductosArr[index].cantidad_variante_total = totalvariante;
                $(`#total_variante-${key_primary}`).text(totalvariante);

                ProductosArr[index].cantidad = cantidadproducto;

                ProductosArr[index].total_pagar = operacion.toFixed(2);
                break;
            } else {
                continue;
            }

        }
        //? Guardamos en el localStorage.
        localStorage.setItem(`productos_${ventaseleccionada}`, JSON.stringify(ProductosArr));
        //? actulizamos los datos secundatos
        DatosSecundariosPedido();

    }

    //! obsoleto

    function MasMenos(estadoBTN, idventadetalle) {
        let cantidadproducto = parseInt($(`#CantidadProducto-${idventadetalle}`).val());
        let cantidadmedida = parseInt($(`#CantidadMedida-${idventadetalle}`).val());
        let urlAjax;
        if (estadoBTN) {
            $(`#BotonMas-${idventadetalle}`).attr("disabled", true);
            $(`#BotonMas-${idventadetalle}`).html("<i class='fa fa-spin fa-spinner'></i>");
            urlAjax = "<?= $this->url ?>/ajax_mas";
        } else {
            $(`#BotonMenos-${idventadetalle}`).attr("disabled", true);
            $(`#BotonMenos-${idventadetalle}`).html("<i class='fa fa-spin fa-spinner'></i>");
            urlAjax = "<?= $this->url ?>/ajax_menos";
        }
        if ($(`#CantidadProducto-${idventadetalle}`).val() == 0 && !estadoBTN) {
            $(`#BotonMenos-${idventadetalle}`).attr("disabled", false);
            $(`#BotonMenos-${idventadetalle}`).html("<i class='fa fa-plus'></i>");
            Lobibox.alert("info", {
                title: "Informacion",
                position: "top right",
                msg: "Verifique la cantidad de la operacion :(",
            });
        } else {
            $.ajax({
                url: urlAjax,
                type: "POST",
                data: {
                    "idventadetalle": idventadetalle,
                },
                dataType: "JSON",
                success: function(data) {
                    if (data.proceso.status) {
                        $(`#BotonMas-${idventadetalle}`).attr("disabled", false);
                        $(`#BotonMas-${idventadetalle}`).html("<i class='fa fa-plus'></i>");
                        $("#pedidosventa").html(data.proceso.dataHtml);
                        DatosSecundariosPedido();
                        /* drawDataProductoLS(idproducto);*/
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //toast.error("El registro no se pudo actualizar. Verifique la operación");
                    Lobibox.notify('error', {
                        size: 'mini',
                        position: "top right",
                        msg: "El registro no se pudo actualizar. Verifique la operación"
                    });
                }
            });
        }
    }

    function cerrarMSJ(key) {
        $(`#ContenedorMensajeStock-${key}`).hide();
    }

    function save() {
        // ajax adding data to database
        if ($("#ventaseleccionada").val() != "") {
            $.ajax({
                url: "<?= $this->url ?>/ajax_update/" + $(`#ventaseleccionada`).val(),
                type: "POST",
                data: $('#form_principal').serialize(),
                dataType: "JSON",
                success: function(data) {
                    if (data.status) {} else {
                        for (var i = 0; i < data.inputerror.length; i++) {
                            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //toast.error("El registro no se pudo actualizar. Verifique la operación");
                    Lobibox.notify('error', {
                        size: 'mini',
                        position: "top right",
                        msg: "El registro no se pudo actualizar. Verifique la operación"
                    });
                }
            });
        } else {
            //? error 100: venta no seleccionada
            Lobibox.alert("info", {
                title: "Informacion",
                msg: "Codigo de error 100"
            })
        }

    };

    function eliminarDataLS(idventa) {
        let key = localStorage.getItem(`productos_${idventa}`);
        if (key != null) {
            localStorage.removeItem(`productos_${idventa}`);
        }
    }

    //TODO: -------------------------------------------------------------------------------------------------------------------------------------------------------------

    function agregarAdicionales(idproducto) {
        $(`#varianteseleccionada`).val("");
        $(`#loteseleccionado`).val("");
        $("#MensajeVariante").html("");
        $("#MensajeVariante").removeClass("alert alert-danger alert-dismissable");
        let ventaseleccionada = $(`#ventaseleccionada`).val();
        if (ventaseleccionada == "") {
            Lobibox.alert("info", {
                title: "Informacion",
                position: "top right",
                msg: "Tienes que crear una venta",
            });
        } else {
            $.ajax({
                url: '<?= $this->url ?>/ajax_agregarAdicionales/' + idproducto,
                type: "POST",
                dataType: "JSON",
                success: function(data) {
                    document.getElementById("ColumnaVariante").className = '';
                    document.getElementById("ColumnaExtra").className = '';
                    document.getElementById("ColumnaLotes").className = '';
                    $(`#ColumnaExtra`).addClass(`col-lg-${data.totalcolumnas}`);
                    $(`#ColumnaVariante`).addClass(`col-lg-${data.totalcolumnas}`);
                    $(`#ColumnaLotes`).addClass(`col-lg-${data.totalcolumnas}`);
                    $("#listaVariante").html(data.variantes.html);
                    $("#listExtras").html(data.extras.html);
                    //? Agregar lote por defecto
                    data.lotes.totalLotes == 1 ? $("#loteseleccionado").val(data.lotes.html) : $("#listLotes").html(data.lotes.html);
                    $(`#ProcesoAddVE`).html(data.boton);
                    data.variantes.status ? $(`#ColumnaVariante`).show() : $(`#ColumnaVariante`).hide();
                    data.lotes.status && data.lotes.totalLotes > 1 ? $(`#ColumnaLotes`).show() : $(`#ColumnaLotes`).hide();
                    data.extras.status ? $(`#ColumnaExtra`).show() : $(`#ColumnaExtra`).hide();
                    $("#addProcesoVE").modal('show');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Codigo de error de ajax: agregarAdicionales")
                }
            });
        }
    }

    function seleccionarVariante(idvariante) {
        //? ocultamos el mensaje de alerta de variantes
        $(`#MensajeVariante`).hide("fast");
        $(`.variantes`).each(function(index, element) {
            let idvariante = $(element).val();
            $(`#label-variante-${idvariante}`).removeClass("selectVariantes");
        });
        $(`#label-variante-${idvariante}`).addClass("selectVariantes");
        $("#varianteseleccionada").val(idvariante);
    }

    function seleccionarLote(idlote) {
        $(`.lotes`).each(function(index, element) {
            let idlote = $(element).val();
            $(`#label-lote-${idlote}`).removeClass("selectLotes");
        });
        $(`#label-lote-${idlote}`).addClass("selectLotes");
        $("#loteseleccionado").val(idlote);
    }

    function procesoAgregarVenta(dataObject) {
        $("#btnAgregarVenta").attr("disabled", true);
        $("#btnAgregarVenta").html(`<i class="fa fa-spin fa-spinner"></i>`);
        $("#MensajeVariante").html("");
        $("#MensajeVariante").addClass("alert alert-danger alert-dismissable");
        let precioproducto = 0;
        let idvariante = $(`#varianteseleccionada`).val();
        if (dataObject.statusvariante == 0) {
            //? si es 0 tomara el precio del producto normal
            precioproducto = parseFloat($(`#precio-producto-${dataObject.idproducto}`).val());
        } else {
            //? Tomara el precio de la variante selecionada
            precioproducto = parseFloat($(`#precioproducto-${idvariante}`).val());
        }
        let textProductoAdicional = "";
        if (dataObject.statusvariante == 1) {
            //? concatenara el nombre de la variante con el producto
            textProductoAdicional += ` ${$(`#text-variante-${idvariante}`).val()} `;
        } else {
            textProductoAdicional += "";
        }
        let precioTotalExtras = 0;
        //? sumamos el total y a los nombres concatenamos, de todos los extras que se seleccionaron
        $(`.ExtraIdenti`).each(function(index, value) {
            let idextra = $(this).val();
            if ($(`#checkep_extra-${idextra}`).is(":checked")) {
                let PrecioExtra = parseFloat($(`#PrecioExtra-${idextra}`).val());
                precioTotalExtras += PrecioExtra
                textProductoAdicional += `, + 1 ${$(`#NombreExtra-${idextra}`).val()}`;
            } else {
                if ($(`#extra-${idextra}`).hasClass("active-tachar-content")) {
                    textProductoAdicional += `, Sin ${$(`#NombreExtra-${idextra}`).val()}`;
                }
            }
        });
        let precioTotalGlobal = precioTotalExtras + precioproducto;
        let VarianExtrData = {
            statusvariante: dataObject.statusvariante,
            statuslote: dataObject.statuslote,
            lote: dataObject.statuslote ? $("#loteseleccionado").val() : null,
            variante: idvariante,
            data: textProductoAdicional
        };
        if (dataObject.statusvariante == 1) {
            let variante = $(`#varianteseleccionada`).val();
            if (variante != "") {
                if (dataObject.statuslote == 1) {
                    let lote = $(`#loteseleccionado`).val();
                    if (lote != "") {
                        agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
                    } else {
                        $(`#MensajeVariante`).show("fast");
                        $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger un <span class="alert-link">lote</span>.`)
                        $("#btnAgregarVenta").attr("disabled", false);
                        $("#btnAgregarVenta").text(`Agregar`);
                    }
                } else {
                    agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
                }
            } else {
                $(`#MensajeVariante`).show("fast");
                $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger una <span class="alert-link">Variante</span>.`)
                $("#btnAgregarVenta").attr("disabled", false);
                $("#btnAgregarVenta").text(`Agregar`);
            }
        } else {
            if (dataObject.statuslote == 1) {
                let lote = $(`#loteseleccionado`).val();
                if (lote != "") {
                    agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
                } else {
                    $(`#MensajeVariante`).show("fast");
                    $("#MensajeVariante").append(`<button type="button" class="close" aria-hidden="true" style="right:0px" onclick="varianteMSG()">×</button> Tienes que escoger un <span class="alert-link">lote</span>.`)
                    $("#btnAgregarVenta").attr("disabled", false);
                    $("#btnAgregarVenta").text(`Agregar`);
                }
            } else {
                agregaarventa(dataObject.idproducto, precioTotalGlobal, VarianExtrData, "");
            }
        }
    }

    function varianteMSG() {
        $("#MensajeVariante").hide("fast");
    }

    function agregaarventa(idproducto, precioproducto, VariExtrData, nombreproducto) {
        let idventaseleccionada = $("#ventaseleccionada").val();
        let idvariante, tipomedida, cantidad_variante, precioinsert, nombreinsert;
        precioinsert = precioproducto;
        if (VariExtrData.statusvariante) {
            idvariante = VariExtrData.variante;
            cantidad_variante = $(`#variante-cantidad-${idvariante}`).length > 0 ? $(`#variante-cantidad-${idvariante}`).val() : 1;
            nombreinsert = $(`#nombre-producto-${idproducto}`).val() + VariExtrData.data;
        } else {
            idvariante = false;
            cantidad_variante = 1;
            nombreinsert = $(`#nombre-producto-${idproducto}`).val();
        }
        $(`#boton-product-${idproducto}`).attr("disabled", true);
        if (idventaseleccionada != "") {
            let idventa = $("#ventaseleccionada").val();
            let textoproducto = nombreinsert;
            if (nombreproducto != "") {
                textoproducto = nombreproducto;
            }
            $.ajax({
                url: '<?= $this->url ?>/ajax_agregarPedido',
                type: "POST",
                data: {
                    "VariExtrData": JSON.stringify(VariExtrData),
                    "idventa": idventa,
                    "idvariante": idvariante,
                    "idproducto": idproducto,
                    "precioinsert": precioinsert,
                    "textoproducto": textoproducto,
                    "cantidadvariante": cantidad_variante,
                },
                dataType: "JSON",
                success: function(data) {
                    if (data.proceso.status) {
                        $(`#ProductoLista${idproducto}`).addClass("productoIsSelected");
                        $("#busquedacodigobarra").html(`<i class="fa fa-search"></i>`);
                        $("#codigodebarra").val("");
                        $(`#boton-product-${idproducto}`).attr("disabled", false);
                        $("#addProcesoVE").modal("hide");
                        $("#btnAgregarVenta").attr("disabled", false);
                        $("#pedidosventa").html("");
                        $("#pedidosventa").html(data.proceso.contenido);
                        DatosSecundariosPedido();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Codigo de error de ajax: ajax_agregarPedido");
                    $(`#boton-product-${idproducto}`).attr("disabled", false);
                }
            });
            /*
            let EstadoBusqueda = false;
            let verifi = localStorage.getItem(`productos_${idventa}`);
            if (verifi != null) {
                for (value of ProductosArr) {
                    if (VariExtrData.statusvariante) {
                        //? buscara a la variante y lo aumentara
                        if (value.id_variante == idvariante) {
                            EstadoBusqueda = true;
                            let cantidad = parseInt(value.cantidad + 1); //?cantidad de seleccion
                            let total = parseFloat(value.precio_producto * cantidad);
                            value.cantidad_variante_total = parseInt(value.cantidad_variante * cantidad);
                            value.cantidad = cantidad;
                            value.total_pagar = total;
                            if (value.cantidad == 1) {
                                value.EstadoMenos = true;
                            } else {
                                value.EstadoMenos = false;
                            }
                            break;
                        } else {
                            continue;
                        }
                    } else {
                        //? buscara el producto y lo aumentara
                        if (value.id_producto == idproducto) {
                            EstadoBusqueda = true;
                            let cantidad = parseInt(value.cantidad + 1); //?cantidad de seleccion
                            let total = parseFloat(value.precio_producto * cantidad);
                            value.cantidad_variante_total = parseInt(value.cantidad_variante * cantidad);
                            value.cantidad = cantidad;
                            value.total_pagar = total;
                            if (value.cantidad == 1) {
                                value.EstadoMenos = true;
                            } else {
                                value.EstadoMenos = false;
                            }
                            break;
                        } else {
                            continue;
                        }
                    }
                }
            }
            if (EstadoBusqueda == false) {
                datainsert(idproducto, precioinsert, idventa, textoproducto, cantidad_variante, idvariante, VariExtrData);
            }
            */
            //? en este metodo agregarmos "productoIsSelected" 
            //guardarProductoLS(idventa, idproducto); 
            //? le ponesmo esto al segundo por que recargargar llamamos a este metodo
            //drawDataProductoLS(idproducto);
            //DatosSecundariosPedido();
        } else {
            //? mensaje para informar que tiene que seleccionar crear un venta
            Lobibox.alert("info", {
                title: "Informacion",
                position: "top right",
                msg: "Tienes que crear una venta",
            });
            $(`#boton-product-${idproducto}`).attr("disabled", false);
        }
    }


    function seleccionarExtra(idextra) {

        if ($(`#extra-${idextra}`).hasClass("active-tachar-content") == false) {
            if ($(`#extra-${idextra}`).hasClass("extra-active")) {
                $(`#extra-${idextra}`).removeClass("extra-active");
                $(`#checkep_extra-${idextra}`).prop("checked", false);
            } else {
                $(`#extra-${idextra}`).addClass("extra-active");
                $(`#checkep_extra-${idextra}`).prop("checked", true);
            }
        }
    }

    function sinextraope(idextra) {

        if ($(`#extra-${idextra}`).hasClass("active-tachar-content")) {
            $(`#sinextra-${idextra} i`).removeClass("active-sin-extra");
            $(`#checkep_extra-${idextra}`).attr("disabled", false);

            $(`#extra-${idextra}`).removeClass("active-tachar-content");
            $(`#extraname-${idextra}`).removeClass("active-tachar-extra");
            $(`#extraprecio-${idextra}`).removeClass("active-tachar-extra");

        } else {

            $(`#sinextra-${idextra} i`).addClass("active-sin-extra");
            $(`#checkep_extra-${idextra}`).attr("checked", false);
            $(`#checkep_extra-${idextra}`).attr("disabled", true);
            $(`#extra-${idextra}`).removeClass("extra-active");
            $(`#extra-${idextra}`).addClass("active-tachar-content");
            $(`#extraname-${idextra}`).addClass("active-tachar-extra");
            $(`#extraprecio-${idextra}`).addClass("active-tachar-extra");

        }

    }

    function DatosSecundariosPedido() {
        $(`#addVariante`).modal("hide");
        let TotalItem = 0;
        let MontoPagar = 0;
        $("#pedidosventa").children().each(function(key, value) {
            let idventadetalle = $(this).children("input").val();
            TotalItem += parseFloat($(`#CantidadProducto-${idventadetalle}`).val());
            MontoPagar += parseFloat($(`#TotalPagar-${idventadetalle}`).val());
        });
        if (TotalItem == 0) {
            $("#ProcesarVenta").attr("disabled", true);
        } else {
            $("#ProcesarVenta").attr("disabled", false);
        }
        $("#ItemsNumVenta span").text(TotalItem.toFixed(2));
        $("#SubtotVenta").text(MontoPagar.toFixed(2));
        $("#total").text(MontoPagar.toFixed(2));
    }

    function grabar() {
        $("#ProcesarVenta").attr("disabled", true);
        $("#ProcesarVenta").html("<i class='fa fa-spin fa-spinner'></i>");
        var cliente = $("#clientes").val();
        cliente = cliente == "" ? "|" : cliente;
        var tipoComprobante = $("#tipoventa").val();
        var grabarventa = false;
        var msg = "";
        var documento = cliente.split("|");
        documento[0] = documento[0].trim();
        switch (tipoComprobante) {
            case 'FACTURA':
                if (documento[0].length != 11) {
                    grabarventa = false;
                    msg = "Debe seleccionar un cliente con RUC";
                } else {
                    grabarventa = true;
                }
                break;

            case 'BOLETA':
                if (documento[0].length != 8) {
                    grabarventa = false;
                    msg = "Debe seleccionar un cliente con DNI";
                } else {
                    grabarventa = true;
                    console.log("SI es Boleta y el cliente esta con DNI de 8");
                }
                break;
            case 'OTROS':
                grabarventa = true;
                break;
        }

        if (grabarventa) {
            let idventa = $("#ventaseleccionada").val();
            $.ajax({
                url: "<?= $this->url ?>/ajax_verif_stock",
                type: "POST",
                data: {
                    "idventa": idventa
                },
                dataType: "JSON",
                success: function(data) {
                    if (data.venta.status) {
                        if (data.dataenviar.length > 0) {
                            for (value of data.dataenviar) {
                                $(`#ContenedorMensajeStock-${value.idventadetalle}`).show("fast");
                                if (value.nombrelote != "") {
                                    $(`#ContenedorMensajeStock-${value.idventadetalle}`).html(`<button type="button" style="position:relative; right:0px" class="close" onclick="cerrarMSJ(${value.idventadetalle})">×</button>
                                  El lote: "${value.nombrelote}" cuenta con <span class="alert-link" id="CantidadStock-${value.idventadetalle}">${value.totalstock}</span> de stock .`);
                                } else {
                                    $(`#ContenedorMensajeStock-${value.idventadetalle}`).html(`<button type="button" style="position:relative; right:0px" class="close" onclick="cerrarMSJ(${value.idventadetalle})">×</button>
                                   Solo cuenta con <span class="alert-link" id="CantidadStock-${value.idventadetalle}">${value.totalstock}</span> stock .`);
                                }
                            };
                        } else {
                            $('#form_vender')[0].reset(); // reset form on modals
                            $('.form-group').removeClass('has-error'); // clear error class
                            $('.help-block').empty(); // clear error string
                            $("#tipoventa").closest('div').removeClass("has-error"); //clear error 
                            DatosPago();
                            $(`#ReturnChange span`).text("0.00");
                            $(`#ReturnChange span`).addClass("red");
                            $('#AddSale').modal('show');
                            $('.modal-title').text('PROCESAR VENTA');
                            $("#tipocard").hide();
                            $("#vencimiento").hide();
                            $("#numberoperacion").hide();
                            $('#operacion').attr('minLength', 4);
                            $('#operacion').attr('maxlength', 4);
                            if ($('#formapago').val() == 'CONTADO') {
                                $('#metodo').show();
                                $('#pagado').show();
                                $("#descontado").show();
                                $("#vencimiento").hide();
                                $('.ReturnChange').show();
                            } else {
                                $('#metodo').hide();
                                $('#pagado').hide();
                                $("#descontado").hide();
                                $("#vencimiento").show();
                                $('.ReturnChange').hide();
                            }
                            if ($('#metodopago').val() == 'EFECTIVO') {
                                $('#tipocard').hide();
                                //$('#numberoperacion').hide();
                            } else {
                                $('#tipocard').show();
                                //($('#numberoperacion').show();
                            }
                        }
                    } else {
                        eliminarDataLS(idventa);
                        ventasReload();
                        Lobibox.alert("info", {
                            title: "Informacion",
                            position: "top right",
                            msg: data.venta.msg,
                        });
                    }
                    $("#ProcesarVenta").attr("disabled", false);
                    $("#ProcesarVenta").text("PROCESAR VENTA");
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("error ajax_verif_stock");
                    $("#ProcesarVenta").attr("disabled", false);
                    $("#ProcesarVenta").text("PROCESAR VENTA");
                }
            });

        } else {
            $("#tipoventa").closest('div').addClass("has-error");
            $('#tipoventa').siblings('span').text(msg);
            $("#ProcesarVenta").attr("disabled", false);
            $("#ProcesarVenta").text("PROCESAR VENTA");
        }

    };

    function DatosPago() {
        let clientes = $("#clientes").val();
        let cliente = $("#cliente").val();
        let tipoventa = $("#tipoventa").val();
        $("#customerName span").text(clientes);
        $('[name="cliente"]').val(cliente);
        $('[name="tipoventa"]').val(tipoventa);
        //TODO: el total pago lo traemos de la vista no hacemos ajax
        $("#ItemsNum2 span").text($(`#ItemsNumVenta span`).text());
        $("#MontoPagar2 span").text($(`#SubtotVenta`).text());
        $("#pago").val($(`#SubtotVenta`).text());

    }

    function grabarcliente2(identificador, idventa) {
        $("#form_cliente2")[0].reset();
        let footer = document.querySelector("#divfooter");
        footer.innerHTML = `
    <button type="button" id="btnSavecliente2" onclick="savecliente2(${identificador}, ${idventa})" class="btn btn-primary">GRABAR</button>
    <button type="button" class="btn btn-danger" data-dismiss="modal" >CERRAR</button>`;
        $('#cliente_form2').modal('show');
        $('.modal-title').text('CREAR CLIENTE 2');
    }


    function verstockactual(id, boton) {
        $(`#verStock-${id}-${boton}`).attr("disabled", true);
        $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-spin fa-spinner'></i>");
        $.ajax({
            url: "<?= $this->url ?>/ajax_stockactual",
            type: "POST",
            data: {
                idproducto: id
            },
            dataType: "JSON",
            success: function(data) {
                $("#datastockactual").html(data.datahtml);
                $('#stockactual').modal('show');
                $(`#verStock-${id}-${boton}`).attr("disabled", false);
                $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-search'></i>");
                $('.modal-title').text('STOCK ACTUAL');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("error de ajax ajax_stockactual");
                $(`#verStock-${id}-${boton}`).attr("disabled", false);
                $(`#verStock-${id}-${boton}`).html("STOCK <i class='fa fa-search'></i>");
            }
        });
    }

    //? Functiones de los pedidos CODIGO MESA

    function crearpedido(idmesa) {
        event.preventDefault();
        $(`#botonMesa-${idmesa}`).attr("disabled", true);
        $(`#content-boton-mesa-${idmesa}`).prepend(`<div id="bloqueo-spinner-${idmesa}" class="content-spinner"><i class="fa fa-spin fa-spinner"></i></div>`);
        $.ajax({
            url: "<?= $this->url ?>/ajax_crearpedido",
            type: "POST",
            data: {
                "idmesa": idmesa
            },
            dataType: "JSON",
            success: function(data) {
                DatosSecundariosPedido();
                $("#pedidosventa").html(data.ocupado.dataHtml);
                if (data.ocupado.status) {
                    Lobibox.alert("info", {
                        title: "Informacion",
                        position: "top right",
                        msg: `La mesa ya fue ocupada. Hora: ${data.ocupado.datamesa.time}`,
                    });
                    $("#ventaseleccionada").val(data.ocupado.dataventa.idventa);
                } else {
                    $("#ventaseleccionada").val(data.ocupado.idventaseleccionada);
                }
                //? removemos el spinner y desbloquemaos el boton
                $(`#botonMesa-${idmesa}`).attr("disabled", false);
                $(`#bloqueo-spinner-${idmesa}`).remove();
                //? proceso para que entre a la mesa
                $("#opcionmenu").hide();
                $("#opcionmenumesa").show();
                $("#datos-mesa").show();
                $("#contenedor-mesa").hide();
                //? Datos de la venta
                $("#cliente").val(data.ocupado.dataventa.cliente);
                $("#clientes").val(data.ocupado.dataventa.textcliente);
                $("#tipoventa").val(data.ocupado.dataventa.tipoventa);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(`#botonMesa-${idmesa}`).attr("disabled", false);
                $(`#bloqueo-spinner-${idmesa}`).remove();
                alert("error de ajax ajax_crearpedido");
            }
        });
    }

    function datosDeMesa(idventa) {
        event.preventDefault();
        $(`#botonMesa-${idventa}`).attr("disabled", true);
        $(`#content-boton-mesa-${idventa}`).prepend(`<div id="bloqueo-spinner-${idventa}" class="content-spinner"><i class="fa fa-spin fa-spinner"></i></div>`);
        $.ajax({
            url: "<?= $this->url ?>/ajax_datosDeMesa",
            type: "POST",
            data: {
                "idventa": idventa
            },
            dataType: "JSON",
            success: function(data) {
                if (data.dataventa.status) {
                    $("#pedidosventa").html(data.dataventa.dataHtml);
                    $("#cliente").val(data.dataventa.venta.cliente);
                    $("#clientes").val(data.dataventa.venta.textcliente);
                    $("#tipoventa").val(data.dataventa.venta.tipoventa);
                    //? removemos el spinner y desbloquemaos el boton
                    $(`#botonMesa-${idventa}`).attr("disabled", false);
                    $(`#bloqueo-spinner-${idventa}`).remove();
                    $("#opcionmenu").hide();
                    $("#opcionmenumesa").show();
                    $("#ventaseleccionada").val(data.dataventa.venta.id);
                    $("#datos-mesa").show();
                    $("#contenedor-mesa").hide();
                    DatosSecundariosPedido();
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(`#botonMesa-${idventa}`).attr("disabled", false);
                $(`#bloqueo-spinner-${idventa}`).remove();
                alert("error de ajax: ajax_datosDeMesa");
            }
        });
        
    }

    function salirdatamesa() {
        $("#botonSalirMesa").attr("disabled", true);
        $("#botonSalirMesa").html("<i class='fa fa-spin fa-spinner' style='font-size: 35px'></i>");
        $.ajax({
            url: "<?= $this->url ?>/ajax_salirdatamesa",
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $("#botonSalirMesa").attr("false", true);
                $("#botonSalirMesa").html("<i class='fa fa-arrow-left' style='font-size: 35px'></i>");
                $("#opcionmenu").show(); //?menu de las mesas
                $("#opcionmenumesa").hide(); //? menu datos mesa
                $("#ventaseleccionada").val("");
                $("#datos-mesa").hide();
                $("#contenedor-mesa").show();
                $("#contenedor-mesa").html(data.dataHtml);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert("error de ajax: ajax_datosDeMesal");
            }
        });
    }

    function limpiarmesa(idventa) {
        event.preventDefault();
        bootbox.confirm("¿Seguro desea limpiar esta Mesa? se perderan todos los pedidos", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_limpiarmesa/" + idventa,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        if (data.proceso.status) {
                            Lobibox.notify('success', {
                                size: 'mini',
                                position: "top right",
                                msg: 'la mesa fue limpiado exitosamente.'
                            });
                        } else {
                            Lobibox.alert("info", {
                                title: "Informacion",
                                msg: data.proceso.msg,
                            });
                        }
                        $("#contenedor-mesa").html(data.proceso.dataHtml);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: "top right",
                            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
                        });
                    }
                });
            }
        });
    }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>