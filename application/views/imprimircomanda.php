<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>COMANDA</title>
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
    <?php if($numerobebida > 0) { ?>
      <table border="0">
      <tr>
          <td align="center" class="negrita"><?= ($zona)? $zona->nombre:"MESA TEMPORAL" ?></td>
        </tr>
        <tr>
          <td align="center" class="negrita">CAJA --> BEBIDA</td>
        </tr>
        <tr>
          <td align="center" class="negrita"><?= date("Y-m-d H:i:s") ?></td>
        </tr>
      </table>
      <hr>
      <table border="0">
        <tr>
          <td><span class="negrita">CAMARERO:</span> <?= $usuario->nombre.' '.$usuario->apellido ?></td>
        </tr>
        <tr>
          <td><span class="negrita">MESA:</span> <?= ($mesa)? $mesa->nombre:"MESA TEMPORAL" ?></td>
        </tr>
      </table>
      <hr>
      <table border="0">
        <?php foreach ($ventadetalle as $value) { ?>
          <?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
          <?php if($producto->categoria == 5) { ?>
            <tr>
              <td>[ <?= $value->cantidad ?> ] <?= $value->nombre.' '.$value->opcion.' '.$value->precio ?></td>
            </tr>
          <?php } ?>
        <?php } ?>
      </table>
      <hr>
    <?php } ?>
    <?php if($numerocomida > 0) { ?>
      <table border="0">
      <tr>
          <td align="center" class="negrita"><?= ($zona)? $zona->nombre:"MESA TEMPORAL" ?></td>
        </tr>
        <tr>
          <td align="center" class="negrita">CAJA --> COMIDA</td>
        </tr>
        <tr>
          <td align="center" class="negrita"><?= date("Y-m-d H:i:s") ?></td>
        </tr>
      </table>
      <hr>
      <table border="0">
        <tr>
          <td><span class="negrita">CAMARERO:</span> <?= $usuario->nombre.' '.$usuario->apellido ?></td>
        </tr>
        <tr>
          <td><span class="negrita">MESA:</span> <?= ($mesa)? $mesa->nombre:"MESA TEMPORAL" ?></td>
        </tr>
      </table>
      <hr>
      <table border="0">
        <?php foreach ($ventadetalle as $value) { ?>
          <?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
          <?php if($producto->categoria <> 5) { ?>
            <tr>
              <td>[ <?= $value->cantidad ?> ] <?= $value->nombre.' '.$value->opcion.' '.$value->precio ?></td>
            </tr>
          <?php } ?>
        <?php } ?>
      </table>
      <hr>
    <?php } ?>
  </body>
</html>
