<?= $this->session->flashdata('mensaje') ?>
<?php 
  // var_dump($almacenes);
?>
<?php if ($this->notasalida) { ?>
  <div class="container">
    <div class="row">
      <div class="col-lg-6" id="content-detalles-notasalida">
        <div class="panel panel-default" id="content-detalles">
          <div class="panel-heading">
            <h3 class="panel-title">DETALLE DE <?= $this->titulo_controlador ?></h3>
          </div>
          <div class="panel-body">
            <form role="form" autocomplete="off" id="form_detalle">
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="exampleInputEmail1">Producto</label>
                    <div class="input-group">
                      <input type="hidden" class="form-control" name="producto" id="producto">
                      <input type="text" class="form-control limpiar" name="productos" id="productos" placeholder="BUSCAR PRODUCTO">
                      <span class="help-block"></span>
                      <span class="input-group-addon"><i class="fa fa-search"></i></span>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="almacen">Almacen</label>
                    <select class="form-control" name="almacen" id="almacen">
                      <option value="0">SELECCIONE</option>
                      <?php foreach ($almacenes as $almacen) { ?>
                        <option value="<?= $almacen->id ?>"><?= $almacen->nombre ?></option>
                      <?php } ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="tipoingreso">Tipo de salida</label>
                    <select class="form-control" id="tipocantidad" name="tipocantidad">
                      <option value="UNIDAD">UNIDAD</option>
                      <option value="PAQUETE">PAQUETE</option>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-md-4" id="content-precio">
                  <div class="form-group">
                    <label for="precio">Precio de compra</label>
                    <div class="input-group">
                      <span class="input-group-addon">
                        S/.
                      </span>
                      <input type="number" class="form-control" id="preciocompra" name="preciocompra" readonly>
                      <input type="number" class="form-control" id="preciocomprapaquete" name="preciocomprapaquete" readonly style="display:none">
                      <!--<span class="input-group-btn">
                       <button type="button" class="btn waves-effect waves-light btn-primary" onclick="actualizarprecio()" title="ACTULIZAR PRECIOS" id="btn-actualizar"><i class="fa fa-edit"></i></button>
                      </span>
                       -->
                    </div>
                  </div>
                </div>
                <div class="col-md-4" id="content-cantidad-paquete">
                  <div class="form-group">
                    <label for="paquete">Cantidad en paquete</label>
                    <input type="number" class="form-control" id="paquete" name="paquete" readonly>
                  </div>
                </div>
              </div>
              <div classs="row">
                <div class="col-md-6" id="content-lote" style="display:none">
                  <div class="form-group">
                    <label for="lote">Lote</label>
                    <div class="input-group">

                      <input type="hidden" class="form-control" name="lote" id="lote">
                      <input type="text" class="form-control" name="lotes" id="lotes">
                      <span class="help-block"></span>
                      <span class="input-group-btn">
                        <button onclick="crearlote()" class="btn btn-primary">
                          <span class="fa fa-plus"></span>
                        </button>
                      </span>

                    </div>
                  </div>
                </div>
                <div class="col-md-12" id="content-cantidadcompra">
                  <div class="form-group">
                    <label for="cantidad">cantidad de salida</label>
                    <input type="number" class="form-control" id="cantidad" name="cantidad" onchange="if(event.keyCode == 13) { savedetalle() }">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-12 text-right">
                  <button type="submit" class="btn btn-success waves-effect waves-light" id="btnSaveDetalle" onclick="savedetalle()">AGREGAR <i class="fa fa-shopping-cart"></i></button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-6" id="content-datos-notasalida">
        <div class="panel panel-default">
          <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left">DATOS DE <?= $this->titulo_controlador ?></h3>
            <div class="pull-right">
              <a onclick="location.reload()" class="btn btn-openid" data-toggle="tooltip">
                <span class="hidden-xs">Recargar</span>
                <i class="fa fa-repeat"></i>
              </a>
              <a href="<?= $this->url ?>/volver" class="btn btn-default" data-toggle="tooltip">
                <span class="hidden-xs">Volver</span>
                <i class="fa fa-arrow-left"></i>
              </a>
            </div>
          </div>
          <form action="" method="POST" id="form_principal" role="form">
            <div class="panel-body">
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Empresa<span class="required">*</span></label>
                    <select class="form-control" name="empresa" id="empresa" onchange="empresaalmacen()">
                      <?php foreach ($empresas as $empresa) { ?>
                        <option value="<?= $empresa->id ?>"><?= $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie ?></option>
                      <?php } ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Usuario<span class="required">*</span></label>
                    <input type="hidden" class="form-control" name="usuario" id="usuario">
                    <input type="text" class="form-control limpiar" name="usuarios" id="usuarios">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label>Nota Salida</label>
                    <input type="text" class="form-control" name="codigo" id="codigo" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-12">
                  <div class="form-group">
                    <label>Monto</label>
                    <input type="text" class="form-control" id="montototal" name="montototal" readonly>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel-footer text-right" id="botones"></div>
          </form>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">LISTA DE <?= $this->titulo_controlador ?></h3>
          </div>
          <div class="panel-body">
            <div class="panel-body table-responsive">
              <table id="tabla_detalle" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Empresa</th>
                    <th>Codigo</th>
                    <th>Producto</th>
                    <th>Lote</th>
                    <th>P/U</th>
                    <th>Cantidad</th>
                    <th>C.Item</th>
                    <th>Subtotal</th>
                    <th>Almacen</th>
                    <th>Acciones</th>
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
                  <input type="date" class="form-control" id="finicio" name="finicio" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-sm-5">
                  <input type="date" class="form-control" id="factual" name="factual" value="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label">Empresa <span class="required">*</span></label>
                <div class="col-sm-10">
                  <select name="empresa" id="empresa" class="form-control">
                    <option value="0">TODOS</option>
                    <?php foreach ($empresas as $value) {  ?>
                      <option value="<?= $value->id ?>"><?= $value->ruc . " | " . $value->serie . " | " . $value->nombre ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="panel-footer text-center">
              <a onclick="generar()" class="btn btn-warning" data-toggle="tooltip" title="GENERAR"><i class="fa fa-list"></i></a>
              <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip" title="PENDIENTE"><i class="fa fa-search"></i></a>
              <!--<a onclick="exportar()" class="btn btn-success" data-toggle="tooltip" title="EXPORTAR"><i class="fa fa-download"></i></a>-->
              <a href="<?= $this->url ?>/crear" class="btn btn-primary" data-toggle="tooltip" title="NUEVO"><i class="fa fa-plus"></i></a>
              <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
            </div>
          </form>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-xs-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title">Lista de <?= $this->titulo_controlador ?></h3>
          </div>
          <!-- /.box-header -->
          <div class="panel-body table-responsive">
            <table id="tabla" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Empresa</th>
                  <th>Codigo</th>
                  <th>Tipo</th>
                  <th>Colaborador</th>
                  <th>Estado</th>
                  <th>Monto</th>
                  <th>Fecha</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
      <!-- /.col -->
    </div>
  </div>
<?php } ?>

<!-- Modal Venta-->
<div class="modal fade" id="compra_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form_compra" class="form-horizontal" rol="form" action="" method="post" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title" id="myModalLabel">Generar Nota de Salida</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
           
            <div class="form-group">
              <label class="col-sm-2 control-label" for="tiposalida">Tipo Ope*</label>
              <div class="col-sm-6">
                <select id="tiposalida" name="tiposalida" class="form-control">
                  <option value="AJUSTE STOCK">AJUSTE STOCK</option>
                  <option value="TRASLADO DE ALMACEN">TRASLADO DE ALMACEN</option>
                </select>
                <span class="help-block"></span>
              </div>
              <label class="col-sm-2 control-label" for="confirmacion">Confirmación  </label>
              <div class="col-sm-2">
                <div class="material-switch">
                  <input id="confirmacion" name="confirmacion" type="checkbox" />
                  <label for="confirmacion" class="label-success"></label>
                </div>
              </div>
                
            </div>

            <div class="form-group" id="contenedorempresatraslado">
              <label class="col-sm-2 control-label" for="destinotraslado">Destino Empresa *</label>
              <div class="col-sm-10">
                <select id="empresatraslado" name="empresatraslado" class="form-control" onchange="dataEmpresaAlmacen()">
                  <?php foreach ($empresas as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->ruc . " | SERIE:" . $value->serie . " | " . $value->nombre ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group" id="contenedordestinotraslado">
              <label class="col-sm-2 control-label" for="destinotraslado">Destino del almacen*</label>
              <div class="col-sm-10">
                <select id="destinotraslado" name="destinotraslado" class="form-control">
                </select>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="empleado">Colaborador</label>
              <div class="col-sm-10">

                <select id="empleado" name="empleado" class="form-control">
                  <option value="">SELECCIONAR COLABORADOR</option>
                  <?php foreach ($empleados as $value) { ?>
                    <option value="<?= $value->id ?>"><?= $value->nombre ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label">Valor</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="totales" name="totales" readonly>
                <span class="help-block"></span>
              </div>
            </div>

            <div class="form-group">
              <label class="col-sm-2 control-label" for="comentario">Observacion</label>
              <div class="col-sm-10">
                <textarea class="form-control" name="comentario" id="comentario"></textarea>
                <span class="help-block"></span>
              </div>
            </div>
           

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
          <button type="button" id="btnSaveprocesar" onclick="saveprocesar()" class="btn btn-primary">GRABAR</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal ticket -->
<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" style="width:50%;">
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


<div class="modal fade" id="lote_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body form">
        <form action="#" role="form" id="form_lote" autocomplete="off">
          <div class="form-body">
            <div class="form-group">
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="c_lote">Codigo de Lote</label>
                    <input id="c_lote" name="c_lote" class="form-control" type="text">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="c_vencimiento">Fecha de vencimiento</label>
                    <input id="c_vencimiento" name="c_vencimiento" class="form-control" type="date">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
        <button type="button" id="btnSaveLote" onclick="savelote()" class="btn btn-primary">GUARDAR</button>
      </div>
    </div>
  </div>
</div>


<script type="text/javascript">
  var table;
  var table_detalle;
  $(document).ready(function() {
    <?php if ($this->notasalida) { ?>
      $("#empresatraslado").val("<?= $this->empresa ?>");
      tipocompra();
      cambiarventa();
      cargar_detalle();
      dataEmpresaAlmacen();
    <?php } else { ?>
      generar();
    <?php } ?>
    $(".limpiar").addClear();
    $('.money').number(true, 6);
    $('#cantidad').numeric();
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('#productos').focus();



    $("#productos").keyup(() => {
      if ($("#productos").val() != "") {
        $("#productos").autocomplete({
          source: function(request, response) {
            $.ajax({
              url: "<?= $this->url ?>/completarproducto",
              dataType: "JSON",
              type: "POST",
              data: {
                term: request.term,
                empresa: $("#empresa").val()
              },
              success: function(data) {
                response(data);
              }
            });
          },
          minLength: 2,
          select: function(event, ui) {
            if (ui.item.producto) {
              $("#producto").val(ui.item.producto);
              $("#preciocompra").val(ui.item.preciocompra);
              $("#preciocomprapaquete").val(ui.item.preciocomprapaquete);
              $("#paquete").val(ui.item.cantidadpaquete);
              if (ui.item.status_lote == '1') {
                $("#content-lote").show();
                //$("#content-regalo").removeClass("col-md-6");
                //$("#content-regalo").addClass("col-md-4");
                $("#content-cantidadcompra").removeClass("col-md-12");
                $("#content-cantidadcompra").addClass("col-md-6");
              } else {
                $("#content-lote").hide();
                //$("#content-regalo").removeClass("col-md-4");
                //$("#content-regalo").addClass("col-md-6");
                $("#content-cantidadcompra").removeClass("col-md-6");
                $("#content-cantidadcompra").addClass("col-md-12");
              }
              $("#cantidad").focus();
            }
          }
        });
      } else {
        $("#form_detalle")[0].reset();
        $("#producto").val("");
        tipocompra();
        $("#content-lote").hide();
        //$("#content-regalo").removeClass("col-md-4");
        $("#content-cantidadcompra").removeClass("col-md-6");
        //$("#content-regalo").addClass("col-md-6");
        $("#content-cantidadcompra").addClass("col-md-12");
      }
    })


    $("#empresas").autocomplete({
      source: "<?= $this->url ?>/completarempresa",
      minLength: 2,
      select: function(event, ui) {
        $("#empresa").val(ui.item.empresa);
        save();
      }
    });
    $("#usuarios").autocomplete({
      source: "<?= $this->url ?>/completarusuario",
      minLength: 2,
      select: function(event, ui) {
        $("#usuario").val(ui.item.usuario);
        save();
      }
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
      $(this).next().empty();
    });
    $("#tipocantidad").change(function() {
      if ($(this).val() == "UNIDAD") {
        $("#content-cantidad-paquete").hide();
        $("#content-precio").removeClass("col-md-4");
        $("#content-precio").addClass("col-md-8");
        $("#preciocompra").show();
        $("#preciocomprapaquete").hide();
      } else {
        $("#content-cantidad-paquete").show();
        $("#content-precio").removeClass("col-md-8");
        $("#content-precio").addClass("col-md-4");
        $("#preciocompra").hide();
        $("#preciocomprapaquete").show();
      }
    });

    if ($("#tiposalida").val() == 'AJUSTE STOCK') {
      $("#contenedorempresatraslado").hide("fast");
      $("#contenedordestinotraslado").hide('fast');
    } else {
      $("#contenedorempresatraslado").show("fast");
      $("#contenedordestinotraslado").show('fast');
    }

    $("#tiposalida").change(() => {
      if ($("#tiposalida").val() == 'AJUSTE STOCK') {
        $("#contenedorempresatraslado").hide("fast");
        $("#contenedordestinotraslado").hide('fast');
      } else {
        $("#contenedorempresatraslado").show("fast");
        $("#contenedordestinotraslado").show('fast');
      }
    });

    $("#lotes").keyup(() => {
      let producto = $("#producto").val();
      if (producto != '') {
        $("#lotes").autocomplete({
          source: "<?= $this->url ?>/completarlote/" + producto,
          minLength: 1,
          select: function(event, ui) {
            $("#lote").val(ui.item.lote);
          }
        });
      } else {
        Lobibox.alert('info', {
          title: "INFORMACION",
          msg: "No hay ningun producto seleccionado :("
        })
      }

    })

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
        url: "<?= $this->url ?>/ajax_generado/" + $('#finicio').val() + '/' + $('#factual').val() + "/" + $("#empresa").val(),
        type: 'GET'
      },
    });
  };

  function datasEmpresaAlmacen() {

  }

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
        url: "<?= $this->url ?>/ajax_pendiente/" + $("#empresa").val(),
        type: 'GET'
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
      //Feature control the processing indicator.
      "processing": true,
      // Load data for the table's content from an Ajax source
      "ajax": {
        url: "<?= $this->url ?>/ajax_list_detalle",
        type: 'GET'
      },
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
            position: "top right",
            msg: "El registro fue actualizado exitosamente."
          });
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

  function savedetalle() {
    $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-spin fa-spinner"></i>'); //change button text
    $('#btnSaveDetalle').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_adddetalle',
      type: "POST",
      data: $('#form_detalle').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          reload_table_detalle();
          $('#form_detalle')[0].reset();
          $("#lote").val("");
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
          cambiarventa();
          $("#productos").focus();
          tipocompra();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().html(data.error_string[i]); //select span help-block class set text error string
          }
        }
        $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-shopping-cart"></i>');
        $('#btnSaveDetalle').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-shopping-cart"></i>');
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
              position: "top right",
              msg: 'El registro fue eliminado exitosamente.'
            });
            cambiarventa();
            $("#productos").focus();
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
        reload_table_detalle();
        Lobibox.notify('success', {
          size: 'mini',
          position: "top right",
          msg: 'Examen actualizado correctamente.'
        });
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
        $('[name="empresa"]').val(data.empresa);
        $('[name="usuario"]').val(data.usuario);
        $('[name="usuarios"]').val(data.nombreusuario);
        $('[name="nombrepro"]').val(data.nombreproveedor);
        $('[name="codigo"]').val(data.codigo);
        $('[name="tiposalida"]').val(data.tiposalida);
        $('[name="montototal"]').val(data.montototal);
        $('[name="totales"]').val(data.montototal);
        if (data.contador == 0) {
          $("#empresa").prop('disabled', false);
        } else {
          $("#empresa").prop('disabled', true);
        }
        document.querySelector("#content-datos-notasalida").className = "";
        document.querySelector("#content-detalles-notasalida").className = "";
        if (data.estado == 0) {
          $("#usuarios").prop('disabled', false);
          $("#content-datos-notasalida").addClass("col-lg-6");
          $("#content-detalles-notasalida").addClass("col-lg-6")
          $("#content-detalles-notasalida").show();
        } else {
          $("#usuarios").prop('disabled', true);
          $("#content-datos-notasalida").addClass("col-lg-12");
          $("#content-detalles-notasalida").hide();
        }

        $("#almacen").html("");
        if (data.empresaAlmacen.length > 0) {
          for (value of data.empresaAlmacen) {
            $("#almacen").append(`<option value="${value.id}">${value.nombre}</option>`);
          }
        } else {
          $("#almacen").append(`<option value="0"><span style="font-weight:bold; color:#e42424">NO SE ENCONTRO NINGUN ALMACEN REGISTRADO</span></option>`);
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

  function reload_table_detalle() {
    table_detalle.ajax.reload(null, false); //reload datatable ajax
  };

  function grabar() {
    $('#compra_form').modal('show');
    $('.modal-title').text('GENERAR <?= $this->titulo_controlador ?>');
  };

  function saveprocesar() {
    $('#btnSaveprocesar').text('guardando...'); //change button text
    $('#btnSaveprocesar').attr('disabled', true); //set button disable
    // ajax adding data to database
    // if($("#confirmacion").is(':checked')) 
    // {
    //   $("#confirmacion").val(true);
    // }else{
    //   $("#confirmacion").val(false);
    // }
    $.ajax({
      url: '<?= $this->url ?>/ajax_addprocesar',
      type: "POST",
      data: $('#form_compra').serialize(),

      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table_detalle();
          $('#compra_form').modal('hide');
          $('#form_compra')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
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
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveprocesar').text('GRABAR'); //change button text
        $('#btnSaveprocesar').attr('disabled', false); //set button enable
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

  function reload_table() {
    table.ajax.reload(null, false); //reload datatable ajax
  };

  function exportar() {
    $.ajax({
      url: '<?= $this->url ?>/excel',
      type: 'POST',
      success: function() {
        window.open('<?= $this->url ?>/excel/' + $('#finicio').val() + '/' + $('#factual').val());
      },
    });
  };

  function imprimir(id) {
    $.ajax({
      url: "<?= $this->url ?>/imprimir/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR');
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

  function visualizar(id) {
    $.ajax({
      url: "<?= $this->url ?>/visualizar/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('NOTA DE SALIDA DETALLE');
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

  function aprobar(id) {
    Swal.fire({
      title: '¿Desea aprobar este movimiento?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: `Si`,
      denyButtonText: `No`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $.ajax({
          url: "<?= $this->url ?>/aprobar/" + id,
          type: "POST",
          success: function(data) {
            Swal.fire('Listo, Movimiento aceptado', '', 'success');
            location.reload();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            Swal.fire('error: '+errorThrown, '', 'error') 
          }
        });
       
      } else if (result.isDenied) {
        Swal.fire('Movimiento denegado', '', 'warning')
      }
    })
  
  };

  function crearlote() {
    event.preventDefault();
    $('#lote_modal').modal('show');
    $('.modal-title').text('CREAR LOTE');
  };

  function savelote() {
    $('#btnSaveLote').text('guardando...'); //change button text
    $('#btnSaveLote').attr('disabled', true); //set button disable
    // ajax adding data to database
    let productolote = $("#producto").val();
    if (productolote != "") {
      $.ajax({
        url: '<?= $this->url ?>/ajax_addlote/' + productolote,
        type: "POST",
        data: $('#form_lote').serialize(),
        dataType: "JSON",
        success: function(data) {
          if (data.status) {
            $('#lote_modal').modal('hide');
            $('#form_lote')[0].reset();
            $(".col-lg-6").removeClass("has-error");
            $(".help-block").empty();
            $("#lotes").val(data.textlote);
            $("#lote").val(data.idlote);
            Lobibox.notify('success', {
              size: 'mini',
              position: "top right",
              msg: 'El registro fue creado exitosamente.'
            });
            //cambiarventa();
          } else {
            for (var i = 0; i < data.inputerror.length; i++) {
              $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
              $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
            }
          }
          $('#btnSaveLote').text('GRABAR'); //change button text
          $('#btnSaveLote').attr('disabled', false); //set button enable
        },
        error: function(jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            position: "top right",
            msg: 'El registro no se pudo crear verifique las validaciones.'
          });
          $('#btnSaveLote').text('GRABAR'); //change button text
          $('#btnSaveLote').attr('disabled', false); //set button enable
        }
      });
    } else {
      Lobibox.notify('warning', {
        size: 'mini',
        position: "top right",
        msg: 'No hay un producto seleccionado :('
      });
    }
  };

  function tipocompra() {
    if ($("#tipocantidad").val() == "UNIDAD") {
      $("#content-cantidad-paquete").hide();
      $("#content-precio").removeClass("col-md-4");
      $("#content-precio").addClass("col-md-8");
      $("#preciocompra").show();
      $("#preciocomprapaquete").hide();
    } else {
      $("#content-cantidad-paquete").show();
      $("#content-precio").removeClass("col-md-8");
      $("#content-precio").addClass("col-md-4");
      $("#preciocompra").hide();
      $("#preciocomprapaquete").show();
    }
    $("input, textarea, select").parent().parent().removeClass('has-error');
    $("textarea, select").next().empty();
    $("#cantidad").next().empty();
  }

  function empresaalmacen() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_empresaAlmacen/" + $("#empresa").val(),
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#almacen").html("");
        if (data.dataAlmacen.length > 0) {
          for (value of data.dataAlmacen) {
            $("#almacen").append(`<option value="${value.id}">${value.nombre}</option>`);
          }
        } else {
          $("#almacen").append(`<option value="0"><span style="font-weight:bold; color:#e42424">NO SE ENCONTRO NINGUN ALMACEN REGISTRADO</span></option>`);
        }
        $("#codigo").val(data.codigoActualizado);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: "El registro no se pudo actualizar. Verifique la operación"
        });
      }
    });
  }

  function dataEmpresaAlmacen() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_dataEmpresaAlmacen/" + $("#empresatraslado").val(),
      type: "POST",
      dataType: "JSON",
      success: function(data) {
        $("#destinotraslado").html("");
        if (data.length > 0) {
          for (value of data) {
            $("#destinotraslado").append(`<option value="${value.id}">${value.nombre}</option>`);
          }
        } else {
          $("#destinotraslado").append(`<option value="0"><span style="font-weight:bold; color:#e42424">NO SE ENCONTRO NINGUN ALMACEN REGISTRADO</span></option>`);
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
  }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>