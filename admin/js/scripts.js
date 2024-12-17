$(document).ready(function() {
    let carrito = [];
    let categoriaActual = 'pasteles';

    // Filtrado de categorías
    $('.categoria-filtro').click(function() {
        categoriaActual = $(this).data('categoria');
        realizarBusqueda();
    });

    // Búsqueda en tiempo real
    $('#busqueda').on('input', function() {
        realizarBusqueda();
    });

    function realizarBusqueda() {
        const termino = $('#busqueda').val();
        
        $.ajax({
            url: '',
            method: 'GET',
            data: {
                busqueda: termino,
                categoria: categoriaActual, 
                ajax: true
            },
            dataType: 'json',
            success: function(productos) {
                actualizarProductos(productos);
            }
        });
    }

    function actualizarContadorCarrito() {
        const totalProductos = carrito.reduce((total, item) => total + item.cantidad, 0);
        const carritoIcono = $('.carrito-icono');
        
        carritoIcono.find('.carrito-badge').remove();
        
        if (totalProductos > 0) {
            carritoIcono.append(`
                <span class="carrito-badge bg-red-500 text-white rounded-full px-2 py-1 text-xs absolute -top-2 -right-2">
                    ${totalProductos}
                </span>
            `);
        }
    }

    function mostrarFormularioPago() {
        const formularioHtml = `
            <div id="formulario-pago" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                <div class="bg-white w-96 p-6 rounded-lg shadow-xl">
                    <h2 class="text-2xl font-bold mb-4">Datos de Compra</h2>
                    <form id="form-datos-compra">
                        <div class="mb-4">
                            <label class="block mb-2">Nombre Completo</label>
                            <input type="text" name="nombre" required class="w-full border p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Teléfono</label>
                            <input type="tel" name="telefono" required class="w-full border p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Cédula</label>
                            <input type="text" name="cedula" required class="w-full border p-2 rounded">
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Tipo de Entrega</label>
                            <select name="tipo_entrega" class="w-full border p-2 rounded">
                                <option value="domicilio">Domicilio</option>
                                <option value="tienda">Recoger en Tienda</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block mb-2">Método de Pago</label>
                            <select name="metodo_pago" class="w-full border p-2 rounded">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia Bancolombia</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded hover:bg-green-600">
                            Finalizar Compra
                        </button>
                    </form>
                </div>
            </div>
        `;

        $('#formulario-pago').remove();
        $('body').append(formularioHtml);

        $('#form-datos-compra').submit(function(e) {
            e.preventDefault();
            const datosCompra = {
                cliente: {
                    nombre: $('input[name="nombre"]').val(),
                    telefono: $('input[name="telefono"]').val(),
                    cedula: $('input[name="cedula"]').val()
                },
                pedido: {
                    tipo_entrega: $('select[name="tipo_entrega"]').val(),
                    metodo_pago: $('select[name="metodo_pago"]').val(),
                    productos: carrito,
                    total: carrito.reduce((total, item) => total + item.precio * item.cantidad, 0)
                }
            };

            $.ajax({
                url: 'guardar_pedido.php',
                method: 'POST',
                data: JSON.stringify(datosCompra),
                contentType: 'application/json',
                success: function(respuesta) {
                    alert('¡Compra realizada con éxito!');
                    carrito = [];
                    actualizarContadorCarrito();
                    $('#formulario-pago').remove();
                    $('#carrito-modal').remove();
                },
                error: function() {
                    alert('Error al procesar la compra');
                }
            });
        });
    }

    window.agregarAlCarrito = function(id, categoria, nombre, precio, imagen) {
        const productoExistente = carrito.find(item => 
            item.id === id && item.categoria === categoria
        );

        if (productoExistente) {
            productoExistente.cantidad++;
        } else {
            carrito.push({
                id, 
                categoria, 
                nombre, 
                precio, 
                imagen, 
                cantidad: 1
            });
        }

        actualizarContadorCarrito();
    }

    function renderCarritoModal() {
        const modalHtml = `
            <div id="carrito-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex justify-end">
                <div class="bg-white w-96 h-full p-6 shadow-xl overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold">Carrito de Compras</h2>
                        <button id="cerrar-carrito" class="text-gray-600 hover:text-gray-900">✕</button>
                    </div>
                    
                    ${carrito.length === 0 
                        ? '<p class="text-center text-gray-500">Tu carrito está vacío</p>' 
                        : carrito.map(item => `
                            <div class="flex items-center justify-between mb-4 pb-4 border-b">
                                <img src="${item.imagen}" alt="${item.nombre}" class="w-24 h-24 object-cover mr-4 rounded-lg">
                                <div class="flex-grow">
                                    <h3 class="font-semibold">${item.nombre}</h3>
                                    <p class="text-gray-600">$${item.precio.toFixed(2)}</p>
                                </div>
                                <div class="flex items-center">
                                    <button class="cantidad-btn" data-id="${item.id}" data-categoria="${item.categoria}" data-accion="reducir">-</button>
                                    <span class="mx-2">${item.cantidad}</span>
                                    <button class="cantidad-btn" data-id="${item.id}" data-categoria="${item.categoria}" data-accion="aumentar">+</button>
                                    <button class="eliminar-btn ml-4 text-red-500" 
                                        data-id="${item.id}" 
                                        data-categoria="${item.categoria}">🗑️</button>
                                </div>
                            </div>
                        `).join('')}
                    
                    ${carrito.length > 0 ? `
                        <div class="mt-4">
                            <p class="font-bold">Total: $${carrito.reduce((total, item) => total + item.precio * item.cantidad, 0).toFixed(2)}</p>
                            <button class="w-full bg-green-500 text-white py-2 rounded mt-4 hover:bg-green-600">
                                Proceder al Pago
                            </button>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        $('#carrito-modal').remove();
        $('body').append(modalHtml);

        $('#cerrar-carrito').click(() => $('#carrito-modal').remove());

        $('.cantidad-btn').click(function() {
            const id = $(this).data('id');
            const categoria = $(this).data('categoria');
            const accion = $(this).data('accion');
            
            const itemIndex = carrito.findIndex(item => 
                item.id === id && item.categoria === categoria
            );

            if (itemIndex !== -1) {
                if (accion === 'aumentar') {
                    carrito[itemIndex].cantidad++;
                } else if (accion === 'reducir') {
                    if (carrito[itemIndex].cantidad > 1) {
                        carrito[itemIndex].cantidad--;
                    } else {
                        carrito.splice(itemIndex, 1);
                    }
                }
                actualizarContadorCarrito();
                renderCarritoModal();
            }
        });

        $('.eliminar-btn').click(function() {
            const id = $(this).data('id');
            const categoria = $(this).data('categoria');
            
            const itemIndex = carrito.findIndex(item => 
                item.id === id && item.categoria === categoria
            );

            if (itemIndex !== -1) {
                carrito.splice(itemIndex, 1);
                actualizarContadorCarrito();
                renderCarritoModal();
            }
        });

        // Modificar botón de pago para abrir formulario de pago
        $('button:contains("Proceder al Pago")').click(() => {
            $('#carrito-modal').remove();
            mostrarFormularioPago();
        });
    }

    function actualizarProductos(productos) {
        const contenedor = $('#productos');
        contenedor.empty();

        productos.forEach(producto => {
            const productoHTML = `
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300">
                    <img src="${producto.imagen}" alt="${producto.nombre}" class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${producto.nombre}</h3>
                        <div class="flex justify-between items-center">
                            <span class="text-blue-600 font-semibold">$${producto.precio.toFixed(2)}</span>
                            <button onclick="agregarAlCarrito(${producto.id}, '${producto.categoria}', '${producto.nombre}', ${producto.precio}, '${producto.imagen}')" 
                                class="bg-green-500 text-white px-4 py-2 rounded-full hover:bg-green-600">
                                Añadir
                            </button>
                        </div>
                    </div>
                </div>
            `;
            contenedor.append(productoHTML);
        });
    }

    // Quitar enlace de carrito y agregar evento para abrir modal
    $('.carrito-icono').click(function(e) {
        e.preventDefault();
        renderCarritoModal();
    });

    // Cargar productos iniciales
    realizarBusqueda();
});

// Función global para agregar al carrito
function agregarAlCarrito(id, categoria, nombre, precio, imagen) {
    const event = new CustomEvent('agregarAlCarrito', { 
        detail: { id, categoria, nombre, precio, imagen } 
    });
    document.dispatchEvent(event);
}

document.addEventListener('agregarAlCarrito', function(e) {
    const { id, categoria, nombre, precio, imagen } = e.detail;
    $(document).trigger('agregarAlCarrito', [id, categoria, nombre, precio, imagen]);
});