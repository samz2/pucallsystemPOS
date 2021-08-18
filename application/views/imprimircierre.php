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

    .negrita {
      font-weight: bold;
    }
  </style>
</head>

<body>
  <table>
    <?php if ($empresa->tipo == 0) { ?>
      <tr>
        <td align="center" class="negrita"><?= $empresa->nombre ?></td>
      </tr>
      <tr>
        <td align="center"><?= $empresa->razonsocial ?></td>
      </tr>
    <?php } else { ?>
      <tr>
        <td align="center" class="negrita"><?= $empresa->razonsocial ?></td>
      </tr>
    <?php } ?>
    <tr>
      <td align="center" class="negrita"><?= substr($empresa->direccion, 0, 30) ?></td>
    </tr>
    <tr>
      <td align="center" class="negrita"><?= $empresa->distrito . ' - ' . $empresa->provincia . ' - ' . $empresa->departamento ?></td>
    </tr>
    <tr>
      <td align="center" class="negrita">RUC <?= $empresa->ruc ?></td>
    </tr>
    <tr>
      <td align="center">TELF. <?= $empresa->telefono ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <hr>
  <?php
  $fechahoraApertura =  explode(" ", $caja->apertura, 2);
  $fechahoraCierre = explode(" ", $caja->cierre, 2);
  ?>
  <table>
    <tr>
      <td align="center" class="negrita">CIERRRE CAJA DIARIO</td>
    </tr>
    <tr>
      <td align="center">ENCARGADO: <?= $usuario->nombre . ' ' . $usuario->apellido ?></td>
    </tr>
    <tr>
      <td align="center">FECHA/HORA DE APERTURA: <?= $fechahoraApertura[0] . " / " . $fechahoraApertura[1] ?></td>
    </tr>
    <tr>
      <td align="center">FECHA/HORA DE CIERRE: <?= $fechahoraCierre[0] . " / " . $fechahoraCierre[1] ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
  </table>
  <hr>

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
      <tr>
        <td>CIERRE DE CAJA:</td>
        <td align="right"><?= number_format($montototal, 2) ?></td>
      </tr>
    </tfoot>
  </table>


  <hr>
  <table>
    <thead>
      <tr>
        <th colspan="2" align="center">RESUMEN DE CAJA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <table>
          <tr>
            <td>VENTAS EN EFECTIVO: </td>
            <td style="text-align: right;"><?= $caja->efectivo ?></td>
          </tr>
          <tr>
            <td>VENTAS CON TARJETA</td>
            <td style="text-align: right;"><?= $caja->tarjeta ?></td>
          </tr>
          <tr style="border-top: 1px solid #000; margin-top:10px; border-top-style:dashed">
            <td colspan="2" style="padding-top: 5px;"></td>
          </tr>
          <tr>
            <td>TOTAL DE VENTAS A CONTADO</td>
            <td style="text-align: right;"><?= $caja->contado ?></td>
          </tr>
        </table>
      </tr>
    </tbody>
  </table>
  <hr>

  <table>
    <tr>
      <td>TOTAL DE VENTAS A CREDITO</td>
      <td style="text-align: right;"><?= $caja->credito ?></td>
    </tr>
  </table>
  <hr>

  <table>
    <thead>
      <tr>
        <th colspan="2" align="center">CUADRE DE CAJA</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <table>
          <?php
          $montoefectivo = ($caja->saldoinicial + $caja->efectivo);
          $montototalefectivo = ($caja->saldoinicial + $caja->efectivo) - $caja->gasto;
          ?>
          <tr>
            <td colspan="2">
              <table>
                <tr>
                  <td>VENTAS EN EFECTIVO: </td>
                  <td style="text-align: right;"><?= number_format($caja->efectivo, 2) ?></td>
                </tr>
                <tr>
                  <td>SALDO INICIAL: </td>
                  <td style="text-align: right;"><?= number_format($caja->saldoinicial, 2) ?></td>
                </tr>

                <tr>
                  <td colspan="2" style="border-top: 1px solid #000; border-top-style:dashed; padding-bottom:3px"></td>
                </tr>
                <tr>
                  <td>MONTO EN CAJA: </td>
                  <td style="text-align: right;"><?= number_format($montoefectivo, 2) ?></td>
                </tr>
                <tr>
                  <td>GASTO EN CAJA: </td>
                  <td style="text-align: right;"><?= number_format($caja->gasto, 2) ?></td>
                </tr>
                <tr>
                  <td colspan="2" style="border-top: 1px solid #000; border-top-style:dashed; padding-bottom:3px"></td>
                </tr>
                <tr>
                  <td>MONTO TOTAL EN CAJA: </td>
                  <td style="text-align: right;"><?= number_format($montototalefectivo, 2) ?></td>
                </tr>
              </table>
            </td>

          </tr>

          <tr>
            <?php
            if ($montototalefectivo >= $montototal) {
              $result = $montototalefectivo  - $montototal;
              $estadocaja = $result == 0 ? "NINGUNA" : "FALTO EN CAJA";
            } else {
              $result = $montototal - $montototalefectivo;
              $estadocaja = "SOBRO EN CAJA";
            }
            ?>
            <td><b style="font-size: 13px;">OBSERVACION</b>: <?= $estadocaja ?> </td>
            <td style="text-align: right;"><?= number_format($result, 2) ?></td>
          </tr>
        </table>
      </tr>
    </tbody>
  </table>

  <hr>

  <hr>
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
</body>

</html>