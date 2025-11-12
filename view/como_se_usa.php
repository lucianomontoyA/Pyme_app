<?php
session_start();
$_SESSION['pagina_actual'] = 'como_se_usa';
require_once '../config/database.php';
include 'partial/header.php';
?>

<main class="content">
    <h2>Cómo se usa el sistema</h2>
   <section class="manual">
  <h2>🎯 Finalidad del sistema</h2>

  <p>
    Este sistema fue desarrollado <strong>a medida</strong> para cubrir las
    necesidades específicas de un cliente dedicado al servicio técnico. Su propósito
    es ofrecer al <strong>Super Admin</strong> un control integral y detallado sobre
    todas las etapas del trabajo: desde la recepción del equipo hasta la entrega final
    y la facturación.
  </p>

  <p>
    El sistema permite gestionar órdenes, registrar reparaciones, generar facturas
    automáticas y realizar cierres diarios e históricos, brindando un control riguroso
    sobre los ingresos y la <strong>ganancia obtenida en cada orden finalizada</strong>.
  </p>

  <p>
    Está pensado principalmente para <strong>uso interno</strong>, de manera que el
    cliente final solo pueda consultar el estado de su orden mediante un código público,
    sin acceder al resto de las funciones.
  </p>

  <p>
    Cada implementación puede adaptarse según los requerimientos de cada cliente,
    incorporando o modificando módulos y funciones de acuerdo a su tipo de negocio o
    flujo de trabajo.
  </p>

  <p>
    En resumen, se trata de un sistema <strong>personalizado, flexible y orientado al
    control administrativo y operativo</strong> del servicio técnico.
  </p>
</section>

    <section class="manual">
        <p>
            Al inicio del sistema tenés un <strong>login</strong> donde debés ingresar tu usuario y contraseña,
            ya seas <strong>Super Admin</strong> o <strong>Técnico</strong>.
        </p>

        <h3>🔹 Acceso y panel principal</h3>
        <p>
            Si sos <strong>Técnico</strong>, vas a poder ver en el menú:
        </p>
        <ul>
            <li><strong>Inicio:</strong> muestra el precio del dólar blue, el bitcoin y un tablero con un gráfico de barras con la cantidad de órdenes.</li>
            <li><strong>Nueva Orden:</strong> permite crear una nueva orden de servicio.</li>
            <li><strong>Órdenes en curso:</strong> muestra las órdenes con estado <em>Ingresado</em> o <em>En Revisión</em>.</li>
        </ul>

        <p>
            Si sos <strong>Super Admin</strong>, además de lo anterior vas a ver más opciones en el menú.
        </p>

        <h3>🔹 Menú del sistema</h3>
        <ul>
            <li><strong>Inicio:</strong> panel principal con información general.</li>
            <li><strong>Nueva Orden:</strong> formulario para registrar un nuevo trabajo.</li>

            <li><strong>Clientes ▼</strong>
                <ul>
                    <li><em>Ver Clientes:</em> lista de todos los clientes registrados.</li>
                    <li><em>Nuevo Cliente:</em> formulario para agregar un nuevo cliente.</li>
                </ul>
            </li>

            <li><strong>Órdenes ▼</strong>
                <ul>
                    <li><em>Ver órdenes en curso</em></li>
                    <li><em>Órdenes Reparadas</em></li>
                    <li><em>Órdenes Finalizadas</em></li>
                    <li><em>Consultar estado de Orden</em></li>
                    <li><em>Factura:</em> permite generar una factura imprimible de la orden finalizada.</li>
                </ul>
            </li>

            <li><strong>Cierres ▼</strong>
                <ul>
                    <li><em>Cierre Diario:</em> muestra la cantidad de dinero facturado del día (solo órdenes entregadas).</li>
                    <li><em>Cierres Históricos:</em> permite generar informes entre fechas.</li>
                </ul>
            </li>

            <li><strong>Más ▼</strong>
                <ul>
                    <li><em>Consultar estado de Orden:</em> búsqueda pública por código.</li>
                    <li><em>Crear Técnico:</em> alta de nuevos usuarios técnicos.</li>
                    <li><em>Cómo se usa:</em> esta guía de uso.</li>
                </ul>
            </li>

            <li><strong>Cerrar sesión:</strong> finaliza la sesión actual.</li>
        </ul>

        <h3>🔹 Estados de las órdenes</h3>
        <p>
            Cada orden pasa por diferentes <strong>estados</strong>:
        </p>
        <ol>
            <li><strong>Ingresado:</strong> cuando se crea la orden.</li>
            <li><strong>En Revisión:</strong> cuando el técnico comienza a revisarlo.</li>
            <li><strong>Reparado:</strong> cuando se termina la reparación (se agrega lo reparado y el gasto).</li>
            <li><strong>Entregado:</strong> cuando se cobra y se entrega el equipo.</li>
        </ol>

        <p>
            El cambio de estado se puede hacer desde el <strong>panel de cambio de estado</strong> o desde el
            <strong>editar orden</strong>. Depende del flujo que el usuario quiera usar.
        </p>

        <h3>🔹 Talón de ingreso</h3>
        <p>
            Cada vez que se crea una nueva orden, el sistema genera automáticamente un <strong>talón de ingreso</strong>.
            Este talón incluye:
        </p>
        <ul>
            <li>El <strong>nombre del cliente</strong>.</li>
            <li>El <strong>código público de seguimiento</strong> de la orden.</li>
            <li>La <strong>fecha de ingreso</strong> y el <strong>número de orden</strong>.</li>
        </ul>
        <p>
            Se imprimen <strong>dos copias</strong>: una para el cliente y otra para el técnico o el local.  
            Este talón sirve para consultar el estado del equipo posteriormente desde la sección de <strong>Consulta de Orden</strong>.
        </p>

        <h3>🔹 Factura de reparación</h3>
        <p>
            Una vez que la orden se encuentra en estado <strong>Finalizado</strong>, se habilita la opción de
            <strong>Generar Factura</strong>. Esta factura incluye:
        </p>
        <ul>
            <li>Los <strong>datos del cliente</strong>.</li>
            <li>El <strong>detalle del trabajo realizado</strong>.</li>
            <li>Los <strong>gastos o repuestos utilizados</strong>.</li>
            <li>El <strong>total a pagar</strong>.</li>
        </ul>
        <p>
            La factura puede <strong>imprimirse directamente</strong> desde el sistema para ser entregada al cliente
            junto con el equipo reparado.
        </p>

        <h3>🔹 Cierres diarios e históricos</h3>
        <p>
            En la sección <strong>Cierre Diario</strong> se genera el resumen de facturación del día.
            Solo se cuentan las órdenes con estado <em>Entregado</em>.
        </p>
        <p>
            En <strong>Cierres Históricos</strong> podés seleccionar un rango de fechas para ver
            cuánto se facturó entre esos días.
        </p>

        <h3>🔹 Consultar estado de orden</h3>
        <p>
            Desde el botón <strong>Más</strong> → <strong>Consultar estado de Orden</strong>,
            se puede ingresar un <em>código público</em> para ver el estado actual de una orden.
            En el futuro, este código podrá enviarse directamente por WhatsApp.
        </p>

        <h3>🔹 Roles y permisos</h3>
        <ul>
            <li><strong>Super Admin:</strong> tiene acceso total a todas las funciones del sistema.</li>
            <li><strong>Técnico:</strong> puede crear órdenes nuevas y ver las que están en estado <em>Ingresado</em> o <em>En Revisión</em>, nada más.</li>
        </ul>

        <p class="final">
            ✅ Con esto ya podés entender el funcionamiento completo del sistema:  
            desde el ingreso de una orden, la reparación, facturación y entrega,  
            hasta los cierres diarios e históricos.
        </p>
    </section>
</main>

<style>
main.content {
    max-width: 900px;
    margin: 30px auto;
    background: #000;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    color: #fff;
}

.manual h2, .manual h3 {
    color: #00c6ff;
    margin-bottom: 10px;
}

.manual ul, .manual ol {
    margin: 10px 0 20px 25px;
}

.manual li {
    margin-bottom: 6px;
}

.manual p {
    margin-bottom: 15px;
    line-height: 1.6;
}

.manual .final {
    font-weight: bold;
    color: #00ff9d;
    margin-top: 20px;
}
</style>

<?php include 'partial/footer.php'; ?>
