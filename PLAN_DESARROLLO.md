# 📋 Plan de Desarrollo - Sistema de Gestión de Gasolina

## Descripción General
Sistema web para la gestión y control de consumo de gasolina de vehículos

---

## Requerimento

Manten nombres de rutas, controladores, archivos, migraciones etc en ingles 

Debe utilizar tailwind como base para los estilos

Debe utilizar auth0 de google con estas rutas

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::get('/login/google', [LoginController::class, 'redirectToProvider'])->name('login.redirect');
Route::get('/auth/google/callback', [LoginController::class, 'handleProviderCallback'])->name('login.callback');
Route::get('/logout', [LoginController::class, 'logout'])->name('login.logout');

Asegurarse que los campos de la bd esten en ingles manteniendo los estándares

La tabla del reporte mensual en excel actualmente contiene los siguientes campos:
 - Folio 
 - Unidad
 - Fecha
 - Regreso
 - Conductor
 - D/N
 - N/P 
 - Proveedor o cliente
 - Descripcion
 - Destino
 - Kilometraje inicial
 - Kilometraje final
 - Kilometraje recorridos
 - Consumo
 - Costo
 - Kilometraje x litro
 - Precio Gasolina
 - Precio Diesel

La tabla de relacion costo de gasolina por vehicula en excel actualmente tiene las siguientes caracteristicas:
 - Unidad(Vehiculo se usa el id)
 - Gasolina (Precio)
 - Rendimiento (km x litro)

La tabla del reporte por auto de excel actualmente tiene las siguientes caracteristicas
 - Fecha
 - Conductor
 - Proyecto
 - Kilometraje inicial
 - Kilometrake final
 - Kilometros recorridos
 - Litros
 - Costo
 - Kilómetros x litro
 - Monto
 - Evidencia (imagen/imagenes)

Considerando que los datos estan en un excel se deben tener en cuenta las siguientes tablas:
 - Tabla de usuarios
 - Tabla de conductores (relacionada con el user_id)
 - Tabla de proyecto
 - Tabla de autos (debe incluir la capacidad del tanque y demas caracteristicas propias de un vehiculo)
 - Tabla de rendimiento/gasolina por vehiculo (relacionada con el auto_id)
 - Tabla del tipo de combustible (gasolina/diesel)
 
Considera los atributos y caracteristicas  que llegansen a faltar dentro de cada tabla apartir de la tabla del reporte por auto de excel y de la tabla del reporte mensual en excel, al final se debe llegar a estas tablas para sacar el reporte mensual general y por vehiculo.

Utiliza laratrust para el sistema de roles y permisos, por defecto vas a crear el usuario con el rol admin.

Prepara el sistema para incorporar otro rol, como el encargado de vehiculo, de momento no trabajes con permisos especificos por rol.

A nivel UI

La pantalla de login debe utilizar auth0 de google, el boton debe indicar "Iniciar sesion con google" retornando a la ruta login.redirect 

Al iniciar sesion el usuario admin va a ver el dashboard general donde puede ver el reporte general con los movimientos del mes (en la parte superior debe existir un filtro que le permita ver todas, filtrar por mes y  buscar en especifico) , puedes usar datatables.js para los filtros internos de la tabla. En el mismo dashboard el usuario admin debe poder visualizar la tabla del rendimiento por litro de los vehiculos y la tabla de cada vehiculo, cada una separada por tabs. 

Considera las mejores practicas de UX/UI para el diseño de este módulo, asi como a nivel de practicas de programación, que el código se mantenga limpio y simple, siguiendo el principio KISS, que sea entendible por usuarios nuevos en laravel y de seguridad, no expongas credenciales en el codigo, mantenlas en el env de ser necesario , la base de datos debe estar en ingles y normalizada las tablas.

A nivel corporativo la brand de la marca es en colores rojos y amarillos, para que realices una mezcla adecuada de los mismos, siguiente los principios de colorimetría, el logo esta en la ruta storage\app\public\img\logo.png

El header y footer deben estar separados del archivo principal, importados directamente en el, debe contar con el logo a la izquierda, y a la derecha la foto del usuario (obtenida desde google), su nombre  y el botón de cerrar sesion.

Si el usuario inicia sesion y el usuario no existe en la bd automaticamente se debe crear, debe validar el correo.

En otra pagina del dashboard debe existir el CRUD tanto para autos y usuarios, el CRUD debe ser funcional para ambos casos, asegurate que asi sea, trata de manejar modales para no tener que estar pasando de una pagina a otra, por ejemplo cuando edito un usuario, puedes usar flowbite para la ui.

