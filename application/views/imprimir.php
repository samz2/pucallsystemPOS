<div class="container">
  <div class="row">
    <!-- Aquí pon las col-x necesarias, comienza tu contenido, etcétera -->
    <div class="col-12 col-lg-6">
      <h2>Ajustes de impresora</h2>
      <strong>Nombre de impresora seleccionada: </strong>
      <p id="impresoraSeleccionada"></p>
      <div class="form-group">
          <select class="form-control" id="listaDeImpresoras"></select>
      </div>
      <button class="btn btn-primary btn-sm" id="btnRefrescarLista">Refrescar lista</button>
      <button class="btn btn-primary btn-sm" id="btnEstablecerImpresora">Establecer como predeterminada</button>
    </div>
    <div class="col-12 col-lg-6">
      <h2>Log</h2>
      <button class="btn btn-warning btn-sm" id="btnLimpiarLog">Limpiar log</button>
      <pre id="estado"></pre>
    </div>
  </div>
</div>

<script type="text/javascript">
const RUTA_API = "http://localhost:8000";
const $estado = document.querySelector("#estado"),
    $listaDeImpresoras = document.querySelector("#listaDeImpresoras"),
    $btnLimpiarLog = document.querySelector("#btnLimpiarLog"),
    $btnRefrescarLista = document.querySelector("#btnRefrescarLista"),
    $btnEstablecerImpresora = document.querySelector("#btnEstablecerImpresora"),
    $texto = document.querySelector("#texto"),
    $impresoraSeleccionada = document.querySelector("#impresoraSeleccionada"),
    $btnImprimir = document.querySelector("#btnImprimir");

const loguear = texto => $estado.textContent += (new Date()).toLocaleString() + " " + texto + "\n";
const limpiarLog = () => $estado.textContent = "";

$btnLimpiarLog.addEventListener("click", limpiarLog);

const limpiarLista = () => {
  for (let i = $listaDeImpresoras.options.length; i >= 0; i--) {
    $listaDeImpresoras.remove(i);
  }
};

const obtenerListaDeImpresoras = () => {
  loguear("Cargando lista...");
  Impresora.getImpresoras().then(listaDeImpresoras => {
    refrescarNombreDeImpresoraSeleccionada();
    loguear("Lista cargada");
    limpiarLista();
    listaDeImpresoras.forEach(nombreImpresora => {
      const option = document.createElement('option');
      option.value = option.text = nombreImpresora;
      $listaDeImpresoras.appendChild(option);
    })
  });
}

const establecerImpresoraComoPredeterminada = nombreImpresora => {
  loguear("Estableciendo impresora...");
  Impresora.setImpresora(nombreImpresora).then(respuesta => {
    refrescarNombreDeImpresoraSeleccionada();
    if (respuesta) {
      loguear(`Impresora ${nombreImpresora} establecida correctamente`);
    } else {
      loguear(`No se pudo establecer la impresora con el nombre ${nombreImpresora}`);
    }
  });
};

const refrescarNombreDeImpresoraSeleccionada = () => {
  Impresora.getImpresora().then(nombreImpresora => {
    $impresoraSeleccionada.textContent = nombreImpresora;
  });
}

$btnRefrescarLista.addEventListener("click", obtenerListaDeImpresoras);
$btnEstablecerImpresora.addEventListener("click", () => {
  const indice = $listaDeImpresoras.selectedIndex;
  if (indice === -1) return loguear("No hay ninguna impresora seleccionada")
  const opcionSeleccionada = $listaDeImpresoras.options[indice];
  establecerImpresoraComoPredeterminada(opcionSeleccionada.value);
});
// En el init, obtenemos la lista
obtenerListaDeImpresoras();
// Y también la impresora seleccionada
refrescarNombreDeImpresoraSeleccionada();
</script>
