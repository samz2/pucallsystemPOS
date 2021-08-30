<!-- Navigation -->
<style>
  .smart-scroll {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1030;
  }

  .scrolled-down {
    transform: translateY(-100%);
    transition: all 0.3s ease-in-out;
  }

  .scrolled-up {
    transform: translateY(0);
    transition: all 0.3s ease-in-out;
  }

  .alertaStock {
    position: fixed;
    z-index: 9999;
    bottom: 10px;
    left: 6px;
  }

  .alertaCP {
    position: fixed;
    z-index: 9999;
    bottom: 70px;
    left: 6px;
  }

  .alertabtn {
    background: var(--always-white);
    color: var(--primary-icon);
    border-bottom-left-radius: 50%;
    border-top-left-radius: 50%;
    border-top-right-radius: 50%;
    border-bottom-right-radius: 50%;
    display: block;
    height: 3em;
    width: 3em;
    padding-top: 12px;
    padding-left: 11px;
    background-image: -webkit-linear-gradient(top, #f4f1ee, #fff);
    box-shadow: 0px 8px 10px 0px rgba(0, 0, 0, .3), inset 0px 4px 1px 1px white, inset 0px -3px 1px 1px rgba(204, 198, 197, .5);
    float: left;
    position: relative;
    -webkit-transition: all .1s linear;
    transition: all .1s linear;
  }

  .alertabtn:hover {
    background-image: -webkit-linear-gradient(top, #fff, #f4f1ee);
    background-image: linear-gradient(top, #fff, #f4f1ee);
    color: #0088cc;
  }

  .alertabtn:active {
    background-image: -webkit-linear-gradient(top, #efedec, #f7f4f4);
    background-image: linear-gradient(top, #efedec, #f7f4f4);
    box-shadow: 0 3px 5px 0 rgba(0, 0, 0, .4), inset 0px -3px 1px 1px rgba(204, 198, 197, .5);
  }

  .bar1,
  .bar2,
  .bar3 {
    width: 28px;
    height: 4px;
    background-color: #333;
    margin: 4px 0;
    transition: 0.4s;
  }

  .change .bar1 {
    -webkit-transform: rotate(-45deg) translate(-9px, 6px);
    transform: rotate(-45deg) translate(-9px, 6px);
  }

  .change .bar2 {
    opacity: 0;
  }

  .change .bar3 {
    -webkit-transform: rotate(45deg) translate(-8px, -8px);
    transform: rotate(45deg) translate(-8px, -8px);
  }

  .usernotifbadge {
    position: absolute;
    top: -5px;
    background: var(--notification-badge);
  }

  .usernotifbadgeCP {
    position: absolute;
    top: -5px;
    background: var(--notification-badge);
  }

  .copyright {
    background: black;
    color: white;
    font-family: monospace;
    text-align: center;
    font-weight: 700;
  }

  /* Color Variables */
  $color-whatsapp: #ef5a92;
  $color-instagram: #527fa6;
  $color-facebook: #3b5a9b;

  /* Social Icon Mixin */
  @mixin social-icon($color) {
    background: $color;
    color: #fff;

    .tooltip {
      background: $color;
      color: currentColor;

      &:after {
        border-top-color: $color;
      }
    }
  }

  /* Social Icons */
  .social-icons {
    display: flex;
  }

  .social-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    width: 50px;
    height: 50px;
    margin: 0 0.5rem;
    border-radius: 50%;
    cursor: pointer;
    font-family: "Helvetica Neue", "Helvetica", "Arial", sans-serif;
    font-size: 2.5rem;
    text-decoration: none;
    transition: all 0.15s ease;

    &:hover {
      color: #fff;

      .tooltip {
        visibility: visible;
        opacity: 1;
        transform: translate(-50%, -150%);
      }
    }

    &:active {
      box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.5) inset;
    }

    &--facebook {
      @include social-icon($color-facebook);
    }

    &--instagram {
      @include social-icon($color-instagram);
    }

    &--whatsapp {
      @include social-icon($color-whatsapp);
    }

    i {
      position: relative;
      top: 1px;
    }
  }

  /* Tooltips */
  .tooltip {
    display: block;
    position: absolute;
    top: 0;
    left: 50%;
    padding: 0.8rem 1rem;
    border-radius: 40px;
    font-size: 0.8rem;
    font-weight: bold;
    opacity: 0;
    pointer-events: none;
    text-transform: uppercase;
    transform: translate(-50%, -100%);
    transition: all 0.3s ease;
    z-index: 1;

    &:after {
      display: block;
      position: absolute;
      bottom: 1px;
      left: 50%;
      width: 0;
      height: 0;
      content: "";
      border: solid;
      border-width: 10px 10px 0 10px;
      border-color: transparent;
      transform: translate(-50%, 100%);
    }
  }

  .CategriaSeleccionar::-webkit-scrollbar {
    width: 10px;
    background: rgb(214, 219, 223);
  }

  .CategriaSeleccionar::-webkit-scrollbar-thumb {
    background-color: rgb(178, 186, 187);
    border-radius: 4px;
  }

  .CategriaSeleccionar::-webkit-scrollbar-thumb:hover {
    background-color: rgb(127, 140, 141);
  }

  .badgetotalesCP {
    position: relative;
    background: #f02849;
    text-transform: uppercase;
    font-weight: normal;
    padding: 3px 5px;
    font-size: 12px;
    margin-top: 1px;
    line-height: 1;
    color: #FFF;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    border-radius: 10px;
  }
</style>
<nav class="navbar navbar-default navbar-fixed-top smart-scroll" id="menuNavSide" role="navigation">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
      </button>
      <?php $empresa = $this->Controlador_model->get($this->empresa, 'empresa'); ?>
      <?php $usuario = $this->Controlador_model->get($this->usuario, 'usuario'); ?>
      <a class="navbar-brand" href="">
        <?php if ($empresa) { ?>
          <img src="<?= base_url() ?>files/Setting/<?= $empresa->logo ?>" alt="logo" style='max-height: 45px; max-width: 200px;'>
        <?php } else { ?>
          <img src="<?= base_url() . RECURSOS ?>img/logo.png" alt="logo">
        <?php } ?>
      </a>
    </div>
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <?= $this->dynamic_menu->build_menu() ?>
      <ul class="nav navbar-nav navbar-right" <?= $empresa->color_menu != "" ? "style='background: $empresa->color_menu'" : "" ?> id="menu-derecho">
        <li>
          <a href="">
            <img class="img-circle topbar-userpic hidden-xs" src="<?= $usuario->avatar ? base_url() . 'files/Avatars/' . $usuario->avatar : base_url() . RECURSOS . 'img/Avatar.jpg' ?>" width="30px" height="30px">
            <span class="hidden-xs"> &nbsp;&nbsp;<?= $usuario->nombre . " " . $usuario->apellido ?> </span>
          </a>
        </li>
        <li class="flat-box">
          <a href="<?= base_url() ?>login/salir" title="Salir"><i class="fa fa-sign-out fa-lg"></i></a>
        </li>
      </ul>
    </div>

    <div id="loadingimg"></div>
  </div>
  <!-- /.container -->
</nav>
<!-- Page Content -->
<!-- Sidebar -->
<div id="mySidebar" class="sidebar">
  <a href="javascript:void(0)" class="toggleBtn">&times;</a>
  <a href="#" style="text-align: center;">
    <?php if ($empresa) { ?>
      <img src="<?= base_url() ?>files/Setting/<?= $empresa->logo ?>" alt="logo" style='max-height: 45px; max-width: 200px;margin-top:5px;'>
    <?php } else { ?>
      <img src="<?= base_url() . RECURSOS ?>img/logo.png" alt="logo">
    <?php } ?>
  </a>
  <a href="">
    <img class="img-circle topbar-userpic hidden-xs" src="<?= $usuario->avatar ? base_url() . 'files/Avatars/' . $usuario->avatar : base_url() . RECURSOS . 'img/Avatar.jpg' ?>" width="30px" height="30px">
    <span class="hidden-xs"> &nbsp;&nbsp;<?= $usuario->nombre . " " . $usuario->apellido ?> </span>
  </a>
  <hr>
  <?= $this->dynamic_menu->build_menu_sidebar() ?>
  <hr>
  <a href="<?= base_url() ?>login/salir" title="Salir"><i class="fa fa-sign-out fa-lg"></i>Salir</a>
  <hr>
  <div class="social-icons">
    <a class="social-icon social-icon--instagram">
      <i class="fa fa-instagram"></i>
      <div class="tooltip">Instagram</div>
    </a>
    <a class="social-icon social-icon--Whatsapp">
      <i class="fa fa-whatsapp fa"></i>
      <div class="tooltip">Whatsapp</div>
    </a>
    <a class="social-icon social-icon--facebook">
      <i class="fa fa-facebook"></i>
      <div class="tooltip">Facebook</div>
    </a>
  </div>
  <div class="copyright">
    <span>v2.1 Hecho con ❤ por PUCALLSYSTEM</span>
  </div>
</div>

<div id="main">
  <div class="burger toggleBtn container" onclick="myFunction(this)">
    <div class="bar1"></div>
    <div class="bar2"></div>
    <div class="bar3"></div>
  </div>
  <script>
    function myFunction(x) {
      x.classList.toggle("change");
    }
  </script>
</div>
<div class="btn-group alertaCP dropup">
  <a class="alertabtn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-bullhorn" style="color:#3666ec; transform: rotate(333deg);" aria-hidden="true"></i>
    <span class="badge usernotifbadgeCP"></span>
  </a>
  <ul class="dropdown-menu list-group CategriaSeleccionar" id="listaAlertaCP" style="max-height:80vh; overflow-y:auto; width:300px">
  </ul>
</div>

<div class="btn-group alertaStock dropup">
  <a class="alertabtn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bell" style="color:var(--primary-icon)" aria-hidden="true"></i><span class="badge usernotifbadge"></span></a>
  <ul class="dropdown-menu list-group CategriaSeleccionar" id="listaAlertaStock" style="max-height:80vh; overflow-y:auto; width:300px">
  </ul>
</div>



<script>
  $(document).ready(function() {
    $(".toggleBtn").on("click", function() {
      $("#mySidebar").toggleClass("active");
      $("#contenedor").toggleClass("sidenav-overlay");
      $("#main").toggleClass("active");
      $(this).toggleClass("active");
    });
    getAlertaStock();
    alertComprobantes();

    $('.alertaCP').on('click', function(e) {
      // $(".alertaCP").addClass("open");
      /*
      if($(".alertaCP").hasClass("open")){
        //? manda true si tiene la clase
        
        console.log("si tiene el open")
      }
      */
    });

  });

  function getAlertaStock() {
    $.ajax({
      url: "<?= base_url() ?>inicio/alertaStock",
      type: "GET",
      dataType: "json",
      success: function(data) {
        console.warn(data["numeroStock"]);
        $("#listaAlertaStock").html(data["data"]);
        $(".usernotifbadge").text(data["numeroStock"]);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  }

  function alertComprobantes() {
    $.ajax({
      url: "<?= base_url() ?>inicio/ajax_alertComprobantes",
      type: "GET",
      dataType: "json",
      success: function(data) {
        $("#listaAlertaCP").html(data.dataCP);
        if (data.totalCP > 0) {
          $(".usernotifbadgeCP").text(data.totalCP);
        }
        $("#subtotalCPBoletas").text(data.subtotalBoletas);
        $("#subtotalCPFacturas").text(data.subtotalFacturas);
        console.log();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Lobibox.notify('error', {
          size: 'mini',
          msg: 'Error al obtener datos de ajax.'
        });
      }
    });
  }
</script>