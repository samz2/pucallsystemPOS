<!-- Page Content -->
<br>
<div class="container" style="margin-top: 35px;">
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-border-info">
        <div class="panel-heading">
          <h3 class="panel-title text-title-panel">Lista de categorias
            <div class="pull-right">
              <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
              <a type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" onclick="add()"><i class="fa fa-plus"></i> NUEVO</a>
            </div>
            <div class="clearfix"></div>
          </h3>
        </div>
        <div class="panel-body table-responsive">
          <table id="tabla" class="table table-striped table-bordered">
            <thead>
              <tr class="text-title-panel">
                <th>#</th>
                <th>Nombre</th>
                <th>Codigo</th>
                <th>Estado</th>
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

</div>
<!-- /.container -->

<script type="text/javascript">
  //for save method string
  var save_method;
  var table;
  var table_dataextras;
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
      var tipo = $('#tipo').val();
      var documento = $('#documento').val();
      $(this).button('loading');
      $.ajax({
        type: 'POST',
        url: "<?= $this->url ?>/consulta_reniec",
        data: {
          "tipo": tipo,
          "documento": documento
        },
        beforeSend: function() {
          $('[name="nombre"]').val("");
          $('[name="apellido"]').val("");
          $('[name="direccion"]').val("");
        },
        success: function(data) {
          $('#botoncito').button('reset');
          document.getElementById('nombre').value = data.nombre;
          document.getElementById('apellido').value = data.apellido;
          document.getElementById('direccion').value = data.direccion;
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
    $('#tipo').change(function(e) {
      if ($('#tipo').val() == 'DNI') { //muestro el div1 y oculto los demas
        $('#documento').attr('minLength', 8);
        $('#documento').attr('maxlength', 8);
      } else if ($('#tipo').val() == 'RUC') { //muestro el div2 y oculto los demas
        $('#documento').attr('minLength', 11);
        $('#documento').attr('maxlength', 11);
      }
    });
  });

  function reload_tablesExtras() {
    table_dataextras.ajax.reload(null, false);
  }

  function extrascategoria(idcategoria) {
    save_method = 'add';
    $('#form_extras')[0].reset(); // reset form on modals
    $('#idcategoria').val("");
    $(".modal-title").text("Registrar Extras");
    $("#btnSaveExtras").text("REGISTRAR");
    $('#idcategoria').val(idcategoria);
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $("#modal_extra").modal("show");
    dataextras(idcategoria);
  }

  function dataextras(idcategoria) {

    table_dataextras = $('#tabla_extras').DataTable({
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
        "url": "<?= $this->url ?>/ajax_list_extras/" + idcategoria,
        "type": "POST"
      },
    });

  }

  function editExtras(idextras) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_edit_extras/" + idextras,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="idextras"]').val(data.id);
        $('[name="nombreextra"]').val(data.nombre);
        $('[name="precioextra"]').val(data.precio);
        $('#btnSaveExtras').text('MODIFICAR');
        $('.modal-title').text('Modificar Extra'); // Set title to Bootstrap modal title
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  }

  function desactivarExtras(idextras) {
    bootbox.confirm("Seguro desea desactivar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_desactivar_extras/" + idextras,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            reload_tablesExtras();
            Lobibox.notify('success', {
              size: 'mini',
              position: 'top right',
              msg: 'El registro fue Desactivado exitosamente.'
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
  }

  function activarExtras(idextras) {

    $.ajax({
      url: "<?= $this->url ?>/ajax_activar_extras/" + idextras,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        reload_tablesExtras();
        Lobibox.notify('success', {
          size: 'mini',
          position: 'top right',
          msg: 'El registro fue Activado exitosamente.'
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

  function borrarExtras(idextras) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_eliminar_extras/" + idextras,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            reload_tablesExtras();
            Lobibox.notify('success', {
              size: 'mini',
              position: 'top right',
              msg: 'El registro fue Eliminado exitosamente.'
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
  }

  function add() {
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear <?= $this->titulo_controlador ?>'); // Set Title to Bootstrap modal title
    $("#chkCocina").attr("checked", true);
    $("#chkCocina").val("1");

  }

  function save() {
    $('#btnSave').text('guardando...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    var url;

    if ($("#chkExtras").is(":checked")) {
      $("#chkExtras").val("1");
    } else {
      $("#chkExtras").val("0");
    }

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
            position: "top right",
            msg: msgsuccess
          });
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSave').text('Guardar'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: msgerror
        });
        $('#btnSave').text('Guardar'); //change button text
        $('#btnSave').attr('disabled', false); //set button enable
      }
    });
  }

  function saveextras() {
    $('#btnSaveExtras').text('guardando...'); //change button text
    $('#btnSaveExtras').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_saveextras";
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_update_saveextras";
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: $('#form_extras').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table

        if (data.status) {

          save_method = 'add';
          $("#idextras").val();
          reload_tablesExtras();
          $('.modal-title').text('Resgistrar Extras');
          $("#nombreextra").val("");
          $("#precioextra").val("");

          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: msgsuccess
          });

          $('#btnSaveExtras').text('REGISTRAR'); //change button text
          $('#btnSaveExtras').attr('disabled', false); //set button enable

        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveExtras').text('REGISTRAR'); //change button text
          $('#btnSaveExtras').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: msgerror
        });
        $('#btnSaveExtras').text('GRABAR'); //change button text
        $('#btnSaveExtras').attr('disabled', false); //set button enable
      }
    });
  };

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  }

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
        
        if (data.estadococina == '1') {
          $("#chkCocina").attr("checked", true);
          $("#chkCocina").val("1");
        } else {
          $("#chkCocina").attr("checked", false);
        }

        if (data.estadoextras == '1') {
          $("#chkExtras").attr("checked", true);
          $("#chkExtras").val("1");
        } else {
          $("#chkExtras").attr("checked", false);
        }

        $('[name="id"]').val(data.id);
        $('[name="nombre"]').val(data.nombre);
        $('[name="descripcion"]').val(data.descripcion);
        $('[name="codigo"]').val(data.codigo);
        $('[name="numero"]').val(data.numero);
        $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Modificar <?= $this->titulo_controlador ?>'); // Set title to Bootstrap modal title
      },
      error: function(jqXHR, textStatus, errorThrown) {
        alert('Error get data from ajax');
      }
    });
  }

  // function desactivar(id) {
  //   bootbox.confirm("Seguro desea desactivar este registro?", function(result) {
  //     if (result === true) {
  //       $.ajax({
  //         url: "<?= $this->url ?>/ajax_desactivar/" + id,
  //         type: "POST",
  //         dataType: "JSON",
  //         success: function(data) {
  //           //if success reload ajax table
  //           $('#modal_form').modal('hide');
  //           reload_table();
  //           Lobibox.notify('success', {
  //             size: 'mini',
  //             position: "top right",
  //             msg: 'El registro fue desactivado exitosamente.'
  //           });
  //         },
  //         error: function(jqXHR, textStatus, errorThrown) {
  //           Lobibox.notify('error', {
  //             size: 'mini',
  //             position: "top right",
  //             msg: 'No se puede desactivar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
  //           });
  //         }
  //       });
  //     }
  //   });
  // };

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
  //             position: "top right",
  //             msg: 'El registro fue activado exitosamente.'
  //           });
  //         },
  //         error: function(jqXHR, textStatus, errorThrown) {
  //           Lobibox.notify('error', {
  //             size: 'mini',
  //             position: "top right",
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
  }
</script>

<!-- Modal -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar Categoria</h4>
      </div>
      <form action="#" role="form" id="form" autocomplete="off">
        <div class="modal-body">
          <input type="hidden" name="id" class="form-control" id="id">
          <div class="form-group">
            <label>Nombre de la Categoria</label>
            <input type="text" name="nombre" class="form-control" id="nombre">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label>Descripcion</label>
            <input type="text" name="descripcion" class="form-control" id="descripcion">
            <span class="help-block"></span>
          </div>
          <div class="form-group" style="display: none;">
            <label>¿Mostrar en cocina?</label>
            <div class="material-switch pull-right">
              <input id="chkCocina" name="chkCocina" type="checkbox" />
              <label for="chkCocina" class="label-success"></label>
            </div>
          </div>
          <div class="form-group">
            <label>¿Extras?</label>
            <div class="material-switch pull-right">
              <input id="chkExtras" name="chkExtras" type="checkbox" />
              <label for="chkExtras" class="label-success"></label>
            </div>
          </div>
          <div class="form-group">
            <label>Imagen</label>
            <input type="file" accept="image/*" name="foto" id="foto">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrado</button>
          <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->
<div id="modal_extra" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="custom-width-modalLabel" aria-hidden="true" style="display: none; overflow:auto">
  <div class="modal-dialog" style="width:55%;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="custom-width-modalLabel">Resgistrar Extras</h4>
      </div>
      <div class="modal-body">
        <form action="#" role="form" id="form_extras" autocomplete="off">
          <input type="hidden" name="idcategoria" class="form-control" id="idcategoria">
          <input type="hidden" class="form-control" name="idextras" id="idextras">
          <div class="form-group">
            <label>Nombre del extra</label>
            <input type="text" name="nombreextra" class="form-control" id="nombreextra">
            <span class="help-block"></span>
          </div>

          <div class="form-group">
            <label>Precio</label>
            <input type="number" name="precioextra" class="form-control" id="precioextra">
            <span class="help-block"></span>
          </div>

          <div class="form-group text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrado</button>
            <button type="button" id="btnSaveExtras" onclick="saveextras()" id="btnSaveExtras" class="btn btn-primary">Guardar</button>
          </div>
        </form>

        <div class="panel-body table-responsive">
          <table id="tabla_extras" class="table table-striped table-bordered">
            <thead>
              <trc class="text-title-panel">
                <th>#</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
                </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">

      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->