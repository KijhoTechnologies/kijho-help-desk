<html>
 <body>

# kijho-help-desk
Bundle para Symfony >= 2.8, para administrar tickets de soporte de clientes


<h1> Aplicación Demo</h1>

<a href="https://github.com/cesar-giraldo/kijho-help-desk/tree/help-desk-demo">Descargue la aplicación demo</a>

<h1>Instalación</h1>

Requisitos:

<pre style="font-family: Courier New;">
    "sensio/distribution-bundle": "~4.0",
    "symfony/swiftmailer-bundle": "~2.3",
</pre>


En la consola del proyecto ejecute:

<pre style="font-family: Courier New;">
    composer require kijho-technologies/kijho-help-desk dev-master
</pre>

Registramos el bundle en el archivo AppKernel.php
<pre style="font-family: Courier New;">
    $bundles = array(
        ...
        new Kijho\HelpDeskBundle\HelpDeskBundle(),
    );
</pre>

En la consola del proyecto ejecute:

<pre style="font-family: Courier New;">
    php app/console d:s:u --force
</pre>

<pre style="font-family: Courier New;">
    php app/console assets:install
</pre>

Editamos los proveedores de usuarios del bundle de tickets en el archivo config.yml

<pre style="font-family: Courier New;">
    # app/config.yml
    help_desk:
        client_provider: Acme\DemoBundle\Entity\YourClient
        operator_provider: Acme\DemoBundle\Entity\YourOperator
</pre>


En tus entidades proveedores de usuarios, debes implementar la siguiente interface:

<pre style="font-family: Courier New;">
    use Kijho\HelpDeskBundle\Model\UserInterface as HelpDeskUserInterface;
    
    /**
    * @ORM\Table()
    * @ORM\Entity
    */
   class YourClient implements HelpDeskUserInterface {
    
   }
</pre>

<pre style="font-family: Courier New;">
    use Kijho\HelpDeskBundle\Model\UserInterface as HelpDeskUserInterface;
    
    /**
    * @ORM\Table()
    * @ORM\Entity
    */
   class YourOperator implements HelpDeskUserInterface {
   
   }
</pre>

Al implementar la interface anterior, es necesario implementar las siguientes funciones:

<pre style="font-family: Courier New;">
    /**
     * Returns client or operator identifier
     * @return string
     */
    public function getId();

    /**
     * Returns client or operator name
     * @return string
     */
    public function getName();

    /**
     * Return client or operator email
     * @return string
     */
    public function getEmail();
    
    /**
     * Return boolean if the user is an allowed Ticket Operator
     * @return boolean
     */
    public function getIsTicketOperator();
</pre>


<strong>NOTA:</strong>
Es posible usar una misma entidad para ser el proveedor de usuarios de Clientes y Operadores.

Los Operadores son las personas que atenderán los Tickets de Soporte enviados por los clientes, 
para ello la función getIsTicketOperator() debe retornar true, o una variable booleana de la entidad para elegir
quienes de tus administradores podrán atender Tickets de Soporte.


Habilitamos el servicio "ticket_provider" en las variables globales de twig, archivo config.yml:

<pre style="font-family: Courier New;">
    # app/config.yml
    # Twig Configuration
    twig:
        globals:
            ticket_provider: @ticket_provider
</pre>

Asegurese de haber configurado el idioma:

<pre style="font-family: Courier New;">
    # app/config.yml
    parameters:
        locale: es

    framework:
        #esi:             ~
        translator:      { fallbacks: ["%locale%"] }
</pre>

Configura las rutas del bundle de soporte en el archivo routing.yml

<pre style="font-family: Courier New;">
    # app/routing.yml
    help_desk_clients:
        resource: "@HelpDeskBundle/Resources/config/routing/client.yml"
        prefix:   /help-desk-client
    
    help_desk_operator:
        resource: "@HelpDeskBundle/Resources/config/routing/operator.yml"
        prefix:   /help-desk-operator
</pre>

Configura tus reglas de control de accesos en el archivo security.yml

<pre style="font-family: Courier New;">
    access_control:
        - { path: '^/help-desk-client/*', roles: YOUR_CLIENT_ROLE }
        - { path: '^/help-desk-operator/*', roles: YOUR_ADMIN_ROLE }
</pre>


Para acceder al menú del cliente, crea un enlace en cualquiera de tus templates con la siguiente ruta:

<pre style="font-family: Courier New;">
    href="{{path('help_desk_client_tickets',{'status':'all'})}}"
</pre>


Para acceder al menú del operador, crea un enlace en cualquiera de tus templates con la siguiente ruta:

<pre style="font-family: Courier New;">
    href="{{path('help_desk_operator_tickets',{'status':'all'})}}"
</pre>

<strong>NOTA:</strong>
Puedes modificar el enlace para abrir el modulo en un iframe, en una ventana emergente o como lo desees.


Desde tus plantillas Twig, puedes acceder a diferentes funciones para obtener información acerca de los Tickets de soporte:

Para el cliente:
<pre style="font-family: Courier New;">
    {# Número total de tickets del cliente #}
    {{ ticket_provider.getCountClientTickets(app.user.id) }}

    {# Número de tickets activos del cliente #}
    {{ticket_provider.getCountClientTickets(app.user.id, 'active')}}

    {# Número de tickets cerrados del cliente #}
    {{ticket_provider.getCountClientTickets(app.user.id, 'closed')}}
</pre>

Para el administrador:
<pre style="font-family: Courier New;">
    {# Número total de tickets #}
    {{ticket_provider.getCountTickets()}}

    {# Número de tickets activos #}
    {{ticket_provider.getCountTickets('active')}}

    {# Número de tickets cerrados #}
    {{ticket_provider.getCountTickets('closed')}}
</pre>


<strong>Categorías de Tickets y Notificaciones por Correo:</strong>
Para que un cliente pueda crear tickets de soporte, debe haber existir en base de datos al menos una categoría de tickets.
Estas categorías son administradas por los operadores en su respectivo modulo, asi que una vez tengas 
todo configurado asegurate de crear tus categorías de tickets y colocar los emails a donde llegarán las
notificaciones por correo electrónico.

</body>
</html>