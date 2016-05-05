<html>
 <body>

# kijho-help-desk
Bundle para Symfony >= 2.8, para administrar tickets de soporte de clientes


<h1>Instalación de Aplicación Demo</h1>

Cree un proyecto symfony 2.8 en su entorno de desarrollo, para ello ejecute en su consola:

<pre style="font-family: Courier New;">symfony new kijho-help-desk-demo 2.8</pre>

Descargue el archivo .zip con la aplicacion.

Descromprima el archivo .zip y copie los archivos en la carpeta del proyecto creado anteriormente (reemplace archivos y fusione carpetas)

Mediante su consola de comandos, ingrese a la carpeta del proyecto y ejecute los siguientes comandos:

<pre style="font-family: Courier New;">composer self-update</pre>

<pre style="font-family: Courier New;">composer update</pre>

Cree una base de datos por ejemplo "demo_help_desk_db".

Configure en el archivo parameters.yml los parametros de base de datos y envio de correos:

<pre style="font-family: Courier New;">
# app/config/parameters.yml
parameters:
    database_host: 127.0.0.1
    database_port: null
    database_name: demo_help_desk_db
    database_user: root
    database_password: ***
    mailer_transport: smtp
    mailer_host: 127.0.0.1
    mailer_user: example@domain.com
    mailer_password: your_password
    ...
</pre>

Mediante su consola de comandos, ejecute los siguientes comandos:

<pre style="font-family: Courier New;">php app/console d:s:u --force</pre>

<pre style="font-family: Courier New;">php app/console assets:install</pre>

<pre style="font-family: Courier New;">php app/console doctrine:fixtures:load </pre>

Una vez realizados los pasos anteriores ya podrá acceder a la aplicación:

<a href="http://localhost/kijho-help-desk-demo/web/app_dev.php/es/login" target="_blank">http://localhost/kijho-help-desk-demo/web/app_dev.php/es/login</a>

Por defecto el sistema crea dos usuarios, un cliente y operador respectivamente.

Los datos de acceso son los siguientes:

<pre style="font-family: Courier New;">
    email: client@example.com
    password: client
</pre>

<pre style="font-family: Courier New;">
    email: operator@example.com
    password: operator
</pre>

<strong>NOTA:</strong>
Si requiere mas clientes u operadores para objeto de pruebas, puede crearlos manualmente en la base de datos.

<h1>Uso</h1>

<h3>Cliente</h3>

Funcionalidades del cliente:
<ul>
    <li>Crear nuevos Tickets de Soporte</li>
    <li>Ver el listado de Tickets enviados</li>
    <li>Ver el listado de Tickets activos</li>
    <li>Ver el listado de Tickets cerrados</li>
    <li>Realizar comentarios sobre un Ticket activo</li>
    <li>Cerrar Tickets</li>
</ul>

<h3>Operador</h3>
Funcionalidades del operador:
<ul>
    <li>Ver el listado de Tickets enviados por los clientes</li>
    <li>Ver el listado de Tickets activos</li>
    <li>Ver el listado de Tickets cerrados</li>
    <li>Realizar comentarios sobre un Ticket activo para dar solucion a un cliente</li>
    <li>Administrar categorias de Tickets</li>
</ul>

<strong>NOTA:</strong>
En las categorias de Tickets se configura el correo electronico a donde llegaran los tickets de los clientes
que se envian a cada categoria.

Para motivos de pruebas puede configurar el archivo config_dev.yml para que todos los correos sean desviados a una dirección:

<pre style="font-family: Courier New;">
    # app/config/config_dev.yml
    swiftmailer:
    delivery_address: example@domain.com
</pre>

</body>
</html>