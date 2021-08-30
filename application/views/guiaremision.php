<?= $this->session->flashdata('mensaje') ?>
<style>
  .partecenter::-webkit-scrollbar {
    width: 10px;
    background: rgb(214, 219, 223);
  }

  .partecenter::-webkit-scrollbar-thumb {
    background-color: rgb(178, 186, 187);
    border-radius: 4px;
  }

  .partecenter::-webkit-scrollbar-thumb:hover {
    background-color: rgb(127, 140, 141);
  }
</style>
<?php if ($this->guiaremision) { ?>
  <div class="row">
    <div class="col-lg-6" id="content-detalles-guiaremision">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">AGREGAR PRODUCTOS</h3>
        </div>
        <!-- form start -->
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
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="almacen">Almacen</label>
                  <select class="form-control" name="almacen" id="almacen">
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="tipoingreso">Tipo</label>
                  <select class="form-control" id="tipocantidad" name="tipocantidad">
                    <option value="UNIDAD">UNIDAD</option>
                    <option value="PAQUETE">PAQUETE</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">

              </div>
            </div>
            <div class="row" id="content-cantidad-paquete">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="paquete">Cantidad en paquete</label>
                  <input type="number" class="form-control" id="paquete" name="paquete" readonly>
                </div>
              </div>
              <!-- 
              <div class="col-md-4" id="content-precio">
                <div class="form-group">
                  <label for="precio">Precio de compra</label>
                  <div class="input-group">
                    <span class="input-group-addon">
                      S/.
                    </span>
                    <input type="number" class="form-control" id="preciocompra" name="preciocompra" readonly>
                    <input type="number" class="form-control" id="preciocomprapaquete" name="preciocomprapaquete" readonly style="display:none">
                    <span class="input-group-btn">
                      <button type="button" class="btn waves-effect waves-light btn-primary" onclick="actualizarprecio()" title="ACTULIZAR PRECIOS" id="btn-actualizar"><i class="fa fa-edit"></i></button>
                    </span>
                  </div>
                </div>
              </div>
              -->
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
                  <label for="cantidad">cantidad</label>
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

    <div class="col-lg-6" id="content-datos-guiaremision">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <h3 class="panel-title pull-left">DATOS DE GUIA DE REMISION</h3>
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

        <!-- form start -->
        <form action="" method="POST" id="form_principal" role="form" autocomplete="off">
          <div class="panel-body partecenter" style="overflow: auto;height: 45vh;">
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
                  <input type="text" class="form-control limpiar" name="usuarios" id="usuarios" readonly>
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Tipo de transporte</label>
                  <select class="form-control" id="modalidadtraslado" name="modalidadtraslado" onchange="save()">
                    <option value="01">PUBLICO</option>
                    <option value="02">PRIVADO</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Motivo de Traslado</label>
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
            </div>


            <div class="row" id="content-transportista-publico">
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="cliente">Transportista <span class="required">*</span></label>
                  <div class="input-group">
                    <input type="hidden" class="form-control" name="transportista" id="transportista">
                    <input type="text" class="form-control" name="transportistas" id="transportistas">
                    <span class="input-group-btn">
                      <a id="botoncliente" onclick="grabartransportista()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" id="content-transportista-privado">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="vehiculo">Vehiculo <span class="required">*</span></label>
                  <div class="input-group">
                    <input type="hidden" class="form-control" name="vehiculo" id="vehiculo">
                    <input type="text" class="form-control" name="vehiculos" id="vehiculos">
                    <span class="input-group-btn">
                      <a id="botoncliente" onclick="grabartransportista()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="chofer">Conductor <span class="required">*</span></label>
                  <div class="input-group">
                    <input type="hidden" class="form-control" name="conductor" id="conductor">
                    <input type="text" class="form-control" name="conductores" id="conductores">
                    <span class="input-group-btn">
                      <a id="botoncliente" onclick="grabartransportista()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="cliente">Peso total de los productos <span class="required">*</span></label>
                  <input class="form-control" type="text" id="pesobrutobienes" name="pesobrutobienes">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label for="cliente">Fecha de traslado<span class="required">*</span></label>
                  <input class="form-control" type="date" id="fechatraslado" name="fechatraslado" min="<?= date("Y-m-d") ?>">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <h4 class="text-center">PUNTO DE LLEGADA</h4>
              </div>
            </div>
            <div class="row" id="content-transportista-privado">
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="cliente">Departamento <span class="required">*</span></label>
                  <select class="form-control" name="destino_departamento" style="padding:5px" id="destino_departamento" onchange="departamento_provincia()">
                    <option value="0">SELECCIONE</option>
                    <?php foreach ($departamentos as $departamento) { ?>
                      <option value="<?= $departamento->id ?>"><?= $departamento->nombre ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="cliente">Provincia <span class="required">*</span></label>
                  <select class="form-control" name="destino_provincia" id="destino_provincia" onchange="provincia_distrito()">
                    <option value="0">SIN DATOS</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label for="cliente">Distrito <span class="required">*</span></label>
                  <select class="form-control" name="destino_distrito" id="destino_distrito">
                    <option value="0">SIN DATOS</option>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
            </div>

            <div class="row" id="content-transportista-privado">
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="cliente">Direccion <span class="required">*</span></label>
                  <input class="form-control" type="text" name="destino_direccion" id="destino_direccion">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>

            <div class="row" id="content-transportista-privado">
              <div class="col-lg-12">
                <div class="form-group">
                  <label for="cliente">Cliente <span class="required">*</span></label>
                  <div class="input-group">
                    <input type="hidden" class="form-control" name="clientedestino" id="clientedestino">
                    <input type="text" class="form-control" name="clientesdestinos" id="clientesdestinos">
                    <span class="input-group-btn">
                      <a id="botoncliente" onclick="grabartransportista()" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                    </span>
                  </div>
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
          <h3 class="panel-title">LISTA DE PRODUCTOS</h3>
        </div>
        <div class="panel-body">
          <div class="panel-body table-responsive">
            <table id="tabla_detalle" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Codigo</th>
                  <th>Producto</th>
                  <th>T. Medida</th>
                  <th>Cant</th>
                  <th>C. Item</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
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
            <a onclick="generar()" class="btn btn-warning" data-toggle="tooltip">BUSCAR <i class="fa fa-search"></i></a>
            <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip">PENDIENTES <i class="fa fa-clipboard"></i></a>
          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-md-12" id="respuesta_proceso"></div>
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title text-dark" style="display:flex; justify-content:space-between; align-items:center;">
            <div>Lista de <?= $this->titulo_controlador ?></div>
            <div>
              <a onclick="location.reload()" class="btn btn-success" data-toggle="tooltip">RECARGAR <i class="fa fa-repeat"></i></a>
              <a href="<?= $this->url ?>/crear" class="btn btn-primary" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a>
            </div>
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">

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
        </div>
      </div>
    </div>
  </div>
<?php } ?>

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
                <span class="help-block"></span>
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
     /*  $("#destino_departamento").select2({
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
        }
      });
      $("#destino_provincia").select2({
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
        }
      });
      $("#destino_distrito").select2({
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
        }
      }); */

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

    $("#ventas").autocomplete({
      source: "<?= $this->url ?>/completarventa",
      minLength: 2,
      select: function(event, ui) {
        $("#venta").val(ui.item.venta);
        save();
        // cambiarventa();
      }
    });

    $("#transportistas").autocomplete({
      source: "<?= $this->url ?>/completar_transportistas",
      minLength: 2,
      select: function(event, ui) {
        $("#venta").val(ui.item.venta);
        $("#transportista").val(ui.item.tranpsortepublico)
        save();
        // cambiarventa();
      }
    });

    $("#conductores").autocomplete({
      source: "<?= $this->url ?>/completar_conductores",
      minLength: 2,
      select: function(event, ui) {
        $("#conductor").val(ui.item.conductor);
        save();
      }
    });

    $("#clientesdestinos").autocomplete({
      source: "<?= $this->url ?>/completar_clientesdestinos",
      minLength: 2,
      select: function(event, ui) {
        $("#clientedestino").val(ui.item.clientedestino);
        save();
      }
    });

    $("#vehiculos").autocomplete({
      source: "<?= $this->url ?>/completar_vehiculo",
      minLength: 2,
      select: function(event, ui) {
        $("#vehiculo").val(ui.item.vehiculo);
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
    $("#modalidadtransporte").change(function(e) {
      e.stopPropagation();
      if ($(this).val() == "02") {
        $("#documentoT").text("DNI CHOFER");
      } else if ($(this).val() == "01") {
        $("#documentoT").text("RUC EMPRESA");
      } else {
        alert("Ha ocurrdio algo inesperado");
      }
    });
    $("#destino_departamento").mayusculassintildes();
    $("#destino_provincia").mayusculassintildes();
    $("#destino_distrito").mayusculassintildes();
    $("#destino_direccion").mayusculassintildes();
    $("#nombreconductor").mayusculassintildes();
    $("#vehiculo").mayusculassintildes();
    $("input").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("input").change(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();

    });
    $("textarea").keyup(function() {

      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();

    });
    $("select").change(function() {
      if ($(this).attr("name") == "destino_departamento" || $(this).attr("name") == "destino_provincia" || $(this).attr("name") == "destino_distrito") {

      } else {
        $(this).parent().removeClass('has-error');
        $(this).parent().parent().removeClass('has-error');
        $(this).next().empty();
      }
    });

    $("#tipocantidad").change(function() {
      tipocompra();
    });
    tipocompra();

  });

  function departamento_provincia() {
    if ($("#destino_departamento").val() != "0") {
      $.ajax({
        url: "<?= $this->url ?>/ajax_departamento_provincia",
        type: "POST",
        data: {
          "destino_departamento": $("#destino_departamento").val()
        },
        dataType: "JSON",
        success: function(data) {
          $("#destino_provincia").html("");
          $("#destino_provincia").append(`<option value="0">SELECCIONE</option>`)
          for (value of data) {
            $("#destino_provincia").append(`<option value="${value.id}">${value.nombre}</option>`)
          }
          $("#destino_provincia").focus();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
          });
        }
      });
    }
  }

  function provincia_distrito() {
    if ($("#destino_provincia").val() != "0") {
      $.ajax({
        url: "<?= $this->url ?>/ajax_provincia_distrito",
        type: "POST",
        data: {
          "destino_provincia": $("#destino_provincia").val()
        },
        dataType: "JSON",
        success: function(data) {
          $("#destino_distrito").html("");
          $("#destino_distrito").append(`<option value="0">SELECCIONE</option>`)
          for (value of data) {
            $("#destino_distrito").append(`<option value="${value.id}">${value.nombre}</option>`)
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            msg: 'No se puede eliminar este registro por seguridad de su base de datos, Contacte al Administrador del Sistema'
          });
        }
      });
    }
  }

  function tipocompra() {
    if ($("#tipocantidad").val() == "UNIDAD") {
      $("#content-cantidad-paquete").hide("fast");
    } else {
      $("#content-cantidad-paquete").show("fast");
    }
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
      "destroy": true,
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
    $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-spin fa-spinner"></i>'); //change button text
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
            position: 'top right',
            msg: 'El registro fue creado exitosamente.'
          });
          cambiarventa();
          $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-shopping-cart"></i>');
          $('#btnSaveDetalle').attr('disabled', false); //set button enable
          $("#productos").focus();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveDetalle').html('AGREGAR <i class="fa fa-shopping-cart"></i>');
          $('#btnSaveDetalle').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
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
              position: 'top right',
              msg: 'El registro fue eliminado exitosamente.'
            });
            cambiarventa();
            $("#productos").focus();
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

  function save() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_update",
      type: "POST",
      data: $('#form_principal').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          cambiarventa();
          reload_table_detalle();
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
          position: 'top right',
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
            position: 'top right',
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
          position: 'top right',
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
        document.querySelector("#content-datos-guiaremision").className = "";
        document.querySelector("#content-detalles-guiaremision").className = "";
        $('[name="pesobrutobienes"]').val(data.pesobrutobienes);
        data.destino_distrito != null ? $('[name="destino_distrito"]').val(data.destino_distrito) : "";
        data.destino_provincia != null ? $('[name="destino_provincia"]').val(data.destino_provincia) : "";
        data.destino_departamento != null ? $('[name="destino_departamento"]').val(data.destino_departamento) : "";
        $('[name="destino_direccion"]').val(data.destino_direccion)
        $('[name="clientedestino"]').val(data.clientedestino);
        $('[name="clientesdestinos"]').val(data.clientesdestinos);
        $('[name="conductor"]').val(data.conductor);
        $('[name="conductores"]').val(data.conductores);
        $('[name="vehiculo"]').val(data.vehiculo);
        $('[name="vehiculos"]').val(data.vehiculos);
        $('[name="transportista"]').val(data.transportista);
        $('[name="transportistas"]').val(data.transportistas);
        $('[name="direccionsalida"]').val(data.direccionsalida);
        $('[name="nombrecliente"]').val(data.nombrecliente);
        $('[name="motivostraslado"]').val(data.motivostraslado);
        $('#usuarios').val(data.usuarios);
        $('[name="modalidadtraslado"]').val(data.modalidadtraslado);
        if (data.contador > 0) {
          $('#empresa').attr('disabled', true);
        } else {
          $('#empresa').attr('disabled', false);
        }
        if (data.estado === '0') {
          $("#content-datos-guiaremision").addClass("col-lg-6");
          $("#content-detalles-guiaremision").addClass("col-lg-6");
          $("#content-detalles-guiaremision").show();
          $('#usuarios').attr('disabled', false);
          $('[name="modalidadtraslado"]').attr("disabled", false);
          $('[name="motivostraslado"]').attr("disabled", false);
          $('[name="vehiculos"]').attr("disabled", false);
          $('[name="conductores"]').attr("disabled", false);
          $('[name="pesobrutobienes"]').attr("disabled", false);
          $('#fechatraslado').attr('disabled', false);
          $('[name="destino_departamento"]').attr("disabled", false);
          $('[name="destino_provincia"]').attr("disabled", false);
          $('[name="destino_distrito"]').attr("disabled", false);
          $('[name="destino_direccion"]').attr("disabled", false);
          $('[name="clientesdestinos"]').attr("disabled", false);
        }
        if (data.estado === '1') {
          $('#usuarios').attr('disabled', true);
          $('[name="modalidadtraslado"]').attr("disabled", true);
          $('[name="motivostraslado"]').attr("disabled", true);
          $('[name="vehiculos"]').attr("disabled", true);
          $('[name="conductores"]').attr("disabled", true);
          $('[name="pesobrutobienes"]').attr("disabled", true);
          $('#fechatraslado').attr('disabled', true);
          $('[name="destino_departamento"]').attr("disabled", true);
          $('[name="destino_provincia"]').attr("disabled", true);
          $('[name="destino_distrito"]').attr("disabled", true);
          $('[name="destino_direccion"]').attr("disabled", true);
          $('[name="clientesdestinos"]').attr("disabled", true);
          $("#content-datos-guiaremision").addClass("col-lg-12");
          $("#content-detalles-guiaremision").hide();
        }

        tipotransporte();
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
          position: 'top right',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  };

  function reload_table_detalle() {
    table_detalle.ajax.reload(null, false); //reload datatable ajax
  };


  function generarGuiaRemision() {
    bootbox.confirm("¿Seguro que desea generar la GUIA DE REMISION REMITENTE?", function(result) {
      if (result === true) {
        $.ajax({
          url: '<?= $this->url ?>/ajax_addprocesar',
          type: "POST",
          data: $('#form_principal').serialize(),
          dataType: "JSON",
          success: function(data) {
            if (data.status) {
              Lobibox.notify('success', {
                size: 'mini',
                position: 'top right',
                msg: 'El registro fue creado exitosamente.'
              });
              reload_table_detalle();
              cambiarventa();
            } else {
              for (var i = 0; i < data.inputerror.length; i++) {
                $('[name="' + data.inputerror[i] + '"]').focus();
                $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
                $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
              }
            }
            $('#btnSaveprocesar').text('GRABAR'); //change button text
            $('#btnSaveprocesar').attr('disabled', false); //set button enable
          },
          error: function(jqXHR, textStatus, errorThrown) {
            Lobibox.notify('error', {
              size: 'mini',
              position: 'top right',
              msg: 'El registro no se pudo crear verifique las validaciones.'
            });
            $('#btnSaveprocesar').text('GRABAR'); //change button text
            $('#btnSaveprocesar').attr('disabled', false); //set button enable
          }
        });
      }
    });
  }


  function imprimirguiaremision(id) {
    $.ajax({
      url: "<?= $this->url ?>/imprimirguiaremision/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('IMPRIMIR GUIA REMISION');
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

  function tipotransporte() {
    if ($("#modalidadtraslado").val() == "01") {
      $("#content-transportista-privado").hide("fast");
      $("#content-transportista-publico").show("fast");
    } else {
      $("#content-transportista-publico").hide("fast");
      $("#content-transportista-privado").show("fast");
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
            position: 'top right',
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
          position: 'top right',
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
            position: 'top right',
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
          position: 'top right',
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
          position: 'top right',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  });

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


  function salvardatos() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_salvardatos",
      type: "POST",
      data: $("#form_principal").serialize(),
      dataType: "JSON",
      success: function(data) {
        Lobibox.notify('success', {
          size: 'mini',
          position: 'top right',
          msg: 'Se salvaron los datos correctamente'
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: 'top right',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>