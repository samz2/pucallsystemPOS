<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="utf-8">
  <title>COMPROBANTE</title>
  <!-- Bootstrap Core CSS -->
  <script type="text/javascript">
    window.print();
  </script>
  <style type="text/css">
    html {
      margin: 0;
    }

    body {
      font-family: Helvetica, Arial, Verdana, sans-serif;
      /*Trebuchet MS,*/
      margin: 5mm 3mm 2mm 3mm;
      font-style: normal;
      font-size: 8pt !important;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    td {
      padding: 0;
    }

    .tabla-contado tr td {
      border: 0.5px solid #000;
      border-collapse: collapse !important;
    }

    .negrita {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div style="font-size:20px; text-align:center"><b><?= $caja->descripcion ?></b></div>
  <hr>
  <?php
  $fechahoraApertura =  explode(" ", $caja->apertura, 2);
  $fechahoraCierre = explode(" ", $caja->cierre, 2);
  ?>
  <table>
    <tr>
      <td align="center"><b>ENCARGADO:</b> <?= $usuario->nombre . ' ' . $usuario->apellido ?></td>
    </tr>
    <tr>
      <td align="center"><b>APERTURADO:</b> <?= $fechahoraApertura[0] . " / " . $fechahoraApertura[1] ?></td>
    </tr>
    <tr>
      <td align="center"><b>CERRADO:</b> <?= $fechahoraCierre[0] . " / " . $fechahoraCierre[1] ?></td>
    </tr>
  </table>

  <table>
    <?php if ($monedero->status == '0') { ?>
      <thead>
        <tr>
          <th>TIPO DE MONEDA (SOLES)</th>
          <th>CANTIDAD DE MONEDA</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>0.10</td>
          <td align="right"><?= $monedero->diezcentimos ?></td>
        </tr>
        <tr>
          <td>0.20</td>
          <td align="right"><?= $monedero->veintecentimos ?></td>
        </tr>
        <tr>
          <td>0.50</td>
          <td align="right"><?= $monedero->cincuentacentimos ?></td>
        </tr>
        <tr>
          <td>1.00</td>
          <td align="right"><?= $monedero->unsol ?></td>
        </tr>
        <tr>
          <td>2.00</td>
          <td align="right"><?= $monedero->dossoles ?></td>
        </tr>
        <tr>
          <td>5.00</td>
          <td align="right"><?= $monedero->cincosoles ?></td>
        </tr>
        <tr>
          <td>10.00</td>
          <td align="right"><?= $monedero->diezsoles ?></td>
        </tr>
        <tr>
          <td>20.00</td>
          <td align="right"><?= $monedero->veintesoles ?></td>
        </tr>
        <tr>
          <td>50.00</td>
          <td align="right"><?= $monedero->cincuentasoles ?></td>
        </tr>
        <tr>
          <td>100.00</td>
          <td align="right"><?= $monedero->ciensoles ?></td>
        </tr>
        <tr>
          <td>200.00</td>
          <td align="right"><?= $monedero->doscientossoles ?></td>
        </tr>
      </tbody>
    <?php } ?>
    <tfoot>
      <?php if ($monedero->status == '0') { ?>
        <tr>
          <td colspan="2" align="center">----------------------------------------------------------------------</td>
        </tr>
      <?php } ?>
    </tfoot>
  </table>


  <hr>
  <table style="margin-bottom:5px">
    <thead>
      <tr>
        <th><span style="border-bottom:1px solid #000">RESUMEN DE CAJA</span></th>
      </tr>
    </thead>
  </table>
  <table class="tabla-contado" style="text-align:center">
    <thead>
      <tr>
        <th colspan="3" style="border: 0.5px solid #000">VENTAS A CONTADO</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>TIPO DE VENTA</td>
        <td>GENERADOS</td>
        <td>ACUMULADO</td>
      </tr>
      <tr>
        <td>EFECTIVO</td>
        <td><?= $caja->efectivogenerados ?></td>
        <td>S/. <?= number_format($caja->efectivocontado, 2) ?></td>
      </tr>
      <tr>
        <td>TARJETA</td>
        <td><?= $caja->tarjetagenerados ?></td>
        <td>S/. <?= number_format($caja->tarjetacontado, 2) ?></td>
      </tr>
    </tbody>
    <tfoot>
      <tr>
        <td>TOTAL</td>
        <td><?= $caja->efectivogenerados + $caja->tarjetagenerados ?></td>
        <td>S/. <?= number_format($caja->efectivocontado +  $caja->tarjetacontado, 2) ?></td>
      </tr>
    </tfoot>
  </table>
  <br>
  <table class="tabla-contado" style="text-align:center">
    <thead>
      <tr>
        <th colspan="2" style="border: 0.5px solid #000">VENTAS A CREDITO</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>GENERADOS</td>
        <td>ACUMULADO</td>
      </tr>
      <tr>
        <td><?= $caja->creditosgenerados ?></td>
        <td>S/. <?= number_format($caja->credito, 2) ?></td>
      </tr>
    </tbody>
  </table>
  <br>
  <table class="tabla-contado" style="text-align:center">
    <thead>
      <tr>
        <th colspan="4" style="border: 0.5px solid #000">VENTAS ANULADAS</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>N°</td>
        <td>RESPONSABLE</td>
        <td>COMBROBANTE</td>
        <td>MOTIVO</td>
      </tr>
      <?php
        $ventasAnuladas = $this->db->where("caja", $caja->id)->where("estado", "3")->get("venta")->result();
      ?>
      <?php foreach($ventasAnuladas as $key => $anuladas){ 
        $dataUsuario = $this->Controlador_model->get($anuladas->usuario_anulado, "usuario");
      ?>
      <tr>
        <td><?= $key + 1 ?></td>
        <td><?= $dataUsuario ? $dataUsuario->nombre." ".$dataUsuario->apellido : "SIN DATOS" ?></td>
        <td><?= $anuladas->serie."-".$anuladas->numero ?></td>
        <td><?= $anuladas->anular_motivo ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <br>
  <table class="tabla-contado" style="text-align:center">
    <thead>
      <tr>
        <th colspan="4" style="border: 0.5px solid #000">ABONOS EN CAJA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>N°</td>
        <td>CONCEPTO</td>
        <td>DETALLE</td>
        <td>ACUMULADO</td>
      </tr>
      <?php
      $dataAbono = $this->db->where("caja", $caja->id)->where("tipo", "OPERACION")->get("ingreso")->result();
      $totalAcumuladoAbono = 0;
      foreach ($dataAbono as $indice => $data) {
        $dataConcepto = $this->Controlador_model->get($data->concepto, "concepto");
        $totalAcumuladoAbono += $data->monto;
      ?>
        <tr>
          <td><?= $indice + 1 ?></td>
          <td><?= $dataConcepto->concepto ?></td>
          <td><?= $data->observacion ?></td>
          <td>S/. <?= number_format($data->monto, 2) ?></td>
        </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">TOTAL</td>
        <td>S/. <?= number_format($totalAcumuladoAbono, 2) ?></td>
      </tr>
    </tfoot>
  </table>
  <br>
  <table class="tabla-contado" style="text-align:center">
    <thead>
      <tr>
        <th colspan="4" style="border: 0.5px solid #000">GASTOS EN CAJA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>N°</td>
        <td>CONCEPTO</td>
        <td>DETALLE</td>
        <td>ACUMULADO</td>
      </tr>
      <?php
      $dataEgreso = $this->db->where("caja", $caja->id)->get("egreso")->result();
      $totalAcumuladoEgreso = 0;
      foreach ($dataEgreso as $indiceEgreso => $valueEgreso) {
        $dataConceptoEgreso = $this->Controlador_model->get($valueEgreso->concepto, "concepto");
        $totalAcumuladoEgreso += $valueEgreso->montototal;
      ?>
        <tr>
          <td><?= $indiceEgreso + 1 ?></td>
          <td><?= $dataConceptoEgreso->concepto ?></td>
          <td><?= $valueEgreso->observacion ?></td>
          <td>S/. <?= number_format($valueEgreso->montototal, 2) ?></td>
        </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">TOTAL</td>
        <td>S/. <?= number_format($totalAcumuladoEgreso, 2) ?></td>
      </tr>
    </tfoot>
  </table>
  <hr>
  <br>
  <table style="margin-bottom:5px">
    <thead>
      <tr>
        <th><span style="border-bottom:1px solid #000">CUADRE DE CAJA</span></th>
      </tr>
    </thead>
  </table>

  <table border="1" style="text-align:center">
    <thead>
      <tr>
        <th>+ SALDO INICIAL</th>
        <td>S/. + <?= $caja->saldoinicial ?></td>
      </tr>
      <tr>
        <th>+ EFECTIVO A CONTADO</th>
        <td>S/. + <?= number_format($caja->efectivocontado, 2) ?></td>
      </tr>
      <tr>
        <th>+ ABONOS EN CAJA</th>
        <td>S/. + <?= number_format($totalAcumuladoAbono, 2) ?></td>
      </tr>
      <tr>
        <th>- GASTOS EN CAJA</th>
        <td>S/. - <?= number_format($totalAcumuladoEgreso, 2) ?></td>
      </tr>
      <tr>
        <th>DINERO EN CAJA</th>
        <?php
        $totalCaja = ($caja->efectivocontado + $totalAcumuladoAbono + $caja->saldoinicial) - $totalAcumuladoEgreso;
        ?>
        <th>S/. <?= number_format($totalCaja, 2) ?></th>
      </tr>
    </thead>
  </table>
  <br>
  <table border="1" style="text-align:center">
    <thead>
      <tr>
        <th>DINERO EN CAJA</th>
        <th>MONTO REGISTRADO AL CERRAR CAJA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <?php
        if ($totalCaja >= $montoCerrarCaja) {
          $resultCuadre = $totalCaja  - $montoCerrarCaja;
          $estadocaja = $resultCuadre == 0 ? "SIN OBSERVACION EN LA CAJA" : "FALTO ENTREGAR";
          $emoji = $resultCuadre == 0 ? "😀" : "😢";
        } else {
          $resultCuadre = $montoCerrarCaja - $totalCaja;
          $estadocaja = "NO SE ENCONTRO REGISTRO DE";
          $emoji = "😢";
        }
        ?>
        <td>S/. <?= number_format($totalCaja, 2) ?></td>
        <td><?= $montoCerrarCaja ?></td>
      </tr>
      <tr>
        <?php $diferencia =  $resultCuadre == 0 ? "" : ": S/. ".number_format($resultCuadre,2); ?>
        <td colspan="2" style="font-size:12px"><b><?=$estadocaja.$diferencia?></b></td>
      </tr>
      <tr>
        <td colspan="2" style="font-size:40px"><?=$emoji?></td>
      </tr>
    </tbody>
  </table>


  <!-- PRODUCTOS VENDIDOS -->
  <!--
    <table>
    <tr>
      <td colspan="2" align="center">PRODUCTOS VENDIDOS</td>
    </tr>
    <?php $totalventas = 0;
    foreach ($posales as $value) { ?>
      <?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
      <?php $sumacantidad = $this->Controlador_model->ventaresumencantidad($caja->id, $value->producto); ?>
      <?php $sumasubtotal = $this->Controlador_model->ventaresumensubtotal($caja->id, $value->producto); ?>
      <?php $totalventas += $sumasubtotal->subtotal; ?>
      <tr>
        <td><?= ucwords(strtolower($producto->nombre)) ?></td>
        <td><?= $sumacantidad->cantidad . " UND" ?></td>
        <td align="right"><?= number_format($value->precio * $sumacantidad->cantidad, 2) ?></td>
      </tr>
    <?php } ?>
  </table>
   -->


</body>

</html>