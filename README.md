# AI3 - Sistema web de registro de clientes

Módulo web en PHP y MariaDB/MySQL para registrar y consultar clientes. Actividad Integradora 3 de Programación de Sistemas Web, Universidad Ecotec.

**Autor:** José Esteban Dávalos Yépez

## Funcionalidades

- **Registro (Create):** formulario HTML5 con validación en el navegador y en el servidor. La inserción usa sentencias preparadas de PDO.
- **Consulta (Read):** tabla responsiva con todos los registros. Cada dato se escapa con `htmlspecialchars()` antes de mostrarse.
- **Seguridad:**
  - Prevención de inyección SQL con sentencias preparadas reales (`PDO::ATTR_EMULATE_PREPARES = false`).
  - Prevención de XSS escapando todas las salidas.
  - Token CSRF en el formulario.
  - Credenciales fuera del repositorio.
  - Errores registrados en el log sin mostrarse al usuario.

## Tecnologías

- PHP 8.3 con PDO (`pdo_mysql`)
- MariaDB 11.4, compatible con MySQL 8
- HTML5 y CSS3
- Docker Compose (opcional) con phpMyAdmin

## Estructura

```
├── config/
│   ├── conexion.php           Conexión PDO con manejo de excepciones
│   └── config.example.php     Plantilla de credenciales para XAMPP o Laragon
├── database/
│   └── db_sistema_web.sql     Script DDL de la base de datos
├── public/                    Única carpeta expuesta por el servidor web
│   ├── index.php              Formulario de registro
│   ├── guardar.php            Procesa el formulario (validación e inserción)
│   ├── listado.php            Tabla de clientes registrados
│   └── css/estilos.css
├── src/
│   ├── bootstrap.php          Arranque común: errores, sesión, módulos
│   ├── helpers.php            Escape de salida, CSRF, mensajes y redirecciones
│   ├── ValidadorCliente.php   Validación del lado del servidor
│   └── ClienteRepositorio.php Acceso a datos con sentencias preparadas
├── views/                     Encabezado y pie compartidos
├── Dockerfile
└── docker-compose.yml
```

## Despliegue local

### Opción A: Docker

Requiere Docker Desktop.

1. Clona el repositorio y entra en la carpeta:
   ```
   git clone https://github.com/jedy-knight11/AI3_DisenoWeb_JoseDavalos.git
   cd AI3_DisenoWeb_JoseDavalos
   ```
2. Crea el archivo de credenciales y cambia las contraseñas:
   ```
   cp .env.example .env
   ```
3. Levanta los servicios:
   ```
   docker compose up -d --build
   ```
   La primera vez, MariaDB ejecuta automáticamente `database/db_sistema_web.sql`.
4. Abre la aplicación y, si quieres, phpMyAdmin:
   - Aplicación: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (usuario y contraseña del `.env`)

### Opción B: XAMPP, WAMP o Laragon

1. Copia la carpeta del proyecto en `htdocs` (XAMPP) o `www` (Laragon).
2. Inicia Apache y MySQL.
3. En phpMyAdmin, importa `database/db_sistema_web.sql`. Esto crea la base `db_sistema_web`.
4. Copia `config/config.example.php` como `config/config.php` y ajusta usuario y contraseña.
5. Abre `http://localhost/AI3_DisenoWeb_JoseDavalos/public/`.

En XAMPP, la extensión `pdo_mysql` viene activada por defecto. Si usas otra instalación de PHP, activa `extension=pdo_mysql` en `php.ini`.

## Archivos ignorados

`.gitignore` excluye `.env`, `config/config.php`, los logs y las carpetas de editores, para que no se publiquen credenciales ni archivos locales.
