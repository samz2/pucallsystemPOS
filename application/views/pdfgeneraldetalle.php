<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>GENERAL DETALLADO</title>
  </head>
  <style type="text/css">
  body {
    font-style: normal;
    font-weight: normal;
    font-family: Helvetica;
    font-size: 12px;
  }
  table {
    width: 100%;
    border-collapse: collapse;
  }
  thead {
    background-color: #BFBFBF;
  }
  #cab {
    font-style: oblique;
  }
  </style>
  <body>
    <?php $empresa = $this->Controlador_model->get($this->empresa, 'empresa'); ?>
    <?php $perfil = $this->Controlador_model->get($this->perfil, 'perfil'); ?>
    <table border="0" id="cab">
      <tr>
        <td style="width: 35%"><?= $empresa->razonsocial ?></td>
        <th>CAJA DETALLE</th>
        <td align="right"><?= date('d/m/Y') ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center"><?= $perfil->nombre ?> ADMINISTRACION GENERAL</td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table border="0">
      <thead>
        <tr>
          <th>#</th>
          <th>Tipo</th>
          <th>Documento</th>
          <th>Descripcion</th>
          <th>Fecha</th>
          <th>Monto</th>
        </tr>
      </thead>
      <tbody>
        <?php $i=0; foreach ($ingresos as $value) { $i++; ?>
          <?php $venta = $this->Controlador_model->get($value->venta, 'venta'); ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= isset($value->venta) ? $venta->tipoventa : '' ?></td>
            <td><?= isset($value->venta) ? $venta->serie.'-'.$venta->numero : '' ?></td>
            <td><?= $value->observacion ?></td>
            <td><?= $value->created ?></td>
            <td align="right"><?= $value->monto ?></td>
          </tr>
        <?php } ?>
        <?php foreach ($egresos as $value) { $i++; ?>
          <?php $compra = $this->Controlador_model->get($value->compra, 'compra'); ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= $compra ? $compra->movimiento : '' ?></td>
            <td><?= $compra ? $compra->codigo : '' ?></td>
            <td><?= $value->observacion ?></td>
            <td><?= $value->created ?></td>
            <td align="right"><?= $value->monto ?></td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </body>
</html>
