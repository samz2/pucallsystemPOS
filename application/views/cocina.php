<!-- Page Content -->
<div class="row">
  <div class="col-xs-12">
    <div class="panel panel-color panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title text-title-panel">
          Lista de <?= $this->controlador ?>
          <div class="pull-right">
            <a onclick="location.reload()" class="btn btn-danger btn-sm" data-toggle="tooltip" title="RECARGAR"><i class="fa fa-repeat"></i></a>
          </div>
          <div class="clearfix"></div>
        </h3>
      </div>
      <div class="panel-body table-responsive" id="listapedido"></div>
    </div>
  </div>
</div>
<!-- /.container -->

<script type="text/javascript">
$(document).ready(function() {
  recargar();
  setInterval("recargar()", 5000);

});

function recargar() {
  $.ajax({
    url : "<?= $this->url ?>/load_pedido",
    type: "GET",
    dataType: "JSON",
    success: function(data) {
      $("#listapedido").html(data);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      Lobibox.notify('error', {
        size: 'mini',
        position:"top right",
        msg: 'Error al obtener datos de ajax.'
      });
    }
  });
}
function preparado(detalle) {
  $.ajax({
    type: "POST",
    url: "<?= $this->url ?>/preparado/" + detalle,
    success: function(data) {
      recargar();
    }
  });
};
function preparados(venta) {
  $.ajax({
    type: "POST",
    url: "<?= $this->url ?>/preparados/" + venta,
    dataType: "JSON",
    success: function(data) {
      if(data.status){
        recargar();
        
      }
      
    }
  });
};
</script>
