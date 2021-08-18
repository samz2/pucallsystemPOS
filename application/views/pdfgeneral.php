<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>CAJA GENERAL</title>
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
    <?php $empresa = $this->Controlador_model->get('1','empresa'); ?>
    <?php $perfil = $this->Controlador_model->get($this->session->userdata('perfil'), 'perfil'); ?>
    <table border="0" id="cab">
      <tr>
        <td style="width: 35%"><?= $empresa->razonsocial ?></td>
        <th>CAJA</th>
        <td align="right"><?= date('d/m/Y', time()) ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center"><?= $perfil->nombre ?> ADMINISTRACION GENERAL</td>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center"><?= date('d/m/Y', strtotime($finicio)).' - '.date('d/m/Y', strtotime($factual)) ?></td>
        <td>&nbsp;</td>
      </tr>
    </table>
    <p>&nbsp;</p>
    <table border="0">
      <thead>
        <tr>
          <th>ORDEN</th>
          <th>CODIGO</th>
          <th>CONCEPTO</th>
          <th>INGRESO</th>
          <th>EGRESO</th>
          <th>MONTO</th>
        </tr>
      </thead>
      <tbody>
        <?php $total = 0; $ingreso = 0; $egreso = 0; $i = 0; ?>
        <?php foreach ($ingresos as $value) { $i++; ?>
          <?php $total += $value->monto; ?>
          <?php $ingreso += $value->monto; ?>
          <?php $concepto = $this->Controlador_model->get($value->concepto, 'concepto'); ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= $concepto->codigo ?></td>
            <td><?= $concepto->concepto ?></td>
            <td align="right"><?= number_format($value->monto, 2) ?></td>
            <td align="right"><?= number_format(0, 2) ?></td>
            <td align="right"><?= number_format($value->monto, 2) ?></td>
          </tr>
        <?php } ?>
        <?php foreach ($egresos as $value) { $i++; ?>
          <?php $total -= $value->monto; ?>
          <?php $egreso += $value->monto; ?>
          <?php $concepto = $this->Controlador_model->get($value->concepto, 'concepto'); ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= $concepto->codigo ?></td>
            <td><?= $concepto->concepto ?></td>
            <td align="right"><?= number_format(0, 2) ?></td>
            <td align="right"><?= number_format($value->monto, 2) ?></td>
            <td align="right"><?= number_format(0 - $value->monto, 2) ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td colspan="6">&nbsp;</td>
        </tr>
        <tr>
          <td align="right" colspan="3"><b>TOTAL</b></td>
          <td align="right"><?= number_format($ingreso, 2) ?></td>
          <td align="right"><?= number_format($egreso, 2) ?></td>
          <td align="right"><?= number_format($total, 2) ?></td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
