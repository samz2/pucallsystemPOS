<?php if ($this->nota) { ?>
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
              <label class="control-label col-md-3">EMPRESA</label>
              <div class="col-md-9">
                <select class="form-control" name="empresa" id="empresa" onchange="save()">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . $value->razonsocial ?></option>
                  <?php } ?>
                </select>
                <input type="text" class="form-control" name="empresas" id="empresas" readonly>
              </div>
            </div>

            <div class="form-group" id="numeraciones">
              <label class="control-label col-md-3"><a id="vtipoventa"><span></span></a></label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="numeracion" name="numeracion" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3">VENTA<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="hidden" class="form-control" name="venta" id="venta">
                <div class="input-group">
                  <input type="text" class="form-control" name="ventas" id="ventas">
                  <span class="input-group-btn" id="">
                    <a id="cargador" onclick="cargar()" class="btn btn-default btn-sm" data-toggle="tooltip" title="DATOS"><i class="fa fa-eye"></i></a>
                    <a id="limpiador" onclick="limpiar()" class="btn btn-warning btn-sm" data-toggle="tooltip" title="LIMPIAR"><i class="fa fa-minus-circle"></i></a>
                  </span>
                </div>

                <span class="help-block"></span>

              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3">TIPO</label>
              <div class="col-md-9">
                <select id="tiponota" name="tiponota" class="form-control" onchange="save()">
                  <option value="07">NOTA DE CREDITO</option>
                  <option value="08">NOTA DE DEBITO</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group" id="motivodebitos">
              <label class="control-label col-md-3">MOTIVO</label>
              <div class="col-md-9">
                <select id="motivodebito" name="motivodebito" class="form-control" onchange="save()">
                  <option value="01">INTERESES POR MORA</option>
                  <option value="02">AUMENTO EN EL VALOR</option>
                  <option value="03">PENALIDADES/OTROS CONCEPTOS</option>
                </select>
              </div>
            </div>

            <div class="form-group" id="motivocreditos">
              <label class="control-label col-md-3">MOTIVO</label>
              <div class="col-md-9">
                <select id="motivocredito" name="motivocredito" class="form-control" onchange="save()">
                  <option value="01">ANULACIÓN DE LA OPERACION</option>
                  <option value="02">ANULACIÓN POR ERROR EN EL RUC</option>
                  <option value="03">CORRECCIÓN POR ERROR EN LA DESCRIPCIÓN</option>
                  <option value="04">DESCUENTO GLOBAL</option>
                  <option value="05">DESCUENTO POR ITEM</option>
                  <option value="06">DEVOLUCION TOTAL</option>
                  <option value="07">DEVOLUCION POR ITEM</option>
                  <option value="08">BONIFICACION</option>
                  <option value="09">DISMINUCIÓN EN EL VALOR</option>
                  <option value="10">OTROS CONCEPTOS</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3">VENDEDOR<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="hidden" class="form-control" name="usuario" id="usuario">
                <input type="text" class="form-control" name="usuarios" id="usuarios" autocomplete="off">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3">MONTO</label>
              <div class="col-md-9">
                <input readonly type="text" class="form-control" id="totales" name="totales">
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
          <h3 class="panel-title">
            Detalle de <?= $this->titulo_controlador ?>
          </h3>
        </div>
        <!-- form start -->
        <div class="panel-body">
          <div class="row">
            <div class="panel-body table-responsive">
              <table id="tabla_detalle" class="table table-striped table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>P/U</th>
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
                <select id="empresa" name="empresa" class="form-control">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->razonsocial . ' ' . $value->nombre ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="panel-footer text-center">
            <a onclick="generar()" class="btn btn-warning btn-sm" data-toggle="tooltip"><i class="fa fa-search"></i> BUSCAR</a>
            <a onclick="pendiente()" class="btn btn-danger btn-sm" data-toggle="tooltip"><i class="fa fa-clipboard"></i> PENDIENTES</a>
          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-md-12" id="respuesta_proceso"></div>
    <hr>
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title clearfix">
            <div class="pull-left">Lista de notas</div>
            <div class="pull-right">
              <a onclick="location.reload()" class="btn btn-yahoo btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
              <a href="<?= $this->url ?>/crear" class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="fa fa-plus"></i> NUEVO</a>
            </div>
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">
          <table id="tabla" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th><b>#</b></th>
                <th><b>Tipo Nota</b></th>
                <th><b>Tipo Venta</b></th>
                <th><b>Nímero</b></th>
                <th><b>Estado</b></th>
                <th><b>Sunat</b></th>
                <th><b>Monto</b></th>
                <th><b>Monto</b></th>
                <th><b>Acciones</b></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>
<?php } ?>

<!-- Modal ticket -->
<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">COMPROBANTE</h4>
      </div>
      <div class="modal-body">
        <div class="panel-body form-body table-responsive" id="printSection"></div>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal ticket -->
<div class="modal fade" id="vernotas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">COMPROBANTE</h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" autocomplete="off" method="post">
          <div class="panel-body form-body table-responsive" id="vernotasdetalle"></div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Modal Venta-->
<div class="modal fade" id="venta_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form_venta" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Venta</label>
              <div class="col-sm-10">
                <input class="form-control" type="hidden" id="venta" name="venta">
                <input class="form-control" type="text" id="ventas" name="ventas" readonly>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Monto</label>
              <div class="col-sm-10">
                <input readonly class="form-control" id="totales" name="totales">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group" id="metodo">
              <label class="col-sm-2 control-label" for="metodopago">Metodo de Pago*</label>
              <div class="col-sm-10">
                <select id="metodopago" name="metodopago" class="form-control">
                  <option value="EFECTIVO">EFECTIVO</option>
                  <option value="TARJETA">TARJETA</option>
                </select>
              </div>
            </div>

            <div class="form-group" id="tipocard">
              <label class="col-sm-2 control-label">Tipo Tarjeta</label>
              <div class="col-sm-10">
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
            </div>

            <div class="form-group" id="numberoperacion">
              <label class="col-sm-2 control-label">Número de operacion</label>
              <div class="col-sm-10">
                <input type="number" name="operacion" class="form-control" id="operacion">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group" id="descontado">
              <label class="col-sm-2 control-label">Descuento</label>
              <div class="col-sm-10">
                <input type="text" class="form-control money" id="descuento" name="descuento" value="0">
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Descripcion<span class="required">*</span></label>
              <div class="col-sm-10">
                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha</label>
              <div class="col-sm-10">
                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>">
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

<script type="text/javascript">
  var table;
  var table_detalle;
  const RUTA_API = "http://localhost:8000";
  const $impresoraSeleccionada = document.querySelector("#impresoraSeleccionada");
  const refrescarNombreDeImpresoraSeleccionada = () => {
    Impresora.getImpresora()
      .then(nombreImpresora => {
        $impresoraSeleccionada.textContent = '80mm Series Printer';
      });
  };
  $(document).ready(function() {
    <?php if ($this->nota) { ?>
      cargar_detalle();
      setTimeout(cambiarventa, 100);
    <?php } else { ?>
      generar();
    <?php } ?>
    $("#ventas").autocomplete({
      source: "<?= $this->url ?>/completarventa",
      minLength: 0,
      select: function(event, ui) {
        if (ui.item.venta != null) {
          $("#venta").val(ui.item.venta);
          save();
        }
      }
    });
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('.money').number(true, 2);
    $('#descripcion').mayusculassintildes();
    $('#operacion').attr('minLength', 4);
    $('#operacion').attr('maxlength', 4);
    $('#tiponota').change(function(e) {
      if ($('#tiponota').val() == '1') {
        $('#motivocreditos').show();
        $('#motivodebitos').hide();
        $('#montos').hide();
        $("#metodo").hide();
        $("#tipocard").hide();
        $("#numberoperacion").hide();
      } else {
        $('#motivocreditos').hide();
        $('#motivodebitos').show();
        $('#montos').show();
        $("#metodo").show();
        if ($('#metodopago').val() == 'TARJETA') {
          $("#tipocard").show();
          $("#numberoperacion").show();
        }
      }
    });

    $("#usuarios").autocomplete({
      source: "<?= $this->url ?>/autocompleteusuarios",
      minLength: 2,
      select: function(event, ui) {
        if (ui.item.usuario != null) {
          $("#usuario").val(ui.item.usuario);
          save();
        }
      }
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
      "destroy": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/ajax_list_generado/" + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val(),
        "type": "GET"
      },
    });
  };

  function pendiente() {
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
        "url": "<?= $this->url ?>/ajax_list_pendiente/" + $('#empresa').val(),
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
      "destroy": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        url: "<?= $this->url ?>/ajax_list_detalle",
        type: 'GET'
      },
    });
  };

  function borrar(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_delete/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_table();
            Lobibox.notify('success', {
              size: 'mini',
              position: "top right",
              msg: 'El registro fue eliminado exitosamente.'
            });
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
          position: "top right",
          msg: "El registro no se pudo actualizar. Verifique la operación"
        });
      }
    });
  };

  function cambiarprecio(producto, tipo) {
    $.ajax({
      data: {
        "producto": producto,
        "tipo": tipo
      },
      url: '<?= $this->url ?>/precio',
      type: 'post',
      success: function(data) {
        $('[name="precioventa"]').val(data.precioventa);
      }
    });
    $("#cantidad").focus();
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
        Lobibox.notify('success', {
          size: 'mini',
          position: "top right",
          msg: "El registro fue actualizado exitosamente."
        });
        reload_table_detalle();
        cambiarventa();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Ocurrió un problema, favor contacte con el administrador del sistema.'
        });
      }
    });
  };

  function cambiarsubtotal(no) {
    $.ajax({
      url: "<?= $this->url ?>/ajax_updatesubtotal",
      type: 'POST',
      data: {
        precio: $('#precio' + no).val(),
        detalle: $('#detalle' + no).val()
      },
      dataType: 'JSON',
      success: function(data) {
        Lobibox.notify('success', {
          size: 'mini',
          position: "top right",
          msg: 'Examen actualizado correctamente.'
        });
        reload_table_detalle();
        cambiarventa();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
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
        $('[name="tiponota"]').val(data.tiponota);
        $('[name="numeracion"]').val(data.numeracion);
        $('[name="motivocredito"]').val(data.motivo);
        $('[name="motivodebito"]').val(data.motivo);
        $('[name="venta"]').val(data.venta);
        $('[name="ventas"]').val(data.nombreventa);
        $('[name="totales"]').val(data.montototal);
        $('[name="pago"]').val(data.montototal);
        $('[name="empresa"]').val(data.empresa);
        $('[name="empresas"]').val(data.razonsocial);
        $('[name="usuario"]').val(data.usuario);
        $('[name="usuarios"]').val(data.nombreusuario);

        if (data.venta) {
          if (data.contador > 0) {
            $('#ventas').attr('disabled', true);
            $('#empresas').attr('disabled', true);
            $('#empresa').hide();
            $('#empresas').show();
            $('#cargador').hide();
            $('#limpiador').show();
          } else {
            $('#ventas').attr('disabled', false);
            $('#empresas').attr('disabled', false);
            $('#empresa').show();
            $('#empresas').hide();
            $('#cargador').show();
            $('#limpiador').hide();
          }
        } else {
          $('#ventas').attr('disabled', false);
          $('#empresas').attr('disabled', false);
          $('#empresa').show();
          $('#empresas').hide();
          $('#cargador').hide();
          $('#limpiador').hide();
        }
        if (data.tiponota === '07') {
          $('#motivocreditos').show();
          $('#motivodebitos').hide();
        } else {
          $('#motivocreditos').hide();
          $('#motivodebitos').show();
        }
        if (data.estado === '0') {
          $('#usuarios').attr('disabled', false);
          $('#botoncliente').show();
          $('#numeraciones').hide();
          $('#totalitos').hide();
          $('#tipoventas').show();
          $('#addproduct').show();
        }
        if (data.estado === '1') {
          $('#usuarios').attr('disabled', true);
          $('#botoncliente').hide();
          $('#numeraciones').show();
          $('#totalitos').show();
          $('#tipoventas').hide();
          $('#addproduct').hide();
          $("#limpiador").hide();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function cargar() {
    // ajax adding data to database
    $("#cargador").attr("disabled", true);
    $("#cargador").html("<i class='fa fa-spin fa-spinner'></i>");
    $.ajax({
      url: "<?= $this->url ?>/ajax_add_detalle",
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#cargador").attr("disabled", false);
        $("#cargador").html("<i class='fa fa-eye'></i>");
        reload_table_detalle();
        cambiarventa();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#cargador").attr("disabled", false);
        $("#cargador").html("<i class='fa fa-eye'></i>");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: "El registro no se pudo actualizar. Verifique la operación"
        });
      }
    });
  };

  function limpiar() {
    bootbox.confirm("Seguro desea Limpiar el Detalle?", function(result) {
      if (result === true) {
        $("#limpiador").attr("disabled", true);
        $("#limpiador").html("<i class='fa fa-spin fa-spinner'></i>");

        $.ajax({
          url: "<?= $this->url ?>/ajax_delete_detalle",
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_table_detalle();
            cambiarventa();
            $("#limpiador").attr("disabled", false);
            $("#limpiador").html("<i class='fa fa-minus-circle'></i>");
          },
          error: function(jqXHR, textStatus, errorThrown) {
            $("#limpiador").attr("disabled", false);
            $("#limpiador").html("<i class='fa fa-minus-circle'></i>");
            Lobibox.notify('error', {
              size: 'mini',
              position: "top right",
              msg: 'No se puede limpiar el Detalle por seguridad de su base de datos, Contacte al Administrador del Sistema'
            });
          }
        });
      }
    });
  };

  function reload_table_detalle() {
    table_detalle.ajax.reload(null, false); //reload datatable ajax
  };

  function grabar() {


    $('#venta_form').modal('show');
    $('.modal-title').text('PROCESAR NOTA');
    $("#tipocard").hide();
    $("#vencimiento").hide();
    $("#numberoperacion").hide();
    $('#operacion').attr('minLength', 4);
    $('#operacion').attr('maxlength', 4);
    if ($('#metodopago').val() == 'EFECTIVO') {
      $('#tipocard').hide();
      $('#numberoperacion').hide();
    } else {
      $('#tipocard').show();
      $('#numberoperacion').show();
    }


  };

  function saveprocesar() {
    $('#btnSaveprocesar').text('guardando...'); //change button text
    $('#btnSaveprocesar').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addprocesar',
      type: "POST",
      data: $('#form_venta').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table_detalle();
          $('#venta_form').modal('hide');
          $('#form_venta')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
          setTimeout(cambiarventa, 100);
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
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveprocesar').text('GRABAR'); //change button text
        $('#btnSaveprocesar').attr('disabled', false); //set button enable
      }
    });
  };

  function imprimir(id, tipoimpresora) {
    if (tipoimpresora === 0) {
      $.ajax({
        url: '<?= $this->url ?>/imprimir/' + id,
        type: 'POST',
      });
    }
    if (tipoimpresora === 1) {
      var Url = '<?= $this->url ?>/cpepdf/' + id;
      window.open(Url, 'Pruebas', 'fullscreen=yes, scrollbars=auto');
    }
    if (tipoimpresora === 2) {
      var request = $.ajax({
        url: "<?= $this->url ?>/getimprimir/" + id,
        method: "POST",
        dataType: "json"
      });
      request.done(function(msg) {
        let impresora = new Impresora(RUTA_API);
        impresora.setFontSize(1, 1);
        impresora.setAlign("center");
        if (msg.empresa.tipo == 0) {
          impresora.write(msg.empresa.nombre + "\n");
          impresora.write(msg.empresa.razonsocial + "\n");
        } else {
          impresora.write(msg.empresa.razonsocial + "\n");
        }
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write(msg.empresa.direccion + "\n");
          impresora.write(msg.empresa.distrito + " " + msg.empresa.provincia + " " + msg.empresa.departamento + "\n");
          impresora.write("RUC " + msg.empresa.ruc + "\n");
          impresora.write("TELF. " + msg.empresa.telefono + "\n");
          impresora.write(msg.ventas.tipoventa + " DE VENTA ELECTRONICA\n");
        } else {
          impresora.write("TICKET DE VENTA\n");
        }
        impresora.write(msg.ventas.serie + "-" + msg.ventas.numero + "\n");
        impresora.setAlign("left");
        impresora.write(msg.cliente.tipo + ": " + msg.cliente.documento + "\n");
        impresora.write("CLIENTE: " + msg.cliente.nombre + " " + msg.cliente.apellido + "\n");
        impresora.write("DIRECCION: " + msg.cliente.direccion + "\n");
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("FECHA DE EMISION: " + msg.ventas.created + " " + msg.ventas.hora + "\n");
          impresora.write("FECHA DE VENC.: " + msg.ventas.created + "\n");
          impresora.write("MONEDA: SOLES\n");
        }
        impresora.feed(2);
        impresora.write("[CANT.]   DESCRIPCION         P/U      TOTAL\n");
        $.each(msg.ventadetalle, function(index, value) {
          impresora.write(" " + value.nombre + "\n");
          impresora.write(" [ " + value.cantidad + " ]                     " + value.precio + "        " + value.subtotal + "\n");
        });
        impresora.setAlign("right");
        impresora.write("OP. EXONERADA: S/ " + msg.ventas.montototal + "\n");
        impresora.write("IGV: S/ 0.00\n");
        impresora.write("TOTAL: S/ " + msg.ventas.montototal + "\n");
        $.each(msg.pagos, function(index, value) {
          impresora.write(value.metodopago + ": S/ " + value.monto + "\n");
        });
        impresora.write("VUELTO: S/ " + msg.vuelto + "\n");
        impresora.setAlign("left");
        impresora.feed(2);
        impresora.write("IMPORTE EN LETRA: " + msg.importeletra + "\n");
        impresora.write("VENDEDOR: " + msg.usuario.nombre + " " + msg.usuario.apellido + "\n");
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("HASH: " + msg.ventas.hash + "\n");
          impresora.setAlign("center");
          impresora.qr(msg.codigoqr);
        }
        impresora.setAlign("center");
        impresora.write("NO SE ACEPTAN DEVOLUCIONES Y/O\n");
        impresora.write("CAMBIOS DESPUES DE LAS 24 HORAS\n");
        impresora.feed(2);
        if (msg.ventas.tipoventa !== 'OTROS') {
          impresora.write("REPRESENTACION IMPRESA DE\n");
          impresora.write("COMPROBANTE ELECTRONICO\n");
          impresora.write("AUTORIZADO MEDIANTE LA RESOLUCION\n");
          impresora.write("DE INTENDENCIA N°. 034-005-0005315\n");
        } else {
          impresora.write("GRACIAS POR SU COMPRA\n");
        }
        impresora.end().then();
      });
      request.fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
      });
    }
    if (tipoimpresora === 3) {
      $.ajax({
        url: "<?= $this->url ?>/pdfimprimir/" + id,
        type: "POST",
        success: function(data) {
          $('#printSection').html(data);
          $('#ticket').modal('show');
          $('.modal-title').text('COMPROBANTE'); // Set Title to Bootstrap modal title
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
  };

  function vernotas(id) {
    $.ajax({
      url: "<?= $this->url ?>/vernotas/" + id,
      type: "POST",
      success: function(data) {
        $('#vernotasdetalle').html(data);
        $('#vernotas').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("error");
      }
    });
  };

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
      url: '<?= $this->url ?>/emitir/' + id,
      method: 'POST',
      dataType: "json",
    }).then(function(data) {
      if (data.respuesta == 'ok') {
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
        HASH: ' + data.hash_cpe + '</div>');
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

  function addnotas() {
    $('#form_notas')[0].reset();
    $('#notas_modal').modal('show');
  };

  function savenotas() {
    $('#btnSavenotas').text('guardando...'); //change button text
    $('#btnSavenotas').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addnota',
      type: "POST",
      data: $('#form_notas').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table();
          $('#notas_modal').modal('hide');
          $('#form_notas')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSavenotas').text('GRABAR'); //change button text
        $('#btnSavenotas').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSavenotas').text('GRABAR'); //change button text
        $('#btnSavenotas').attr('disabled', false); //set button enable
      }
    });
  };

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>