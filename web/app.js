// web/app.js · Consume la API REST de la papelería con fetch()
// La página NO toca la base de datos: todo lo pide a la API en JSON.

// Dirección de la API. En producción se cambia por la del servidor real.
const API = '../api';

// ---------- Utilidades ----------

// Hace una petición a la API y devuelve { ok, estado, cuerpo }
async function pedir(metodo, ruta, datos = null) {
  const opciones = { method: metodo, headers: { 'Accept': 'application/json' } };
  if (datos !== null) {
    opciones.headers['Content-Type'] = 'application/json';
    opciones.body = JSON.stringify(datos);   // objeto JavaScript → texto JSON
  }
  const respuesta = await fetch(API + ruta, opciones);
  const cuerpo = await respuesta.json();      // texto JSON → objeto JavaScript
  return { ok: respuesta.ok, estado: respuesta.status, cuerpo };
}

// Evita que un texto con etiquetas HTML se ejecute (igual que e() en PHP)
function escapar(texto) {
  const div = document.createElement('div');
  div.textContent = texto ?? '';
  return div.innerHTML;
}

const quetzales = monto => 'Q' + Number(monto).toFixed(2);

// Muestra el mensaje de éxito o de error que devolvió la API
function mostrarMensaje(respuesta) {
  const caja = document.getElementById('mensaje');
  const { ok, estado, cuerpo } = respuesta;
  let texto = cuerpo.mensaje || cuerpo.error || '';
  if (cuerpo.errores) texto += ': ' + cuerpo.errores.join(' ');
  caja.textContent = `${texto} (código ${estado})`;
  caja.className = 'mensaje ' + (ok ? 'exito' : 'error');
  caja.hidden = false;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ---------- Resumen ----------

async function cargarResumen() {
  const { cuerpo } = await pedir('GET', '/resumen');
  document.getElementById('totalVentas').textContent = cuerpo.datos.ventas;
  document.getElementById('totalVendido').textContent = quetzales(cuerpo.datos.total_vendido);
  document.getElementById('ticketPromedio').textContent = quetzales(cuerpo.datos.ticket_promedio);
}

// ---------- Productos ----------

async function cargarProductos() {
  const buscar = document.getElementById('buscar').value.trim();
  const { cuerpo } = await pedir('GET', '/productos?buscar=' + encodeURIComponent(buscar));
  const filas = cuerpo.datos.map(p => `
    <tr>
      <td>${escapar(p.codigo)}</td>
      <td>${escapar(p.nombre)}</td>
      <td>${escapar(p.categoria)}</td>
      <td class="derecha">${quetzales(p.precio)}</td>
      <td class="derecha">${p.stock}</td>
      <td class="estado-${escapar(p.estado)}">${escapar(p.estado)}</td>
      <td class="acciones">
        <button class="boton secundario" onclick="editarProducto(${p.id_producto})">Editar</button>
        <button class="boton peligro" onclick="eliminarProducto(${p.id_producto})">Eliminar</button>
      </td>
    </tr>`);
  document.getElementById('tablaProductos').innerHTML = filas.join('');
}

async function cargarCategorias() {
  const { cuerpo } = await pedir('GET', '/categorias');
  document.getElementById('idCategoria').innerHTML = '<option value="">-- Selecciona --</option>' +
    cuerpo.datos.map(c => `<option value="${c.id_categoria}">${escapar(c.nombre)}</option>`).join('');
}

// Llena el formulario con los datos del producto (GET /productos/{id})
async function editarProducto(id) {
  const { cuerpo } = await pedir('GET', '/productos/' + id);
  const p = cuerpo.datos;
  document.getElementById('idProducto').value = p.id_producto;
  document.getElementById('codigo').value = p.codigo;
  document.getElementById('nombre').value = p.nombre;
  document.getElementById('precio').value = p.precio;
  document.getElementById('stock').value = p.stock;
  document.getElementById('idCategoria').value = p.id_categoria;
  document.getElementById('tituloFormulario').textContent = 'Editar producto ' + p.codigo;
  document.getElementById('formProducto').scrollIntoView({ behavior: 'smooth' });
}

function limpiarFormulario() {
  document.getElementById('formProducto').reset();
  document.getElementById('idProducto').value = '';
  document.getElementById('tituloFormulario').textContent = 'Nuevo producto';
}

// Guarda: POST si es nuevo, PUT si se está editando
async function guardarProducto(evento) {
  evento.preventDefault();   // evita que la página se recargue
  const id = document.getElementById('idProducto').value;
  const datos = {
    codigo: document.getElementById('codigo').value,
    nombre: document.getElementById('nombre').value,
    precio: Number(document.getElementById('precio').value),
    stock: Number(document.getElementById('stock').value),
    id_categoria: Number(document.getElementById('idCategoria').value),
  };
  const respuesta = id ? await pedir('PUT', '/productos/' + id, datos)
                       : await pedir('POST', '/productos', datos);
  mostrarMensaje(respuesta);
  if (respuesta.ok) {
    limpiarFormulario();
    await Promise.all([cargarProductos(), cargarOpcionesVenta()]);
  }
}

async function eliminarProducto(id) {
  if (!confirm('¿Eliminar este producto?')) return;
  const respuesta = await pedir('DELETE', '/productos/' + id);
  mostrarMensaje(respuesta);   // si ya tiene ventas, la API responde 409
  if (respuesta.ok) await cargarProductos();
}

// ---------- Ventas ----------

// Llena las listas desplegables con datos de tres recursos de la API
async function cargarOpcionesVenta() {
  const [clientes, empleados, productos] = await Promise.all([
    pedir('GET', '/clientes'), pedir('GET', '/empleados'), pedir('GET', '/productos'),
  ]);
  const opcion = (valor, texto) => `<option value="${valor}">${escapar(texto)}</option>`;
  document.getElementById('ventaCliente').innerHTML = clientes.cuerpo.datos.map(c => opcion(c.id_cliente, c.nombre)).join('');
  document.getElementById('ventaEmpleado').innerHTML = empleados.cuerpo.datos.map(e => opcion(e.id_empleado, `${e.nombre} (${e.puesto})`)).join('');
  document.getElementById('ventaProducto').innerHTML = productos.cuerpo.datos
    .filter(p => p.stock > 0)
    .map(p => opcion(p.id_producto, `${p.nombre} · ${quetzales(p.precio)} · quedan ${p.stock}`)).join('');
}

async function cargarVentas() {
  const { cuerpo } = await pedir('GET', '/ventas');
  document.getElementById('tablaVentas').innerHTML = cuerpo.datos.map(v => `
    <tr>
      <td>${v.id_venta}</td>
      <td>${escapar(v.fecha)}</td>
      <td>${escapar(v.cliente)}</td>
      <td>${escapar(v.empleado ?? 'Sin asignar')}</td>
      <td class="derecha">${quetzales(v.total)}</td>
      <td><button class="boton secundario" onclick="verTicket(${v.id_venta})">Ver</button></td>
    </tr>`).join('');
}

function dibujarTicket(t) {
  const lineas = t.lineas.map(l => `
    <tr><td>${escapar(l.producto)}</td><td class="derecha">${l.cantidad}</td>
        <td class="derecha">${quetzales(l.precio_unitario)}</td><td class="derecha">${quetzales(l.subtotal)}</td></tr>`).join('');
  document.getElementById('ticket').innerHTML = `
    <div class="ticket">
      <strong>Venta #${t.id_venta}</strong> · ${escapar(t.cliente)} · atendió ${escapar(t.empleado ?? 'Sin asignar')} · ${escapar(t.fecha)}
      <table>
        <tr><th>Producto</th><th class="derecha">Cantidad</th><th class="derecha">Precio</th><th class="derecha">Subtotal</th></tr>
        ${lineas}
        <tr><th colspan="3" class="derecha">TOTAL</th><th class="derecha">${quetzales(t.total)}</th></tr>
      </table>
    </div>`;
}

async function verTicket(id) {
  const { cuerpo } = await pedir('GET', '/ventas/' + id);
  dibujarTicket(cuerpo.datos);
  document.getElementById('ticket').scrollIntoView({ behavior: 'smooth' });
}

// POST /api/ventas → la API llama a sp_registrar_venta (transacción de la AHD2)
async function registrarVenta(evento) {
  evento.preventDefault();
  const respuesta = await pedir('POST', '/ventas', {
    id_cliente: Number(document.getElementById('ventaCliente').value),
    id_empleado: Number(document.getElementById('ventaEmpleado').value),
    id_producto: Number(document.getElementById('ventaProducto').value),
    cantidad: Number(document.getElementById('ventaCantidad').value),
  });
  mostrarMensaje(respuesta);   // si no hay stock, la API responde 409
  if (respuesta.ok) {
    dibujarTicket(respuesta.cuerpo.datos);
    await Promise.all([cargarResumen(), cargarProductos(), cargarVentas(), cargarOpcionesVenta()]);
  }
}

// ---------- Inicio: conectar eventos y cargar datos ----------

document.getElementById('formBuscar').addEventListener('submit', e => { e.preventDefault(); cargarProductos(); });
document.getElementById('formProducto').addEventListener('submit', guardarProducto);
document.getElementById('cancelarEdicion').addEventListener('click', limpiarFormulario);
document.getElementById('formVenta').addEventListener('submit', registrarVenta);

cargarResumen();
cargarProductos();
cargarCategorias();
cargarOpcionesVenta();
cargarVentas();
