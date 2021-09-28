<link rel='stylesheet' href='https://github.com/khadkamhn/lock-screen/raw/master/patt/pattern-lock.min.css'>
<link rel="stylesheet" href="<?= base_url() . RECURSOS ?>css/pattern.styl.css">
<style>
  #patronModal {
    z-index: 9999999 !important;
  }

  .modal {
    overflow: auto !important;
  }
</style>
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-border panel-border-info">
      <div class="panel-heading">
        <h3 class="panel-title text-title-panel">
          Lista de <?= $this->titulo_controlador ?>
          <div class="pull-right">
            <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
            <a onclick="add()" class="btn btn-primary btn-sm" data-toggle="tooltip"><i class="fa fa-plus"></i> NUEVO</a>
          </div>
          <div class="clearfix"></div>
        </h3>
      </div>
      <!-- /.box-header -->
      <div class="panel-body table-responsive">
        <table id="tabla" class="table table-striped table-bordered">
          <thead>
            <tr class="text-title-panel">
              <th>#</th>
              <th>Documento</th>
              <th>Nombre y apellido</th>
              <th>Usuario</th>
              <th>Perfil</th>
              <th>Acceso al sistema</th>
              <th></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>

<!-- Modal Pattern Lock -->
<div class="modal fade" id="patronModal" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="">Crear Patron</h3>
      </div>
      <div class="modal-body">
        <div class="ui-lock">
          <div class="mhn-ui-date-time">
            <div class="mhn-ui-time">6:02 PM</div>
            <div class="mhn-ui-day">Friday</div>
            <div class="mhn-ui-date">September 05, 2015</div>
          </div>
          <div class="mhn-lock-wrap">
            <div class="mhn-lock-title" data-title="Dibuja un patron"></div>
            <div class="mhn-lock"></div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
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
        <h3 class="modal-title text-center"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form" autocomplete="off">
          <div class="form-body">
            <input type="hidden" class="form-control" id="id" name="id">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Nombre<span class="required">*</span></label>
                  <input class="form-control" id="nombre" type="text" name="nombre">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Apellido<span class="required">*</span></label>
                  <input class="form-control" id="apellido" type="text" name="apellido">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">DNI</label>
                  <input class="form-control" id="documento" type="text" name="documento" minlength='8' maxlength="8">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Telefono<span class="required">*</span></label>
                  <input class="form-control" id="telefono" type="text" name="telefono">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <label class="control-label">Direccion</label>
                  <input class="form-control" id="direccion" type="text" name="direccion">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">¿Acceso al sistema?</label>
                  <select class="form-control" name="estado" id="estado" onchange="operacionacceso()">
                    <option value="1">NO</option>
                    <option value="0">SI</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="control-label">Empresa</label>
                  <select class="form-control" name="empresa" id="empresa">
                    <?php foreach ($empresas as $value) { ?>
                      <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>

            <div id="content-acceso-sistema">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Usuario<span class="required">*</span></label>
                    <input class="form-control" id="usuario" type="text" name="usuario" autocomplete="off">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Perfil</label>
                    <select class="form-control" name="perfil" id="perfil">
                      <?php foreach ($perfiles as $value) { ?>
                        <option value="<?= $value->id ?>"><?= $value->nombre ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Tipo de ingreso</label>
                    <select class="form-control" id="tipoingreso" name="tipoingreso" onchange="operaciontipoingreso()">
                      <option value="0">PATRON</option>
                      <option value="1">CONTRASEÑA</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Avatar</label>
                    <input class="form-control" type="file" accept="image/*" name="foto" id="foto">
                  </div>
                </div>
              </div>

              <div class="row" id="content-contraseña">
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Contraseña<span class="required">*</span></label>
                    <input class="form-control password" id="password" type="password" name="password">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label class="control-label">Repita Contraseña<span class="required">*</span></label>
                    <input class="form-control password" id="re_password" type="password" name="re_password">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>

              <div class="row" id="content-patron">
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="control-label">Patron<span class="required">*</span></label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="pin" name="pin" placeholder="****" readonly>
                      <span class="help-block"></span>
                      <span class="input-group-btn" style="vertical-align: top">
                        <button class="btn btn-default" type="button" data-toggle="modal" data-target="#patronModal">Go!</button>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>



          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">GUARDAR</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/pattern-user.lock.js"></script>
<script type="text/javascript">
  //for save method string
  var save_method;
  var table;
  $(document).ready(function() {
    operacionacceso();
    operaciontipoingreso();
    $("#nombre").mayusculassintildes();
    $("#apellido").mayusculassintildes();
    $("#direccion").mayusculassintildes();
    $('#dni').attr('minLength', 8);
    $('#dni').attr('maxlength', 8);
    $('#dni').numeric();
    $('#dni').bind('cut copy paste', function(e) {
      e.preventDefault();
    });
    //datatables
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
        "type": "GET"
      },
    });
    //set input/textarea/select event when change value, remove class error and remove text help block
    $("input").change(function() {
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("input").keyup(function() {
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("textarea").keyup(function() {
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("select").change(function() {
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
  });
  $('#password').bind('blur', function() {
    if ($(this).val() !== '') {
      $('#re_password').attr('required', 'required');
    } else {
      $('.re_password').removeAttr('required').valid();
    }
  });

  function add() {
    save_method = 'add';
    $("#id").val("");
    $('#form')[0].reset();
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $('#modal_form').modal('show');
    operacionacceso();
    operaciontipoingreso();
    $('.modal-title').text('REGISTRAR EMPLEADO');
  };

  function save() {
    $('#btnSave').text('GUARDANDO...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_add";
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_update";
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: new FormData($("#form")[0]),
      dataType: "JSON",
      contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
      processData: false,
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#modal_form').modal('hide');
          reload_table();
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: msgsuccess
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSave').text('GUARDAR'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: msgerror
        });
        $('#btnSave').text('GUARDAR'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      }
    });
  };

  function operacionacceso() {
    if ($("#estado").val() == "1") {
      $("#content-acceso-sistema").hide("fast");
    } else {
      $("#content-acceso-sistema").show("fast");
    }
  }

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function edit(id) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_edit/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="id"]').val(data.id);
        $('[name="usuario"]').val(data.usuario);
        $('[name="nombre"]').val(data.nombre);
        $('[name="apellido"]').val(data.apellido);
        $('[name="documento"]').val(data.documento);
        $('[name="direccion"]').val(data.direccion);
        $('[name="telefono"]').val(data.telefono);
        $('[name="perfil"]').val(data.perfil);
        $('[name="pin"]').val(data.pin);
        $('[name="estado"]').val(data.estado);
        $('[name="tipoingreso"]').val(data.inputpassword);
        operacionacceso();
        operaciontipoingreso();
        $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('MODIFICAR EMPLEADO'); // Set title to Bootstrap modal title
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

  function borrar(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_delete/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            $('#modal_form').modal('hide');
            reload_table();
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

  function desactivar(id) {
    bootbox.confirm("Seguro desea desactivar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_desactivar/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            $('#modal_form').modal('hide');
            reload_table();
            Lobibox.notify('success', {
              size: 'mini',
              position: 'top right',
              msg: 'El registro fue desactivado exitosamente.'
            });
          },
          error: function(jqXHR, textStatus, errorThrown) {
            Lobibox.notify('error', {
              size: 'mini',
              position: 'top right',
              msg: 'No se puede desactivar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
            });
          }
        });
      }
    });
  };

  // function activar(id) {
  //   bootbox.confirm("Seguro desea activar este registro?", function(result) {
  //     if (result === true) {
  //       $.ajax({
  //         url: "<?= $this->url ?>/ajax_activar/" + id,
  //         type: "POST",
  //         dataType: "JSON",
  //         success: function(data) {
  //           //if success reload ajax table
  //           $('#modal_form').modal('hide');
  //           reload_table();
  //           Lobibox.notify('success', {
  //             size: 'mini',
  //             position: 'top right',
  //             msg: 'El registro fue activado exitosamente.'
  //           });
  //         },
  //         error: function(jqXHR, textStatus, errorThrown) {
  //           Lobibox.notify('error', {
  //             size: 'mini',
  //             position: 'top right',
  //             msg: 'No se puede activar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
  //           });
  //         }
  //       });
  //     }
  //   });
  // };

  function activa(event, id) {
    // if (event.currentTarget.checked) {

    $.ajax({
      url: "<?= $this->url ?>/ajax_activar/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        //if success reload ajax table
        reload_table();
        Lobibox.notify('success', {
          size: 'mini',
          position: "top right",
          msg: 'Producto activado correctamente'
        });
      }
      // error: function(jqXHR, textStatus, errorThrown) {
      //   reload_table();
      //   Lobibox.notify('warning', {
      //     size: 'mini',
      //     position: "top right",
      //     msg: 'Advertencia: Producto desactivado'
      //   });
      // }
    });

    // }
  }

  function desactiva(event, id) {
    // if (event.currentTarget.checked) {

    $.ajax({
      url: "<?= $this->url ?>/ajax_desactivar/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        reload_table();
        Lobibox.notify('warning', {
          size: 'mini',
          position: "top right",
          msg: 'Advertencia: Producto desactivado'
        });
      },
      // error: function(jqXHR, textStatus, errorThrown) {
      //   reload_table();
      //   Lobibox.notify('warning', {
      //     size: 'mini',
      //     position: "top right",
      //     msg: 'Advertencia: Producto desactivado'
      //   });
      // }
    });
    // }
  }

  function operaciontipoingreso() {
    if ($("#tipoingreso").val() == "0") {
      $("#content-contraseña").hide("fast")
      $("#content-patron").show("fast")
    } else {
      $("#content-contraseña").show("fast")
      $("#content-patron").hide("fast")
    }
  }
</script>