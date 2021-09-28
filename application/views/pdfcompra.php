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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>COMPRA</title>
	<link rel="shortcut icon" href="<?= base_url().RECURSOS ?>images/favicon_1.ico">
</head>

<body>
	<table class="cab">
		<?php $sucursal = $this->Controlador_model->get($data->empresa, 'empresa'); ?>
		<?php $proveedor = $this->Controlador_model->get($data->proveedor, 'proveedor'); ?>
		<tr>
			<th style="width:10%" rowspan="5">
			<th style="width:10%" rowspan="5"><img style="width:1.60in;height:0.70in" src="<?= base_url() ?>/files/Setting/<?=$sucursal->ruc?>.png" /></th>
		</th>
			<th style="width:90%"><span style="font-size:11pt;"><?= $sucursal->razonsocial ?></span></th>
			<th class="cubon"><span style="font-size:9pt;">COMPRA</span></th>
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
			<th><span style="font-size:8pt;"><?= $sucursal->departamento.' '.$sucursal->provincia.' '.$sucursal->distrito ?></span></th>
			<th></th>
		</tr>
	</table>
	<table class="det">
		<tr>
			<td><span style="font-weight:bold;font-size:9pt;">Se&ntilde;or(es)</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $proveedor->nombre ?></span></td>
			<td></td>
			<td><span style="font-weight:bold;font-size:9pt;">RUC</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $proveedor->ruc ?></span></td>
		</tr>
		<tr>
			<td><span style="font-weight:bold;font-size:9pt;">Direcci&oacute;n</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= substr($proveedor->direccion, 0 , 50) ?></span></td>
			<td></td>
			<td><span style="font-weight:bold;font-size:9pt;">Forma de Pago</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $data->formapago ?></span></td>
		</tr>
		<tr>
			<td><span style="font-weight:bold;font-size:9pt;">Telefono</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $proveedor->telefono.' - '.$proveedor->celular ?></span></td>
			<td></td>
			<td><span style="font-weight:bold;font-size:9pt;">Tipo de Moneda</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: PEN</span></td>
		</tr>
		<tr>
			<td><span style="font-weight:bold;font-size:9pt;">Contacto</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $proveedor->contacto ?></span></td>
			<td></td>
			<td><span style="font-weight:bold;font-size:9pt;">Fecha Entrega</span></td>
			<td style="border-bottom: 2px solid;"><span style="font-size:9pt;">: <?= $data->created ?></span></td>
		</tr>
		<tr>
			<td colspan="5"><span style="font-size:9pt;">Sirvanse atender los materiales que se detallan a continuación.</span></td>
		</tr>
	</table>
	<table class="art">
		<tr>
			<td class="desart"><span style="font-size:9pt;">Item</span></td>
			<td class="desart"><span style="font-size:9pt;">Código</span></td>
			<td class="desart"><span style="font-size:9pt;">Destino</span></td>
			<td class="desart"><span style="font-size:9pt;">Descripción</span></td>
			<td class="desart"><span style="font-size:9pt;">Tipo</span></td>
			<td class="desart"><span style="font-size:9pt;">Cant.</span></td>
			<td class="desart"><span style="font-size:9pt;">Regalo.</span></td>
			<td class="desart"><span style="font-size:9pt;">P.Unit</span></th>
			<td class="desart"><span style="font-size:9pt;">Total</span></td>
		</tr>
			<?php $subtotal = 0; $i = 0; 
			foreach ($datas as $value) { $i++; ?>
			<?php $producto = $this->Controlador_model->get($value->producto, 'producto'); ?>
			<?php $almacen = $this->Controlador_model->get($value->almacen, 'almacen'); ?>
			<?php $subtotal += $value->subtotal; ?>
			<tr>
				<td style="text-align: center;"><span style="font-size:8pt;"><?= $i ?></span></td>
				<td><span style="font-size:8pt;"><?= $producto->codigo == '' ? $producto->codigoexterno : $producto->codigo ?></span></td>
				<td><span style="font-size:8pt;"><?= $almacen->nombre ?></span></td>
				<td><span style="font-size:8pt;"><?= $producto->nombre ?></span></td>
				<td style="text-align: center;"><span style="font-size:8pt;"><?= $value->medida ?></span></td>
				<td style="text-align: center;"><span style="font-size:8pt;"><?= $value->cantidad ?></span></td>
				<td style="text-align: center;"><span style="font-size:8pt;"><?= $value->cantidaditemregalo ?></span></td>
				<td style="text-align: right;"><span style="font-size:8pt;"><?= $value->precioneto ?></span></td>
				<td style="text-align: right;"><span style="font-size:8pt;"><?= number_format($value->subtotal, 2) ?></span></td>
			</tr>
		<?php } ?>
	</table>
	<table class="pie" border="1">
		<tr>
			<td colspan="3"><span style="font-size:9pt;">Lugar de Entrega : </span><span style="font-size:7pt;"><?= $sucursal->direccion ?></span></td>
			<td style="text-align: center;"><span style="font-size:9pt;">Valor Venta</span></td>
			<td colspan="2" style="text-align: right;"><span style="font-size:9pt;"><?= number_format($subtotal, 2) ?></span></td>
		</tr>
		<tr>
			<td colspan="3"><span style="font-size:9pt;">SON : </span><span style="font-size:8pt;"><?= $letras ?></span></td>
			<td style="text-align: center;"><span style="font-size:9pt;">IGV (%)</span></td>
			<td colspan="2" style="text-align: right;"><span style="font-size:9pt;"><?= number_format(0, 2) ?></span></td>
		</tr>
		<tr>
			<td rowspan="2"colspan="3"><span style="font-size:9pt;">Obs : </span><span style="font-size:9pt;"><?= $data->observacion ?></span></td>
			<td style="text-align: center;"><span style="font-size:9pt;">Total</span></td>
			<td colspan="2" style="text-align: right;"><span style="font-size:9pt;"><?= number_format($subtotal, 2) ?></span></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php $usuario = $this->Controlador_model->get($data->usuario, 'usuario'); ?>
		<tr>
			<td colspan="2" style="text-align: center;">
				&nbsp;<br>&nbsp;<br>
				<span style="font-size:9pt;">1_____________________________</span><br>
				<span style="font-size:9pt;">Solicitado por : </span>
				<span style="font-size:7pt;"><?= $usuario->apellido.' '.$usuario->nombre ?></span>
			</td>
			<td colspan="2" style="text-align: center;">
				&nbsp;<br>&nbsp;<br>
				<span style="font-size:9pt;">2_______________________________</span><br>
				<span style="font-size:9pt;">Aprobado por : </span>
				<span style="font-size:7pt;"><?= $usuario->apellido.' '.$usuario->nombre ?></span>
			</td>
			<td colspan="2"></td>
		</tr>
		<tr>
			<td colspan="6">
				<span style="font-size:9pt;">Nota Importante : </span><br>
				<span style="font-size:9pt;">1.- El proveedor debe adjuntar a su Factura copia de la OC atendida.</span><br>
				<span style="font-size:9pt;">2.- Esta Orden es nula sin las firmas y sellos reglamentarios o autorizados.</span><br>
				<span style="font-size:9pt;">3.- Nos reservamos el derecho de devolver la mercaderia que no este de acuerdo con las especificaciones t&eacute;cnicas.</span>
			</td>
		</tr>
	</table>
</body>

</html>
