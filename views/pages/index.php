<!-- Estilos para dar vida -->
<style>
    /* Animaciones y efectos hover */
    .card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(106, 90, 205, 0.3) !important;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Pulso en iconos */
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .stat-icon {
        animation: pulse 2s ease-in-out infinite;
    }

    /* Rotación en hover */
    .service-icon:hover {
        transform: rotate(360deg);
        transition: transform 0.5s ease;
    }

    /* Efecto de brillo */
    @keyframes shine {
        0% { background-position: -200px; }
        100% { background-position: 200px; }
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .card:hover::before {
        left: 100%;
    }
</style>

<!-- Header -->
<section style="background: linear-gradient(135deg, #6a5acd 0%, #9370db 100%); color: white; padding: 50px 0; margin-bottom: 40px; position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; viewBox=&quot;0 0 100 20&quot;><defs><radialGradient id=&quot;a&quot; cx=&quot;50%&quot; cy=&quot;40%&quot;><stop offset=&quot;0%&quot; stop-color=&quot;%23ffffff&quot; stop-opacity=&quot;0.1&quot;/><stop offset=&quot;100%&quot; stop-color=&quot;%23ffffff&quot; stop-opacity=&quot;0&quot;/></radialGradient></defs><rect width=&quot;100&quot; height=&quot;20&quot; fill=&quot;url(%23a)&quot;/></svg>'); opacity: 0.3;"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div class="text-center">
            <div style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; border: 3px solid rgba(255, 255, 255, 0.2); animation: pulse 2s ease-in-out infinite;">
                <i class="fas fa-mobile-alt" style="font-size: 2.2rem; color: #fff;"></i>
            </div>
            <h1 style="font-size: 2.8rem; font-weight: 700; margin-bottom: 15px; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">
                📱 TechRepair Pro 🔧
            </h1>
            <p style="font-size: 1.2rem; opacity: 0.9; font-weight: 300; margin-bottom: 30px;">
                ⚡ Sistema de Gestión para Reparación de Teléfonos ⚡
            </p>
            
            <!-- Estadísticas -->
            <div class="row" style="background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 15px; padding: 30px; margin-top: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                <div class="col-md-3 col-6">
                    <div style="text-align: center; padding: 20px;">
                        <div class="stat-icon" style="margin-bottom: 10px;">
                            <i class="fas fa-tools" style="font-size: 2rem; color: #ffd700;"></i>
                        </div>
                        <span style="font-size: 2.5rem; font-weight: 700; color: white; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">248</span>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                            🔧 Reparaciones
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div style="text-align: center; padding: 20px;">
                        <div class="stat-icon" style="margin-bottom: 10px;">
                            <i class="fas fa-users" style="font-size: 2rem; color: #00ff7f;"></i>
                        </div>
                        <span style="font-size: 2.5rem; font-weight: 700; color: white; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">156</span>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                            👥 Clientes
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div style="text-align: center; padding: 20px;">
                        <div class="stat-icon" style="margin-bottom: 10px;">
                            <i class="fas fa-shopping-bag" style="font-size: 2rem; color: #ff6b6b;"></i>
                        </div>
                        <span style="font-size: 2.5rem; font-weight: 700; color: white; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">32</span>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                            💰 Ventas Hoy
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div style="text-align: center; padding: 20px;">
                        <div class="stat-icon" style="margin-bottom: 10px;">
                            <i class="fas fa-warehouse" style="font-size: 2rem; color: #4ecdc4;"></i>
                        </div>
                        <span style="font-size: 2.5rem; font-weight: 700; color: white; display: block; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">89</span>
                        <div style="font-size: 0.9rem; color: rgba(255,255,255,0.8); margin-top: 8px; text-transform: uppercase; letter-spacing: 1px;">
                            📦 En Stock
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Servicios -->
<div class="row mb-5" style="margin-top: -20px; position: relative; z-index: 3;">
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(106, 90, 205, 0.2); border: 1px solid rgba(106, 90, 205, 0.1); transition: all 0.3s ease; background: white; overflow: hidden; position: relative;">
            <div class="card-body text-center" style="padding: 40px 30px;">
                <div class="service-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #6a5acd 0%, #9370db 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; box-shadow: 0 8px 25px rgba(106, 90, 205, 0.3); transition: transform 0.5s ease;">
                    <i class="fas fa-screwdriver-wrench" style="font-size: 1.8rem;"></i>
                </div>
                <h5 style="font-weight: 600; color: #2c3e50; margin-bottom: 15px; font-size: 1.3rem;">
                    🔧 Órdenes de Reparación
                </h5>
                <p style="color: #6c757d; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.6;">
                    📋 Gestiona todas las órdenes de reparación, asigna técnicos y da seguimiento al progreso.
                </p>
                <a href="/proyecto_pmlx/ordenes_reparacion" class="btn" style="background: linear-gradient(135deg, #6a5acd 0%, #9370db 100%); color: white; border-radius: 25px; padding: 12px 30px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease; border: none; text-decoration: none;">
                    <i class="fas fa-cogs me-2"></i>Gestionar
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(138, 43, 226, 0.2); border: 1px solid rgba(138, 43, 226, 0.1); transition: all 0.3s ease; background: white; overflow: hidden; position: relative;">
            <div class="card-body text-center" style="padding: 40px 30px;">
                <div class="service-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #8a2be2 0%, #ba55d3 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; box-shadow: 0 8px 25px rgba(138, 43, 226, 0.3); transition: transform 0.5s ease;">
                    <i class="fas fa-cash-register" style="font-size: 1.8rem;"></i>
                </div>
                <h5 style="font-weight: 600; color: #2c3e50; margin-bottom: 15px; font-size: 1.3rem;">
                    💰 Ventas y Facturación
                </h5>
                <p style="color: #6c757d; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.6;">
                    🧾 Registra ventas de productos y servicios, genera facturas y controla el flujo de caja.
                </p>
                <a href="/proyecto_pmlx/ventas" class="btn" style="background: linear-gradient(135deg, #8a2be2 0%, #ba55d3 100%); color: white; border-radius: 25px; padding: 12px 30px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease; border: none; text-decoration: none;">
                    <i class="fas fa-shopping-cart me-2"></i>Vender
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(147, 112, 219, 0.2); border: 1px solid rgba(147, 112, 219, 0.1); transition: all 0.3s ease; background: white; overflow: hidden; position: relative;">
            <div class="card-body text-center" style="padding: 40px 30px;">
                <div class="service-icon" style="width: 70px; height: 70px; background: linear-gradient(135deg, #9370db 0%, #dda0dd 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; box-shadow: 0 8px 25px rgba(147, 112, 219, 0.3); transition: transform 0.5s ease;">
                    <i class="fas fa-boxes" style="font-size: 1.8rem;"></i>
                </div>
                <h5 style="font-weight: 600; color: #2c3e50; margin-bottom: 15px; font-size: 1.3rem;">
                    📦 Inventario
                </h5>
                <p style="color: #6c757d; font-size: 0.95rem; margin-bottom: 25px; line-height: 1.6;">
                    📱 Controla el stock de repuestos, teléfonos y accesorios. Alertas de stock mínimo.
                </p>
                <a href="/proyecto_pmlx/inventario" class="btn" style="background: linear-gradient(135deg, #9370db 0%, #dda0dd 100%); color: white; border-radius: 25px; padding: 12px 30px; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; transition: all 0.3s ease; border: none; text-decoration: none;">
                    <i class="fas fa-search me-2"></i>Ver Stock
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Acciones y Actividad -->
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(106, 90, 205, 0.1); border: 1px solid rgba(106, 90, 205, 0.1); background: white; position: relative; overflow: hidden;">
            <div class="card-body" style="padding: 30px;">
                <h5 style="color: #2c3e50; margin-bottom: 25px; font-weight: 600; font-size: 1.2rem;">
                    <i class="fas fa-bolt me-2" style="color: #6a5acd;"></i>⚡ Acciones Rápidas
                </h5>
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="/proyecto_pmlx/clientes" class="btn w-100" style="background: linear-gradient(135deg, #6a5acd 0%, #9370db 100%); color: white; border-radius: 12px; padding: 15px 20px; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; border: none; text-decoration: none;">
                            <i class="fas fa-user-plus me-2"></i>👤 Nuevo Cliente
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="/proyecto_pmlx/ordenes_reparacion" class="btn w-100" style="background: linear-gradient(135deg, #8a2be2 0%, #ba55d3 100%); color: white; border-radius: 12px; padding: 15px 20px; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; border: none; text-decoration: none;">
                            <i class="fas fa-plus-circle me-2"></i>📝 Nueva Orden
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="/proyecto_pmlx/inventario" class="btn w-100" style="background: linear-gradient(135deg, #9370db 0%, #dda0dd 100%); color: white; border-radius: 12px; padding: 15px 20px; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; border: none; text-decoration: none;">
                            <i class="fas fa-search me-2"></i>🔍 Buscar Producto
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="/proyecto_pmlx/estadisticas" class="btn w-100" style="background: linear-gradient(135deg, #da70d6 0%, #e6e6fa 100%); color: #2c3e50; border-radius: 12px; padding: 15px 20px; font-size: 0.85rem; font-weight: 600; transition: all 0.3s ease; border: none; text-decoration: none;">
                            <i class="fas fa-chart-line me-2"></i>📊 Reportes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-4">
        <div class="card" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(106, 90, 205, 0.1); border: 1px solid rgba(106, 90, 205, 0.1); background: white; position: relative; overflow: hidden;">
            <div class="card-body" style="padding: 30px;">
                <h5 style="color: #2c3e50; margin-bottom: 25px; font-weight: 600; font-size: 1.2rem;">
                    <i class="fas fa-clock me-2" style="color: #6a5acd;"></i>🕐 Actividad Reciente
                </h5>
                
                <div style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f3f4; transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(10px)'; this.style.backgroundColor='rgba(106, 90, 205, 0.05)'" onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='transparent'">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #6a5acd 0%, #9370db 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white;">
                        <i class="fas fa-mobile-screen" style="font-size: 0.9rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 5px; font-size: 0.95rem;">
                            ✅ Reparación iPhone 13 completada
                        </div>
                        <div style="font-size: 0.85rem; color: #6c757d;">
                            🔧 Cambio de pantalla • ⏰ Hace 15 minutos
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f3f4; transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(10px)'; this.style.backgroundColor='rgba(138, 43, 226, 0.05)'" onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='transparent'">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8a2be2 0%, #ba55d3 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white;">
                        <i class="fas fa-user-check" style="font-size: 0.9rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 5px; font-size: 0.95rem;">
                            👤 Nuevo cliente registrado
                        </div>
                        <div style="font-size: 0.85rem; color: #6c757d;">
                            📝 María González • ⏰ Hace 32 minutos
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f3f4; transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(10px)'; this.style.backgroundColor='rgba(147, 112, 219, 0.05)'" onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='transparent'">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #9370db 0%, #dda0dd 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: white;">
                        <i class="fas fa-mobile-alt" style="font-size: 0.9rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 5px; font-size: 0.95rem;">
                            💰 Venta de Samsung Galaxy S23
                        </div>
                        <div style="font-size: 0.85rem; color: #6c757d;">
                            📱 $1,200.00 • ⏰ Hace 1 hora
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; align-items: center; padding: 15px 0; transition: all 0.3s ease;" onmouseover="this.style.transform='translateX(10px)'; this.style.backgroundColor='rgba(218, 112, 214, 0.05)'" onmouseout="this.style.transform='translateX(0)'; this.style.backgroundColor='transparent'">
                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #da70d6 0%, #e6e6fa 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; color: #2c3e50;">
                        <i class="fas fa-user-cog" style="font-size: 0.9rem;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 5px; font-size: 0.95rem;">
                            🔧 Orden de reparación asignada
                        </div>
                        <div style="font-size: 0.85rem; color: #6c757d;">
                            📱 Xiaomi Mi 11 • ⏰ Hace 2 horas
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Contador animado para las estadísticas
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    
    function timer() {
        start += increment;
        if (start >= target) {
            element.textContent = target;
        } else {
            element.textContent = Math.floor(start);
            requestAnimationFrame(timer);
        }
    }
    timer();
}

// Inicializar contadores cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[style*="font-size: 2.5rem"]');
    
    setTimeout(() => {
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            counter.textContent = '0';
            animateCounter(counter, target);
        });
    }, 500);
});

// Efectos de hover adicionales
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

<script src="build/js/inicio.js"></script>

