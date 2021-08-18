<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;

class Pdf extends Dompdf {
	public function __construct() {
		 parent::__construct();
     // include autoloader
     require_once dirname(__FILE__).'/dompdf/autoload.inc.php';
     // instantiate and use the dompdf class
     $pdf = new DOMPDF();
     $CI =& get_instance();
     $CI->dompdf = $pdf;
	}
}

?>
