<style>
  .sinstockinicio {
    text-decoration: line-through;
  }
</style>
<!-- Page Content -->

<div class="container">
  <div class="row">
    <div class="col-lg-12">
      <div class="panel  panel-border-info">
        <div class="panel-heading">
          <h3 class="panel-title text-title-panel">Operaciones</h3>
        </div>
        <div class="panel-body">
          <form class="form-horizontal">
            <div class="form-group">
              <label class="col-sm-2 control-label">Empresa</label>
              <div class="col-sm-9">
                <div class="row">
                  <div class="col-lg-9">
                    <select class="form-control" name="empresaproducto" id="empresaproducto" style="margin-left:6px">
                      <?php foreach ($empresas as $value) { ?>
                        <option value="<?= $value->id ?>"><?= $value->ruc . " | SERIE: " . $value->serie . " | " . $value->nombre ?></option>
                      <?php } ?>
                    </select>
                    <span class="help-block"></span>
                  </div>
                  <div class="col-lg-3">
                    <button onclick="detallesproductos()" id="botonprocesar" class="btn btn-warning btn-sm"><i class="fa fa-search"></i> BUSCAR</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <div class="panel panel-border-default">
        <div class="panel-heading">
          <h3 class="panel-title text-title-panel">Lista de <?= $this->controlador ?>
            <div class="pull-right">
              <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip"><i class="fa fa-repeat"></i> RECARGAR</a>
              <a type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" onclick="add()"><i class="fa fa-plus"></i> NUEVO</a>
              <a type="button" class="btn btn-info btn-sm" data-toggle="tooltip" onclick="cargardatos()" title="CARGAR EXCEL"><i class="fa fa-file-excel-o"></i></a>
            </div>
            <div class="clearfix"></div>
          </h3>
        </div>
        <!--
        <div class="panel-body table-responsive">
        <div id="div_grafico"></div>  
        </div>
          -->
        <div class="panel-body table-responsive">
          <table id="tablamain" class="table table-striped table-bordered">
            <thead>
              <tr class="text-title-panel">
                <th>#</th>
                <th>Codigo</th>
                <th>Tipo</th>
                <th>Nombre</th>
                <th>C. Barra</th>
                <th>Lotes</th>
                <th>Categoria</th>
                <th>Stock Total</th>
                <th>Precio</th>
                <th>Estado</th>
                <th> <span style="padding-right:50px">Acciones BTN</span> </th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="overflow:auto">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <form action="#" enctype="multipart/form-data" id="form" autocomplete="off">
        <div class="modal-body">
          <input type="hidden" name="id" class="form-control" id="id">

          <div class="row">
            <div class="col-lg-6" id="content-tipo">
              <div class="form-group">
                <label>Tipo</label>
                <select class="form-control" name="tipo" id="tipo">
                  <option value="0">Estándar</option>
                  <option value="1">Servicio</option>
                  <option value="2">combinación</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6" id="content-stockinicial">
              <div class="form-group">
                <label id="label-stockinicial">Stock Inicial</label>
                <div class="input-group">
                  <input type="hidden" name="tiendastockingreso" id="tiendastockingreso" class="form-control">
                  <input type="hidden" name="almacenstockingreso" id="almacenstockingreso" class="form-control">
                  <input type="text" readonly="readonly" name="cantidadstockingreso" id="cantidadstockingreso" class="form-control" value="0">
                  <span class="help-block"></span>
                  <span class="input-group-btn" style="vertical-align: top">
                    <button type="button" onclick="ingreso()" id="btn-ingresostockinicial" class="btn waves-effect waves-light btn-success"><i class="fa fa-shopping-cart"></i></button>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label>Categoria</label>
                <div class="input-group">
                  <select class="form-control" name="categoria" id="categoria">
                    <option value="0">SELECCIONE</option>
                    <?php foreach ($categories as $category) { ?>
                      <option value="<?= $category->id ?>"><?= $category->nombre ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"></span>
                  <span class="input-group-btn" style="vertical-align: top"><a type="button" class="btn waves-effect waves-light btn-primary" onclick="crearcategoria()"><i class="fa fa-plus"></i></a></span>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label>Marca</label>
                <div class="input-group">
                  <select class="form-control" name="marca" id="marca">
                    <option value="0">SELECCIONE</option>
                    <?php foreach ($marcas as $marca) { ?>
                      <option value="<?= $marca->id ?>"><?= $marca->nombre ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"></span>
                  <span class="input-group-btn"><a class="btn waves-effect waves-light btn-primary" onclick="crearmarca()"><i class="fa fa-plus"></i></a></span>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-4">
              <div class="form-group">
                <label>Codigo</label>
                <input type="hidden" name="numero" class="form-control" id="numero">
                <input type="text" readonly name="codigo" class="form-control" id="codigo">
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group"><label>Codigo Barra</label>
                <input type="text" name="codigoBarra" class="form-control" id="codigoBarra">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="form-group"><label>Codigo Interno</label>
                <input type="text" name="codigoInterno" class="form-control" id="codigoInterno">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" class="form-control" id="nombre">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6"></div>
            <div class="col-lg-6"></div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label for="chkVariante">¿Tiene variantes?</label>
                <div class="material-switch pull-right">
                  <input id="chkVariante" name="chkVariante" type="checkbox" onclick="selectvariante()" />
                  <label for="chkVariante" class="label-success"></label>
                </div>
              </div>
            </div>
            <div class="col-lg-6" id="content-lote">
              <!-- <label for="chkLotes">¿Controlar por lotes?</label>
              <div class="material-switch pull-right">
                <input id="chkLotes" name="chkLotes" type="checkbox">
                <label for="chkLotes" class="label-success"></label>
              </div> -->
              <label for="estado_stockcaja">¿Controlar stock en caja?</label>
              <div class="material-switch pull-right">
                <input id="estado_stockcaja" name="estado_stockcaja" type="checkbox">
                <label for="estado_stockcaja" class="label-success"></label>
              </div>
            </div>
          </div>

          <div class="form-group" id="soloestandar" style="display: none;">
            <div class="row">
              <div class="col-lg-6 col-md-6">
                <label>Precio Compra Paquete (S/)</label>
                <input type="text" value="0" name="preciocomprapaquetes" class="form-control money" id="preciocomprapaquetes">
                <span class="help-block"></span>
              </div>
              <div class="col-lg-6 col-md-6">
                <div class="form-group">
                  <label>Cantidad en paquete</label>
                  <input type="number" name="cantidadpaquete" class="form-control" id="cantidadpaquete">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
          </div>



          <div class="form-group" id="costos">
            <label>Precio Compra Unidad (S/)</label>
            <input type="text" value="0" name="preciocompra" class="form-control" id="preciocompra">
            <span class="help-block"></span>
          </div>

          <div class="form-group" id="content-venta-1">
            <div class="row">
              <div class="col-lg-6">
                <label>Precio Venta Unidad (S/)</label>
                <div class="material-switch pull-right">
                  <input id="estado_precioventa" name="estado_precioventa" type="checkbox" />
                  <label for="estado_precioventa" class="label-success"></label>
                </div>
                <input type="text" value="0" name="precioventa" class="form-control money" id="precioventa">
                <span class="help-block"></span>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>Precio Distribuidor unidad (S/)</label>
                  <div class="material-switch pull-right">
                    <input id="estado_preciodistribuidor" name="estado_preciodistribuidor" type="checkbox" />
                    <label for="estado_preciodistribuidor" class="label-success"></label>
                  </div>
                  <input type="text" value="0" name="preciodistribuidor" class="form-control money" id="preciodistribuidor">
                  <span class="help-block"></span>
                </div>
              </div>

            </div>
          </div>

          <div class="form-group" id="content-venta-2">
            <div class="row">
              <div class="col-lg-6">
                <label>Precio Mayorista unidad (S/)</label>
                <div class="material-switch pull-right">
                  <input id="estado_preciomayorista" name="estado_preciomayorista" type="checkbox" />
                  <label for="estado_preciomayorista" class="label-success"></label>
                </div>
                <input type="text" value="0" name="preciomayorista" class="form-control money" id="preciomayorista">
                <span class="help-block"></span>
              </div>
              <div class="col-lg-6">
                <label>Precio Especial unidad(Soles)</label>
                <div class="material-switch pull-right">
                  <input id="estado_precioespecial" name="estado_precioespecial" type="checkbox" />
                  <label for="estado_precioespecial" class="label-success"></label>
                </div>
                <input type="text" value="0" name="precioespecial" class="form-control money" id="precioespecial">
                <span class="help-block"></span>
              </div>

            </div>
          </div>

          <div class="form-group">
            <div class="row">
              <div class="col-lg-6">
                <label>Unidad medida</label>
                <input type="text" name="unidad" class="form-control" id="unidad" value="UND">
                <span class="help-block"></span>
              </div>
              <div class="col-lg-6">
                <label>alerta Cantidad</label>
                <input type="text" value="0" name="alertqt" class="form-control enteros" id="alertqt">
                <span class="help-block"></span>
              </div>

            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label>Fecha de caducidad</label>
                <input type="date" name="fecha_caducidad" class="form-control" id="fecha_caducidad">
                <span class="help-block"></span>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group">
                <label>Establecer color.</label>
                <select class="form-control" name="color" id="color">
                  <option style="background-color:#000000; color:white" value="color01">NEGRO</option>
                  <option style="background-color:#008000" value="color02">VERDE</option>
                  <option style="background-color:#7cfc00" value="color03">LIMA</option>
                  <option style="background-color:#0000ff" value="color04">AZUL</option>
                  <option style="background-color:#800080" value="color05">MORADO</option>
                  <option style="background-color:#ff4500" value="color06">NARANJA</option>
                  <option style="background-color:#ff0000" value="color07">ROJO</option>
                  <option style="background-color:#ffffff" value="color08">BLANCO</option>
                  <option style="background-color:#8b4513" value="color09">MARRON</option>
                  <option style="background-color:#ffff00" value="color10">AMARILLO</option>
                  <option style="background-color:#808080" value="color11">GRIS</option>
                  <option style="background-color:#ff69b4" value="color12">ROSADO</option>
                  <option style="background-color:#008b8b" value="color13">CYAN</option>
                  <option style="background-color:#20b2aa" value="color14">CELESTE</option>
                </select>
              </div>
            </div>
          </div>


          <div class="form-group">
            <label>Imagen</label>
            <input type="file" accept="image/*" name="foto" id="foto">
          </div>
          <div class="form-group">
            <label>Descripcion del Producto</label>
            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
          </div>

          <?php if ($this->perfil == 1 || $this->perfil == 2) { ?>
            <div class="form-group">
              <label>Empresa</label>
              <select class="form-control" name="empresa" id="empresa">
                <?php foreach ($empresas as $value) { ?>
                  <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                <?php } ?>
              </select>
            </div>
          <?php } ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrado</button>
          <button type="button" id="btnSave" onclick="save()" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /.Modal -->

<!-- Bootstrap modal -->
<div class="modal fade" id="detalle_form" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <div class="form-body table-responsive">
          <table id="tabla" class="table table-striped table-bordered">
            <tr>
              <th>TIPO</th>
              <td><input readonly class="form-control" name="tipo"></td>
            </tr>
            <tr>
              <th>CATEGORIA</th>
              <td><input readonly class="form-control" name="categoria"></td>
            </tr>
            <tr>
              <th>CODIGO</th>
              <td><input readonly class="form-control" name="codigo"></td>
            </tr>
            <tr>
              <th>NOMBRE</th>
              <td><input readonly class="form-control" name="nombre"></td>
            </tr>
            <tr>
              <th>ALERTA</th>
              <td><input readonly class="form-control" name="alertqt"></td>
            </tr>
            <?php if ($this->perfil == 1) { ?>
              <tr>
                <th>COSTO</th>
                <td><input readonly class="form-control" name="costo"></td>
              </tr>
            <?php } ?>
            <tr>
              <th>PRECIO</th>
              <td><input readonly class="form-control" name="precio"></td>
            </tr>
            <tr>
            <tr>
              <th>MARGEN DEL PRODUCTO</th>
              <td><input readonly class="form-control" name="margen_producto"></td>
            </tr>
            <tr>
              <th>DESCRIPCION</th>
              <td><input readonly class="form-control" name="descripcion"></td>
            </tr>
            <tr id="despuesstocks">
              <th>OPCIONES</th>
              <td><input readonly class="form-control" name="options"></td>
            </tr>

            <tr>
              <th>UNIDAD</th>
              <td><input readonly class="form-control" name="unidad"></td>
            </tr>
            <tr>
              <th>FOTO</th>
              <td style="height:100px" id="contentimagen"></td>
            </tr>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal" name="button">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End Bootstrap modal -->

<!-- Bootstrap modal -->
<div class="modal fade" id="combo_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="form_combo" class="form-horizontal" autocomplete="off">
          <input type="hidden" name="combo" id="combo">
          <input id="id" name="id" type="hidden">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">PRODUCTO<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="hidden" name="producto" id="producto">
                <input type="text" class="form-control" name="productos" id="productos" required>
                <span class="help-block"></span>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3">CANTIDAD<span class="required">*</span></label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="cantidad" name="cantidad" required>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="form-group text-right">
            <button type="button" id="btnSaveCombo" onclick="savecombo()" class="btn btn-primary"></button>
          </div>
        </form>

      </div>
      <div class="modal-footer">
        <div class="row">
          <div class="col-xs-12">
            <div class="portlet">
              <div class="portlet-heading">
                <h3 class="portlet-title text-dark">Lista de Combinaciones</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive">
                <table id="tabla_combo" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Codigo</th>
                      <th>Descripción</th>
                      <th>Categoria</th>
                      <th>Cantidad</th>
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
<div class="modal fade" id="variante_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="overflow:auto">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body form">
        <div style="display:flex; justify-content:space-evenly">
          <div class="form-group">
            <label class="control-label">Precio de paquete</label>
            <div class="input-group">
              <span class="input-group-addon">S/.</span>
              <input type="text" class="form-control" name="datapreciopaquete" id="datapreciopaquete" readonly>
            </div>

          </div>
          <div class="form-group">
            <label class="control-label">Cantidad en paquete</label>
            <div class="input-group">
              <span class="input-group-addon">S/.</span>
              <input type="text" class="form-control" name="datacantodadpaquete" id="datacantodadpaquete" readonly>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label">Precio en unidad</label>
            <div class="input-group">
              <span class="input-group-addon">S/.</span>
              <input type="text" class="form-control" name="datapreciounidad" id="datapreciounidad" readonly>
            </div>
          </div>
        </div>
        <form action="#" id="form_zona" autocomplete="off">
          <input type="hidden" class="form-control" name="varianteproducto" id="varianteproducto">
          <input type="hidden" class="form-control" name="idvariante" id="idvariante">
          <div class="form-body">
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>NOMBRE<span class="required">*</span></label>
                  <input type="text" class="form-control" name="nombrevariante" id="nombrevariante">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group" id="cont-cant-varia">
                  <label>CANTIDAD<span class="required">*</span></label>
                  <input type="number" class="form-control" name="cantidadvariante" id="cantidadvariante">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-6">
                <div class="form-group">
                  <label>PRECIO COMPRA<span class="required">*</span></label>
                  <input type="number" class="form-control" name="preciocompravariante" id="preciocompravariante">
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="form-group">
                  <label>PRECIO VENTA<span class="required">*</span></label>
                  <input type="number" class="form-control" name="preciovariante" id="preciovariante">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>





          </div>
        </form>
        <button type="button" id="btnSaveZona" onclick="savevariante()" class="btn btn-primary pull-right"></button>
        <div class="clearfix"></div>

        <div class="row m-row-1">
          <div class="col-xs-12">
            <div class="panel panel-border panel-border-info">
              <div class="panel-heading">
                <h3 class="panel-title text-title-panel">LISTA DE VARIANTE</h3>
                <div class="clearfix"></div>
              </div>
              <!-- /.box-header -->
              <div class="panel-body table-responsive" id="cont-tabl-variante">
                <table id="tabla_variantes" class="table table-bordered table-striped">
                  <thead>
                    <tr class="text-title-panel">
                      <th>#</th>
                      <th>Descripción</th>
                      <th>Cantidad</th>
                      <th>Precio Compra</th>
                      <th>Precio Venta</th>
                      <th>Btn Acciones</th>
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

<!-- Modal cargar excel -->
<div class="modal fade" id="CargarDatos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document" id="stockModal">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="CargarDatos">IMPORTAR DATOS</h4>
      </div>
      <div class="modal-body" id="modal-body" style="overflow:auto">
        <form class="form-horizontal" id="form" method="POST" autocomplete="off">
          <div class="panel-body">
            <div class="form-group">
              <label class="col-sm-2 control-label" for="fecha">Excel<span class="required">*</span></label>
              <div class="col-sm-10">
                <input type="file" id="my_file_input" class="form-control" accept="application/vnd.ms-excel">
              </div>
            </div>
            <div class="form-group">
              <div class="col-sm-12">
                <table class="table table-bordered  table-responsive table-striped">
                  <thead>
                    <tr>
                      <th style="vertical-align: middle">Tipo producto</th>
                      <th style="vertical-align: middle">Categoria</th>
                      <th style="vertical-align: middle">Nombre</th>
                      <th style="vertical-align: middle">Precio compra</th>
                      <th style="vertical-align: middle">Precio venta</th>
                      <th style="vertical-align: middle">Medida</th>
                      <th style="vertical-align: middle">Vencimiento</th>
                      <th style="vertical-align: middle">Codigo barra</th>
                      <th style="vertical-align: middle">Marca</th>
                      <th style="vertical-align: middle">Almacen</th>
                      <th style="vertical-align: middle">Stock</th>
                    </tr>
                  </thead>
                  <tbody id="my_file_output">
                  </tbody>

                </table>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <a class="btn btn-warning" data-toggle="tooltip" title="GENERAR" onclick="generar()" id="procesarExcel"><i class="fa fa-upload"></i></a>
        <a href="<?= $this->url ?>/downloads/producto.xls" class="btn btn-default" data-toggle="tooltip"><i class="fa fa-download"></i> Descargar modelo</a>
      </div>
    </div>
  </div>
</div>
<!-- Modal end-->

  <!--Modal de agregar un ingreso individual-->
<div class="modal fade" id="ingreso_productos" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title-stock-inicial text-center" id="ingreso-title"></h4>
      </div>
      <div class="modal-body">
        <form action="" id="form_ingreso_guardar" method="POST" role="form">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Empresa</label>
                <select class="form-control" name="empresa" id="empresastockinicial" onchange="empresaalmacen()">
                  <?php foreach ($empresas as $empresa) { ?>
                    <option value="<?= $empresa->id ?>"><?= $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Almacen</label>
                <select class="form-control" name="almacen" id="almacen">
                </select>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="" class="col-form-label">Cantidad de Ingreso:</label>
                <input type="text" class="form-control" id="cantidadstock" autocomplete="off" placeholder="Ingrese el stock inicial">
              </div>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" id="btnSaveStock" class="btn btn-primary" data-dismiss="modal" onclick="">GUARDAR</button>
      </div>
    </div>
  </div>
</div>

<!--  -->

<div class="modal fade" id="modal_lotesAlmacen" role="dialog" style="overflow:auto; ">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title"></h3>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-12" id="stockSinLote">

          </div>
        </div>
        <form action="#" enctype="multipart/form-data" id="formLotizar" autocomplete="off">
          <input type="hidden" id="productolote" name="productolote">
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Lote</label>
                  <div class="input-group">
                    <input type="hidden" name="lote" class="form-control" id="lote">
                    <input type="text" name="lotes" class="form-control" id="lotes">
                    <span class="help-block"></span>
                    <span class="input-group-btn">
                      <button class="btn btn-primary" id="BTNcrearlote" onclick="crearlote()">
                        <i class="fa fa-plus"></i>
                      </button>
                    </span>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Almacen a lotificar</label>
                  <select class="form-control" name="almacenlote" id="almacenlote">
                    <option value="0">SELECCIONE</option>
                    <?php foreach ($almacenes as $almacen) {  ?>
                      <option value="<?= $almacen->id ?>"><?= $almacen->nombre ?></option>
                    <?php } ?>
                  </select>
                  <span class="help-block"></span>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="form-group">
                  <label>Cantidad a lotificar</label>
                  <input type="number" name="cantidadlote" class="form-control" id="cantidadlote">
                  <span class="help-block"></span>
                </div>
              </div>
            </div>
          </div>
        </form>

        <div class="row">
          <div class="col-lg-12 text-right">
            <button type="button" class="btn btn-default" data-dismiss="modal">CERRAR</button>
            <button type="button" id="btnSaveLotizar" onclick="savelotizar()" class="btn btn-primary">LOTIZAR</button>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12" id="dataLoteAlmacen">

          </div>
        </div>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="lote_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="titleModalLote"></h4>
      </div>
      <div class="modal-body form">
        <form action="#" role="form" id="form_lote" autocomplete="off">
          <input type="hidden" name="idproducto-lote" id="idproducto-lote">
          <div class="form-body">
            <div class="form-group">
              <div class="row">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Codigo de Lote</label>
                    <input id="codigo_lote" name="codigo_lote" class="form-control" type="text">
                    <span class="help-block"></span>
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label>Fecha de vencimiento</label>
                    <input id="vencimiento_lote" name="vencimiento_lote" class="form-control" type="date">
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
<div class="modal fade" id="modal_crearmarca" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h3 class="modal-title text-center" id="title-marca"></h3>
      </div>
      <div class="modal-body form">
        <form action="#" id="formMarca" class="form-horizontal" autocomplete="off">
          <div class="form-body">
            <div class="form-group">
              <label class="control-label col-md-3">Nombre de la marca<span class="required">*</span></label>
              <div class="col-md-9">
                <input class="form-control" id="nombremarca" type="text" name="nombremarca">
                <span class="help-block"></span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" id="btnSaveMarca" onclick="saveMarca()" class="btn btn-primary">GUARDAR</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal  crear categoria-->
<div class="modal fade" id="modal_crearcategoria" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="modal-title-categoria">Nombre de la Categoria</h4>
      </div>
      <div class="modal-body form">
        <form action="#" role="form" id="formcategoria" autocomplete="off">
          <input type="hidden" name="id" class="form-control" id="id">
          <div class="form-group">
            <label>Nombre de la Categoria</label>
            <input type="text" name="nombrecategoria" class="form-control" id="nombrecategoria">
            <span class="help-block"></span>
          </div>
          <div class="form-group">
            <label>Descripcion</label>
            <input type="text" name="descripcioncategoria" class="form-control" id="descripcioncategoria">
            <span class="help-block"></span>
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
            <input type="file" accept="image/*" name="foto2" id="foto2">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrado</button>
        <button type="button" id="btnSaveCategoria" onclick="savecategoria()" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.Modal -->


<div class="modal fade" id="ingreso" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center" id="ingreso-title"></h4>
      </div>
      <div class="modal-body">
        <form action="" id="formularioingreso_guardar" method="POST" role="form">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Empresa</label>
                <select class="form-control" name="empresa" id="empresastockinicial" onchange="empresaalmacen()">
                  <?php foreach ($empresas as $empresa) { ?>
                    <option value="<?= $empresa->id ?>"><?= $empresa->ruc . " | " . $empresa->nombre . " | " . $empresa->serie ?></option>
                  <?php } ?>
                </select>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label>Almacen</label>
                <select class="form-control" name="almacen" id="almacen">
                </select>
                <span class="help-block"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label for="" class="col-form-label">Cantidad de Ingreso:</label>
                <input type="text" class="form-control" id="cantidadstock" autocomplete="off" placeholder="Ingrese el stock inicial">
              </div>
            </div>
          </div>
        </form>
      </div>


      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="guardarstock_inicial()">AGREGAR</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  //for save method string
  var save_method;
  var table;
  var tables;
  var tablest;
  $(document).ready(function() {
    empresaalmacen();
    detallesproductos();
    dataInsert = [];
    /*
    $("#nombre").maxlength({
      max: 100
    });
    */
    $("#nombrecategoria").mayusculassintildes();
    $("#nombrevariante").mayusculassintildes();
    $("#nombre").mayusculassintildes();
    $("#unidad").mayusculassintildes();
    $("#nombremarca").mayusculassintildes();
    $("#alerta").numeric();
    $("#ventaminima").numeric();
    $('.money').number(true, 2);
    $('.enteros').on('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
    });

    $("#tipo").change(function() {
      $.ajax({
        data: {
          "tipo": $('#tipo').val(),
          "categoria": $('#categoria').val()
        },
        url: '<?= $this->url ?>/codigo',
        type: 'post',
        success: function(data) {
          $("#codigo").val(data.codigo);
          $("#numero").val(data.numero);
        }
      });

      if ($('#tipo').val() === '0') {
        $("#label-stockinicial").removeClass("sinstockinicio");
        $("#btn-ingresostockinicial").attr("disabled", false);
        $("#content-lote").show("fast");
        $('#costos').show("fast");
        $('#alertas').show("fast");
        $('#unidades').show("fast");
        $("#soloestandar").show("fast");
      } else {
        $("#btn-ingresostockinicial").attr("disabled", true);
        $("#label-stockinicial").addClass("sinstockinicio");
        $("#soloestandar").hide("fast");
        $('#costos').hide("fast");
        $('#alertas').hide("fast");
        $('#unidades').hide("fast");
        $("#content-lote").hide("fast");
      }

    });

    $("#categoria").change(function() {
      $.ajax({
        data: {
          "tipo": $('#tipo').val(),
          "categoria": $('#categoria').val()
        },
        url: '<?= $this->url ?>/codigo',
        type: 'post',
        success: function(data) {
          $("#codigo").val(data.codigo);
          $("#numero").val(data.numero);
        }
      });
    });
    $("#productos").autocomplete({
      source: "<?= $this->url ?>/autocompletar",
      minLength: 2,
      select: function(event, ui) {
        $("#producto").val(ui.item.producto);
        $("#cantidad").focus();
      }
    });

    $("#lotes").keyup(() => {
      $("#lotes").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: "<?= $this->url ?>/autocompletarlotes",
            dataType: "json",
            type: "POST",
            data: {
              term: request.term,
              idproducto: $("#productolote").val()
            },
            success: function(data) {
              response(data);
            }
          });
        },
        minLength: 2,
        select: function(event, ui) {
          $("#lote").val(ui.item.lote);
        }
      });
    })

    //set input/textarea/select event when change value, remove class error and remove text help block
    $("input").keyup(function() {
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });
    $("[type='date']").change(function() {
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
      $(this).parent().parent().removeClass('has-error');
      $(this).parent().removeClass('has-error');
      $(this).next().empty();
    });


    $("#cantidadpaquete").keyup(function() {
      let preciopaquete = parseFloat($("#preciocomprapaquetes").val());
      let cantidadpaquete = parseFloat($("#cantidadpaquete").val());
      let compraunidad = preciopaquete / cantidadpaquete;

      if (Number.isNaN(compraunidad)) {
        $("#preciocompra").val("0.00");
      } else {
        $("#preciocompra").val(compraunidad.toFixed(2));
      }
    });


    $("#preciocomprapaquetes").keyup(function() {
      let preciopaquete = parseFloat($("#preciocomprapaquetes").val());
      let cantidadpaquete = parseFloat($("#cantidadpaquete").val());
      let compraunidad = preciopaquete / cantidadpaquete;
      if (Number.isNaN(compraunidad)) {
        $("#preciocompra").val("0.00");
      } else {
        $("#preciocompra").val(compraunidad.toFixed(2));
      }
    });



  });


  function detallesproductos() {
    event.preventDefault();
    table = $('#tablamain').DataTable({
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
        "url": "<?= $this->url ?>/ajax_list/" + $("#empresaproducto").val(),
        "type": "POST",
      },
    });
  }

  /*
    function detallesproductos() {
      event.preventDefault();
      $.ajax({
        url: '<-?= $this->url ?>/detallesproductos/' + $('#empresa').val(),
        type: 'post',
        beforeSend: function() {
          $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
        },
        success: function(data) {
          $("#div_grafico").html(data);
          let tabladata =  $('#tablamain').dataTable({
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
          });
        }
      });
    };
  */
  function add() {
    save_method = 'add';
    $('.col-lg-6').removeClass('has-error');
    $('#form')[0].reset(); // reset form on modals
    $("#chkVariante").attr("checked", false);
    $("#content-tipo").removeClass('col-lg-12');
    $("#content-tipo").addClass('col-lg-6');
    $("#content-stockinicial").show();
    if ($("#chkVariante").is(":checked")) {
      $("#content-venta-1").hide("fast");
      $("#content-venta-2").hide("fast");
    } else {
      $("#content-venta-1").show("fast");
      $("#content-venta-2").show("fast");
    }
    if ($('#tipo').val() === '0') {
      $("#btn-ingresostockinicial").attr("disabled", false);
      $("#label-stockinicial").removeClass("sinstockinicio");
      $("#content-lote").show("fast")
      $('#costos').show("fast");
      $('#alertas').show("fast");
      $('#unidades').show("fast");
      $("#soloestandar").show("fast");
    } else {
      $("#btn-ingresostockinicial").attr("disabled", true);
      $("#label-stockinicial").addClass("sinstockinicio");
      $("#soloestandar").hide("fast");
      $('#costos').hide("fast");
      $('#alertas').hide("fast");
      $('#unidades').hide("fast");
      $("#content-lote").hide("fast");
    }
    $(".input-group").removeClass('has-error');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#modal_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear <?= $this->titulo_controlador ?>'); // Set Title to Bootstrap modal title

  };

  var oFileIn;
  $(function() {
    oFileIn = document.getElementById('my_file_input');
    if (oFileIn.addEventListener) {
      oFileIn.addEventListener('change', filePicked, false);
    }
  });
  //Método que hace el proceso de importar excel a html
  function filePicked(oEvent) {

    // Obtener el archivo del input
    var oFile = oEvent.target.files[0];
    var sFilename = oFile.name;
    // Crear un Archivo de Lectura HTML5
    var reader = new FileReader();
    // Leyendo los eventos cuando el archivo ha sido seleccionado
    reader.onload = function(e) {
      var data = e.target.result;
      var cfb = XLS.CFB.read(data, {
        type: 'binary'
      });
      var wb = XLS.parse_xlscfb(cfb);
      // Iterando sobre cada sheet
      wb.SheetNames.forEach(function(sheetName) {
        // Obtener la fila actual como CSV
        var sCSV = XLS.utils.make_csv(wb.Sheets[sheetName]);
        var data = XLS.utils.sheet_to_json(wb.Sheets[sheetName], {
          header: 1
        });
        $.each(data, function(indexR, valueR) {
          dataInsert.push([]);
          var sRow = "<tr>";
          $.each(data[indexR], function(indexC, valueC) {
            dataInsert[indexR].push((valueC == undefined ? "" : valueC));
            sRow = sRow + "<td>" + (valueC == undefined ? "" : valueC) + "</td>";
          });
          sRow = sRow + "</tr>";
          $("#my_file_output").append(sRow);
        });
      });
    };
    // Llamar al JS Para empezar a leer el archivo .. Se podría retrasar esto si se desea
    reader.readAsBinaryString(oFile);
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

    // ajax adding data to database

    if ($("input#chkVariante").is(":checked")) {
      $("input#chkVariante").val("1");
    }
    $("input#estado_stockcaja").is(":checked") ? $("input#estado_stockcaja").val("1") : $("input#estado_stockcaja").val("0");
    $("input#chkVariante").is(":checked") ? $("input#chkVariante").val("1") : $("input#chkVariante").val("0");
    $("input#chkLotes").is(":checked") ? $("input#chkLotes").val("1") : $("input#chkLotes").val("0");
    $("input#estado_precioventa").is(":checked") ? $("input#estado_precioventa").val("1") : $("input#estado_precioventa").val("1");
    $("input#estado_preciodistribuidor").is(":checked") ? $("input#estado_preciodistribuidor").val("1") : $("input#estado_preciodistribuidor").val("1");
    $("input#estado_preciomayorista").is(":checked") ? $("input#estado_preciomayorista").val("1") : $("input#estado_preciomayorista").val("1");
    $("input#estado_precioespecial").is(":checked") ? $("input#estado_precioespecial").val("1") : $("input#estado_precioespecial").val("1");

    $.ajax({
      url: url,
      type: "POST",
      //data: $("#form").serialize(),
      data: new FormData($("#form")[0]),
      dataType: "JSON",
      contentType: false,
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
          $("#cantidadstock").val("");
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            if (i == 0) {
              $('[name="' + data.inputerror[i] + '"]').focus();
            }
            //$('[name="' + data.inputerror[i] + '"]').closest('div').addClass("has-error");
            //$('#tipoventa').siblings('span').text(msg);
            $('[name="' + data.inputerror[i] + '"]').parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
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
    event.preventDefault();
    table.ajax.reload(null, false)
  };

  function edit(id) {
    save_method = 'update';
    $('#form')[0].reset();
    $('.col-lg-6').removeClass('has-error');
    $('.input-group').removeClass('has-error');
    $('.form-group').removeClass('has-error');
    $('.help-block').empty();
    $("#content-tipo").removeClass('col-lg-6');
    $("#content-tipo").addClass('col-lg-12');
    $("#content-stockinicial").hide();
    $.ajax({
      url: "<?= $this->url ?>/ajax_edit/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {

        $('[name="id"]').val(data.id);
        $('[name="tipo"]').val(data.tipo);

        if (data.tipo !== '0') {
          $("#soloestandar").hide("fast");
          $('#costos').hide("fast");
          $('#alertas').hide("fast");
          $('#unidades').hide("fast");
          $("#content-lote").hide("fast");
        } else {
          $("#soloestandar").show("fast");
          $('#costos').show("fast");
          $('#alertas').show("fast");
          $('#unidades').show("fast");
          $("#content-lote").show("fast");
        }


        $('[name="preciocomprapaquetes"]').val(data.preciocomprapaquete);

        $('[name="codigoBarra"]').val(data.codigoBarra);
        $('[name="categoria"]').val(data.categoria);
        $('[name="codigo"]').val(data.codigo);
        $('[name="unidad"]').val(data.unidad);
        $('[name="nombre"]').val(data.nombre);
        $('[name="fecha_caducidad"]').val(data.fechacaducidad);

        if (data.variante == 1) {
          $('[name="chkVariante"]').prop('checked', true);
          $("#content-venta-1").hide("fast");
          $("#content-venta-2").hide("fast");

        } else {
          $('[name="chkVariante"]').prop('checked', false);
          $("#content-venta-1").show("fast");
          $("#content-venta-2").show("fast");
        }

        if (data.status_lote == 1) {
          $('[name="chkLotes"]').prop('checked', true);
        } else {
          $('[name="chkLotes"]').prop('checked', false);
        }

        data.estado_precioventa == "1" ? $('[name="estado_precioventa"]').prop('checked', true) : "";
        data.estado_preciodistribuidor == "1" ? $('[name="estado_preciodistribuidor"]').prop('checked', true) : "";
        data.estado_preciomayorista == "1" ? $('[name="estado_preciomayorista"]').prop('checked', true) : "";
        data.estado_precioespecial == "1" ? $('[name="estado_precioespecial"]').prop('checked', true) : "";
        data.estado_stockcaja == "1" ? $('[name="estado_stockcaja"]').prop('checked', true) : "";

        $('[name="numero"]').val(data.numero);
        if (data.marca != null) {
          $('[name="marca"]').val(data.marca)
        };
        $('[name="preciocompra"]').val(data.preciocompra);
        $('[name="descripcion"]').val(data.descripcion);
        $('[name="precioventa"]').val(data.precioventa);
        $('[name="preciodistribuidor"]').val(data.preciodistribuidor);
        $('[name="preciomayorista"]').val(data.preciomayorista);
        $('[name="precioespecial"]').val(data.precioespecial);
        $('[name="alertqt"]').val(data.alertqt);
        $('[name="cantidadpaquete"]').val(data.cantidadpaquete);
        $('[name="color"]').val(data.color);


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
  }


  function stock_inicial($id){
    save_method = 'update';
    $('#form')[0].reset();
    $('#btnSave').text('guardando...'); //change button text
    $('#btnSave').attr('disabled', true); //set button disable
    $.ajax({
      url: "<?= $this->url ?>/ajax_edit/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        
        

        $('#ingreso_productos').modal('show');
        $('.modal-title-stock-inicial').text('Aumentar su Stock Inicial');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });

  }


  function ingreso() {
    $("#ingreso").addClass("centrarmodal");
    $('#ingreso-title').text('STOCK INICIAL');
    $('#ingreso').modal('show');
  }

  function empresaalmacen() {
    $.ajax({
      url: "<?= $this->url ?>/ajax_empresaAlmacen/" + $("#empresastockinicial").val(),
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

  function detalle(id, empresa) {
    //Ajax Load data from ajax
    $(".fila-stock").remove();
    $(`#boton-detalles-${id}`).attr("disabled", true);

    $.ajax({
      url: "<?= $this->url ?>/ajax_detalle/" + id + "/" + empresa,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $(`#boton-detalles-${id}`).attr("disabled", false);
        $('[name="tipo"]').val(data.tipo);
        $('[name="categoria"]').val(data.categoria);
        $('[name="codigo"]').val(data.codigo);
        $('[name="nombre"]').val(data.nombre);
        $('[name="descripcion"]').val(data.descripcion);
        $('[name="costo"]').val(data.costo);
        $('[name="margen_producto"]').val(data.margen_producto); //adc
        $('[name="precio"]').val(data.precio);
        $('[name="alertqt"]').val(data.alertqt);
        $('[name="options"]').val(data.options);
        $('[name="unidad"]').val(data.unidad);
        $('#despuesstocks').after(data.dataAlmacenes);
        if (data.photo) {
          $("#contentimagen").html(`<img src="<?= base_url() ?>files/products/${data.photo}" width="200px" readonly name="photo">`);
        } else {
          $("#contentimagen").text("SIN IMAGEN");
        }
        $("#stockalmacen").html(data.stock);
        $('#detalle_form').modal('show'); // show bootstrap modal when complete loaded
        $('.modal-title').text('Detalle del Producto'); // Set title to Bootstrap modal title
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

  function combo(combo) {
    save_method = 'addcombo';
    $('#form_combo')[0].reset(); // reset form on
    $('#combo').val(combo);
    $('#btnSaveCombo').text('GRABAR');
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#combo_form').modal('show'); // show bootstrap modal
    $('.modal-title').text('Crear Combinacion'); // Set Title to Bootstrap modal title
    cargar_combos(combo);
  };

  function cargar_combos(combo) {
    tables = $('#tabla_combo').DataTable({
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
        "url": "<?= $this->url ?>/ajax_list_combo/" + combo,
        "type": "POST"
      },
    });
  };

  function savecombo() {
    event.preventDefault();
    $('#btnSaveCombo').text('guardando...'); //change button text
    $('#btnSaveCombo').attr('disabled', true); //set button disable
    // ajax adding data to database
    $.ajax({
      url: '<?= $this->url ?>/ajax_addcombo',
      type: "POST",
      data: $('#form_combo').serialize(),
      dataType: "JSON",
      success: function(data) {
        //if success close modal and reload ajax table
        if (data.status) {
          reload_tables();

          reload_table();


          $('#form_combo')[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: 'El registro fue creado exitosamente.'
          });
          $('#btnSaveCombo').text('GRABAR'); //change button text
          $('#btnSaveCombo').attr('disabled', false); //set button enable
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $('[name="' + data.inputerror[i] + '"]').parent().parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
          }
          $('#btnSaveCombo').text('GRABAR'); //change button text
          $('#btnSaveCombo').attr('disabled', false); //set button enable
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'El registro no se pudo crear verifique las validaciones.'
        });
        $('#btnSaveCombo').text('GRABAR'); //change button text
        $('#btnSaveCombo').attr('disabled', false); //set button enable
      }
    });


  };

  function borrarcombo(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletecombo/" + id,
          type: "POST",
          dataType: "JSON",
          success: function(data) {
            //if success reload ajax table
            reload_tables();
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

  function reload_tables() {
    tables.ajax.reload(null, false); //reload datatable ajax
  };

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
  //             msg: 'Prodcuto activado correctamente'
  //           });
  //         }
  //       });
  //     }
  //   });
  // };

  // ZONAS
  function variante(producto, tipo) {
    save_method = 'add';
    $(`#variante-${producto}`).attr('disabled', true);
    $(`#variante-${producto}`).html("<i class='fa fa-spinner fa-spin'></i>");
    $.ajax({
      url: "<?= $this->url ?>/ajax_variante_precicompra/" + producto,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $(`#variante-${producto}`).attr('disabled', false);
        $(`#variante-${producto}`).html("<i class='fa fa-th'></i>");
        $('[name="datapreciopaquete"]').val(data.preciocomprapaquete);
        $('[name="datacantodadpaquete"]').val(data.cantidadpaquete);
        $('[name="datapreciounidad"]').val(data.preciocompra);
        $('#form_zona')[0].reset(); // reset form on
        $('#varianteproducto').val("");
        $('#varianteproducto').val(producto);
        $('#btnSaveZona').text('GRABAR');
        $('.form-group').removeClass('has-error'); // clear error class
        $('.help-block').empty(); // clear error string
        $('#variante_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Crear Variante'); // Set Title to Bootstrap modal title
        cargar_variantes(producto);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $(`#variante-${producto}`).attr('disabled', false);
        $(`#variante-${producto}`).html("<i class='fa fa-th'></i>");
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });

  };

  function cargar_variantes(producto) {
    tableZ = $('#tabla_variantes').DataTable({
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
        "url": "<?= $this->url ?>/ajax_listvariante/" + producto,
        "type": "GET"
      },
    });
  };

  function savevariante() {
    $('#btnSaveZona').text('guardando...'); //change button text
    $('#btnSaveZona').attr('disabled', true); //set button disable
    var url;

    if (save_method == 'add') {
      url = "<?= $this->url ?>/ajax_addvariante";
      msgsuccess = "El registro fue creado exitosamente.";
      msgerror = "El registro no se pudo crear verifique las validaciones.";
    } else {
      url = "<?= $this->url ?>/ajax_updatevariante";
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

          save_method = 'add';
          $("#varianteproducto").val();
          reload_tablesZ();
          $('.modal-title').text('Crear Variante');
          $("#idvariante").val("");
          $("#nombrevariante").val("");
          $("#preciovariante").val("");
          $("#cantidadvariante").val("");
          $("#preciocompravariante").val("");

          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
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
          position: "top right",
          msg: msgerror
        });
        $('#btnSaveZona').text('GRABAR'); //change button text
        $('#btnSaveZona').attr('disabled', false); //set button enable
      }
    });
  };

  function editvariante(id) {
    save_method = 'update';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    //Ajax Load data from ajax
    $.ajax({
      url: "<?= $this->url ?>/ajax_editvariante/" + id,
      type: "GET",
      dataType: "JSON",
      success: function(data) {
        $('[name="idvariante"]').val(data.id);
        $('[name="nombrevariante"]').val(data.nombre);
        $('[name="preciovariante"]').val(data.precio);
        $('[name="preciocompravariante"]').val(data.preciocompra);
        $('[name="cantidadvariante"]').val(data.cantidad);
        $('#varianteproducto').val(data.producto);
        $('#btnSaveZona').text('MODIFICAR');
        $('.modal-title').text('Modificar Variante'); // Set title to Bootstrap modal title
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

  function borrarvariante(id) {
    bootbox.confirm("Seguro desea Eliminar este registro?", function(result) {
      if (result === true) {
        $.ajax({
          url: "<?= $this->url ?>/ajax_deletevariante/" + id,
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

  function cargardatos() {
    save_method = 'add';
    $('#form')[0].reset(); // reset form on modals
    $('.form-group').removeClass('has-error'); // clear error class
    $('.help-block').empty(); // clear error string
    $('#CargarDatos').modal('show'); // show bootstrap modal
    $('.modal-title').text('IMPORTAR PRODUCTOS'); // Set Title to Bootstrap modal title
  };

  function generar() {

    if (document.getElementById('my_file_input').value == '') {
      Lobibox.notify('warning', {
        size: 'mini',
        position: "top right",
        msg: "Debes seleccionar un archivo"
      });
    } else {
      $("#procesarExcel").attr("disabled", true);
      $("#procesarExcel").html("<i class='fa fa-spin fa-spinner'></i>");
      $.ajax({
        url: '<?= $this->url ?>/insertarNew',
        type: "POST",
        data: {
          'data': JSON.stringify(dataInsert),
        },
        success: function(data) {
          $("#my_file_input").val("");
          $("#my_file_output").empty();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: "Se proceso correctamente"
          });
          $("#procesarExcel").attr("disabled", false);
          $("#procesarExcel").html("<i class='fa fa-upload'></i>");
          $('#CargarDatos').modal('hide');
          reload_table();
        },
        error: function(jqXHR, textStatus, errorThrown) {
          Lobibox.notify('error', {
            size: 'mini',
            position: "top right",
            msg: '¡Error! Contactate con el administrador'
          });
          $("#procesarExcel").attr("disabled", false);
          $("#procesarExcel").html("<i class='fa fa-upload'></i>");
        },
      });
    }
  };

  function selectvariante() {

    if ($("#chkVariante").is(":checked")) {
      $("#content-venta-1").hide("splice");
      $("#content-venta-2").hide("splice");
    } else {
      $("#content-venta-1").show("splice");
      $("#content-venta-2").show("splice");
    }
  }

  function verLotesAlmacen(idproducto, empresa) {
    $(`#button-lote-${idproducto}`).attr("disabled", true);
    $(`#button-lote-${idproducto}`).html("<i class='fa fa-spin fa-spinner'></i>");
    $(".col-lg-4").removeClass('has-error');
    $(".form-group").removeClass('has-error');
    $(".help-block").empty();
    $("#formLotizar")[0].reset();
    $("#lote").val("");
    event.preventDefault();
    $.ajax({
      url: '<?= $this->url ?>/ajax_dataLoteAlmacen/' + idproducto + "/" + empresa,
      type: "POST",
      dataType: 'json',
      success: function(data) {
        $("#productolote").val(idproducto);
        $(".modal-title").text("Lotes");
        $("#modal_lotesAlmacen").modal('show');
        $("#stockSinLote").html(data.htmlSinLotificar);
        $("#dataLoteAlmacen").html(data.dataHtml);
        $('#example1').dataTable({
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
        });
        $(`#button-lote-${idproducto}`).attr("disabled", false);
        $(`#button-lote-${idproducto}`).html("<i class='fa fa-bar-chart-o'></i>");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: '¡Error! Contactate con el administrador'
        });
        $(`#button-lote-${idproducto}`).attr("disabled", true);
        $(`#button-lote-${idproducto}`).html("<i class='fa fa-spin fa-spinner'></i>");
      },
    });
  }

  function crearlote() {
    event.preventDefault();
    let idproducto = $("#productolote").val();
    $("#idproducto-lote").val(idproducto);
    $(".form-control").removeClass('has-error');
    $(".col-lg-6").removeClass('has-error');
    $(".help-block").empty();
    $('#lote_modal').addClass("centrarmodal");
    $("#titleModalLote").text("Crear lote");
    $('#lote_modal').modal('show');
  }

  function savelote() {
    $("#btnSaveLote").attr("disabled", true);
    $("#btnSaveLote").text("GUARDANDO...");
    $.ajax({
      url: '<?= $this->url ?>/ajax_addLote',
      type: "POST",
      data: $("#form_lote").serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          $("#form_lote")[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: "Se guardo correctamente"
          });
          verLotesAlmacen($("#idproducto-lote").val()); //recargar lotes
          reload_table(); //Recargar tabla de los productos
          $('#lote_modal').modal('hide');
          $("#lotes").val(data.textlote);
          $("#lote").val(data.idlote);
        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $(`[name="${data.inputerror[i]}"]`).parent().parent().addClass('has-error');
            $(`[name="${data.inputerror[i]}"]`).next().text(data.error_string[i]);
          }
        }
        $("#btnSaveLote").attr("disabled", false);
        $("#btnSaveLote").text("GUARDAR");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: '¡Error! Contactate con el administrador'
        });
        $("#btnSaveLote").attr("disabled", false);
        $("#btnSaveLote").text("GUARDAR");
      },
    });
  }

  function savelotizar() {
    $("#btnSaveLotizar").attr("disabled", true);
    $("#btnSaveLotizar").text("LOTIZANDO...");
    $.ajax({
      url: '<?= $this->url ?>/ajax_lotizar',
      type: "POST",
      data: $("#formLotizar").serialize(),
      dataType: "JSON",
      success: function(data) {
        if (data.status) {
          verLotesAlmacen($("#productolote").val());
          $("#lote").val("");
          $("#formLotizar")[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: "Se lotizo correctamente"
          });
          $(".form-group").removeClass('has-error');
          $(".help-block").empty();

        } else {
          for (var i = 0; i < data.inputerror.length; i++) {
            $(`[name="${data.inputerror[i]}"]`).parent().parent().addClass('has-error');
            $(`[name="${data.inputerror[i]}"]`).next().text(data.error_string[i]);
          }
        }
        $("#btnSaveLotizar").attr("disabled", false);
        $("#btnSaveLotizar").text("LOTIZAR");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: '¡Error! Contactate con el administrador'
        });
        $("#btnSaveLotizar").attr("disabled", false);
        $("#btnSaveLotizar").text("LOTIZAR");
      },
    });
  }

  function crearmarca() {
    $("#title-marca").text("CREAR MARCA");
    $("#modal_crearmarca").modal("show");
    $("#modal_crearmarca").addClass("centrarmodal");
  }

  function saveMarca() {
    $("#btnSaveMarca").attr("disabled", true);
    $("#btnSaveMarca").text("GUARDANDO...");
    $.ajax({
      url: '<?= $this->url ?>/ajax_addmarca',
      type: "POST",
      data: $("#formMarca").serialize(),
      dataType: "JSON",
      success: function(parametrorecibido) {
        if (parametrorecibido.status) {
          $("#marca").empty();
          $("#marca").append(`<option value="0">SELECCIONE</option>`)
          for (value of parametrorecibido.marcas) {
            $("#marca").append(`<option value="${value.id}">${value.nombre}</option>`);
          }
          $("#marca").val(parametrorecibido.idregistrado);
          $("#formMarca")[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: "top right",
            msg: "Se guardo correctamente"
          });
          $('#modal_crearmarca').modal('hide');
        } else {
          for (var i = 0; i < parametrorecibido.inputerror.length; i++) {
            $(`[name="${parametrorecibido.inputerror[i]}"]`).parent().parent().addClass('has-error');
            $(`[name="${parametrorecibido.inputerror[i]}"]`).next().text(parametrorecibido.error_string[i]);
          }
        }
        $("#btnSaveMarca").attr("disabled", false);
        $("#btnSaveMarca").text("GUARDAR");
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: '¡Error! Contactate con el administrador'
        });
        $("#btnSaveMarca").attr("disabled", false);
        $("#btnSaveMarca").text("GUARDAR");
      },
    });
  }

  function crearcategoria() {
    $("#modal-title-categoria").text("CREAR CATEGORIA")
    $("#modal_crearcategoria").modal("show");
    $("#modal_crearcategoria").addClass("centrarmodal");

  }

  function savecategoria() {
    $('#btnSaveCategoria').text('Guardando...'); //change button text
    $('#btnSaveCategoria').attr('disabled', true); //set button disable
    if ($("#chkExtras").is(":checked")) {
      $("#chkExtras").val("1");
    } else {
      $("#chkExtras").val("0");
    }
    $.ajax({
      url: "<?= $this->url ?>/ajax_addcategoria/" + $("#tipo").val(),
      type: "POST",
      data: new FormData($("#formcategoria")[0]), //guardar un objeto o un arreglo en una base de datos metodo serialize.
      dataType: "JSON",
      contentType: false, // NEEDED, DON'T OMIT THIS (requires jQuery 1.6+)
      processData: false,
      success: function(parametrorecibido) {
        if (parametrorecibido.status) {
          $("#numero").val(parametrorecibido.codigoproducto.numero);
          $("#codigo").val(parametrorecibido.codigoproducto.codigo);
          $('#categoria').empty();
          $('#categoria').append(`<option value=0>SELECCIONE</option>`)
          for (value of parametrorecibido.karl) {
            $("#categoria").append(`<option value="${value.id}">${value.nombre}</option>`);
          }
          $("#categoria").val(parametrorecibido.idregistrado);
          $("#formcategoria")[0].reset();
          Lobibox.notify('success', {
            size: 'mini',
            position: 'top right',
            msg: "Se guardo correctamente"
          });
          $('#modal_crearcategoria').modal('hide');
        } else {
          for (var i = 0; i < parametrorecibido.inputerror.length; i++) {
            $(`[name="${parametrorecibido.inputerror[i]}"]`).parent().addClass('has-error'); //select parent twice to select div form-group class and add has-error class
            $(`[name="${parametrorecibido.inputerror[i]}"]`).next().text(parametrorecibido.error_string[i]); //select span help-block class set text error string
          }
        }

        $('#btnSaveCategoria').text('Guardar'); //change button text
        $('#btnSaveCategoria').attr('disabled', false); //set button enable
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          position: "top right",
          msg: '¡Error! Contactatese con el administrador'
        });
        $('#btnSaveCategoria').text('Guardar'); //change button text
        $('#btnSaveCategoria').attr('disabled', false); //set button enable
      },
    });

  }

  function desactiva(event, id) {
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

    })
  }


  function activa(event, id) {
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
    });
  }

  function guardarstock_inicial() {
    let cantidadstock = $("#cantidadstock").val();
    let tienda = $("#empresastockinicial").val();
    let almacen = $("#almacen").val();
    $("#cantidadstockingreso").val(cantidadstock);
    $("#tiendastockingreso").val(tienda);
    $("#almacenstockingreso").val(almacen)
  }
</script>
<script type="text/javascript" src="<?= base_url() . RECURSOS ?>js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>