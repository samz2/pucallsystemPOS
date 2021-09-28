<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Filtro por fecha</h3>
        </div>
        <form class="form-horizontal" autocomplete="off">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Fecha<span class="required">*</span></label>
              <div class="col-sm-5">
                <input class="form-control" id="finicio" type="date" name="finicio" value="<?= date('Y-m-d') ?>">
              </div>
              <div class="col-sm-5">
                <input class="form-control" id="factual" type="date" name="factual" value="<?= date('Y-m-d') ?>">
              </div>
            </div>
          </div>
          <div class="panel-footer text-center">
            <a onclick="generado()" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="top"><i class="fa fa-search"></i> BUSCAR</a>
            <a onclick="location.reload()" class="btn btn-success btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading" style="display:flex; align-items: center; justify-content: space-between">
          <h3 class="panel-title text-dark">Lista de <?= $this->titulo_controlador ?></h3>
          <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="fa fa-plus"></i> NUEVO INGRESO</a>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">
          <table id="tabla" class="table table-striped table-bordered">
            <thead>
              <tr>
                <th><b>#</b></th>
                <th><b>Tienda</b></th>
                <th><b>Caja</b></th>
                <th><b>Metodo de ingreso</b></th>
                <th><b>Usuario Responsable</b></th>
                <th><b>Tipo ingreso</b></th>
                <th><b>Recibo</b></th>
                <th><b>Concepto</b></th>
                <th><b>Observación</b></th>
                <th><b>Monto</b></th>
                <th><b>Fecha / Hora</b></th>
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
              <label class="control-label col-md-3">Caja <span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="caja" id="caja" class="form-control" style="width:100%">
                </select>
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
                <input id="fecha" name="fecha" class="form-control" type="date" value="<?= date('Y-m-d') ?>" readonly>
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
    generado();
    operaciontienda();
    $('.money').number(true, 2);
    $("#observacion").mayusculassintildes();
    $('#operacion').numeric();
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
    $("select").change(function() {
      if (this.id == "concepto") {
        $(this).next().next().empty();
      } else {
        $(this).next().empty();
      }
      $(this).parent().parent().removeClass('has-error');
    });
    $("input").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
  });

  function generado() {
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
        "url": "<?= $this->url ?>/ajax_list/" + $('#finicio').val() + '/' + $('#factual').val(),
        "type": "GET"
      },
    });
  };

  function add() {
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    operaciontienda();
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear <?= $this->titulo_controlador ?>'); // Set Title to Bootstrap modal title
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
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            if (data.inputerror[i] == "concepto") {
              $('[name="' + data.inputerror[i] + '"]').next().next().text(data.error_string[i]); //select span help-block class set text error string
            } else {
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
            }
          }
        }
        $('#btnSave').text('Guardar'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(this.url);
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

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function operaciontienda() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_operaciontienda/" + $("#tienda").val(),
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#caja").empty();
        for (value of data) {
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
</script>