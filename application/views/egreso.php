<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">
            Filtro por fecha
          </h3>
        </div>
        <form class="form-horizontal" autocomplete="off">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipo de egreso<span class="required">*</span></label>
              <div class="col-sm-10">
                <select class="form-control" id="tipoegreso">
                  <option value="CAJA">CAJA</option>
                  <option value="EMPRESA">EMPRESA</option>
                </select>
              </div>
            </div>
          </div>
          <div class="panel-footer text-center">
            <a onclick="generado()" class="btn btn-warning btn-sm" data-toggle="tooltip"><i class="fa fa-search"></i> BUSCAR</a>

          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-dark clearfix">
            <div class="pull-left">
              <span>Lista de <?= $this->titulo_controlador . "S" ?></span>
            </div>
            <div class="pull-right">
              <a onclick="location.reload()" class="btn btn-success btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
              <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="fa fa-plus"></i> NUEVO EGRESO</a>
            </div>
          </h3>
        </div>
        <div class="panel-body table-responsive" id="content-tablaCaja">
          <table id="tablaCaja" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th><b>#</b></th>
                <th><b>Empresa</b></th>
                <th><b>Usuario</b></th>
                <th><b>Caja</b></th>
                <th><b>Concepto</b></th>
                <th><b>Observación</b></th>
                <th><b>Monto</b></th>
                <th><b>Fecha</b></th>
                <th></th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <div class="panel-body table-responsive" id="content-tablaEmpresa">
          <table id="tablaEmpresa" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Empresa</th>
                <th>Proveedor</th>
                <th>Usuario</th>
                <th>Concepto</th>
                <th>Detalle</th>
                <th>Monto</th>
                <th>Fecha</th>
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

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title">Person Form</h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Tipo de Egreso<span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="tipoegresoproceso" id="tipoegresoproceso" onchange="tipoegresoprocesoselect()" class="form-control">
                  <option value="EMPRESA">EGRESOS DE LA TIENDA</option>
                  <option value="CAJA">CAJA</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group" id="content-tienda">
              <label class="control-label col-md-3">Tienda <span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="tienda" id="tienda" class="form-control" style="width:100%" onchange="operaciontienda()">
                  <?php foreach ($empresas as $empresa) { ?>
                    <option value="<?= $empresa->id ?>"><?= $empresa->ruc . " SERIE " . $empresa->serie . " | " . $empresa->nombre ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group" id="content-caja">
              <label class="control-label col-md-3">Caja<span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="caja" id="caja" class="form-control" style="width:100%">
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group" id="content-metodo-pago">
              <label class="col-sm-3 control-label" for="metodopago">Metodo de Pago*</label>
              <div class="col-sm-9">
                <select id="metodopago" name="metodopago" class="form-control" onchange="metodopagooperacion()">
                  <option value="EFECTIVO">EFECTIVO</option>
                  <option value="TARJETA">TARJETA</option>
                  <option value="DEPOSITO">DEPOSITO</option>
                  <option value="TRANSFERENCIA">TRANSFERENCIA</option>
                  <!-- <option value="PAGO ANTERIOR">PAGO ANTERIOR</option> -->
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group" id="tipocard">
              <label class="col-sm-3 control-label" for="ztipotarjeta">Tipo Tarjeta</label>
              <div class="col-sm-9">
                <i class="fa fa-cc-visa fa-2x" id="visa" aria-hidden="true"></i>
                <i class="fa fa-cc-mastercard fa-2x" id="mastercard" aria-hidden="true"></i>
                <i class="fa fa-cc-amex fa-2x" id="amex" aria-hidden="true"></i>
                <i class="fa fa-cc-discover fa-2x" id="discover" aria-hidden="true"></i>
                <select class="form-control" name="ztipotarjeta" id="ztipotarjeta">
                  <option value="VISA">VISA</option>
                  <option value="DISCOVER">DISCOVER</option>
                  <option value="MASTERCARD">MASTERCARD</option>
                  <option value="DINERS CLUB">DINERS CLUB</option>
                  <option value="AMERICAN EXPRESS">AMERICAN EXPRESS</option>
                </select>
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
              <label class="control-label col-md-3">Concepto<span class="required">*</span></label>
              <div class="col-md-9">
                <select id="concepto" name="concepto" class="form-control" style="width:100%">
                  <option value="">SELECCIONAR CONCEPTO</option>
                  <?php foreach ($conceptos as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->concepto ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Monto<span class="required">*</span></label>
              <div class="col-md-9">
                <input id="monto" name="monto" class="form-control money" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Observacion<span class="required">*</span></label>
              <div class="col-md-9">
                <textarea class="form-control" id="observacion" name="observacion"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Fecha</label>
              <div class="col-md-9">
                <input id="fecha" name="fecha" readonly class="form-control" type="date" value="<?= date('Y-m-d') ?>">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
  var table;
  $(document).ready(function() {
    operaciontienda();
    $("#concepto").select2({
      dropdownParent: $("#modal_form"),
      theme: "classic",
      language: {
        noResults: function() {
          return "No hay resultados";
        },
        searching: function() {
          return "Buscando..";
        },
        inputTooShort: function() {
          return "Debes ingresar mas caracteres...";
        }
      },
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
      if (this.id == "concepto") {
        $(this).next().next().empty();
      } else {
        $(this).next().empty();
      }
      $(this).parent().parent().removeClass('has-error');
    });
    generado();
    $('.money').number(true, 2);
    $("#observacion").mayusculassintildes();
    $('#operacion').numeric();
    tipoegresoprocesoselect();
    metodopagooperacion();
  });

  function operaciontienda() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_operaciontienda/" + $("#tienda").val(),
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#caja").empty();
        for(value of data){
          $("#caja").append(`<option value="${value.id}">${value.nombre}</option>`);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: "El registro no se pudo crear verifique las validaciones."
        });
      }
    });
  }

  function generado() {
    let tablaDataTable;
    if ($("#tipoegreso").val() == "CAJA") {
      $('#content-tablaCaja').show();
      $('#content-tablaEmpresa').hide();
      tablaDataTable = $('#tablaCaja');
    } else {
      $('#content-tablaCaja').hide();
      $('#content-tablaEmpresa').show();
      tablaDataTable = $('#tablaEmpresa');
    }
    table = tablaDataTable.DataTable({
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
        "url": "<?= $this->url ?>/ajax_list/" + $('#finicio').val() + '/' + $('#factual').val() + '/' + $("#tipoegreso").val(),
        "type": "GET"
      },
    });
  };

  function add() {
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    tipoegresoprocesoselect()
    $('#modal_form').modal('show');
    $('.modal-title').text('Crear <?= $this->titulo_controlador ?>');
  };

  function save() {
    $('#btnSave').text('guardando...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: "<?= $this->url ?>/ajax_add",
      type: "POST",
      data: $('#form').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: "El registro fue creado exitosamente."
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            if (data.inputerror[i] == "concepto") {
              $('[name="' + data.inputerror[i] + '"]').next().next().text(data.error_string[i]); //select span help-block class set text error string
            } else {
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
            }
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class

          }
        }
        $('#btnSave').text('Guardar'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: "El registro no se pudo crear verifique las validaciones."
        });
        $('#btnSave').text('Guardar'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      }
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
            if (data.status) {
              $('#modal_form').modal('hide');
              reload_table();
              Lobibox.notify('success', {
                size: 'mini',
                position: "top right",
                msg: 'El registro fue eliminado exitosamente.'
              });
            } else {
              Lobibox.alert("info", {
                title: "INFORMACION",
                msg: "NO SE PUEDE ELIMINAR PORQUE LA CAJA YA ESTA CERRADA"
              });
            }
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

  function tipoegresoprocesoselect() {
    if ($("#tipoegresoproceso").val() == "EMPRESA") {
      $("#content-caja").hide("fast");
      $("#content-metodo-pago").show("fast");
    } else {
      $("#content-caja").show("fast");
      $("#content-metodo-pago").hide("fast");
    }
    operaciontienda();
  }

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function metodopagooperacion() {
    if ($('#metodopago').val() == 'EFECTIVO') {
      $('#tipocard').hide("fast");
      $('#numberoperacion').hide("fast");
    } else {
      if ($('#metodopago').val() == 'TARJETA') {
        $('#tipocard').show("fast");
      } else {
        $('#tipocard').hide("fast");
      }
      $('#numberoperacion').show("fast");
    }
  }
</script>