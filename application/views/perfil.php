<div class="row">
  <div class="col-xs-12">
    <div class="panel panel-border panel-border-info">
      <div class="panel-heading">
        <h3 class="panel-title text-title-panel">
          Lista de <?= $this->titulo_controlador ?>
          <div class="pull-right">
            <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
            <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Nuevo"><i class="fa fa-plus"></i></a>
          </div>
          <div class="clearfix"></div>
        </h3>

      </div>
      <!-- /.box-header -->
      <div class="panel-body table-responsive">
        <table id="tabla" class="table table-bordered table-striped">
          <thead>
            <tr class="text-title-panel">
              <th>#</th>
              <th>Perfil</th>
              <th>Descripcion</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <!-- /.box -->
    </div>
  </div>
</div>

<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="" id="form" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="form-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Perfil<span class="required">*</span></label>
              <div class="col-md-10">
                <input id="nombre" name="nombre" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="chkCobradorCaja">¿Cobrador de caja?</label>
                <div class="col-md-10 material-switch pull-right">
                  <input id="chkCobradorCaja" name="chkCobradorCaja" type="checkbox"/>
                  <label for="chkCobradorCaja" class="label-success"></label>
                </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Descripcion<span class="required">*</span></label>
              <div class="col-md-10">
                <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
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
    </div>
  </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="menu_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="" id="menu" class="form-horizontal" autocomplete="off">
          <div class="form-body">
            <div class="form-group">
              <div class="col-md-10" id="listarmenu"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" id="btnSavemenu" onclick="savemenu()" class="btn btn-primary">Guardar</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<script type="text/javascript">
//for save method string
var save_method;
var table;
$(document).ready(function() {
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
    // Load data for the table's content from an Ajax source
    "ajax": {
      "url": "<?= $this->url ?>/ajax_list",
      "type": "POST"
    },
  });
  //set input/textarea/select event when change value, remove class error and remove text help block
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
  $("#perfil").mayusculassintildes();
});
function add() {
  save_method = 'add';
  $('#form')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  $('#modal_form').modal('show'); // show bootstrap modal
  $('.modal-title').text('Crear <?= $this->titulo_controlador ?>'); // Set Title to Bootstrap modal title
};
function save() {
  $('#btnSave').text('guardando...'); //change button text
  $('#btnSave').attr('disabled',true); //set button disable
  var url;
  if(save_method == 'add') {
    url = "<?= $this->url ?>/ajax_add";
    msgsuccess = "El registro fue creado exitosamente.";
    msgerror = "El registro no se pudo crear verifique las validaciones.";
  } else {
    url = "<?= $this->url ?>/ajax_update";
    msgsuccess = "El registro fue actualizado exitosamente.";
    msgerror = "El registro no se pudo actualizar. Verifique la operación";
  }

  if ($("input#chkCobradorCaja").is(":checked")) {
      $("input#chkCobradorCaja").val("1");
  }

  $.ajax({
    url : url,
    type: "POST",
    data: $('#form').serialize(),
    dataType: "JSON",
    success: function(data) {
      //if success close modal and reload ajax table
      if(data.status) {
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            position:"top right",
            msg: msgsuccess
          });
      } else {
        for (var i = 0; i < data.inputerror.length; i++) {
          $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
          $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
        }
      }
      $('#btnSave').text('Guardar'); //change button text
      $('#btnSave').attr('disabled',false); //set button enable
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Lobibox.notify('error', {
        size: 'mini',
        position:"top right",
        msg: msgerror
      });
      $('#btnSave').text('Guardar'); //change button text
      $('#btnSave').attr('disabled',false); //set button enable
    }
  });
};
function edit(id) {
  save_method = 'update';
  $('#form')[0].reset(); // reset form on modals
  $('.form-group').removeClass('has-error'); // clear error class
  $('.help-block').empty(); // clear error string
  //Ajax Load data from ajax
  $.ajax({
    url : "<?= $this->url ?>/ajax_edit/" + id,
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      $('[name="id"]').val(data.id);
      if(data.cobradorcaja === '1'){
        $('[name="chkCobradorCaja"]').prop('checked', true);
      }else{
        $('[name="chkCobradorCaja"]').prop('checked', false);
      }
      $('[name="nombre"]').val(data.nombre);
      $('[name="descripcion"]').val(data.descripcion);
      $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
      $('.modal-title').text('Modificar <?= $this->titulo_controlador ?>'); // Set title to Bootstrap modal title
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert('Error get data from ajax');
    }
  });
};
function borrar(id) {
  bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
    if (result === true) {
      $.ajax({
        url : "<?= $this->url ?>/ajax_delete/"+id,
        type: "POST",
        dataType: "JSON",
        success: function(data) {
          //if success reload ajax table
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            position:"top right",
            msg: 'El registro fue eliminado exitosamente.'
          });
        },
        error: function (jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            position:"top right",
            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
          });
        }
      });
    }
  });
};
function menu(id) {
  $.ajax({
    url : "<?= $this->url ?>/listarmenu/"+id,
    type: "POST",
    success: function(data) {
      $('#listarmenu').html(data);
      $('#menu_form').modal('show'); // show bootstrap modal
      $('.modal-title').text('Seleccionar menu'); // Set Title to Bootstrap modal title
      $("ul.checktree").checktree();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("error");
    }
  });
};
function savemenu() {
  $('#btnSavemenu').text('guardando...'); //change button text
  $('#btnSavemenu').attr('disabled',true); //set button disable
  // ajax adding data to database
  $.ajax({
    url : "<?= $this->url ?>/ajax_add_menu",
    type: "POST",
    data: $('#menu').serialize(),
    dataType: "JSON",
    success: function(data) {
      //if success close modal and reload ajax table
      if(data.status) {
          $('#menu_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            position:"top right",
            msg: "El registro fue creado exitosamente."
          });
      } else {
        for (var i = 0; i < data.inputerror.length; i++) {
          $('[name="'+data.inputerror[i]+'"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
          $('[name="'+data.inputerror[i]+'"]').next().text(data.error_string[i]); //select span help-block class set text error string
        }
      }
      $('#btnSavemenu').text('Guardar'); //change button text
      $('#btnSavemenu').attr('disabled',false); //set button enable
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Lobibox.notify('error', {
        size: 'mini',
        position:"top right",
        msg: "El registro no se pudo crear verifique las validaciones."
      });
      $('#btnSavemenu').text('Guardar'); //change button text
      $('#btnSavemenu').attr('disabled',false); //set button enable
    }
  });
};
function reload_table() {
  table.ajax.reload(null,false); //reload datatable ajax
};
</script>
