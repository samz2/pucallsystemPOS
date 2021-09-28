<?= $this->session->flashdata('mensaje') ?>
<style>
    .filaSup {
        text-align: left;
        font-weight: bold;
        padding-top: 5px;
    }

    .filaInf {
        border-top: 1px dashed #000;
        padding-top: 5px;
        text-align: right;
        font-weight: bold;
    }
</style>
<div class="row">
    <!-- /.col -->
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filtro de la operacion</h3>
            </div>

            <div class="panel-body">
                <form>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="control-label">Tienda <span class="required">*</span></label>
                                <select class="form-control" name="tienda" id="tienda" class="form-control" style="width:100%">
                                    <?php foreach ($empresas as $empresa) { ?>
                                        <option value="<?= $empresa->id ?>"><?= $empresa->ruc . " SERIE " . $empresa->serie . " | " . $empresa->nombre ?></option>
                                    <?php } ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Tipo de Credito<span class="required">*</span></label>
                                <select id="tipocredito" name="tipocredito" class="form-control" onchange="tipocreditoproceso()">
                                    <option value="1">CREDITO DE VENTAS</option>
                                    <option value="2">CREDITO DE CLIENTES</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="content-cliente">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select id="cliente" name="cliente" class="js-states form-control" lang="es"></select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="content-fecha">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>Fecha Final</label>
                                <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="panel-footer text-center">
                <a onclick="procesoBuscar()" class="btn btn-warning btn-sm" data-toggle="tooltip"><i class="fa fa-search"></i> BUSCAR <span id="complemento-buscar"></span></a>
                <button id="pendienteproceso" onclick="pendiente()" class="btn btn-danger btn-sm"><i class="fa fa-clock-o"></i> VENCIDOS</button>

            </div>

        </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Lista de <span id="tiposelect"></span> <!-- <button title="pagar"> <i class="fa fa-credit-card" onclick="cobrar()"></i></button> -->
                    <!-- <button  tooltip="Seleccionar Todo"> <i class="fa fa-check" onclick="seleccionarTodo(1)"></i></button> -->
                </h3>
            </div>
            <!-- /.box-header -->
            <div class="panel-body table-responsive">
                <table id="tabla" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th> <b>Numero</b></th>
                            <th><b>Nombre</b></th>
                            <th><b>Inicio</b></th>
                            <th><b>Vence</b></th>
                            <th><b>Estado</b></th>
                            <th><b>Monto</b></th>
                            <th><b>Saldo</b></th>
                            <th><b>Accion</b></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="tablaCliente" class="table table-bordered table-striped" style="display: none;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><b>Tienda</b></th>
                            <th><b>Codigo</b></th>
                            <th><b>Cliente</b></th>
                            <th><b>Inicio</b></th>
                            <th><b>Final</b></th>
                            <th><b>Comprobante</b></th>
                            <th><b>Monto Total</b></th>
                            <!-- <th><b>Total de Pedidos</b></th>
                            <th><b>Items</b></th> -->
                            <th><b>Estado</b></th>
                            <th><b>Accion BTN</b></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- /.box-body -->
        </div>
    </div>
</div>
<!-- modal  -->
<div class="modal fade" id="AddSale" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">PROCESAR CREDITO</h4>
            </div>
            <form id="form_credito" action="" method="post" role="form" autocomplete="off">
                <div class="modal-body">
                    <input type="hidden" name="credidoseleccionado" id="credidoseleccionado">
                    <div class="row">
                        <div class="col-lg-12">
                            <h3 class="text-center">TOTAL S/ <span id="montocredito"></span></h3>
                        </div>
                    </div>
                    <!-- <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>Pago realizado</label>
                                <select id="pagorealizadocredito" name="pagorealizadocredito" class="form-control" onchange="operacion_pagorealizadocredito()">
                                    <option value="CAJA">CAJA</option>
                                    <option value="EMPRESA">EMPRESA</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div> -->

                    <div class="row" id="content-tienda_pagar">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="paymentMethod">Tienda</label>
                                <select class="form-control" name="tienda_pagar" id="tienda_pagar" onchange="operaciontiendapagar()">
                                    <?php foreach ($empresas as $empresa) { ?>
                                        <option value="<?=$empresa->id?>"><?= $empresa->ruc . "  SERIE " . $empresa->serie . " | " . $empresa->nombre ?></option>
                                    <?php } ?>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="content-cajacredito">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="paymentMethod">Caja</label>
                                <select class="form-control" name="cajacredito" id="cajacredito">
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="paymentMethod">Tipo de comprobante:</label>
                                <select class="form-control" name="tcomprobantecredito" id="tcomprobantecredito">
                                    <option value="BOLETA">BOLETA</option>
                                    <option value="FACTURA" id="factura">FACTURA</option>
                                    <option value="OTROS">TICKET</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group" id="metodo">
                                <label for="paymentMethod">Metodo de Pago:</label>
                                <select class="form-control" name="metodopagocredito" id="metodopagocredito" onchange="tipoPagocredito()">
                                    <option value="EFECTIVO">EFECTIVO</option>
                                    <option value="TARJETA">TARJETA</option>
                                    <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                    <option value="DEPOSITO">DEPOSITO</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="tipocardcredito">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="tipotarjetacredito">Tipo Tarjetas</label>
                                <i class="fa fa-cc-visa fa-2x" id="visa" aria-hidden="true"></i>
                                <i class="fa fa-cc-mastercard fa-2x" id="mastercard" aria-hidden="true"></i>
                                <i class="fa fa-cc-amex fa-2x" id="amex" aria-hidden="true"></i>
                                <i class="fa fa-cc-discover fa-2x" id="discover" aria-hidden="true"></i>
                                <select class="form-control" name="tipotarjetacredito" id="tipotarjetacredito">
                                    <option value="VISA">VISA</option>
                                    <option value="DISCOVER">DISCOVER</option>
                                    <option value="MASTERCARD">MASTERCARD</option>
                                    <option value="DINERS CLUB">DINERS CLUB</option>
                                    <option value="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
                                </select>
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="content-n_operacioncredito">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>N° Operacion</label>
                                <input type="text" class="form-control" id="n_operacioncredito" name="n_operacioncredito">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6" id="descontado">
                            <div class="form-group">
                                <label>Descuento</label>
                                <input type="text" class="form-control money" id="descuentocredito" name="descuentocredito" value="0">
                                <span class="help-block"></span>
                            </div>
                        </div>
                        <div class="col-lg-6" id="pagado">
                            <div class="form-group">
                                <label>Pagado</label>
                                <input type="text" value="0" name="pagocredito" class="form-control money" id="pagocredito">
                                <span class="help-block"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Fecha</label>
                        <input type="date" class="form-control" id="fecha" readonly="true" name="fecha" value="<?= date('Y-m-d') ?>">
                        <span class="help-block"></span>
                    </div>
                    <div class="form-group ReturnChange">
                        <h3 id="vueltocredito" class="text-right">VUELTO S/ <span>0</span></h3>
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
<!-- fin_modal -->
<!-- modal comprobante -->
<div class="modal fade" id="comprobante" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="dataventadetalle">
                    <h4 class="text-center" id="cabeceraSerie"></h4>
                    <div style="clear:both;">
                    </div>
                    <span class="float-left" id="cabeceraFecha"></span><br>
                    <div style="clear:both;">
                        <span class="float-left" id="cabeceraCliente"></span>
                        <div style="clear:both;">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><b>#</b></th>
                                        <th><b>Descripcion</b></th>
                                        <th><b>Cant</b></th>
                                        <th><b>SubTotal</b></th>
                                    </tr>

                                </thead>
                                <tbody id="filaComprobante">

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-8" id="SendMail">
                    </div>
                    <div class="col-sm-4" id="SendWP">
                    </div>
                </div>
                <div class="modal-footer" id="fotter-cerrar">
                    <button data-dismiss="modal" class="btn btn-sm btn-danger">Cerrar <i class="fa fa-close"></i></button>
                    <button type="submit" class="btn btn-sm btn-success" id="btncomprobante" onclick="pagar()">Pagar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_pago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="form_pago" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
            <input type="hidden" class="form-control" id="id" name="id">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title text-center"></h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Monto</label>
                        <div class="col-sm-9">
                            <input readonly class="form-control" id="monto" name="monto">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tipo de Cobro</label>
                        <div class="col-sm-9">
                            <select id="pagorealizado" name="pagorealizado" class="form-control" onchange="operacion_pagorealizado()">
                                <option value="CAJA">CAJA</option>
                                <option value="EMPRESA">EMPRESA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="content-caja">
                        <label class="col-sm-3 control-label">Cajas</label>
                        <div class="col-sm-9">
                            <select id="caja" name="caja" class="form-control">
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="metodopago">Metodo de Pago <span class="required">*</span></label>
                        <div class="col-sm-9">
                            <select id="metodopago" name="metodopago" class="form-control">
                                <option value="EFECTIVO">EFECTIVO</option>
                                <option value="TARJETA">TARJETA</option>
                                <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                                <option value="DEPOSITO">DEPOSITO</option>
                                <!-- <option value="DESCUENTO PLANILLA">DESCUENTO PLANILLA</option> -->
                            </select>
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group" id="tipocard-creditoventas">
                        <label class="col-sm-3 control-label">Tipo Tarjeta</label>
                        <div class="col-sm-9">
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
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group" id="numberoperacion">
                        <label class="col-sm-3 control-label">Numero de operacion</label>
                        <div class="col-sm-9">
                            <input type="text" name="operacion" class="form-control enteros" id="operacion">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="pago">Pago<span class="required">*</span></label>
                        <div class="col-sm-9">
                            <input class="form-control money" type="text" id="pago" name="pago">
                            <span class="help-block"></span>
                        </div>
                    </div>

                    <div class="form-group ReturnChange">
                        <h4 class="col-sm-12" id="ReturnChange">Deuda <span>0</span> Soles</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
                    <button type="button" id="btnSavePago" onclick="savePago()" class="btn btn-primary">GUARDAR</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="cobro_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title"></h3>
            </div>
            <div class="modal-body form">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title text-dark">Lista de pagos</h3>
                            </div>
                            <!-- /.box-header -->
                            <div class="panel-body table-responsive">
                                <table id="tabla_cobro" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tipo de Cobro</th>
                                            <th>Metodo Pago</th>
                                            <th>Fecha/Hora</th>
                                            <th>Monto Cancelado</th>
                                            <th>Saldo</th>
                                            <th>Observacion</th>
                                            <th>Acciones BTN</th>
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
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<!-- Bootstrap modal -->
<div class="modal fade" id="productos_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:auto">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title text-center" id="title-productos_modal"></h3>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="tabla_productos" class="table table-bordered table-striped ">
                        <thead>
                            <tr class="text-title-panel">
                                <th>#</th>
                                <th>Fecha / Hora</th>
                                <th>Responsable</th>
                                <th>Tipo Precio</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Sub Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- fin modal comprobante  -->
<!-- Modal -->

<!-- <div class="modal fade" id="AddSale" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                    <div class="form-group ReturnChange">
                        <h3 id="ReturnChange">Vuelto <span>0</span> Soles</h3>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="button" id="vendiendo" class="btn btn-add" onclick="saleBtn()">Procesar</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!-- /.Modal -->
<script type="text/javascript">
    //for save method string
    var change;
    var table;
    var table_cobro;
    var subtotal = 0;
    var cant = 0;
    var CclienteNombre = "";
    var idCliente = "";
    var pago = 0;
    var recibido = 0;
    var tipoventa = "";
    var vuelto = 0;
    var descuento = "";
    var elementos = new Array();
    var cant = 0;
    var table_productos;
    $(document).ready(function() {
        tipocreditoproceso();
        metodopagoproceso();
        operacion_pagorealizado()
        $('#tipocard').hide();
        $("#cliente").select2({
            language: {
                noResults: function() {
                    return "No hay resultado";
                },
                searching: function() {
                    return "Buscando..";
                },
                inputTooShort: function() {
                    return "Debes ingresar mas caracteres...";
                }
            },
            placeholder: 'Seleccione un cliente',
            theme: "classic",
            minimumInputLength: 2,
            ajax: {
                url: "<?= $this->url ?>/buscarCliente",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    }
                },
                processResults: function(response) {
                    return {
                        results: response
                    }
                },
                cache: true
            },

        });
        procesoBuscar();
        $('.money').number(true, 2);
        $('.enteros').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        $("input").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("textarea").keyup(function() {
            $(this).parent().parent().removeClass('has-error');
            $(this).next().empty();
        });
        $("select").change(function() {
            $(this).parent().parent().removeClass('has-error');
            /* $(this).next().empty(); */
            if ($(this).attr("id") == "caja" || $(this).attr("id") == "cajacredito" || $(this).attr("id") == "tcomprobantecredito") {
                $(this).next().empty();
            }
            $(this).next().next().empty();
        });
        $('#metodopago').change(function(e) {
            metodopagoproceso();
        });
        $('#pago').on('keyup', function() {
            this.change = parseFloat($('#monto').val()) - parseFloat($(this).val());
            if (this.change < 0) {
                $('#ReturnChange span').text(this.change.toFixed(2));
                $('#ReturnChange span').addClass("red");
                $('#ReturnChange span').removeClass("light-blue");
            } else {
                $('#ReturnChange span').text(this.change.toFixed(2));
                $('#ReturnChange span').removeClass("red");
                $('#ReturnChange span').addClass("light-blue");
            }
        });

        $('#pagocredito').on('keyup', function() {
            operacionpago();
        });

        $('#descuentocredito').on('keyup', function() {
            operacionpago();
        });

    });

    function operacionpago() {
        let descuentocredito = $("#descuentocredito").val() == "" ? 0 : $("#descuentocredito").val();
        let pagocredito = $("#pagocredito").val() == "" ? 0 : $("#pagocredito").val()
        let operacion = parseFloat(pagocredito) - (parseFloat($("#montocredito").text()) - parseFloat(descuentocredito));
        if (operacion < 0) {
            $('#vueltocredito span').text(operacion.toFixed(2));
            $('#vueltocredito span').addClass("red");
            $('#vueltocredito span').removeClass("light-blue");
        } else {
            $('#vueltocredito span').text(operacion.toFixed(2));
            $('#vueltocredito span').removeClass("red");
            $('#vueltocredito span').addClass("light-blue");
        }
    }

    function tipocreditoproceso() {
        if ($("#tipocredito").val() == "1") {
            $("#pendienteproceso").show("fast");
            $("#content-cliente").hide("fast");
            $("#content-fecha").show("fast");
            $("#tiposelect").text("CREDITO DE VENTAS");
        } else {
            $("#pendienteproceso").hide("fast");
            $("#content-cliente").show("fast");
            $("#content-fecha").hide("fast");
            $("#tiposelect").text("CREDITO DE CLIENTES");
        }
    }

    function metodopagoproceso() {
        if ($('#metodopago').val() == 'EFECTIVO') {
            $('#tipocard-creditoventas').hide("fast");
            $('#numberoperacion').hide("fast");
            $('#tipotarjeta').removeAttr('required');
            $('#operacion').removeAttr('required');
        } else {
            if ($('#metodopago').val() == 'TARJETA') {
                $('#tipocard-creditoventas').show("fast");
                $('#tipotarjeta').attr('required', 'required');
            } else {
                $('#tipocard-creditoventas').hide("fast");
            }
            $('#numberoperacion').show("fast");
            $('#operacion').attr('required', 'required');
        }
    }

    /* function saleBtn() {
        $('#AddSale').modal('hide');
        $('#cabeceraSerie').html("Venta Núm:.: " + this.subtotal);
        var today = new Date();
        var date = today.getDate() + '-' + (today.getMonth() + 1) + '-' + today.getFullYear();
        $('#cabeceraFecha').html("<b>Fecha: </b>" + date);
        $('#cabeceraCliente').html("<b>Cliente: </b>" + this.CclienteNombre);
        cont = 0;
        var texto = "";
        while (cont < this.elementos.length) {
            texto += "<tr><td>" + cont + "</td>" +
                "<td>" + this.elementos[cont].nombre + "</td>" +
                "<td>" + this.elementos[cont].cantidad + "</td>" +
                "<td>" + this.elementos[cont].SubTotal + "</td></tr>";

            cont++;
        }
        texto += "<tr>" +
            "<td style='text-align:left;font-weight:bold;'>Total Items</td>" +
            "<td style='text-align:right; padding-right:1.5%;' id='comprobanteItems'></td>" +
            "<td style='text-align:left; padding-left:1.5%;'>Total</td>" +
            "<td style='text-align:right;font-weight:bold;' id='comprobanteTotal'></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='2' class='filaSup'>Grand Total</td>" +
            "<td colspan='2' class='filaInf' id='comprobanteGT'><span></span></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='2' class='filaSup'>Descuento</td>" +
            "<td colspan='2' class='filaInf' id='comprobanteDescuento'><span></span></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='2' class='filaSup'>Pagado</td>" +
            "<td colspan='2' class='filaInf' id='comprobantePago'><span></span></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='2' class='filaSup'>Recibido</td>" +
            "<td colspan='2' class='filaInf' id='comprobanteRecibido'><span></span></td>" +
            "</tr>" +
            "<tr>" +
            "<td colspan='2' class='filaSup'>Vuelto</td>" +
            "<td colspan='2' class='filaInf' id='comprobanteVuelto'><span></span></td>" +
            "</tr>";
        if (this.change === undefined) this.change = 0.0;
        var desc = $("#zdescuento").val();
        if (desc === null) desc = 0.0;
        $('#filaComprobante').html(texto);
        $("#comprobanteGT span").text(this.subtotal + " Soles");
        $("#comprobanteVuelto span").text(this.change + " Soles");
        $("#comprobantePago span").text($("#zmonto").val() + " Soles");
        $("#comprobanteRecibido span").text($("#zpago").val() + " Soles");
        $("#comprobanteDescuento span").text(desc + " Soles");
        $('#comprobante').modal('show');
    } */

    function saleBtn() {
        $('#vendiendo').html("<i class='fa fa-spinner fa-spin'></i>");
        $('#vendiendo').attr("disabled", true);
        $.ajax({
            url: "<?= $this->url ?>/ajax_procesarCredito/" + $("#credidoseleccionado").val(),
            type: "POST",
            data: $("#form_credito").serialize(),
            dataType: "JSON",
            success: function(data) {
                if (data.status) {
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: 'Procesado con éxito'
                    });
                    generadoCliente();
                    $("#AddSale").modal("hide");
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#vendiendo').html("Procesar");
                $('#vendiendo').attr("disabled", false);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $('#vendiendo').html("Procesar");
                $('#vendiendo').attr("disabled", false);
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: errorThrown
                });
            }
        });
    }

    function pagar() {
        $.ajax({
            url: "<?= $this->url ?>/pagos",
            type: "POST",
            data: {
                "subtotal": this.subtotal,
                "pago": $("#zpago").val(),
                "tipoventa": this.tipoventa,
                "vuelto": this.change,
                "descuento": $("#zdescuento").val(),
                // "totalItems":  this.elementos.length,
                "datos": this.elementos,
                "empresa": $("#empresa").val(),
                "cliente": this.idCliente,
            },
            dataType: "JSON",
            success: function(data) {
                Lobibox.notify('success', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Procesado con éxito'
                });
                location.reload();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: errorThrown
                });
                location.reload();
            }
        });

    }

    function cobrar() {
        $('#AddSale').modal('show');
    }

    function tipoPagocredito() {
        if ($('#metodopagocredito').val() == 'EFECTIVO') {
            $('#tipocardcredito').hide("fast");
            $('#content-n_operacioncredito').hide("fast");
        } else {
            if ($('#metodopagocredito').val() == 'TARJETA') {
                $('#tipocardcredito').show("fast");
            } else {
                $('#tipocardcredito').hide("fast");
            }
            $('#content-n_operacioncredito').show("fast");
        }
    }

    function generadoCliente() {
        $("#tabla_wrapper").hide();
        $("#tablaCliente").show();
        let cliente = $("#cliente").val();
        table = $('#tablaCliente').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "destroy": true,
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list_generadocobrocliente",
                "data": {
                    'tienda': $('#tienda').val(),
                    'cliente': cliente
                },
                "type": "POST"
            },
        });
    }

    function procesoBuscar() {
        if ($("#tipocredito").val() == "1") {
            generado();
        } else {
            if ($("#cliente").children().length > 0) {
                generadoCliente();
            } else {
                $("#cliente").parent().addClass("has-error");
                $("#cliente").next().next().text("Debe seleccionar un cliente");
            }
        }
    }

    function seleccionarTodo(tipo) {
        var ztable = $('#tablaCliente').DataTable();
        var numrows = ztable.data().length;
        var i = 1;
        if (tipo == 1) {
            while (i < numrows) {
                $("#chk_" + i).prop("checked", 'checked');
                i++;
            }

        }
        if (tipo == 2) {
            while (i < numrows) {
                $("#chk_" + i).prop("checked", 'checked');
                i++;
            }

        }
    }

    function returnPos(arr, id) {
        var i = 0;
        while (i < arr.length) {
            if (arr[i].id == id) return i;
            i++;
        }
    }

    function agregarPago(idcliente, id, cantii, tott, cli, zdocumento, prd) {
        this.idCliente = idcliente;
        if (zdocumento.length == 11) $("#factura").show();
        else $("#factura").hide();
        var clientName = cli;
        var prdName = prd;
        while (prdName.includes("_")) {
            prdName = prdName.replace("_", " ");
        }
        while (clientName.includes("_")) {
            clientName = clientName.replace("_", " ");
        }
        var datosArray = {
            "id": id,
            "nombre": prdName,
            "cantidad": cantii,
            "SubTotal": tott,
        }

        if ($("#chk_" + id).is(':checked')) {
            this.subtotal += Number.parseFloat(tott);
            this.cant += Number.parseInt(cantii);
            this.elementos.push(datosArray);
        } else {
            this.subtotal -= Number.parseFloat(tott);
            this.cant -= Number.parseInt(cantii);
            this.elementos.splice(returnPos(this.elementos, id), 1);
        }

        $("#zmonto").val(this.subtotal);
        if ($("#zpago").val() == "" || $("#zpago").val() === undefined) $("#zpago").val(this.subtotal);
        this.pago = $("#zpago").val();
        this.descuento = $("#comprobanteDescuento").val();
        this.tipoventa = $("#ztcomprobante").val();
        $("#zinputdocumento").val(zdocumento);
        $("#customerName span").text(clientName);
        $("#MontoPagar2 span").text(this.subtotal);
        $("#ItemsNum2 span").text(this.cant);
        $("#zdocumento span").text(zdocumento);
        this.CclienteNombre = clientName;
        // console.log(this.elementos);
    }

    function generado() {
        $("#tablaCliente").hide();
        $("#tabla_wrapper").show();
        table = $('#tabla').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "destroy": true,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list_generadocobro",
                "data": {
                    'inicio': $('#finicio').val(),
                    'final': $('#factual').val(),
                    'tienda': $('#tienda').val()
                },
                "type": "POST"
            }
        });
    };

    function pendiente() {
        event.preventDefault();
        table = $('#tabla').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "destroy": true,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= $this->url ?>/ajax_list_pendientecobro/" + $('#tienda').val(),
                "type": "POST"
            },
        });
    };

    function verIngreso(id, tipo) {
        $("#tipocard").hide();
        $("#numberoperacion").hide();
        jQuery(".opcion").select2({
            width: '100%'
        });
        if (tipo == 0) {
            var controlador = 'ingresofaltante';
        } else {
            var controlador = 'ingresocredito';
        }
        $('#form_pago')[0].reset(); // reset form on modals
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        //Ajax Load data from ajax
        $.ajax({
            url: "<?= $this->url ?>/" + controlador + '/' + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                $('[name="id"]').val(data.id);
                $('[name="monto"]').val(data.montoactual);
                operacion_pagorealizado(); //? Cambios de tipo cobro
                metodopagoproceso(); //? Cambios de metodo pago
                $("#caja").empty();
                if (data.cajas.length > 0) {
                    for (value of data.cajas) {
                        $("#caja").append(`<option value = "${value.id}">${value.nombre} | ${value.descripcion}</option>`);
                    }
                } else {
                    $("#caja").append(`<option value = "0"> ¯\_(ツ)_/¯ LA TIENDA NO TIENE CAJAS REGISTRADAS </option>`);
                }

                $('#modal_pago').modal('show');
                $('.modal-title').text('CUENTA X COBRAR');

            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Error al obtener datos de ajax.'
                });
            }
        });
    };

    function operaciontiendapagar() {
        $.ajax({
            url: "<?= $this->url ?>/ajax_cajatiendapagar/" + $("#tienda_pagar").val(),
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $("#cajacredito").empty();
                if (data.length > 0) {
                    for (value of data) {
                        $("#cajacredito").append(`<option value = "${value.id}">${value.nombre} | ${value.descripcion}</option>`);
                    }
                } else {
                    $("#cajacredito").append(`<option value = "0"> ¯\_(ツ)_/¯ LA TIENDA NO TIENE CAJAS REGISTRADAS </option>`);
                }
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

    function savePago() {
        $('#btnSavePago').text('guardando...');
        $('#btnSavePago').attr('disabled', true);
        $.ajax({
            url: "<?= $this->url ?>/ajax_updatecobro",
            type: "POST",
            data: $('#form_pago').serialize(),
            dataType: "JSON",
            success: function(data) {
                //if success close modal and reload ajax table
                if (data.status) {

                    $('#modal_pago').modal('hide');
                    reload_table();
                    Lobibox.notify('success', {
                        size: 'mini',
                        position: 'top right',
                        msg: "El registro fue actualizado exitosamente."
                    });
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                        $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                    }
                }
                $('#btnSavePago').text('GUARDAR'); //change button text
                $('#btnSavePago').attr('disabled', false); //set button enable
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: "El registro no se pudo actualizar. Verifique la operación"
                });
                $('#btnSavePago').text('GUARDAR'); //change button text
                $('#btnSavePago').attr('disabled', false); //set button enable
            }
        });
    };

    function verPagos(venta, tipo) {
        $('#cobro_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('VER PAGO'); // Set Title to Bootstrap modal title
        if (tipo == 0) {
            var controlador = 'ajax_list_faltantes';
        } else {
            var controlador = 'ajax_list_creditos';
        }
        cargar_pagos(venta, controlador);
    };

    function cargar_pagos(venta, controlador) {
        table_cobro = $('#tabla_cobro').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "destroy": true,
            // Load data for the table's content from an Ajax source
            "ajax": {
                "url": "<?= $this->url ?>/" + controlador + "/" + venta,
                "type": "POST"
            },
        });
    };

    function imprimircomprobante(id, venta) {
        var Url = '<?= $this->url ?>/imprimircomprobante/' + id + "/" + venta;
        window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    };

    function reload_table() {
        table.ajax.reload(null, false); //reload datatable ajax
    };

    function reload_tablecobro() {
        table_cobro.ajax.reload(null, false); //reload datatable ajax
    };

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

    }

    function borrarpagos(id) {
        bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_deletecobro/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        //if success reload ajax table
                        reload_table();
                        reload_tablecobro();
                        Lobibox.notify('success', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'El registro fue eliminado exitosamente.'
                        });
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
                        });
                    }
                });
            }
        });
    };

    function operacion_pagorealizado() {
        let pagorealizado = $("#pagorealizado").val();
        if (pagorealizado == "CAJA") {
            $("#content-caja").show("fast");
        } else {
            $("#content-caja").hide("fast");
        }
    }

    function operacion_pagorealizadocredito() {
        let pagorealizadocredito = $("#pagorealizadocredito").val();
        if (pagorealizadocredito == "CAJA") {
            $("#content-cajacredito").show("fast");
        } else {
            $("#content-cajacredito").hide("fast");
        }
    }

    function verProductos(idcredito) {
        $("#productos_modal").modal("show");
        $("#title-productos_modal").text("HISTORIAL DE PEDIDOS");
        detallesProductos(idcredito);
    }

    function detallesProductos(idcredito) {
        table_productos = $('#tabla_productos').DataTable({
            language: {
                "decimal": "",
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "destroy": true,
            "ajax": {
                "url": "<?= $this->url ?>/ajax_detallesproductos/" + idcredito,
                "type": "POST"
            },
        });
    }

    function eliminarItemCredito(idventadetalle) {
        bootbox.confirm("Seguro que desea Eliminar este registro?", function(result) {
            if (result === true) {
                $.ajax({
                    url: "<?= $this->url ?>/ajax_delete_item/" + idventadetalle,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        if (data.status) {
                            table_productos.ajax.reload(null, false);
                            reload_table();
                            Lobibox.notify('success', {
                                size: 'mini',
                                position: 'top right',
                                msg: 'El registro fue eliminado exitosamente.'
                            });
                        } else {
                            Lobibox.notify('warning', {
                                size: 'mini',
                                position: 'top right',
                                msg: 'No se encontro registro para poder eliminar'
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        Lobibox.notify('error', {
                            size: 'mini',
                            position: 'top right',
                            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
                        });
                    }
                });
            }
        });
    }

    function pagarcredito(idcredito) {
        event.preventDefault();
        $(`#btn-cobrar-${idcredito}`).html("<i class='fa fa-spin fa-spinner'></i>");
        $(`#btn-cobrar-${idcredito}`).attr("disabled", true);
        $.ajax({
            url: "<?= $this->url ?>/ajax_datapagarcredito/" + idcredito + "/" + $("#tienda_pagar").val(),
            type: "POST",
            dataType: "JSON",
            success: function(data) {
                $("#cajacredito").empty();
                if (data.cajas.length > 0) {
                    for (value of data.cajas) {
                        $("#cajacredito").append(`<option value = "${value.id}">${value.nombre} | ${value.descripcion}</option>`);
                    }
                } else {
                    $("#cajacredito").append(`<option value = "0"> ¯\_(ツ)_/¯ LA TIENDA NO TIENE CAJAS REGISTRADAS </option>`);
                }

                $("#credidoseleccionado").val(idcredito);
                $("#montocredito").text(data.montoactual);
                tipoPagocredito();
                $(`#btn-cobrar-${idcredito}`).html("<i class='fa fa-money'></i>");
                $(`#btn-cobrar-${idcredito}`).attr("disabled", false);
                $("#AddSale").modal("show");
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $(`#btn-cobrar-${idcredito}`).html("<i class='fa fa-money'></i>");
                $(`#btn-cobrar-${idcredito}`).attr("disabled", false);
                Lobibox.notify('error', {
                    size: 'mini',
                    position: 'top right',
                    msg: 'Error al obtener datos del credito'
                });
            }
        });
    }
</script>