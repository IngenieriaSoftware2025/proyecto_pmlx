<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Usuarios;
use MVC\Router;

class AppController
{
    public static function index(Router $router)
    {
        $router->render('login/index', [], 'layouts/login');
    }

    public static function logout()
    {
        isAuth();
        $_SESSION = [];
        session_destroy();
        $login = $_ENV['APP_NAME'];
        header("Location: /$login");
    }



    public static function renderInicio(Router $router)
    {
        hasPermission(['ADMIN']);
        
        $router->render('pages/index', [], 'layouts/inicio');
    }

    public static function isAuthenticated()
    {
        session_start();
        return isset($_SESSION['usuario_id']);
    }

    public static function getCurrentUser()
    {
        session_start();
        if (isset($_SESSION['usuario_id'])) {
            return Usuarios::find($_SESSION['usuario_id']);
        }
        return null;
    }

    public static function crearUsuario()
    {
        getHeadersApi();

        try {
            $nombre_usuario = htmlspecialchars($_POST['nombre_usuario']);
            $password = htmlspecialchars($_POST['password']);
            $nombre_completo = htmlspecialchars($_POST['nombre_completo']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $telefono = htmlspecialchars($_POST['telefono']);
            $id_rol = filter_var($_POST['id_rol'], FILTER_SANITIZE_NUMBER_INT);

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $usuario = new Usuarios([
                'nombre_usuario' => $nombre_usuario,
                'password' => $passwordHash,
                'nombre_completo' => $nombre_completo,
                'email' => $email,
                'telefono' => $telefono,
                'id_rol' => $id_rol,
                'activo' => 'T'
            ]);

            $resultado = $usuario->guardar();

            if ($resultado) {
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario creado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al crear usuario'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al crear usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function login()
    {
        getHeadersApi();

        try {
            $nombre_usuario = htmlspecialchars($_POST['nombre_usuario'] ?? '');
            $password = htmlspecialchars($_POST['password'] ?? '');

            if (empty($nombre_usuario) || empty($password)) {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Por favor llena todos los campos'
                ]);
                return;
            }

            $query = "SELECT id_usuario, nombre_usuario, password, nombre_completo, email, id_rol FROM usuarios WHERE nombre_usuario = '$nombre_usuario' AND activo = 'T'";
            $resultado = ActiveRecord::fetchArray($query);

            if (empty($resultado)) {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado o inactivo'
                ]);
                return;
            }

            $usuario = $resultado[0];
            
            $debug_info = [
                'usuario_enviado' => $nombre_usuario,
                'password_enviado' => $password,
                'longitud_password_enviado' => strlen($password),
                'password_en_bd' => $usuario['password'],
                'longitud_password_bd' => strlen($usuario['password']),
                'empieza_con_dollar2y' => (strpos($usuario['password'], '$2y$') === 0)
            ];
            
            $passwords_comunes = ['123456', 'juanita', 'password', '12345', '1234'];
            $test_results = [];
            
            foreach ($passwords_comunes as $test_pass) {
                $test_results[$test_pass] = password_verify($test_pass, $usuario['password']);
            }
            
            $debug_info['test_passwords'] = $test_results;
            
            $verify_result = password_verify($password, $usuario['password']);
            $debug_info['password_verify_result'] = $verify_result;
            
            if ($verify_result) {
                session_start();
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
                $_SESSION['nombre_completo'] = $usuario['nombre_completo'];

                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario logueado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'La contraseña que ingresó es incorrecta',
                    'debug' => $debug_info
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al intentar loguearse',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function hashearPassword()
    {
        getHeadersApi();
        
        $contraseña = "123456";
        $hash = password_hash($contraseña, PASSWORD_DEFAULT);
        
        echo json_encode([
            'contraseña_original' => $contraseña,
            'contraseña_hasheada' => $hash,
            'consulta_sql' => "UPDATE usuarios SET password = '$hash' WHERE nombre_usuario = 'juanita';"
        ]);
    }

    public static function actualizarPasswordsExistentes()
    {
        getHeadersApi();
        
        try {
            $query = "SELECT id_usuario, nombre_usuario, password FROM usuarios WHERE activo = 'T'";
            $usuarios = ActiveRecord::fetchArray($query);
            
            $actualizados = 0;
            $errores = [];
            
            foreach ($usuarios as $usuario) {
                $passwordActual = $usuario['password'];
                
                if (strpos($passwordActual, '$2y$') !== 0) {
                    
                    $passwordHasheado = password_hash($passwordActual, PASSWORD_DEFAULT);
                    
                    $updateQuery = "UPDATE usuarios SET password = '$passwordHasheado' WHERE id_usuario = " . $usuario['id_usuario'];
                    
                    $resultado = ActiveRecord::SQL($updateQuery);
                    
                    if ($resultado) {
                        $actualizados++;
                        echo "✅ Usuario '{$usuario['nombre_usuario']}' actualizado<br>";
                    } else {
                        $errores[] = $usuario['nombre_usuario'];
                        echo "❌ Error al actualizar '{$usuario['nombre_usuario']}'<br>";
                    }
                } else {
                    echo "⏭️ Usuario '{$usuario['nombre_usuario']}' ya tiene contraseña hasheada<br>";
                }
            }
            
            echo "<br><strong>Resumen:</strong><br>";
            echo "✅ Usuarios actualizados: $actualizados<br>";
            if (!empty($errores)) {
                echo "❌ Errores en: " . implode(', ', $errores) . "<br>";
            }
            
            echo "<br><em>¡Ahora todos los usuarios pueden hacer login con sus contraseñas originales!</em>";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

   public static function inicio(Router $router)
{
    verificarLogin(); 
    
    
    $router->render('pages/index',[]);
}

public static function sinPermisos(Router $router)
{
    $router->render('pages/sin-permisos', []);
}

}