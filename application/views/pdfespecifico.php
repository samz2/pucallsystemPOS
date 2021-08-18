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
    <?php $empresa = $this->Controlador_model->get($this->empresa, 'empresa'); ?>
    <?php $perfil = $this->Controlador_model->get($this->perfil, 'perfil'); ?>
    <table border="0" id="cab">
      <tr>
        <td style="width: 35%"><?= $empresa->razonsocial ?></td>
        <th>CAJA</th>
        <td align="right"><?= date('d/m/Y') ?></td>
      </tr>
      <tr>
        <td>&nbsp;</td>
        <td align="center">CAJA <?= $perfil->nombre ?> ADMINISTRACION GENERAL</td>
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
          <th>NRO</th>
          <th>DESCRIPCION</th>
          <th>COLABORADOR</th>
          <th>FECHA</th>
          <th>S.I.</th>
          <th>CONTADO</th>
					<th>CREDITO</th>
          <th>EFECTIVO</th>
					<th>TARJETA</th>
					<th>GASTO</th>
					<th>S.F.</th>
        </tr>
      </thead>
      <?php $saldo = 0; $contado = 0; $credito = 0; $efectivo = 0; $tarjeta = 0; $gasto = 0; $total = 0; $i=0; ?>
      <tbody>
        <?php foreach ($datas as $data) { $i++; ?>
				<?php $saldo += $data->saldoinicial; ?>
				<?php $contado += $data->contado; ?>
				<?php $credito += $data->credito; ?>
        <?php $efectivo += $data->efectivo; ?>
				<?php $tarjeta += $data->tarjeta; ?>
				<?php $gasto += $data->gasto; ?>
				<?php $total += $data->saldoinicial + $data->contado - $data->gasto; ?>
				<?php $usuario = $this->Controlador_model->get($data->usuario, 'usuario'); ?>
          <tr>
            <td><?= $i ?></td>
            <td><?= $data->descripcion ?></td>
						<td><?= $usuario->nombre ?></td>
						<td><?= date('d/m/Y', strtotime($data->created)) ?></td>
						<td align="right"><?= $data->saldoinicial ?></td>
						<td align="right"><?= $data->contado ?></td>
						<td align="right"><?= $data->credito ?></td>
            <td align="right"><?= $data->efectivo ?></td>
						<td align="right"><?= $data->tarjeta ?></td>
						<td align="right"><?= $data->gasto ?></td>
						<td align="right"><?= number_format($data->saldoinicial + $data->contado - $data->gasto, 2) ?></td>
          </tr>
        <?php } ?>
        <tr>
          <td colspan="8">&nbsp;</td>
        </tr>
				<tr>
					<td colspan="4"></td>
					<td align="center"><b>Total:</b></td>
					<td align="right"><?= number_format($contado, 2) ?></td>
					<td align="right"><?= number_format($credito, 2) ?></td>
					<td align="right"><?= number_format($efectivo, 2) ?></td>
          <td align="right"><?= number_format($tarjeta, 2) ?></td>
					<td align="right"><?= number_format($gasto, 2) ?></td>
					<td align="right"><?= number_format($total, 2) ?></td>
				</tr>
      </tbody>
    </table>
  </body>
</html>
