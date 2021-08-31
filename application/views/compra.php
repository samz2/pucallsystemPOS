<?= $this->session->flashdata('mensaje') ?>.
<?php if ($this->compra) { ?>
  <div class="row">
    <div class="col-md-6" id="content-detalles-compra">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Detalles de compra</h3>
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
                  <label>Tipo de compra</label>
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
                    <span class="input-group-btn">
                      <button type="button" class="btn waves-effect waves-light btn-primary" onclick="actualizarprecio()" title="ACTULIZAR PRECIOS" id="btn-actualizar"><i class="fa fa-edit"></i></button>
                    </span>
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
              <div class="col-md-4" id="content-lote" style="display:none">

                <div class="form-group">
                  <label for="lote">Lote</label>
                  <div class="input-group">

                    <input type="hidden" class="form-control" name="lote" id="lote">
                    <input type="text" class="form-control limpiar" name="lotes" id="lotes">
                    <span class="help-block"></span>
                    <span class="input-group-btn">
                      <a onclick="crearlote()" class="btn btn-primary">
                        <span class="fa fa-plus"></span>
                      </a>
                    </span>

                  </div>
                </div>

              </div>
              <div class="col-md-6" id="content-regalo">
                <div class="form-group">
                  <label for="regalo">Regalo UND</label>
                  <input type="number" class="form-control" id="regalo" name="regalo">
                </div>
              </div>
              <div class="col-md-6" id="content-cantidadcompra">
                <div class="form-group">
                  <label for="cantidad">cantidad de compra</label>
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
        </div><!-- panel-body -->
      </div>
    </div>

    <div class="col-md-6" id="content-datos-compra">
      <div class="panel panel-default">
        <div class="panel-heading clearfix">
          <h3 class="panel-title pull-left">
            DATOS DE <?= $this->titulo_controlador ?>
          </h3>
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
        <form action="" class="" method="POST" id="form_principal" role="form" autocomplete="off">
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
                  <div>
                    <input type="hidden" class="form-control" name="usuario" id="usuario">
                    <input type="text" class="form-control limpiar" name="usuarios" id="usuarios">
                    <span class="help-block"></span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>IGV<span class="required">*</span></label>
                  <select id="igv" name="igv" onchange="save()" class="form-control">
                    <option value="0">0 %</option>
                    <option value="18">18 %</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Monto</label>
                  <input type="text" class="form-control" id="montototal" name="montototal" readonly>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Codigo</label>
                  <input class="form-control" type="text" id="codigo" name="codigo" readonly>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Proveedor</label>
                  <div class="input-group">
                    <input type="hidden" class="form-control" name="proveedor" id="proveedor">
                    <input type="text" class="form-control limpiar" name="proveedores" id="proveedores">
                    <span class="help-block"></span>
                    <span class="input-group-btn">
                      <a onclick="crearproveedor()" class="btn btn-primary">
                        <span class="fa fa-plus"></span>
                      </a>
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <button type="button" class="btn btn-warning btn-block waves-effect waves-light" id="btnSaveDetalle" onclick="cosotoadicionales()">COSTOS ADICIONALES <i class="fa fa-money"></i></button>
                </div>
              </div>
            </div>

          </div>
        </form>
        <div class="panel-footer text-right" id="botones"></div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">LISTA DE <?= $this->titulo_controlador ?>S</h3>
        </div>
        <div class="panel-body">
          <div class="panel-body table-responsive">
            <table id="tabla_detalle" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Destino</th>
                  <th>Producto</th>
                  <th>Lote</th>
                  <th>T. Medida</th>
                  <th>Precio sin IGV</th>
                  <th>Precio con IGV</th>
                  <th>Regalo</th>
                  <th>Cantidad</th>
                  <th>C. Item</th>
                  <th>Total</th>
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
  <div class="row">
    <div class="col-xs-12">
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
            <a onclick="generar()" class="btn btn-warning" data-toggle="tooltip">BUSCAR <i class="fa fa-search"></i></a>
            <a onclick="pendiente()" class="btn btn-danger" data-toggle="tooltip">PENDIENTES <i class="fa fa-clipboard"></i></a>
            <!--<a onclick="exportar()" class="btn btn-success" data-toggle="tooltip" title="EXPORTAR"><i class="fa fa-download"></i></a>-->

          </div>
        </form>
      </div>
    </div>
    <!-- /.col -->
    <div class="col-xs-12">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
              Lista de <?= $this->titulo_controlador ?>
            </div>
            <div>
              <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip">RECARGAR <i class="fa fa-repeat"></i></a>
              <a href="<?= $this->url ?>/crear" class="btn btn-primary" data-toggle="tooltip">NUEVO <i class="fa fa-plus"></i></a>
            </div>
          </h3>
        </div>
        <!-- /.box-header -->
        <div class="panel-body table-responsive">
          <table id="tabla" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>#</th>
                <th>Codigo</th>
                <th>Empresa</th>
                <th>Tipo</th>
                <th>N° Doc.</th>
                <th>Proveedor</th>
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
<?php } ?>

<!-- Modal Compra-->
<div class="modal fade" id="compra_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="form_compra" class="form-horizontal" rol="form" method="post" autocomplete="off">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title text-center" id="myModalLabel">Generar Compra</h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="movimiento">Tipo Doc.</label>
              <div class="col-sm-10">
                <select class="form-control" id="movimiento" name="movimiento">
                  <option value="FACTURA">FACTURA</option>
                  <option value="BOLETA">BOLETA</option>
                  <option value="OTROS">OTROS</option>
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Serie</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="serie" name="serie">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Numero</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="numero" name="numero">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Proveedor</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="nombrepro" name="nombrepro" readonly>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label">Valor</label>
              <div class="col-sm-10">
                <input type="text" class="form-control" id="totales" name="totales" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="formapago">Forma Pago</label>
              <div class="col-sm-10">
                <select class="form-control" name="formapago" id="formapago">
                  <option value="CONTADO">CONTADO</option>
                  <!--<option value="CREDITO">CREDITO</option>-->
                </select>
              </div>
            </div>
            <div id="metodo" class="form-group">
              <label class="col-sm-2 control-label" for="metodopago">Metodo Pago</label>
              <div class="col-sm-10">
                <select class="form-control" name="metodopago" id="metodopago">
                  <option value="EFECTIVO">EFECTIVO</option>
                  <option value="INTERNET">INTERNET</option>
                  <!--<option value="PAGO ANTERIOR">PAGO ANTERIOR</option>-->
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-sm-2 control-label" for="observacion">Comentario</label>
              <div class="col-sm-10">
                <textarea class="form-control" id="observacion" name="observacion"></textarea>
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

<!-- Modal Proveedor -->
<div class="modal fade" id="proveedor_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="" class="form-horizontal" autocomplete="off" method="POST" id="form_proveedor" role="form">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">

            <div class="form-group">

              <label class="col-sm-2 control-label">RUC<span class="required">*</span></label>
              <div class="col-md-9">
                <input id="ruc" name="ruc" class="form-control enteros" type="text">
                <span class="help-block"></span>
              </div>
              <span class="input-group-btn">
                <button type="button" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i>" id="botoncito">
                  <span class="fa fa-search"></span>
                </button>
              </span>


            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Razon Social</label>
              <div class="col-md-9">
                <input class="form-control" type="text" id="nombre" name="nombre">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Direccion</label>
              <div class="col-md-9">
                <input class="form-control" id="direccion" type="text" name="direccion">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Referencia</label>
              <div class="col-md-9">
                <input class="form-control" id="referencia" type="text" name="referencia">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Celular</label>
              <div class="col-md-9">
                <input class="form-control" id="celular" type="text" name="celular">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="button" id="btnSaveproveedor" onclick="saveproveedor()" class="btn btn-primary">Grabar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal ticket -->
<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" id="ticketModal" style="width:50%">
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


<div class="modal fade" id="modal_actualizarprecio" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"></h4>
      </div>
      <div class="modal-body form">
        <form action="#" role="form" id="form_precio" autocomplete="off">
          <input type="hidden" name="productoactualizar" id="productoactualizar">
          <div class="form-body">
            <div class="form-group">
              <div class="row">
                <div class="col-lg-6">
                  <label for="pc_paquete">Precio compra paquete (S/.)</label>
                  <input id="pc_paquete" name="pc_paquete" class="form-control" type="number">
                  <span class="help-block"></span>
                </div>
                <div class="col-lg-6">
                  <label for="cantidadpaquete">Cantidad en paquete</label>
                  <input id="cantidadpaquete" name="cantidadpaquete" class="form-control" type="number">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="row">
                <div class="col-lg-6">
                  <label for="preciocompraunidad">Precio compra unidad (S/.)</label>
                  <input id="preciocompraunidad" name="preciocompraunidad" class="form-control" type="number">
                  <span class="help-block"></span>
                </div>
                <div class="col-lg-6">
                  <label for="p_ventaunidad">Precio venta Unidad (S/.)</label>
                  <input id="p_ventaunidad" name="p_ventaunidad" class="form-control" type="number">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
        <button type="button" id="btnSavePrecioCompra" onclick="updateprecios()" class="btn btn-primary">ACTUALIZAR</button>
      </div>
    </div>
  </div>
</div>

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


<!-- Bootstrap modal -->
<div class="modal fade" id="costos_adicionales_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:auto">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title text-center"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form_costos_adicionales" class="form-horizontal" autocomplete="off">
          <input type="hidden" class="form-control" name="id_costo_adicional" id="id_costo_adicional">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Descripcion<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="descripcion_adicional" id="descripcion_adicional">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">Costo <span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" name="costo_adicional" id="costo_adicional">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
        <button type="button" id="btnSaveCostoAdicional" onclick="save_costo_adicional()" class="btn btn-primary pull-right"></button>
        <div class="clearfix"></div>

        <div class="row m-row-1">
          <div class="col-xs-12">
            <div class="panel panel-border panel-border-info">
              <div class="panel-heading">
                <h3 class="panel-title text-title-panel">Lista de costos adicionales</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive">
                <table id="tabla_almacen" class="table table-bordered table-striped">
                  <thead>
                    <tr class="text-title-panel">
                      <th>#</th>
                      <th>Descripcion</th>
                      <th>Monto</th>
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
  var table;
  var table_detalle;
  var save_method;
  $(document).ready(function() {
    <?php if ($this->compra) { ?>
      
      var table_costoadicional;
      cambiarventa();
      cargar_detalle();
      tipocompra();
    <?php } else { ?>
      generar();
    <?php } ?>
    $(".limpiar").addClear();
    $('.money').number(true, 6);
    $('#cantidad').numeric();
    $("#nombre").mayusculassintildes();
    $("#serie").mayusculassintildes();
    $("#direccion").mayusculassintildes();
    $("#referencia").mayusculassintildes();
    $('#ruc').attr('minLength', 11);
    $('#ruc').attr('maxlength', 11);
    $('#ruc').numeric();
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
                $("#content-regalo").removeClass("col-md-6");
                $("#content-regalo").addClass("col-md-4");
                $("#content-cantidadcompra").removeClass("col-md-6");
                $("#content-cantidadcompra").addClass("col-md-4");
              } else {
                $("#content-lote").hide();
                $("#content-regalo").removeClass("col-md-4");
                $("#content-regalo").addClass("col-md-6");
                $("#content-cantidadcompra").removeClass("col-md-4");
                $("#content-cantidadcompra").addClass("col-md-6");
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
        $("#content-regalo").removeClass("col-md-4");
        $("#content-cantidadcompra").removeClass("col-md-4");
        $("#content-regalo").addClass("col-md-6");
        $("#content-cantidadcompra").addClass("col-md-6");
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
    $("#proveedores").autocomplete({
      source: "<?= $this->url ?>/completarproveedor",
      minLength: 2,
      select: function(event, ui) {
        $("#proveedor").val(ui.item.proveedor);
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
      $(this).parent().removeClass('has-error');
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
      $(this).next().next().empty();
    });
    $("textarea").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("select").change(function() {
      $(this).parent().removeClass('has-error');
      $(this).parent().parent().removeClass('has-error');
      $(this).next().empty();
    });
    $('#botoncito').on('click', function() {
      /*
      $(this).button('loading');
      $.ajax({
        method: 'GET',
        url: "https://dni.optimizeperu.com/api/company/" + $('#ruc').val() + "?format=json",
        beforeSend: function() {
          $('[name="nombre"]').val("");
          $('[name="direccion"]').val("");
        },
        success: function(data) {
          $('#botoncito').button('reset');
          $('[name="nombre"]').val(data.razon_social);
          $('[name="direccion"]').val(data.domicilio_fiscal);
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
      */
      $(this).button('loading');
      $.ajax({
        method: 'GET',
        url: `https://apiperu.dev/api/ruc/${$('#ruc').val()}?api_token=7460d2fa0d1d01c5fe9c96448ea0c3a1d800bae62461f6c27bfd48914e466e14`,
        beforeSend: function() {
          $('[name="nombre"]').val("");
          $('[name="direccion"]').val("");
          $('[name="apellido"]').val("");
        },
        success: function(data) {
          $('#botoncito').button('reset');
          if (data.success === true) {
            $("#nombre").val(data.data.nombre_o_razon_social);
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

    });

    //LOTEEEEEEEE
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


    //LOTEEEEEEEE


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


  });

  function crearlote() {
    event.preventDefault();
    $('#lote_modal').modal('show');
    $('.modal-title').text('CREAR LOTE');
  };

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

  function visualizar(id) {
    $.ajax({
      url: "<?= $this->url ?>/visualizar/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('DETALLES DE LA COMPRA');
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

  function crearproveedor() {
    $('#proveedor_modal').modal('show');
    $('.modal-title').text('CREAR PROVEEDOR');
  };


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
        //if success close modal and reload ajax table
        if (data.status) {
          reload_table_detalle();
          $("#producto").val("");
          $('#form_detalle')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
          cambiarventa();
          tipocompra();
          $("#productos").focus();
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error');
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
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
    $.ajax({
      url: "<?= $this->url ?>/ajax_updateventa",
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="empresa"]').val(data.empresa);
        $('[name="empresas"]').val(data.nombreempresa);
        $('[name="usuario"]').val(data.usuario);
        $('[name="usuarios"]').val(data.nombreusuario);
        $('[name="proveedor"]').val(data.proveedor);
        $('[name="proveedores"]').val(data.nombreproveedor);
        $('[name="nombrepro"]').val(data.nombreproveedor);
        $('[name="codigo"]').val(data.codigo);
        $('[name="igv"]').val(data.igv);
        $('[name="montototal"]').val(data.montototal);
        $('[name="totales"]').val(data.montototal);
        if (data.contador == 0) {
          $("#empresa").prop('disabled', false);
          $("#igv").prop('disabled', false);
        } else {
          $("#empresa").prop('disabled', true);
          $("#igv").prop('disabled', true);
        }
        document.querySelector("#content-datos-compra").className = "";
        document.querySelector("#content-detalles-compra").className = "";
        if (data.estado == 0) {
          $("#usuarios").prop('disabled', false);
          $("#proveedores").prop('disabled', false);
          $("#content-datos-compra").addClass("col-lg-6");
          $("#content-detalles-compra").addClass("col-lg-6")
          $("#content-detalles-compra").show();
        } else {
          $("#usuarios").prop('disabled', true);
          $("#proveedores").prop('disabled', true);
          $("#content-datos-compra").addClass("col-lg-12");
          $("#content-detalles-compra").hide();
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
    $('#btnSaveprocesar').text('guardando...');
    $('#btnSaveprocesar').attr('disabled', true);
    $.ajax({
      url: '<?= $this->url ?>/ajax_addprocesar',
      type: "POST",
      data: $('#form_compra').serialize(),
      dataType: "JSON",
      success: function(data) {
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
          //if success close modal and reload ajax table
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

  function saveproveedor() {
    $('#btnSaveproveedor').text('guardando...'); //change button text
    $('#btnSaveproveedor').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addproveedor',
      type: "POST",
      data: $('#form_proveedor').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          $('#proveedor_modal').modal('hide');
          $('#form_proveedor')[0].reset();
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
        $('#btnSaveproveedor').text('GRABAR'); //change button text
        $('#btnSaveproveedor').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveproveedor').text('GRABAR'); //change button text
        $('#btnSaveproveedor').attr('disabled', false); //set button enable
      }
    });
  };

  function imprimir(id) {
    $.ajax({
      url: "<?= $this->url ?>/imprimir/" + id,
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('GENERAR PDF');
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

  function actualizarprecio() {
    let productoseleccionado = $("#producto").val();
    if (productoseleccionado == '') {
      Lobibox.alert('info', {
        title: 'Informacion',
        msg: 'No hay un producto seleccionado :('
      })
    } else {
      $("#btn-actualizar").attr('disabled', true);
      $("#btn-actualizar").html('<i class="fa fa-spin fa-spinner"></i>');
      $.ajax({
        url: "<?= $this->url ?>/ajax_preciosUpdate",
        type: "POST",
        data: {
          "idproducto": productoseleccionado
        },
        dataType: "JSON",
        success: function(data) {
          //? Datos para actualizar
          $("#pc_paquete").val(data.preciocomprapaquete);
          $("#preciocompraunidad").val(data.preciocompra);
          $("#cantidadpaquete").val(data.cantidadpaquete);
          $("#p_ventaunidad").val(data.precioventa);
          $("#productoactualizar").val(data.id);
          $(".modal-title").text("ACTUALIZAR PRECIOS")
          $("#btn-actualizar").attr('disabled', false);
          $("#btn-actualizar").html('<i class="fa fa-edit"></i>');
          $("#modal_actualizarprecio").modal('show');
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("#btn-actualizar").attr('disabled', false);
          $("#btn-actualizar").html('<i class="fa fa-edit"></i>');
          Lobibox.notify('error', {
            size: 'mini',
            position: "top right",
            msg: 'No se puede actualizar este registro Contacte al Administrador del Sistema'
          });
        }
      });
    }
  }

  function updateprecios() {
    $("#btnSavePrecioCompra").attr("disabled", true);
    $("#btnSavePrecioCompra").text("ACTUALIZANDO...");
    $.ajax({
      url: "<?= $this->url ?>/ajax_updateprecios",
      type: "POST",
      data: $("#form_precio").serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $("#btnSavePrecioCompra").attr("disabled", false);
          $("#btnSavePrecioCompra").text("ACTUALIZAR");
          $("#preciocompra").val(data.dataactualizado.preciocompra);
          $("#preciocomprapaquete").val(data.dataactualizado.preciocomprapaquete);
          $("#paquete").val(data.dataactualizado.cantidadpaquete);
          let textproducto = `${data.dataactualizado.codigo} | ${data.dataactualizado.nombre} | Compra: ${data.dataactualizado.preciocompra} | STOCK: ${data.dataactualizado.cantidadStock}`;
          $("#productos").val(textproducto);
          $("#modal_actualizarprecio").modal('hide');
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El producto fue actualizado correctamente.'
          });
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $("#btnSavePrecioCompra").attr("disabled", false);
        $("#btnSavePrecioCompra").text("ACTUALIZAR");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'No se puede actualizar este registro Contacte al Administrador del Sistema'
        });
      }
    });
  }

  $("#cantidadpaquete").keyup(function() {
    let preciopaquete = parseFloat($("#pc_paquete").val());
    let cantidadpaquete = parseFloat($("#cantidadpaquete").val());
    let compraunidad = preciopaquete / cantidadpaquete;
    if (Number.isNaN(compraunidad)) {
      $("#preciocompraunidad").val("0.00");
    } else {
      $("#preciocompraunidad").val(compraunidad.toFixed(2));
    }
  });

  $("#pc_paquete").keyup(function() {
    let preciopaquete = parseFloat($("#pc_paquete").val());
    let cantidadpaquete = parseFloat($("#cantidadpaquete").val());
    let compraunidad = preciopaquete / cantidadpaquete;
    if (Number.isNaN(compraunidad)) {
      $("#preciocompraunidad").val("0.00");
    } else {
      $("#preciocompraunidad").val(compraunidad.toFixed(2));
    }
  });

  function cosotoadicionales() {
    event.preventDefault();
    save_method = 'add';
    $('#form_costos_adicionales')[0].reset();
    $('#btnSaveCostoAdicional').html(`AGREGAR <i class="fa fa-check-circle"></i>`);
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $("#costos_adicionales_modal").modal("show");
    $('.modal-title').text('AGREGAR COSTO ADICIONAL'); // Set Title to Bootstrap modal title
    cargar_cosotoadicionales();
  }

  function cargar_cosotoadicionales() {
    table_costoadicional = $('#tabla_almacen').DataTable({
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
        "url": "<?= $this->url ?>/ajax_cosotoadicionales",
        "type": "GET"
      },
    });
  };

  function save_costo_adicional() {
    $('#btnSaveCostoAdicional').html('AGREGAR <i class="fa fa-spin fa-spinner"></i>'); //change button text
    $('#btnSaveCostoAdicional').attr('disabled', true); //set button disable
    var url;
    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_add_costoadicional";
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
      data: $('#form_costos_adicionales').serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          save_method = 'add';
          $(".modal-title").text("AGREGAR COSTO ADICIONAL");
          reload_tables_cosotosadicionales();
          $('#form_costos_adicionales')[0].reset();
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
        $('#btnSaveCostoAdicional').html('AGREGAR <i class="fa fa-check-circle"></i>'); //change button text
        $('#btnSaveCostoAdicional').attr('disabled', false); //set button disable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: msgerror
        });
        $('#btnSaveCostoAdicional').text('AGREGAR <i class="fa fa-check-circle"></i>'); //change button text
        $('#btnSaveCostoAdicional').attr('disabled', false); //set button disable
      }
    });
  };

  function reload_tables_cosotosadicionales() {
    table_costoadicional.ajax.reload(null, false); //reload datatable ajax
  };

</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>