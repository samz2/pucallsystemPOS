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
  <div>
    <img src="<?= base_url() . 'files/Setting/' . $empresa->ruc ?>.png" width="100%">
  </div>
  <table>
    <tbody>
      <tr>
        <td align="center" class="negrita"><?= $empresa->nombre ?></td>
      </tr>
      <tr>
        <td align="center"><?= $empresa->razonsocial ?></td>
      </tr>
      <tr>
        <td align="center" class="negrita"><?= substr($empresa->direccion, 0, 30) ?></td>
      </tr>
      <!-- Uno es direccion filcal y otro es la direccion donde se esta emitienedo el comprobante -->
      <tr>
        <td align="center" class="negrita"><?= $empresa->distrito . ' - ' . $empresa->provincia . ' - ' . $empresa->departamento ?></td>
      </tr>
      <tr>
        <td align="center" class="negrita">R.U.C: <?= $empresa->ruc ?></td>
      </tr>
      <tr>
        <td align="center">TELF: <?= $empresa->telefono ?></td>
      </tr>
      <tr>
        <td align="center" class="negrita"><?= $venta->tipoventa == 'OTROS' ? 'NOTA DE VENTA' : $venta->tipoventa . ' DE VENTA ELECTRONICA' ?></td>
      </tr>
      <tr>
        <td align="center" class="negrita"><?= $venta->serie . '-' . $venta->numero ?></td>
      </tr>
    </tbody>
  </table>
  <hr>
  <table>
    <tr>
      <td><span class="negrita"><?= $cliente->tipodocumento ?>:</span> <?= $cliente->documento ?></td>
    </tr>
    <tr>
      <td><span class="negrita">SEÑOR(RES):</span><?= $cliente->nombre . ' ' . $cliente->apellido ?></td>
    </tr>
    <tr>
      <td><span class="negrita">DIRECCION:</span><?= $cliente->direccion ?></td>
    </tr>
    <?php if ($venta->tipoventa <> 'OTROS') { ?>
      <tr>
        <td><span class="negrita">FECHA DE EMISION:</span> <?= $venta->created . ' ' . $venta->hora ?></td>
      </tr>
      <tr>
        <td><span class="negrita">FECHA DE VENC.:</span> <?= $venta->created ?></td>
      </tr>
      <tr>
        <td><span class="negrita">MONEDA:</span> SOLES</td>
      </tr>
    <?php } else { ?>
      <tr>
        <td><span class="negrita">FECHA:</span> <?= $venta->created ?></td>
      </tr>
    <?php } ?>
  </table>
  <hr>
  <table>
    <thead>
      <tr>
        <th>DESCRIPCION</th>
        <th>P/U</th>
        <th style="text-align:center">[CANT.]</th>
        <th align="right">IMPORTE</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($venta->consumo == '1') {  ?>
        <tr>
          <td>POR CONSUMO</td>
          <td style="text-align:center"><?= number_format($venta->montototal, 2) ?></td>
          <td style="text-align:center">1</td>
          <td align="right"><?= number_format($venta->montototal, 2) ?></td>
        </tr>
      <?php } else { ?>
        <?php foreach ($ventadetalle as $value) { ?>
          <?php if ($value->tipo == '0') { ?>
            <?php
            $producto = $this->Controlador_model->get($value->producto, 'producto');
            $suma = $this->Controlador_model->sumacomanda($venta->id, $value->producto);
            ?>
            <tr>
              <?php $datavariante = $this->Controlador_model->get($value->variante, "productovariante");
              $totalItems = $value->variante ? $suma->cantidad *  $datavariante->cantidad : $suma->cantidad;
              ?>
              <td><?= $value->nombre . " " . $producto->codigo ?>[<?= $totalItems ?>]</td>
              <td style="text-align:center"><?= $value->precio ?></td>
              <td style="text-align:center">[ <?= $suma->cantidad ?> ]</td>
              <td align="right"><?= number_format($suma->cantidad * $value->precio, 2) ?></td>
            </tr>
          <?php } else { ?>
            <tr>
              <td><?= $value->nombre ?></td>
              <td><?= $value->subtotal ?></td>
              <td style="text-align:center"><?= $value->cantidad ?></td>
              <td align="right"><?= number_format($value->subtotal, 2) ?></td>
            </tr>
          <?php } ?>
        <?php } ?>
      <?php } ?>
    </tbody>


  </table>
  <hr>
  <table>
    <tr>
      <th align="right" colspan="3">DESCUENTO</th>
      <th align="right"><?= $venta->descuento ?></th>
    </tr>
    <?php if ($venta->tipoventa <> 'OTROS') { ?>
      <tr>
        <th align="right" colspan="3">IGV S/</th>
        <th align="right">0.00</th>
      </tr>
      <tr>
        <th align="right" colspan="3">OP. EXONERADA S/</th>
        <th align="right"><?= $venta->deudatotal ?></th>
      </tr>
    <?php } ?>
    <tr>
      <th align="right" colspan="3">TOTAL S/</th>
      <th align="right"><?= $venta->deudatotal ?></th>
    </tr>
    <tr>
      <th align="right" colspan="3">RECIBIO S/</th>
      <th align="right"><?= $venta->pago ?></th>
    </tr>
    <tr>
      <th align="right" colspan="3">VUELTO S/</th>
      <th align="right"><?= number_format($venta->vuelto, 2) ?></th>
    </tr>
  </table>
  <hr>
  <table>
    <tr>
      <td><span class="negrita">IMPORTE EN LETRA:</span> <?= num_to_letras($venta->montototal) ?></td>
    </tr>
    <tr>
      <td><span class="negrita">VENDEDOR:</span> <?= $usuario->nombre ?></td>
    </tr>
    <?php if ($venta->tipoventa <> 'OTROS') { ?>
      <tr>
        <td><span class="negrita">HASH:</span> <?= $codigohash ?></td>
      </tr>
    <?php } ?>
  </table>
  <hr>
  <table>
    <?php if ($venta->tipoventa == 'OTROS') { ?>
       <tr>
          <td align="center">PUEDE CAMBIAR POR BOLETA/FACTURA</td>
        </tr>
        <tr>
          <td align="center">SOLO TIENE 24 HORAS PARA REALIZAR</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td align="center">GRACIAS POR SU COMPRA</td>
        </tr> 
    <?php } else { ?>
      <tr>
        <td align="center">NO SE ACEPTAN DEVOLUCIONES Y/O</td>
      </tr>
      <tr>
        <td align="center">CAMBIOS DESPUES DE LOS 2 DIAS</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center"><img src="<?= $qrcode ?>" alt="" width="80"></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">REPRESENTACION IMPRESA DE</td>
      </tr>
      <tr>
        <td align="center">COMPROBANTE ELECTRONICO</td>
      </tr>
      <tr>
        <td align="center">AUTORIZADO MEDIANTE LA RESOLUCION</td>
      </tr>
      <tr>
        <td align="center">DE INTENDENCIA N°. 034-005-0005315</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">BIENES TRANSFERIDOS EN LA AMAZONIA</td>
      </tr>
      <tr>
        <td align="center">SERVICIOS PRESTADOS EN LA AMAZONIA</td>
      </tr>
    <?php } ?>
  </table>
</body>

</html>