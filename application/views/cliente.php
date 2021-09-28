<!-- Page Content -->
<div class="row">
  <div class="col-xs-12">
    <div class="panel panel-border panel-border-info">
      <div class="panel-heading">
        <h3 class="panel-title text-title-panel">Lista de clientes
          <div class="pull-right">
            <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip" ><i class="fa fa-repeat"></i> RECARGAR</a>
            <a type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" onclick="add()" ><i class="fa fa-plus"></i> NUEVO</a>
          </div>
          <div class="clearfix"></div>
        </h3>
      </div>
      <div class="panel-body table-responsive">
        <table id="tabla" class="table table-striped table-bordered">
          <thead>
            <tr class="text-title-panel">
              <th>#</th>
              <th>Tipo</th>
              <th>Documento</th>
              <th>Nombre</th>
              <th>Direccion</th>
              <th>Accion</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- Button trigger modal -->

<!-- Modal -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar Cliente</h4>
      </div>
      <div class="modal-body form">
        <form action="#" role="form" id="form" autocomplete="off">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="form-body">
            <div class="form-group">
              <label for="tipo">Tipo Documento<span class="required">*</span></label>
              <select id="tipo" name="tipo" class="form-control">
                <option value="DNI">DNI</option>
                <option value="RUC">RUC</option>
              </select>
            </div>
            <div class="form-group">
              <label for="documento">Documento<span class="required">*</span></label>
              <div class="input-group">
                <input class="form-control enteros" id="documento" type="text" name="documento">
                <span class="help-block"></span>
                <span class="input-group-btn">
                  <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                    <span class="fa fa-search"></span>
                  </button>
                </span>
              </div>
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <label for="nombre">Nombre</label>
              <input id="nombre" name="nombre" class="form-control" type="text">
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <label for="apellido">Apellido</label>
              <input id="apellido" name="apellido" class="form-control" type="text">
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <label for="direccion">Direccion</label>
              <input id="direccion" name="direccion" class="form-control" type="text">
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <label for="telefono">Telefono</label>
              <input id="telefono" name="telefono" class="form-control enteros" type="text">
              <span class="help-block"></span>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" name="email" class="form-control" type="email">
              <span class="help-block"></span>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">GUARDAR</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->

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
        "type": "GET"
      },
    });
    //set input/textarea/select event when change value, remove class error and remove text help block
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
    $("#nombre").mayusculassintildes();
    $("#apellido").mayusculassintildes();
    $("#direccion").mayusculassintildes();
    $('#documento').attr('minLength', 8);
    $('#documento').attr('maxlength', 8);
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    //datatables
    $('#botoncito').on('click', function() {
      /*
            $(this).button('loading');
            $.ajax({
              method: 'post',
              url: "<?= $this->url ?>/consulta_reniec",
              data: {
                tipo: $('#tipo').val(),
                documento: $('#documento').val()
              },
              beforeSend: function() {
                $('[name="nombre"]').val("");
                $('[name="apellido"]').val("");
                $('[name="direccion"]').val("");
              },
              success: function(data) {
                $('#botoncito').button('reset');
                nombre = data.nombre;
                apellido = data.apellido;
                direccion = data.direccion;
                $('[name="nombre"]').val(nombre);
                $('[name="apellido"]').val(apellido);
                $('[name="direccion"]').val(direccion);
                console.error(data);
              },
              error: function(data) {
                $('#botoncito').button('reset');
                Lobibox.notify('error', {
                  size: 'mini',
                  position: "top right",
                  msg: 'Error al obtener datos de ajax.'
                });
              }
            });*/

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
        $("#documento").prop("placeholder", "BUSCAR POR RENIEC");
        $('#apellido').parent().show();
      } else if ($('#tipo').val() == 'RUC') { //muestro el div2 y oculto los demas
        $('#documento').attr('minLength', 11);
        $('#documento').attr('maxlength', 11);
        $('#nombre').siblings('label').text("Razón social");
        $("#documento").prop("placeholder", "BUSCAR POR SUNAT");
        $('#apellido').parent().hide();
      }
    });

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
          position: "top right",
          msg: msgerror
        });
        $('#btnSave').text('GUARDAR'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      }
    });
  };

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
        $('[name="tipo"]').val(data.tipodocumento);


        if ($('#tipo').val() == 'DNI') { //muestro el div1 y oculto los demas
          $('#documento').attr('minLength', 8);
          $('#documento').attr('maxlength', 8);
          $('#nombre').siblings('label').text("Nombre");
          $('#apellido').parent().show();

        } else if ($('#tipo').val() == 'RUC') { //muestro el div2 y oculto los demas
          $('#documento').attr('minLength', 11);
          $('#documento').attr('maxlength', 11);
          $('#nombre').siblings('label').text("Razón social");
          $('#apellido').parent().hide();

        }
        $('[name="documento"]').val(data.documento);
        $('[name="nombre"]').val(data.nombre);
        $('[name="apellido"]').val(data.apellido);
        $('[name="direccion"]').val(data.direccion);
        $('[name="telefono"]').val(data.telefono);
        $('[name="email"]').val(data.correo);
        $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Modificar <?= $this->titulo_controlador ?>'); // Set title to Bootstrap modal title
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

  function registroautomatico() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_registroautomatico",
      //url: "https://beta.pucallsystem.com/PucallPosV2.2/cliente/ajax_registroautomatico",
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        console.log(data.result1);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert("hay un error manco")
      }
    });
    
  }
  
</script>