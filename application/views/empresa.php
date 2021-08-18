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
        </h3>
        <div class="clearfix"></div>
      </div>
      <!-- /.box-header -->
      <div class="panel-body table-responsive">
        <table id="tabla" class="table table-bordered table-striped">
          <thead>
            <tr class="text-title-panel">
              <th>#</th>
              <th>RUC</th>
              <th>Razon Social</th>
              <th>Nombre</th>
              <th>Direccion</th>
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

<!-- Modal -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="" id="form" class="form-horizontal" enctype="multipart/form-data" autocomplete="off">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="form-body">
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipo<span class="required">*</span></label>
              <div class="col-md-10">
                <select id="tipo" name="tipo" class="form-control">
                  <option value="0">NATURAL</option>
                  <option value="1">JURIDICA</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">RUC<span class="required">*</span></label>
              <div class="col-md-9">
                <input id="ruc" name="ruc" class="form-control enteros" type="text" maxlength="11">
                <span class="help-block"></span>
              </div>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                  <span class="fa fa-search"></span>
                </button>
              </span>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Razon Social</label>
              <div class="col-md-10">
                <input id="razonsocial" name="razonsocial" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Nombre</label>
              <div class="col-md-10">
                <input id="nombre" name="nombre" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Direccion</label>
              <div class="col-md-10">
                <textarea class="form-control" name="direccion" id="direccion"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Telefono</label>
              <div class="col-md-10">
                <input id="telefono" name="telefono" class="form-control enteros" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Serie<span class="required">*</span></label>
              <div class="col-md-10">
                <input class="form-control" id="serie" type="text" name="serie">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Ubigeo</label>
              <div class="col-md-10">
                <input id="ubigeo" name="ubigeo" class="form-control enteros" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Departamento</label>
              <div class="col-md-10">
                <input id="departamento" name="departamento" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Provincia</label>
              <div class="col-md-10">
                <input id="provincia" name="provincia" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Distrito</label>
              <div class="col-md-10">
                <input id="distrito" name="distrito" class="form-control" type="text">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Usuario SOL<span class="required">*</span></label>
              <div class="col-md-10">
                <input class="form-control" id="usuariosol" type="text" name="usuariosol">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Clave SOL</label>
              <div class="col-md-10">
                <input class="form-control" id="clavesol" type="text" name="clavesol">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Logo</label>
              <div class="col-md-10">
                <input class="form-control" id="logo" type="file" accept="image/*" name="logo">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipo Proceso<span class="required">*</span></label>
              <div class="col-md-10">
                <select id="tipoproceso" name="tipoproceso" class="form-control">
                  <option value="1">PRODUCCION</option>
                  <option value="2">HOMOLOGACION</option>
                  <option value="3">BETA</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipo de Impresora<span class="required">*</span></label>
              <div class="col-md-10">
                <select id="tipoimpresora" name="tipoimpresora" class="form-control">
                  <option value="0">TERMICA</option>
                  <option value="1">TIQUETERA</option>
                  <option value="2">DOMINIO</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Tipo de Venta<span class="required">*</span></label>
              <div class="col-md-10">
                <select id="tipoventa" name="tipoventa" class="form-control">
                  <option value="OTROS">OTROS</option>
                  <option value="BOLETA">BOLETA</option>
                  <option value="FACTURA">FACTURA</option>
                  <!--ADC-->
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Almacen POS<span class="required">*</span></label>
              <div class="col-md-10">
                <select class="form-control" name="almacen" id="almacen">
                  <option value="0">SELECCIONE</option>
                  <?php foreach ($almacenes as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->nombre ?></option>
                  <?php } ?>

                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Nombre Impresora</label>
              <div class="col-md-10">
                <input class="form-control" id="nombreimpresora" type="text" name="nombreimpresora">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-5 control-label">Proceso de venta<span class="required">*</span></label>
              <div class="col-md-7">
                <div>
                  <label class="control-label" for="pasos">Tres pasos</label>
                  <div class="material-switch">
                    <input id="pasos" name="pasos" type="checkbox" />
                    <label for="pasos" class="label-success"></label>
                  </div>
                </div>
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
<!-- /.Modal -->

<!-- Bootstrap modal -->
<div class="modal fade" id="almacen_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:auto">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form_almacen" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" name="idempresa" id="idempresa">
          <input type="hidden" class="form-control" name="idalmacen" id="idalmacen">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Nombre<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="nombrealmacen" id="nombrealmacen">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
        <button type="button" id="btnSaveAlmacen" onclick="savealmacen()" class="btn btn-primary pull-right"></button>
        <div class="clearfix"></div>

        <div class="row m-row-1">
          <div class="col-xs-12">
            <div class="panel panel-border panel-border-info">
              <div class="panel-heading">
                <h3 class="panel-title text-title-panel">Lista de almacenes</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive">
                <table id="tabla_almacen" class="table table-bordered table-striped">
                  <thead>
                    <tr class="text-title-panel">
                      <th>#</th>
                      <th>Nombre</th>
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
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<!-- Bootstrap modal -->
<div class="modal fade" id="mesa_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form_mesa" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" name="empresamesa" id="empresamesa">
          <input type="hidden" class="form-control" name="idmesa" id="idmesa">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">NOMBRE<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="nombremesa" id="nombremesa">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">ZONA<span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="zonamesa" id="zonamesa">
                  <?php foreach ($zonamesa as $key => $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->nombre ?></option>
                  <?php  } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">TIPO<span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" name="tipomesa" id="tipomesa">
                  <option value="ALQUILER">ALQUILER</option>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">PRECIO ALQUILER<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="number" class="form-control" name="precioalquiler" id="precioalquiler">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
        <button type="button" id="btnSaveMesa" onclick="savemesa()" class="btn btn-primary pull-right"></button>
        <div class="clearfix"></div>
        <div class="row m-row-1">
          <div class="col-xs-12">
            <div class="panel panel-border panel-border-info">
              <div class="panel-heading">
                <h3 class="panel-title text-title-panel">LISTA DE MESAS</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive">
                <table id="tabla_mesa" class="table table-bordered table-striped">
                  <thead>
                    <tr class="text-title-panel">
                      <th>#</th>
                      <th>Nombre</th>
                      <th>Zona</th>
                      <th>Tipo</th>
                      <th>Precio alquiler</th>
                      <th>Acciones</th>
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

<!--  modal zona-->
<div class="modal fade" id="zona_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow: auto;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form_zona" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" name="empresazona" id="empresazona">
          <input type="hidden" class="form-control" name="idzona" id="idzona">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">NOMBRE<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="nombrezona" id="nombrezona">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">ICONO<span class="required">*</span></label>
              <div class="col-md-9">
                <select class="form-control" id="iconozona" name="iconozona">
                  <?php foreach ($iconos as $value) { ?>
                    <option value="<?= $value->nombre ?>"><?= $value->imagen . ' ' . $value->nombre ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
        <button type="button" id="btnSaveZona" onclick="savezona()" class="btn btn-primary pull-right"></button>
        <div class="clearfix"></div>

        <div class="row m-row-1">
          <div class="col-xs-12">
            <div class="panel panel-border panel-border-info">
              <div class="panel-heading">
                <h3 class="panel-title text-title-panel">LISTA DE ZONA</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive">
                <table id="tabla_zona" class="table table-bordered table-striped">
                  <thead>
                    <tr class="text-title-panel">
                      <th>#</th>
                      <th>Descripción</th>
                      <th>Icono</th>
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
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
  //for save method string
  var save_method;
  var table;
  var tableZ;
  var tableM;
  $(document).ready(function() {
    $("#razonsocial").mayusculassintildes();
    $("#nombre").mayusculassintildes();
    $("#direccion").mayusculassintildes();
    $("#departamento").mayusculassintildes();
    $("#distrito").mayusculassintildes();
    $("#provincia").mayusculassintildes();
    $("#usuariosol").mayusculassintildes();
    $("#ruc").numeric();
    $("#serie").numeric();
    $("#ubigeo").numeric();
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    $("#nombrealmacen").mayusculassintildes();
    $("#nombremesa").mayusculassintildes();
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
    $("input").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
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
    $('#btnSave').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_add/" + save_method;
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_update/" + save_method;
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    if ($("input#pasos").is(":checked")) {
      $("input#pasos").val("1");
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: new FormData($("#form")[0]),
      dataType: "JSON",
      contentType: false,
      cache: false,
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
        if (data.pasos == 1) {
          $('[name="pasos"]').prop('checked', true);
          $("#content-venta-1").hide();
          $("#content-venta-2").hide();
        } else {
          $('[name="pasos"]').prop('checked', false);
          $("#content-venta-1").show();
          $("#content-venta-2").show();
        }
        $('[name="id"]').val(data.id);
        $('[name="tipo"]').val(data.tipo);
        $('[name="ruc"]').val(data.ruc);
        $('[name="razonsocial"]').val(data.razonsocial);
        $('[name="nombre"]').val(data.nombre);
        $('[name="serie"]').val(data.serie);
        $('[name="direccion"]').val(data.direccion);
        $('[name="telefono"]').val(data.telefono);
        $('[name="celular"]').val(data.celular);
        $('[name="departamento"]').val(data.departamento);
        $('[name="provincia"]').val(data.provincia);
        $('[name="distrito"]').val(data.distrito);
        $('[name="ubigeo"]').val(data.ubigeo);
        $('[name="usuariosol"]').val(data.usuariosol);
        $('[name="almacen"]').val(data.almacen);
        $('[name="clavesol"]').val(data.clavesol);
        $('[name="tipoproceso"]').val(data.tipoproceso);
        $('[name="tipoimpresora"]').val(data.tipoimpresora);
        $('[name="tipoventa"]').val(data.tipoventa);
        $('[name="nombreimpresora"]').val(data.nombreimpresora);
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

  function almacen(empresa) {
    save_method = 'add';
    $('#form_almacen')[0].reset(); // reset form on
    $('#idempresa').val(empresa);
    $('#btnSaveAlmacen').text('GRABAR');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#almacen_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear Almacen'); // Set Title to Bootstrap modal title
    cargar_almacenes(empresa);
  };

  function cargar_almacenes(empresa) {
    tableZ = $('#tabla_almacen').DataTable({
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
        "url": "<?= $this->url ?>/ajax_listalmacen/" + empresa,
        "type": "GET"
      },
    });
  };

  function savealmacen() {
    $('#btnSaveAlmacen').text('guardando...'); //change button text
    $('#btnSaveAlmacen').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_addalmacen";
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_updatealmacen";
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: $('#form_almacen').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          save_method = 'add';
          $(".modal-title").text("Crear Almacen");
          reload_tablesZ();
          $('#form_almacen')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: msgsuccess
          });
          $('#btnSaveAlmacen').text('GRABAR'); //change button text
          $('#btnSaveAlmacen').attr('disabled', false); //set button enable
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveAlmacen').text('GRABAR'); //change button text
          $('#btnSaveAlmacen').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: msgerror
        });
        $('#btnSaveAlmacen').text('GRABAR'); //change button text
        $('#btnSaveAlmacen').attr('disabled', false); //set button enable
      }
    });
  };

  function editalmacen(id) {
    save_method = 'update';
    $('#form_almacen')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_editalmacen/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="idalmacen"]').val(data.id);
        $('[name="nombrealmacen"]').val(data.nombre);
        $('#idempresa').val(data.empresa);
        $('#btnSaveAlmacen').text('MODIFICAR');
        $('.modal-title').text('Modificar Almacen'); // Set title to Bootstrap modal title
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

  function borraralmacen(id) {
    bootbox.confirm("¿ Seguro desea Eliminar este registro ?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletealmacen/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_tablesZ();
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

  function reload_tablesZ() {
    tableZ.ajax.reload(null, false); //reload datatable ajax
  };

  function mesa(empresa) {
    save_method = 'add';
    $('#form_mesa')[0].reset(); // reset form on
    $('#empresamesa').val(empresa);
    $('#btnSaveMesa').text('GRABAR');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#mesa_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear Mesa'); // Set Title to Bootstrap modal title
    cargar_zonasmesa(empresa);
    cargar_mesas(empresa);
  };

  function cargar_zonasmesa(empresa) {
    // ajax adding data to database
    $.ajax({
      url: "<?= $this->url ?>/ajax_datazona",
      data: {
        "idempresa": empresa
      },
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#zonamesa").empty();
        for (value of data.datazona) {
          $("#zonamesa").append(`<option value="${value.id}">${value.nombre}</option>`);
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: msgerror
        });
      }
    });

  }

  function cargar_mesas(empresa) {
    tableM = $('#tabla_mesa').DataTable({
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
      //Feature control the processing indicator.
      "processing": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/ajax_listmesa/" + empresa,
        "type": "GET"
      },
    });
  };

  function savemesa() {
    $('#btnSaveMesa').text('guardando...'); //change button text
    $('#btnSaveMesa').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_addmesa/" + save_method;
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_updatemesa/" + save_method;
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: $('#form_mesa').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_tableM();
          save_method = 'add';
          $(".modal-title").text("Crear Mesa")
          $('#form_mesa')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: msgsuccess
          });
          $('#btnSaveMesa').text('GRABAR'); //change button text
          $('#btnSaveMesa').attr('disabled', false); //set button enable
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveMesa').text('GRABAR'); //change button text
          $('#btnSaveMesa').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: msgerror
        });
        $('#btnSaveMesa').text('GRABAR'); //change button text
        $('#btnSaveMesa').attr('disabled', false); //set button enable
      }
    });
  };

  function editmesa(id) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_editmesa/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="idmesa"]').val(data.id);
        $('[name="nombremesa"]').val(data.nombre);
        $('#empresamesa').val(data.empresa);
        $('#tipomesa').val(data.tipo);
        $('#zonamesa').val(data.zona);
        $('#precioalquiler').val(data.precioalquiler);
        $('#btnSaveMesa').text('MODIFICAR');
        $('.modal-title').text('Modificar Mesa'); // Set title to Bootstrap modal title
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

  function borrarmesa(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletemesa/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_tableM();
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

  function reload_tableM() {
    tableM.ajax.reload(null, false); //reload datatable ajax
  };
  $('#botoncito').on('click', function() {
    if ($("#ruc").val() == "") {
      $("#ruc").parent().addClass("has-error");
      $("#ruc").next().text("Esta campo es obligatorio.");
    } else {
      $(this).button('loading');
      $.ajax({
        method: 'GET',
        url: `https://apiperu.dev/api/ruc/${$('#ruc').val()}?api_token=7460d2fa0d1d01c5fe9c96448ea0c3a1d800bae62461f6c27bfd48914e466e14`,
        beforeSend: function() {
          $('[name="razonsocial"]').val("");
          $('[name="direccion"]').val("");
          $('[name="nombre"]').val("");
        },
        success: function(data) {
          $('#botoncito').button('reset');
          if (data.success === true) {
            $("#razonsocial").val(data.data.nombre_o_razon_social);
            $("#direccion").val(data.data.direccion_completa);
          } else {
            Lobibox.notify('warning', {
              size: 'mini',
              position: "top right",
              msg: "El RUC NO EXISTE"
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
    }

  });

  //? Codigo de zona

  function zona(empresa) {
    save_method = 'add';
    $('#form_zona')[0].reset(); // reset form on
    $('#empresazona').val(empresa);
    $('#btnSaveZona').text('GRABAR');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#zona_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear Zona'); // Set Title to Bootstrap modal title
    cargar_zonas(empresa);
  };

  function cargar_zonas(empresa) {
    tableZ = $('#tabla_zona').DataTable({
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
      //Feature control the processing indicator.
      "processing": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        "url": "<?= $this->url ?>/ajax_listzona/" + empresa,
        "type": "GET"
      },
    });
  };

  function savezona() {
    $('#btnSaveZona').text('guardando...'); //change button text
    $('#btnSaveZona').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_addzona/" + save_method;
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_updatezona/" + save_method;
      msgsuccess = "El registro fue actualizado exitosamente.";
      msgerror = "El registro no se pudo actualizar. Verifique la operación";
    }
    // ajax adding data to database
    $.ajax({
      url: url,
      type: "POST",
      data: $('#form_zona').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          save_method = "add";
          reload_tablesZ();
          $('#form_zona')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: msgsuccess
          });
          $('#btnSaveZona').text('GRABAR'); //change button text
          $('#btnSaveZona').attr('disabled', false); //set button enable
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveZona').text('GRABAR'); //change button text
          $('#btnSaveZona').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: msgerror
        });
        $('#btnSaveZona').text('GRABAR'); //change button text
        $('#btnSaveZona').attr('disabled', false); //set button enable
      }
    });
  };

  function editzona(id) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_editzona/" + id,
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $('[name="idzona"]').val(data.id);
        $('[name="nombrezona"]').val(data.nombre);
        $('[name="iconozona"]').val(data.icono);
        $('#empresazona').val(data.empresa);
        $('#btnSaveZona').text('MODIFICAR');
        $('.modal-title').text('Modificar Zona'); // Set title to Bootstrap modal title
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

  function borrarzona(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletezona/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_tablesZ();
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

  function reload_tablesZ() {
    tableZ.ajax.reload(null, false); //reload datatable ajax
  };
</script>