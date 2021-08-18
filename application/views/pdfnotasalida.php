<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>

<head>
  <style>
    .cab {
      width: 100%;
      text-align: center;
      border-spacing: 0;
      border-collapse: collapse;
    }

    .det {
      width: 100%;
      border-style: solid;
      line-height: 130%;
    }

    .art {
      width: 100%;
      border-style: solid;
    }

    .desart {
      text-align: center;
      background-color: #BFBFBF;
      border-right-width: 2px;
      border-bottom-width: 2px;
      border-right-style: solid;
      border-bottom-style: solid;
      border-collapse: collapse;
    }

    .pie {
      width: 100%;
      border-collapse: collapse;
      border-spacing: 5px;
    }

    th {
      width: 50%;
    }

    span {
      font-style: normal;
      font-weight: normal;
      font-family: Helvetica;
    }

    .cubon {
      background-color: #bfbfbf;
      border-style: solid;
    }

    .cubo {
      border-style: solid;
    }
  </style>
  <meta charset="utf-8" />
  <title>NOTA SALIDA</title>
  <link rel="shortcut icon" href="<?= base_url() . RECURSOS ?>images/favicon_1.ico">
</head>

<body>
  <table class="cab">
    <?php $sucursal = $this->Controlador_model->get($data->empresa, 'empresa'); ?>
    <tr>
      <th style="width:10%" rowspan="5"><img style="width:1.60in;height:0.70in" src="<?= base_url() ?>/files/Setting/<?= $sucursal->ruc ?>.png" /></th>
      <th style="width:90%"><span style="font-size:11pt;"><?= $sucursal->serie . ' ' . $sucursal->razonsocial ?></span></th>
      <th class="cubon"><span style="font-size:9pt;">NOTA DE SALIDA</span></th>
    </tr>
    <tr>
      <th><span style="font-size:10pt;">RUC: <?= $sucursal->ruc ?></span></th>
      <th class="cubo"><span style="font-size:9pt;"><?= $data->codigo ?></span></th>
    </tr>
    <tr>
      <th><span style="font-size:8pt;"><?= $sucursal->direccion ?></span></th>
      <th class="cubo"><span style="font-size:9pt;">FECHA</span></th>
    </tr>
    <tr>
      <th><span style="font-size:8pt;"><?= $sucursal->telefono ?></span></th>
      <th class="cubo"><span style="font-size:9pt;"><?= $data->created ?></span></th>
    </tr>
    <tr>
      <th><span style="font-size:8pt;"><?= $sucursal->departamento . ' ' . $sucursal->provincia . ' ' . $sucursal->distrito ?></span></th>
      <th></th>
    </tr>
  </table>
  <table class="det">
    <?php $usuario = $this->Controlador_model->get($data->usuario, 'usuario'); ?>
    <tr>
      <td><span style="font-weight:bold;font-size:9pt;">Registro</span></td>
      <td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $usuario->nombre . ' ' . $usuario->apellido ?></span></td>
      <td></td>
      <td><span style="font-weight:bold;font-size:9pt;">Tipo Referencia</span></td>
      <td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $data->tiposalida ?></span></td>
    </tr>
    <?php if ($data->tiposalida == 'TRASLADO DE ALMACEN') { ?>
    <?php $datalmacen = $this->Controlador_model->get($data->almacendestino,"almacen"); ?>
      <tr>
        <td>
          <span style="font-weight:bold;font-size:9pt;">Destino</span>
        </td>
        <td colspan="4">
          : <span style="font-size:9pt;"><?= $datalmacen->nombre ?></span>
        </td>
      </tr>
    <?php } ?>
    <tr>
      <td colspan="5">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="5"><span style="font-size:9pt;">Sirvanse atender los materiales que se detallan a continuación.</span></td>
    </tr>
  </table>
  <table class="art">
    <tr>
      <td class="desart"><span style="font-size:9pt;">Item</span></td>
      <td class="desart"><span style="font-size:9pt;">Código</span></td>
      <td class="desart"><span style="font-size:9pt;">Descripción</span></td>
      <td class="desart"><span style="font-size:9pt;">Tipo</span></td>
      <td class="desart"><span style="font-size:9pt;">Cant.</span></td>
    </tr>
    <?php $i = 0;
    foreach ($datas as $value) {
      $i++; ?>
      <?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
      <?php $categoria = $this->Controlador_model->get($producto->categoria, 'productocategoria'); ?>
      <tr>
        <td align="center"><span style="font-size:8pt;"><?= $i ?></span></td>
        <td><span style="font-size:8pt;"><?= $producto->codigo ?></span></td>
        <td><span style="font-size:8pt;"><?= $producto->nombre . ' ' . ($categoria ? $categoria->nombre : '') ?></span></td>
        <td align="center"><span style="font-size:8pt;"><?= $value->medida ?></span></td>
        <td align="center"><span style="font-size:8pt;"><?= $value->cantidad ?></span></td>
      </tr>
    <?php } ?>
  </table>
  <table class="pie" border="1">
    <tr>
      <td colspan="3">&nbsp;</td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="3"><span style="font-size:9pt;">Observaciones : </span><span style="font-size:9pt;"><?= $data->comentario ?></span></td>
      <td colspan="2">&nbsp;</td>
    </tr>
    <?php $usuario = $this->Controlador_model->get($data->usuario, 'usuario'); ?>
    <tr>
      <td rowspan="3" colspan="2" align="center">
        <span style="font-size:8pt;">1________________________</span><br>
        <?php if ($data->empleado) { ?>
          <?php $usuario = $this->Controlador_model->get($data->empleado, 'usuario'); ?>
          <span style="font-size:8pt;">Recibido <?= $usuario->nombre . ' ' . $usuario->apellido ?></span>
        <?php } ?>
      </td>
      <td colspan="2" align="center">
        &nbsp;<br>
        <span style="font-size:8pt;">3_______________________________</span><br>
        <span style="font-size:8pt;">JEFE ALMACEN</span><span style="font-size:8pt;"></span>
      </td>
      <td rowspan="3" style="width: 10%;">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" align="center"><span style="font-size:9pt;"><?= date('d/m/Y', strtotime($data->created)) ?></span></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><span style="font-size:8pt;">DIA/MES/ANO</span></td>
    </tr>
  </table>
</body>

</html>