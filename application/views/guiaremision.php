<?= $this->session->flashdata('mensaje') ?>
<?php if ($this->guiaremision) { ?>
  <div class="row">
    <div class="col-md-4">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Datos de <?= $this->titulo_controlador ?></h3>
        </div>
        <!-- form start -->
        <form action="" class="form-horizontal" id="form_principal" method="POST" role="form">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-3 control-label">EMPRESA</label>
              <div class="col-sm-9">
                <select class="form-control" name="empresa" id="empresa" onchange="save()">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . $value->razonsocial ?></option>
                  <?php } ?>
                </select>
                <input type="text" class="form-control" name="empresas" id="empresas" readonly>
              </div>
            </div>
            <div class="form-group" id="numeraciones">
              <label class="col-sm-2 control-label"><a id="vtipoventa"><span></span></a></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="numeracion" name="numeracion" readonly>
              </div>
            </div>
            <!-- <div class="form-group">
              <label class="col-sm-3 control-label" for="cliente">Cliente<span class="required">*</span></label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="hidden" class="form-control" name="cliente" id="cliente">
                  <input type="text" class="form-control" name="clientes" id="clientes">
                  <span class="input-group-btn">
                    <a id="botoncliente" onclick="grabarcliente()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                  </span>
                </div>
              </div>
            </div> -->
            <div class="form-group">
              <label class="col-sm-3 control-label" for="cliente">Transportista<span class="required">*</span></label>
              <div class="col-sm-9">
                <div class="input-group">
                  <input type="hidden" class="form-control" name="transportista" id="transportista">
                  <input type="text" class="form-control" name="transportistas" id="transportistas">
                  <span class="input-group-btn">
                    <a id="botoncliente" onclick="grabartransportista()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                  </span>
                </div>
              </div>
            </div>
            <!-- <div class="form-group">
              <label class="col-sm-2 control-label">MOLADIDAD TRASLADO</label>
              <div class="col-sm-10">
                <select class="form-control" id="modalidadtraslado" name="modalidadtraslado" onchange="modalidad()">
                  <option value="01">TRANSPORTE PUBLICO</option>
                  <option value="02">TRANSPORTE PRIVADO</option>
                </select>
              </div>
            </div> -->
            <div class="form-group">
              <label class="col-sm-3 control-label">Motivo Traslado</label>
              <div class="col-sm-9">
                <select class="form-control" id="motivostraslado" name="motivostraslado" onchange="save()">
                  <option value="01">VENTA</option>
                  <option value="14">VENTA SUJETA A CONFIRMACIÓN DEL COMPRADOR</option>
                  <option value="02">COMPRA</option>
                  <option value="04">TRASLADO ENTRE ESTABLECIMIENTOS DE LA MISMA EMPRESA</option>
                  <option value="18">TRASLADO EMISOR ITINERANTE CP</option>
                  <option value="08">IMPORTACION</option>
                  <option value="09">EXPORTACION</option>
                  <option value="19">TRASLADO A ZONA PRIMARIA</option>
                  <option value="13">OTROS</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="cliente">VENTA<span class="required">*</span></label>
              <div class="col-sm-10">
                <input type="hidden" class="form-control" name="venta" id="venta">
                <input type="text" class="form-control" name="ventas" id="ventas">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">CLIENTE</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="nombrecliente" name="nombrecliente" readonly>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="panel-footer text-center" id="botones"></div>
        </form>
      </div>
    </div>

    <div class="col-md-8">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Detalle de <?= $this->titulo_controlador ?></h3>
        </div>
        <!-- form start -->
        <div class="panel-body">
          <form id="form_detalle" class="form-horizontal" method="post" role="form" autocomplete="off">
            <div class="row" id="addproduct">
              <div class="col-md-12">
                <div class="box-body table-responsive">
                  <table class="table table-striped">
                    <tr>
                      <td colspan="5">
                        <label>Descripcion</label>
                        <div class="col-md-12">
                          <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-keyboard-o"></i></span>
                            <input type="hidden" class="form-control" name="producto" id="producto">
                            <input type="text" class="form-control" name="productos" id="productos">
                            <span class="help-block"></span>
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-cubes"></i></span>
                          <input type="text" id="saldo" name="saldo" class="form-control" readonly>
                        </div>
                      </td>
                      <td>
                        <div class="input-group">
                          <span class="input-group-addon"><i class="fa fa-unsorted"></i></span>
                          <input type="text" id="cantidad" name="cantidad" class="form-control" onkeydown="if(event.keyCode == 13) { savedetalle() }">
                          <span class="help-block"></span>
                        </div>
                      </td>
                      <td>
                        <button type="button" id="btnSave" onclick="savedetalle()" class="btn btn-success" title="Añadir"><i class="icon-white icon-plus"></i> A</button>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </form>
          <div class="row">
            <div class="box-body table-responsive">
              <table id="tabla_detalle" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Cant.</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php } else { ?>
  <div class="row" id="cuerpo_comprobante">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Filtro por fecha</h3>
        </div>
        <form class="form-horizontal" autocomplete="off">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="fecha">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Empresa<span class="required">*</span></label>
              <div class="col-sm-10">
                <select id="empresa" name="empresa" class="form-control" required>
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->razonsocial ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="panel-footer text-center">
            <a onclick="generar()" class="btn btn-warning" data-toggle="tooltip" title="GENERAR"><i class="fa fa-upload"></i></a>
            <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip" title="PENDIENTE"><i class="fa fa-upload"></i></a>
            <a href="<?= $this->url ?>/crear" class="btn btn-primary" data-toggle="tooltip" title="NUEVO"><i class="fa fa-plus"></i></a>
            <a onclick="location.reload()" class="btn btn-success" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-md-12" id="respuesta_proceso"></div><hr>
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-dark">Lista de <?= $this->titulo_controlador ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive" >
		
          <table id="tabla" class="table table-striped table-bordered">
            <thead>
			<tr>
			<th>#</th>
			<th>Empresa</th>
			<th>Cliente</th>
			<th>Guia Remision</th>
			<th>Nro Doc</th>
			<th>Estado</th>
			<th>Sunat</th>
			<th>Fecha</th>
			<th>Acciones</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
          </table>
		  
		  <table id="tablapendientes" class="table table-striped table-bordered">
            <thead>
			<tr>
			<th>#</th>
			<th>Empresa</th>
			<th>Cliente</th>
			<th>Guia Remision</th>
			<th>Nro Doc</th>
			<th>Estado</th>
			<th>Fecha</th>
			<th>Acciones</th>
			</tr>
			</thead>
			<tbody>
			</tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php } ?>

<!-- Modal Venta-->
<div class="modal fade" id="guiaremision_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form_guiaremision" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Ubigeo Partida</label>
              <div class="col-sm-10">
                <input class="form-control" id="ubigeosalida" name="ubigeosalida" readonly>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Direccion Partida</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="direccionsalida" name="direccionsalida" readonly></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Ubigeo Llegada</label>
              <div class="col-sm-10">
                <input type="text" class="form-control entero" id="ubigeodestino" name="ubigeodestino">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Direccion Llegada</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="direcciondestino" name="direcciondestino"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Peso Bruto</label>
              <div class="col-sm-10">
                <input type="text" class="form-control money" id="pesobruto" name="pesobruto" value="0">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha</label>
              <div class="col-sm-10">
                <input type="date" class="form-control" id="fechatraslado" name="fechatraslado" value="<?= date('Y-m-d') ?>">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSaveprocesar" onclick="saveprocesar()" class="btn btn-primary">GRABAR</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal ticket -->
<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" id="ticketModal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div id="printSection" class="modal-body" id="modal-body"></div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal Marca -->
<div class="modal fade" id="cliente_form" style="z-index: 99999;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="" class="form-horizontal" autocomplete="off" method="POST" id="form_cliente" role="form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="tipo">Tipo<span class="required">*</span></label>
              <div class="col-sm-10">
                <select id="tipo" name="tipo" class="form-control">
                  <option value="DNI">DNI</option>
                  <option value="RUC">RUC</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="documento">Documento<span class="required">*</span></label>
              <div class="col-sm-9">
                <input class="form-control" id="documento" type="text" name="documento">
                <span class="help-block"></span>
              </div>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                  <span class="glyphicon glyphicon-search"></span>
                </button>
              </span>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="nombre">Nombre<span class="required">*</span></label>
              <div class="col-sm-10">
                <input class="form-control" id="nombre" type="text" name="nombre">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="apellido">Apellido</label>
              <div class="col-sm-10">
                <input class="form-control" id="apellido" type="text" name="apellido">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="direccion">Direccion</label>
              <div class="col-sm-10">
                <input class="form-control" id="direccion" type="text" name="direccion">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">Celular</label>
            <div class="col-md-10">
              <input id="celular" name="celular" class="form-control enteros" type="text">
              <span class="help-block"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="btnSavecliente" onclick="savecliente()" class="btn btn-primary">GRABAR</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Marca -->
<div class="modal fade" id="transportista_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="" class="form-horizontal" autocomplete="off" method="POST" id="transportista_form" role="form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-4 control-label" for="modalidadtransporte">Modalidad de traslado<span class="required">*</span></label>
              <div class="col-sm-8">
                <select id="modalidadtransporte" name="modalidadtransporte" class="form-control">
                  <option value="01">TRANSPORTE PUBLICO</option>
                  <option value="02" selected>TRANSPORTE PRIVADO</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-4 control-label" id="documentoT" for="datosT">DNI CHOFER<span class="required">*</span></label>
              <div class="col-sm-7">
                <input class="form-control" id="datosT" type="text" name="datosT">
                <input id="datoT" type="hidden" name="datoT">
                <span class="help-block"></span>
              </div>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" onclick="grabarcliente()">
                  <i class="fa fa-plus"></i>
                </button>
              </span>
            </div> 
            <div class="form-group">
              <label class="col-sm-4 control-label" for="vehiculo">Placa Vehiculo<span class="required">*</span></label>
              <div class="col-sm-8">
                <input class="form-control" id="vehiculo" type="text" name="vehiculo">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" id="btnSavetransportista" onclick="save_transportista()" class="btn btn-primary">GRABAR</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
          </div>
      </form>
    </div>
  </div>
</div>



<script type="text/javascript">
  var table;
  var table_detalle;
  $(document).ready(function() {
    <?php if ($this->guiaremision) { ?>
      cargar_detalle();
      cambiarventa();
    <?php } else { ?>
      generar();
    <?php } ?>
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('.money').number(true, 2);
    $('#productos').focus();
    $("#productos").autocomplete({
      source: "<?= $this->url . '/completarproducto' ?>",
      minLength: 2,
      select: function(event, ui) {
        $("#producto").val(ui.item.producto);
        $("#saldo").val(ui.item.saldo);
        $("#cantidad").focus();
      }
    });
    $("#ventas").autocomplete({
      source: "<?= $this->url ?>/completarventa",
      minLength: 2,
      select: function(event, ui) {
        $("#venta").val(ui.item.venta);
        save();
        // cambiarventa();
      }
    });
    $("#clientes").autocomplete({
      source: "<?= base_url() ?>pedido/completarcliente",
      minLength: 2,
      select: function(event, ui) {
        $("#cliente").val(ui.item.cliente);
        save();
      }
    });
    $("#datosT").autocomplete({
      source: "<?= $this->url ?>/completarT",
      minLength: 2,
      select: function(event, ui) {
        $("#datoT").val(ui.item.transportista);
        // save();
      }
    });
    $("#modalidadtransporte").change(function(e){
      e.stopPropagation();
      if ($(this).val() == "02") {
        $("#documentoT").text("DNI CHOFER");
      }else if ($(this).val() == "01") {
        $("#documentoT").text("RUC EMPRESA");
      } else {
        alert("Ha ocurrdio algo inesperado");
      }
    });
    $("#nombreconductor").mayusculassintildes();
    $("#vehiculo").mayusculassintildes();
    $("#direcciondestino").mayusculassintildes();
    $("input").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("textarea").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("select").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
  });

  function generar() {
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
      //Feature control the processing indicator.
      "processing": true,
      "destroy": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/ajax_generar",
        "data": {
          'finicio': $('#finicio').val(),
          'factual': $('#factual').val(),
          'empresa': $('#empresa').val()
        },
        "type": "GET"
      },
    });
  };

  function pendiente() {
	//$("#tablapendie").hide();
    table = $('#tablapendientes').DataTable({
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
      //Feature control the processing indicator.
      "processing": true,
      "destroy": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/ajax_pendiente",
        "data": {
          'empresa': $('#empresa').val()
        },
        "type": "GET"
      },
    });
  };

  function cargar_detalle() {
    table_detalle = $('#tabla_detalle').DataTable({
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
      //Feature control the processing indicator.
      "processing": true,
      "destroy": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        url: "<?= $this->url ?>/ajax_list_detalle",
        type: 'GET'
      },
    });
  };

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function add() {
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear <?= $this->titulo_controlador ?>'); // Set Title to Bootstrap modal title
  };

  // function save() {
  //   $('#btnSave').text('guardando...'); //change button text
  //   $('#btnSave').attr('disabled', true); //set button disable
  //   // ajax adding data to database
  //   $.ajax({
  //     url: "<?= $this->url ?>/ajax_add",
  //     type: "POST",
  //     data: $('#form').serialize(),
  //     dataType: "JSON",
  //     success: function(data) {
  //       //if success close modal and reload ajax table
  //       if (data.status) {
  //         reload_table();
  //         $('#modal_form').modal('hide');
  //         Lobibox.notify('success', {
  //           size: 'mini',
  //           msg: "El registro fue creado exitosamente."
  //         });
  //       } else {
  //         for (var i = 0; i < data.inputerror.length; i++) {
  //           $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
  //           $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
  //         }
  //       }
  //       $('#btnSave').text('Guardar'); //change button text
  //       $('#btnSave').attr('disabled', false); //set button enable
  //     },
  //     error: function(jqXHR, textStatus, errorThrown) {
  //       Lobibox.notify('error', {
  //         size: 'mini',
  //         msg: "El registro no se pudo crear verifique las validaciones."
  //       });
  //       $('#btnSave').text('Guardar'); //change button text
  //       $('#btnSave').attr('disabled', false); //set button enable
  //     }
  //   });
  // };

  function borrar(id) {
    Lobibox.confirm({
      closeOnEsc: true,
      draggable: false,
      title: 'Eliminar',
      msg: "Esta seguro de elimanar el registro?",
      buttons: {
        ok: {
          'class': 'lobibox-btn lobibox-btn-default',
          text: 'OK',
          closeOnClick: true
        },
        cancel: {
          'class': 'lobibox-btn lobibox-btn-cancel',
          text: 'Cancel',
          closeOnClick: true
        },
      },
      callback: function($this, type) {
        if (type === 'ok') {
          // ajax delete data to database
          $.ajax({
            url: "<?= $this->url ?>/ajax_delete/" + id,
            type: "POST",
            dataType: "JSON",
            success: function(data) {
              //if success reload ajax table
              reload_table();
              Lobibox.notify('success', {
                size: 'mini',
                msg: 'El registro fue eliminado exitosamente.'
              });
            },
            error: function(jqXHR, textStatus, errorThrown) {
              Lobibox.notify('error', {
                size: 'mini',
                msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
              });
            }
          });
        } else if (type === 'cancel') {
          Lobibox.notify('info', {
            size: 'mini',
            msg: 'UD. cancelo la operacion de eliminar registro.'
          });
        }
      }
    });
  };

  function savedetalle() {
    $('#btnSaveDetalle').text('...'); //change button text
    $('#btnSaveDetalle').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_adddetalle',
      type: "POST",
      data: $('#form_detalle').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table_detalle();
          $('#form_detalle')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue creado exitosamente.'
          });
          cambiarventa();
          $('#btnSaveDetalle').text('A'); //change button text
          $('#btnSaveDetalle').attr('disabled', false); //set button enable
          $("#productos").focus();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveDetalle').text('A'); //change button text
          $('#btnSaveDetalle').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveDetalle').text('A'); //change button text
        $('#btnSaveDetalle').attr('disabled', false); //set button enable
      }
    });
  };

  function borrardetalle(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletedetalle/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_table_detalle();
            Lobibox.notify('success', {
              size: 'mini',
              msg: 'El registro fue eliminado exitosamente.'
            });
            cambiarventa();
            $("#productos").focus();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            Lobibox.notify('error', {
              size: 'mini',
              msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
            });
          }
        });
      }
    });
  };

  function save() {
    // ajax adding data to database
    $.ajax({
      url: "<?= $this->url ?>/ajax_update",
      type: "POST",
      data: $('#form_principal').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          Lobibox.notify('success', {
            size: 'mini',
            msg: "El registro fue actualizado exitosamente."
          });
          reload_table_detalle();
          cambiarventa();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: "El registro no se pudo actualizar. Verifique la operación"
        });
      }
    });
  };

  function cambiarcantidad(no) {
    $.ajax({
      url: "<?= $this->url ?>/ajax_updatecantidad",
      type: 'POST',
      data: {
        cantidad: $('#cantidad' + no).val(),
        detalle: $('#detalle' + no).val()
      },
      dataType: 'JSON',
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          Lobibox.notify('success', {
            size: 'mini',
            msg: "El registro fue actualizado exitosamente."
          });
          reload_table_detalle();
          cambiarventa();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Ocurrió un problema, favor contacte con el administrador del sistema.'
        });
      }
    });
  };

  function cambiarventa() {
    $('#botones').load("<?= $this->url ?>/botonpedido");
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_updateventa",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('#vtipoventa span').text('GUIA REMISION');
        $('[name="numeracion"]').val(data.numeracion);
        $('[name="empresa"]').val(data.empresa);
        $('[name="empresas"]').val(data.razonsocial);
        $('[name="venta"]').val(data.venta);
        $('[name="ventas"]').val(data.nombrecompleto);
        $('[name="ubigeosalida"]').val(data.ubigeosalida);
        $('[name="direccionsalida"]').val(data.direccionsalida);
        $('[name="nombrecliente"]').val(data.nombrecliente);
        $('[name="motivostraslado"]').val(data.motivostraslado);
        $('[name="modalidadtraslado"]').val(data.modalidadtraslado);
        if (data.contador > 0) {
          $('#empresas').attr('disabled', true);
          $('#empresa').hide();
          $('#empresas').show();
        } else {
          $('#empresas').attr('disabled', false);
          $('#empresa').show();
          $('#empresas').hide();
        }
        if (data.estado === '0') {
          $('#usuarios').attr('disabled', false);
          $('#clientes').attr('disabled', false);
          $('#ventas').attr('disabled', false);
          $('#direccion').attr('disabled', false);
          $('#modalidadtraslado').attr('disabled', false);
          $('#motivostraslado').attr('disabled', false);
          $('#numeraciones').hide();
          $('#addproduct').show();
        }
        if (data.estado === '1') {
          $('#usuarios').attr('disabled', true);
          $('#clientes').attr('disabled', true);
          $('#ventas').attr('disabled', true);
          $('#direccion').attr('disabled', true);
          $('#motivostraslado').attr('disabled', true);
          $('#modalidadtraslado').attr('disabled', true);
          $('#numeraciones').show();
          $('#addproduct').hide();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function reload_table_detalle() {
    table_detalle.ajax.reload(null, false); //reload datatable ajax
  };

  function grabar() {
    $('#guiaremision_form').modal('show');
    $('.modal-title').text('GRABAR GUIA REMISION');
  };

  function saveprocesar() {
    $('#btnSaveprocesar').text('guardando...'); //change button text
    $('#btnSaveprocesar').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addprocesar',
      type: "POST",
      data: $('#form_guiaremision').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#guiaremision_form').modal('hide');
          $('#form_guiaremision')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue creado exitosamente.'
          });
          reload_table_detalle();
          cambiarventa();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSaveprocesar').text('GRABAR'); //change button text
        $('#btnSaveprocesar').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveprocesar').text('GRABAR'); //change button text
        $('#btnSaveprocesar').attr('disabled', false); //set button enable
      }
    });
  };

  function imprimir(id) {
    $.ajax({
      url: "<?= $this->url ?>/imprimir/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR GUIA REMISION');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function modalidad() {
    const modalidadTraslado = $("#modalidadtraslado").val();
    if (modalidadTraslado) {

    }
  }

  function grabartransportista() {
    $('#transportista_modal').modal('show');
    $('.modal-title').text('CREAR TRANSPORTISTA');
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
          reload_table_detalle();
          $('#cliente_form').modal('hide');
          $('#form_cliente')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue creado exitosamente.'
          });
          // cambiarventa();
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
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSavecliente').text('GRABAR'); //change button text
        $('#btnSavecliente').attr('disabled', false); //set button enable
      }
    });
  };

  function save_transportista() {
    $('#btnSavetransportista').text('guardando...'); //change button text
    $('#btnSavetransportista').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addtransportista',
      type: "POST",
      data: $('#transportista_form').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table_detalle();
          $('#transportista_modal').modal('hide');
          $('#transportista_form')[0].reset();
          $("#transportistas").val(data.transportista);
          Lobibox.notify('success', {
            size: 'mini',
            msg: 'El registro fue creado exitosamente.'
          });
          // cambiarventa();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSavetransportista').text('GRABAR'); //change button text
        $('#btnSavetransportista').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSavetransportista').text('GRABAR'); //change button text
        $('#btnSavetransportista').attr('disabled', false); //set button enable
      }
    });
  };

  function grabarcliente() {
    $('#cliente_form').modal('show');
    $('.modal-title').text('CREAR CLIENTE');
  };

  $('#botoncito').on('click', function() {
    $(this).button('loading');
    if ($('#tipo').val() == 'DNI') {
      var url = "https://dni.optimizeperu.com/api/persons/" + $('#documento').val() + "?format=json";
    } else {
      var url = "https://dni.optimizeperu.com/api/company/" + $('#documento').val() + "?format=json";
    }
    $.ajax({
      method: 'GET',
      url: url,
      beforeSend: function() {
        $('[name="nombre"]').val("");
        $('[name="apellido"]').val("");
        $('[name="direccion"]').val("");
      },
      success: function(data) {
        $('#botoncito').button('reset');
        if ($('#tipo').val() == 'DNI') {
          nombre = data.name;
          if (data.first_name === undefined) {
            apellido = '';
          } else {
            apellido = data.first_name + " " + data.last_name;
          }
          direccion = "";
        } else {
          nombre = data.razon_social;
          apellido = "";
          direccion = data.domicilio_fiscal;
        }
        $('[name="nombre"]').val(nombre);
        $('[name="apellido"]').val(apellido);
        $('[name="direccion"]').val(direccion);
      },
      error: function(data) {
        $('#botoncito').button('reset');
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  });

  // $('#botoncitoTransportista').on('click', function() {
  //   $(this).button('loading');
  //   if ($('#tipo').val() == 'DNI') {
  //     var url = "https://dni.optimizeperu.com/api/persons/" + $('#documento').val() + "?format=json";
  //   } else {
  //     var url = "https://dni.optimizeperu.com/api/company/" + $('#documento').val() + "?format=json";
  //   }
  //   $.ajax({
  //     method: 'GET',
  //     url: url,
  //     beforeSend: function() {
  //       $('[name="nombre"]').val("");
  //       $('[name="apellido"]').val("");
  //       $('[name="direccion"]').val("");
  //     },
  //     success: function(data) {
  //       $('#botoncitoTransportista').button('reset');
  //       if ($('#tipo').val() == 'DNI') {
  //         nombre = data.name;
  //         if (data.first_name === undefined) {
  //           apellido = '';
  //         } else {
  //           apellido = data.first_name + " " + data.last_name;
  //         }
  //         direccion = "";
  //       } else {
  //         nombre = data.razon_social;
  //         apellido = "";
  //         direccion = data.domicilio_fiscal;
  //       }
  //       $('[name="nombre"]').val(nombre);
  //       $('[name="apellido"]').val(apellido);
  //       $('[name="direccion"]').val(direccion);
  //     },
  //     error: function(data) {
  //       $('#botoncito').button('reset');
  //       Lobibox.notify('error', {
  //         size: 'mini',
  //         msg: 'Error al obtener datos de ajax.'
  //       });
  //     }
  //   });
  // });

  function procesar_documento_electronico(id) {
  var light = $('#cuerpo_comprobante').parent();
  $("#vernotas").modal('hide');
  $(light).block({
    message: '<div class="loader"></div> <p><br />Enviando data, espera un momento!...</p>',
    overlayCSS: {
      backgroundColor: '#fff',
      opacity: 0.8,
      cursor: 'wait'
    },
    css: {
      border: 0,
      padding: 0,
      backgroundColor: 'none'
    }
  });
	$.ajax({
    url : '<?= $this->url ?>/emitir/' + id,
    method : 'POST',
    dataType : "json",
  }).then(function(data) {
    if(data.respuesta == 'ok') {
      swal({
        title: 'Resultado',
        text: 'Su comprobante se ha procesado correctamente!',
        html: true,
        type: "success",
        confirmButtonText: "Ok",
        confirmButtonColor: "#2196F3"
      }, function() {
        $("#respuesta_proceso").html('<div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        Su Documento se ha procesado correctamente...<br><br>\
        HASH: '+ data.hash_cpe +'</div>');
      });
    } else {
      swal({
        title: 'ERROR',
        text: data.mensaje,
        html: true,
        type: "error",
        confirmButtonText: "Ok",
        confirmButtonColor: "#2196F3"
      }, function() {
        $("#respuesta_proceso").html('<div class="alert alert-danger alert-styled-left alert-bordered">\
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>\
        ' + data.mensaje + '.\
        </div>');
      });
    }
    $(light).unblock();
    reload_table();
  }, function(reason) {
    console.log(reason);
    $(light).unblock();
    reload_table();
  });
};

</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>