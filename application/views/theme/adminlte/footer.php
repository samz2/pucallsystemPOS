<footer class="footer text-right">
  <?php $empresa = $this->Controlador_model->get($this->session->userdata('empresa'), 'empresa'); ?>
  <?= date('Y', time()) ?> © <?= $empresa->razonsocial ?> - SERVICIOS Y REPUESTOS.
</footer>
                                                                                                                
