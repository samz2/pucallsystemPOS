<?php $empresa = $this->Controlador_model->get($this->empresa, 'empresa'); ?>
<div class="container">
  <div class="row" style="margin-top:60px;">
    <div class="col-md-4">
      <div class="statCart Statcolor01">
        <i class="fa fa-users" aria-hidden="true"></i>
        <h1 class="count"><?= $CustomerNumber ?></h1><br>
        <span>Clientes</span>
      </div>
    </div>
    <div class="col-md-4">
      <div class="statCart Statcolor02">
        <i class="fa fa-archive" aria-hidden="true"></i>
        <h1 class="count"><?= $ProductNumber ?></h1><br>
        <span>Productos (En <?= $CategoriesNumber ?> Categorias)</span>
      </div>
    </div>
    <div class="col-md-4">
      <div class="statCart Statcolor03">
        <i class="fa fa-money" aria-hidden="true"></i>
        <h2 style="display: inline"><span class="count"><?= number_format($TodaySales, 2, '.', '') ?></span> Soles</h2><br>
        <span>Ventas HOY</span>
      </div>
    </div>
  </div>
  <div class="row" style="margin-top:50px;">
    <div class="col-md-8">
      <!-- chart container  -->
      <div class="statCart">
        <h3>estadisticas mensuales</h3>
        <div style="width:100%">
          <canvas id="canvas" height="330" width="750"></canvas>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <!-- pie container  -->
      <div class="statCart">
        <h3>5 Productos TOP este mes</h3>
        <div id="canvas-holder">
          <?= count($Top5product) >= 5 ? '<canvas id="chart-area2" style="width:auto"/>' : '<h3 style="margin: 50px 0">NO HAY PRODUCTOS</h3>'; ?>
        </div>
      </div>
    </div>
  </div>
  <?php if (count($Top5product) >= 5) { ?>
    <div class="statCart" style="margin-top: 50px;">
      <div class="row">
        <div class="col-md-2">
          <h4>
            <center>5 Productos TOP este mes</center>
          </h4>
        </div>
        <?php $producto0 = $this->Controlador_model->get($Top5product[0]->producto, 'producto'); ?>
        <?php $producto1 = $this->Controlador_model->get($Top5product[1]->producto, 'producto'); ?>
        <?php $producto2 = $this->Controlador_model->get($Top5product[2]->producto, 'producto'); ?>
        <?php $producto3 = $this->Controlador_model->get($Top5product[3]->producto, 'producto'); ?>
        <?php $producto4 = $this->Controlador_model->get($Top5product[4]->producto, 'producto'); ?>
        <div class="col-lg-10">
          <span class="label label-default" style="background-color: #F3565D; font-size:15px; margin:5px; display:inline-block"><?= $producto0->nombre ?></span>
          <span class="label label-default" style="background-color: #FC9D9B; font-size:15px; margin:5px; display:inline-block"><?= $producto1->nombre ?></span>
          <span class="label label-default" style="background-color: #FACDAE; font-size:15px; margin:5px; display:inline-block"><?= $producto2->nombre ?></span>
          <span class="label label-default" style="background-color: #9FC2C4; font-size:15px; margin:5px; display:inline-block"><?= $producto3->nombre ?></span>
          <span class="label label-default" style="background-color: #8297A8; font-size:15px; margin:5px; display:inline-block"><?= $producto4->nombre ?></span>
        </div>
      </div>
    </div>
  <?php } ?>
  <!-- ************************************************************************************************** -->

  <?php if (count($Top5product) >= 5) { ?>
    <div class="statCart" style="margin-top: 50px;">
      <div class="row">
        <div class="col-md-2">
          <h4>
            <center>TOP Vendedores</center>
          </h4>
        </div>
        <?php $producto0 = $this->Controlador_model->get($Top5product[0]->producto, 'producto'); ?>
        <?php $producto1 = $this->Controlador_model->get($Top5product[1]->producto, 'producto'); ?>
        <?php $producto2 = $this->Controlador_model->get($Top5product[2]->producto, 'producto'); ?>
        <?php $producto3 = $this->Controlador_model->get($Top5product[3]->producto, 'producto'); ?>
        <?php $producto4 = $this->Controlador_model->get($Top5product[4]->producto, 'producto'); ?>
        <div class="col-lg-10">
          <span class="label label-default" style="background-color: #F3565D; font-size:15px; margin:5px; display:inline-block"><?= $producto0->nombre ?></span>
          <span class="label label-default" style="background-color: #FC9D9B; font-size:15px; margin:5px; display:inline-block"><?= $producto1->nombre ?></span>
          <span class="label label-default" style="background-color: #FACDAE; font-size:15px; margin:5px; display:inline-block"><?= $producto2->nombre ?></span>
          <span class="label label-default" style="background-color: #9FC2C4; font-size:15px; margin:5px; display:inline-block"><?= $producto3->nombre ?></span>
          <span class="label label-default" style="background-color: #8297A8; font-size:15px; margin:5px; display:inline-block"><?= $producto4->nombre ?></span>
        </div>
      </div>
    </div>
  <?php } ?>
  <!-- ************************************************************************************************** -->
  <div class="row rangeStat" style="margin-top:50px; margin-bottom:70px;">
    <div class="col-md-12">
      <div class="statCart">
        <h1 class="statYear"><?= $year; ?></h1>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:5px">
          <div>
            <button class="btn btn-info btn-sm" type="button" style="margin-top:0px" onclick="getyearstats('next')">
              < </button>
                <button class="btn btn-info btn-sm" type="button" style="margin-top:0px" onclick="getyearstats('prev')"> > </button>
          </div>
          <div>
            <span class="revenuespan" style="font-size:11px;">Ingresos</span>
            <span class="expencespan" style="font-size:11px;">Gastos</span>
          </div>
        </div>
        <div id="statyears">
          <table class="StatTable">
            <tr>
              <td><span class="revenuespan"><?= $monthly->january ?> Soles</span><span class="expencespan"><?= $monthlyExp->january ?> Soles</span>Enero</td>
              <td><span class="revenuespan"><?= $monthly->feburary ?> Soles</span><span class="expencespan"><?= $monthlyExp->feburary ?> Soles</span>Febrero</td>
              <td><span class="revenuespan"><?= $monthly->march ?> Soles</span><span class="expencespan"><?= $monthlyExp->march ?> Soles</span>Marzo</td>
              <td><span class="revenuespan"><?= $monthly->april ?> Soles</span><span class="expencespan"><?= $monthlyExp->april ?> Soles</span>Abril</td>
            </tr>
            <tr>
              <td><span class="revenuespan"><?= $monthly->may ?> Soles</span><span class="expencespan"><?= $monthlyExp->may ?> Soles</span>Mayo</td>
              <td><span class="revenuespan"><?= $monthly->june ?> Soles</span><span class="expencespan"><?= $monthlyExp->june ?> Soles</span>Junio</td>
              <td><span class="revenuespan"><?= $monthly->july ?> Soles</span><span class="expencespan"><?= $monthlyExp->july ?> Soles</span>Julio</td>
              <td><span class="revenuespan"><?= $monthly->august ?> Soles</span><span class="expencespan"><?= $monthlyExp->august ?> Soles</span>Agosto</td>
            </tr>
            <tr>
              <td><span class="revenuespan"><?= $monthly->september ?> Soles</span><span class="expencespan"><?= $monthlyExp->september ?> Soles</span>Setiembre</td>
              <td><span class="revenuespan"><?= $monthly->october ?> Soles</span><span class="expencespan"><?= $monthlyExp->october ?> Soles</span>Octubre</td>
              <td><span class="revenuespan"><?= $monthly->november ?> Soles</span><span class="expencespan"><?= $monthlyExp->november ?> Soles</span>Noviembre</td>
              <td><span class="revenuespan"><?= $monthly->december ?> Soles</span><span class="expencespan"><?= $monthlyExp->december ?> Soles</span>Diciembre</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- START ALERTS AND CALLOUTS -->
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Reporte <?= $this->controlador ?></h3>
      </div>
      <!-- /.box-header -->
      <form action="" class="form-horizontal" method="POST" id="form_01" role="form">
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

          <div class="form-group">
            <label class="col-sm-2 control-label">Empresa<span class="required">*</span></label>
            <div class="col-sm-10">
              <select id="empresa" name="empresa" class="form-control" required>
                <?php foreach ($empresas as $value) { ?>
                  <option value="<?= $value->id ?>"><?= $value->ruc . ' | ' . ($value->tipo == 0 ? $value->nombre : $value->razonsocial) ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <!-- /.box-body -->
        <div class="panel-footer text-center">
          <!--
          <a onclick="consolidar()" class="btn btn-warning" data-toggle="tooltip" title="CONSOLIDAR"><i class="fa fa-upload"></i></a>
          <a style="position:relative" onclick="consolidarexcel()" class="btn btn-danger" data-toggle="tooltip" title="CONSOLIDAR">
            <i class="fa fa-file-excel-o"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>
          -->



          <a onclick="vendido()" class="btn btn-warning" data-toggle="tooltip" title="VENDIDO"><i class="fa fa-upload"></i></a>
          <a style="position: relative;" onclick="vendidoexcel()" class="btn btn-danger" data-toggle="tooltip" title="VENDIDO">
            <i class="fa fa-file-excel-o"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>
          <a onclick="valorizado()" class="btn btn-warning" data-toggle="tooltip" title="VALORIZADO"><i class="fa fa-upload"></i></a>
          <a style="position: relative;" onclick="valorizadoexcel()" class="btn btn-danger" data-toggle="tooltip" title="VALORIZADO">
            <i class="fa fa-file-excel-o"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>

          <a onclick="margenproducto()" class="btn btn-warning" data-toggle="tooltip" title="INVENTARIO"><i class="fa fa-upload"></i></a>
          <a style="position: relative;" onclick="margenproductoexcel()" class="btn btn-danger" data-toggle="tooltip" title="INVENTARIO EXCEL">
            <i class="fa fa-file-excel-o"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>
          <!--
          <a style="position: relative;" onclick="inventarioexcel()" class="btn btn-danger" data-toggle="tooltip" title="INVENTARIO">
            <i class="fa fa-file-excel-o"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>
                -->
          <a style="position: relative;" onclick="inventarioproducto()" class="btn btn-info" data-toggle="tooltip" title="INVENTARIO PDF">
            <i class="fa fa-file"></i>
            <i style="font-size:10px; position:absolute; bottom:6px; right:4px" class="fa fa-download"></i>
          </a>
          <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
        </div>
      </form>
    </div>
    <!-- /.box -->
  </div>
  <!-- /.box -->
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title text-dark">Lista de <?= $this->controlador ?></h3>
        <div class="clearfix"></div>
      </div>
      <!-- /.box-header -->
      <div class="panel-body table-responsive">
        <div id="div_grafico"></div>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>
<!-- /.row -->
<!--
<div class="row">
  <div class="panel panel-default">
    <div class="panel-heading">
        
      <h3 class="panel-title text-dark">Carga masiva de productos</h3>
      
      <div class="pull-right">
        <div class="form-group">
          <div class="col-md-12 file-upload">
            <div class="file-select">
              <div class="file-select-button" id="fileName">Elija el archivo</div>
              <div class="file-select-name" id="noFile">Ningún archivo elegido...</div>
              <input type="file" name="file" id="file" accept="application/vnd.ms-excel" required>
            </div>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-12">
            <a href="<?= $this->url ?>/downloads/motor.xls" class="btn btn-danger btn-sm" role="button" data-toggle="tooltip" title="Descargar"><i class="fa fa-download"></i></a>
            <button type="submit" class="btn btn-info btn-sm" data-toggle="tooltip" title="Ver Documento"><i class="fa fa-barcode"></i></button>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
        
    </div>
    
    <div class="panel-body table-responsive">

    </div>
   
  </div>
</div>
-->
<!--  reporte de Vendedores -->



<!-- START ALERTS AND CALLOUTS -->  
<div class="row">
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title">Reporte Top vendedores</h3>
      </div>
      <!-- /.box-header -->
      <form action="" class="form-horizontal" method="POST" id="form_01" role="form">
        <div class="panel-body">
          <div class="form-group">
            <label class="col-sm-2 control-label">Fecha<span class="required">*</span></label>
            <div class="col-sm-5">
              <input class="form-control" id="finicio2" type="date" name="finicio2" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="col-sm-5">
              <input class="form-control" id="factual2" type="date" name="factual2" value="<?= date('Y-m-d') ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label">Perfil<span class="required">*</span></label>
            <div class="col-sm-10">
              <select id="perfiles" name="perfiles" class="form-control">
              <option value="TODOS">TODOS</option>
                <?php foreach ($perfiles as $value) { ?>
                  <option value="<?= $value->nombre ?>"> <?= $value->nombre ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>

        <div class="panel-footer text-center">

          <a onclick="reportevendedores()" class="btn btn-warning" data-toggle="tooltip" title="VENDIDO"><i class="fa fa-upload"></i></a>
          <a onclick="location.reload()" class="btn btn-yahoo" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
        </div>
      </form>
    </div>
    <!-- /.box -->
  </div>
  <!-- /.box -->
  <div class="col-xs-12">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title text-dark">Lista de Vendedores</h3>
        <div class="clearfix"></div>
      </div>
      <!-- /.box-header -->
      <div class="panel-body table-responsive">
        <div id="div_grafico2"></div>
      </div>
      <!-- /.box-body -->
    </div>
  </div>
</div>
<!-- /.row -->

<!--[ footer ] -->
<div id="footer" style="background-color: #8297A8;width: 100%;">
  <div class="container">
    <p class="footer-block" style="margin: 20px 0;color:#fff;">Punto de Ventas <?= $empresa->razonsocial ?>.</p>
  </div>
</div>


<div class="modal fade" id="ticket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="form-body table-responsive" id="printSection"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function consolidar() {
    $.ajax({
      url: '<?= $this->url ?>/consolidar',
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val(),
        'empresa': $('#empresa').val()
      },
      dataType: 'json',
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        console.log(data.dataQuery);
        $("#div_grafico").html(data.dataHtml);
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
      }
    });
  };

  function vendido() {
    $.ajax({
      url: '<?= $this->url ?>/vendido',
      data: {
        'finicio': $('#finicio').val(),
        'factual': $('#factual').val(),
        'empresa': $('#empresa').val()
      },
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
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
      }
    });
  };
  
  function reportevendedores() {
    $.ajax({
      url: '<?= $this->url ?>/vendido2',
      data: {
        'finicio2': $('#finicio2').val(),
        'factual2': $('#factual2').val(),
        'perfiles': $('#perfiles').val()
      },
      type: 'post',
      beforeSend: function() {
        $("#div_grafico2").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico2").html(data);
        $('#example2').dataTable({
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

  function valorizado() {
    $.ajax({
      url: '<?= $this->url ?>/valorizado',
      data: {
        'empresa': $('#empresa').val()
      },
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
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
      }
    });
  };


  function consolidarexcel() {
    $.ajax({
      url: '<?= $this->url ?>/consolidarexcel',
      type: 'POST',
      success: function() {
        window.open('<?= $this->url ?>/consolidarexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
      },
    });
  };

  function vendidoexcel() {
    $.ajax({
      url: '<?= $this->url ?>/vendidoexcel',
      type: 'POST',
      success: function() {
        window.open('<?= $this->url ?>/vendidoexcel/' + $('#finicio').val() + '/' + $('#factual').val() + '/' + $('#empresa').val());
      },
    });
  };

  function valorizadoexcel() {
    window.open('<?= $this->url ?>/valorizadoexcel/' + $('#empresa').val());
  };

  function inventarioexcel() {
    $.ajax({
      url: '<?= $this->url ?>/inventarioexcel',
      type: 'POST',
      success: function() {
        window.open('<?= $this->url ?>/inventarioexcel/' + $('#empresa').val());
      },
    });
  };

  function inventarioproducto() {

    $.ajax({
      url: "<?= $this->url ?>/ajax_inventarioproducto_pdf",
      data: {
        'empresa': $("#empresa").val()
      },
      type: "POST",
      success: function(data) {
        $('#printSection').html(data);
        $('#ticket').modal('show');
        $('.modal-title').text('PRODUCTOS');
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

  /******* Range date picker *******/
  $(function() {
    $('input[name="daterange"]').daterangepicker();
    $('input[name="daterangeC"]').daterangepicker();
    $('input[name="daterangeP"]').daterangepicker();
    $('input[name="daterangeR"]').daterangepicker();
    $('input[name="daterangePC"]').daterangepicker();
    var a = new Date().getFullYear();
    var m = new Date().getMonth() + 1;
    var d = new Date().getDate();
    if (d < 10) {
      d = '0' + d;
    }
    if (m < 10) {
      m = '0' + m;
    }
    $('#ProductRange').val(m + '/01/' + a + ' - ' + m + '/' + d + '/' + a);
    $('#CustomerRange').val(m + '/01/' + a + ' - ' + m + '/' + d + '/' + a);
    $('#CamareroRange').val(m + '/01/' + a + ' - ' + m + '/' + d + '/' + a);
    $('#RegisterRange').val(m + '/01/' + a + ' - ' + m + '/' + d + '/' + a);
    $('#categoriaRange').val(m + '/01/' + a + ' - ' + m + '/' + d + '/' + a);
  });
  /************************ Chart Data *************************/
  var randomScalingFactor = function() {
    return Math.round(Math.random() * 100)
  };
  var lineChartData = {
    labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Setiembre", "Octubre", "Noviembre", "Diciembre"],
    datasets: [{
        label: "Gastos",
        backgroundColor: "rgba(255,99,132,0.2)",
        borderColor: "#FE9375",
        pointBackgroundColor: "#FE9375",
        pointBorderColor: "#fff",
        pointHoverBackgroundColor: "#fff",
        pointHoverBorderColor: "#FE9375",
        data: [<?= $monthlyExp->january; ?>, <?= $monthlyExp->feburary; ?>, <?= $monthlyExp->march; ?>, <?= $monthlyExp->april; ?>, <?= $monthlyExp->may; ?>, <?= $monthlyExp->june; ?>, <?= $monthlyExp->july; ?>, <?= $monthlyExp->august; ?>, <?= $monthlyExp->september; ?>, <?= $monthlyExp->october; ?>, <?= $monthlyExp->november; ?>, <?= $monthlyExp->december; ?>]
      },
      {
        label: "Ingresos",
        backgroundColor: "#2AC4C0",
        borderColor: "#26a5a2",
        pointBackgroundColor: "#2AC4C0",
        pointBorderColor: "#fff",
        pointHoverBackgroundColor: "#fff",
        pointHoverBorderColor: "#fff",
        data: [<?= $monthly->january; ?>, <?= $monthly->feburary; ?>, <?= $monthly->march; ?>, <?= $monthly->april; ?>, <?= $monthly->may; ?>, <?= $monthly->june; ?>, <?= $monthly->july; ?>, <?= $monthly->august; ?>, <?= $monthly->september; ?>, <?= $monthly->october; ?>, <?= $monthly->november; ?>, <?= $monthly->december; ?>]
      }
    ]
  }
  window.onload = function() {
    // Chart.defaults.global.gridLines.display = false;

    var ctx = document.getElementById("canvas").getContext("2d");
    window.myLine = new Chart(ctx, {
      type: 'line',
      data: lineChartData,
      options: {
        scales: {
          xAxes: [{
            gridLines: {
              display: false
            }
          }],
          yAxes: [{
            gridLines: {
              display: true
            }
          }]
        },
        scaleFontSize: 9,
        tooltipFillColor: "rgba(0, 0, 0, 0.71)",
        tooltipFontSize: 10,
        responsive: true
      }
    });

    /********************* pie **********************/
    <?php if (count($Top5product) >= 5) { ?>


      var pieData = {
        labels: [
          "<?= $producto0->nombre ?>",
          "<?= $producto1->nombre ?>",
          "<?= $producto2->nombre ?>",
          "<?= $producto3->nombre ?>",
          "<?= $producto4->nombre ?>"
        ],
        datasets: [{
          data: [<?= $Top5product[0]->totalcantidad . ', ' . $Top5product[1]->totalcantidad . ', ' . $Top5product[2]->totalcantidad . ', ' . $Top5product[3]->totalcantidad . ', ' . $Top5product[4]->totalcantidad ?>],
          backgroundColor: [
            "#F3565D",
            "#FC9D9B",
            "#FACDAE",
            "#9FC2C4",
            "#8297A8"
          ],
          hoverBackgroundColor: [
            "#3e5367",
            "#95a5a6",
            "#f5fbfc",
            "#459eda",
            "#2dc6a8"
          ],
          hoverBorderWidth: [5, 5, 5, 5, 5]
        }]
      };

      Chart.defaults.global.legend.display = false;

      var ctx2 = document.getElementById("chart-area2").getContext("2d");
      window.myPie = new Chart(ctx2, {
        type: 'doughnut',
        data: pieData
      });
      $('.count').each(function(index) {
        var size = $(this).text().split(".")[1] ? $(this).text().split(".")[1].length : 0;
        $(this).prop('count', 0).animate({
          Counter: $(this).text()
        }, {
          duration: 2000,
          easing: 'swing',
          step: function(now) {
            $(this).text(parseFloat(now).toFixed(size));
          }
        });
      });

    <?php } ?>
  }

  function margenproducto() {
    $.ajax({
      url: '<?= $this->url ?>/ajax_margeproducto',
      data: {
        'empresa': $('#empresa').val()
      },
      type: 'post',
      beforeSend: function() {
        $("#div_grafico").html('<br><h3>Cargando datos...</h3>');
      },
      success: function(data) {
        $("#div_grafico").html(data);
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
      }
    });
  };

  function margenproductoexcel() {
    window.open('<?= $this->url ?>/margenproductoexcel/' + $('#empresa').val());
  }
</script>