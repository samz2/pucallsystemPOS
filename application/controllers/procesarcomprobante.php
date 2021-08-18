<?php

include "apisunat_2_1.php"; //ubl 2.1
include "signature.php";

class Procesarcomprobante {

	public function procesar_factura($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_factura($data_comprobante, $items_detalle, $rutas['ruta_xml']);
		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);
		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}
		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_nota_de_credito($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_nota_credito($data_comprobante, $items_detalle, $rutas['ruta_xml']);
		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);
		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}
		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}
		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_nota_de_debito($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_nota_debito($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}

		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_guia_de_remision($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_guia_remision($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);

		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}

		$resp['respuesta'] = 'ok';
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_envio['hash_cdr'];
		$resp['cod_sunat'] = $resp_envio['cod_sunat'];
		$resp['msj_sunat'] = $resp_envio['mensaje'];
		return $resp;
	}

	public function procesar_resumen_boletas($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_resumen_documentos($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_resumen_boletas($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}

		$resp_ticket = $apisunat->consultar_envio_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $resp_envio['cod_ticket'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);

		$resp['respuesta'] = 'ok';
		$resp['resp_envio_doc'] = $resp_envio['respuesta'];
		$resp['resp_consulta_ticket'] = $resp_ticket['respuesta'];
		$resp['resp_error_consult_ticket'] = 'Cod: '.$resp_ticket['cod_sunat'].' Mensaje: '.$resp_ticket['cod_sunat'];
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_ticket['hash_cdr'];
		$resp['msj_sunat'] = $resp_ticket['mensaje'];
		return $resp;
	}

	public function procesar_baja_sunat($data_comprobante, $items_detalle, $rutas) {
		$apisunat = new apisunat();
		$resp = $apisunat->crear_xml_baja_sunat($data_comprobante, $items_detalle, $rutas['ruta_xml']);

		$signature = new Signature();
		$flg_firma = "0";
		$resp_firma = $signature->signature_xml($flg_firma, $rutas['ruta_xml'], $rutas['ruta_firma'], $rutas['pass_firma']);

		if($resp_firma['respuesta'] == 'error') {
			return $resp_firma;
		}

		$resp_envio = $apisunat->enviar_documento_para_baja($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $rutas['ruta_xml'], $rutas['ruta_cdr'], $rutas['nombre_archivo'], $rutas['ruta_ws']);
		if($resp_envio['respuesta'] == 'error') {
			return $resp_envio;
		}

		$resp_ticket = $apisunat->consultar_envio_ticket($data_comprobante['EMISOR_RUC'], $data_comprobante['EMISOR_USUARIO_SOL'], $data_comprobante['EMISOR_PASS_SOL'], $resp_envio['cod_ticket'], $rutas['nombre_archivo'], $rutas['ruta_cdr'], $rutas['ruta_ws']);

		$resp['respuesta'] = 'ok';
		$resp['resp_envio_doc'] = $resp_envio['respuesta'];
		$resp['resp_consulta_ticket'] = $resp_ticket['respuesta'];
		$resp['resp_error_consult_ticket'] = 'Cod: '.$resp_ticket['cod_sunat'].' Mensaje: '.$resp_ticket['cod_sunat'];
		$resp['hash_cpe'] = $resp_firma['hash_cpe'];
		$resp['hash_cdr'] = $resp_ticket['hash_cdr'];
		$resp['msj_sunat'] = $resp_ticket['mensaje'];
		return $resp;
	}
}
?>
