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
        font-family: Helvetica, Arial, Verdana, sans-serif; /*Trebuchet MS,*/
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
      <tbody>
        <tr>
          <td align="center" class="negrita">PRE-CUENTA</td>
        </tr>
      </tbody>
    </table>
    <hr>
    <table>
      <tr>
        <td><span class="negrita"><?= $cliente->tipodocumento ?>:</span> <?= $cliente->documento ?></td>
      </tr>
      <tr>
        <td><span class="negrita">SEÑOR(RES):</span><?= $cliente->nombre.' '.$cliente->apellido ?></td>
      </tr>
      <tr>
        <td><span class="negrita">DIRECCION:</span><?= $cliente->direccion ? $cliente->direccion : '-' ?></td>
      </tr>
    </table>
    <hr>
    <table>
      <tr>
        <th style="width: 20%;">[CANT.]</th>
        <th style="width: 50%;">DESCRIPCION</th>
        <th>P/U</th>
        <th align="right">TOTAL</th>
      </tr>
      <?php foreach ($ventadetalle as $value) { ?>
        <?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
        <?php $suma = $this->Controlador_model->sumacomanda($venta->id, $value->producto); ?>
        <tr>
          <td colspan="4"><?= $producto->nombre.'  '.$value->opcion ?></td>
        </tr>
        <tr>
          <td colspan="2">[ <?= $suma->cantidad ?> ] <?= $producto->unidad ?></td>
          <td><?= $value->precio ?></td>
          <td align="right"><?= number_format($suma->cantidad * $value->precio, 2) ?></td>
        </tr>
      <?php } ?>
    </table>
    <hr>
    <table>
      <tr>
        <th align="right" colspan="2">TOTAL</th>
        <th align="center">S/</th>
        <th align="right"><?= number_format($venta->montototal, 2) ?></th>
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
      <tr>
        <td><span class="negrita">MESA:</span> <?= ($mesa)? $mesa->nombre:"MESA TEMPORAL" ?></td>
      </tr>
      <tr>
        <td><span class="negrita">ZONA:</span> <?= ($zona)? $zona->nombre:"MESA TEMPORAL" ?></td>
      </tr>
    </table>
    <hr>
    <table>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td align="center">GRACIAS POR SU COMPRA</td>
      </tr>
    </table>
  </body>
</html>
