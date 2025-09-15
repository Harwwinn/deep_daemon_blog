<?php
// Iniciar la sesión si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deepdaemon</title>
    <link href="/src/output.css" rel="stylesheet">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/png" href="/my-favicon/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/my-favicon/favicon.svg" />
    <link rel="shortcut icon" href="/my-favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/my-favicon/apple-touch-icon.png" />
    <link rel="manifest" href="/my-favicon/site.webmanifest" />
    <!-- Alpine.js para la interactividad del dropdown -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center py-10">
                
                <a href="/index.php">
                    <img class="h-40 w-auto" src="<?php echo BASE_URL; ?>/assets/img/donny_final_logo_transparent.png" alt="Logo de Deepdaemon">
                </a>
                
                <p class="text-sm text-gray-500 mt-2">By Marco A. Moreno Armendariz</p>
                
                <!-- Navegación Unificada y Centrada -->
                <!-- CAMBIO: Aumentamos el espaciado horizontal (gap-x-8 md:gap-x-12) -->
                <nav class="flex flex-wrap justify-center items-center gap-x-8 md:gap-x-12 gap-y-4 pt-4">
                    <!-- CAMBIO: Aumentamos el tamaño de la fuente (text-lg) y el grosor (font-semibold) -->
                    <a href="/index.php" class="text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">Home</a>
                    <a href="/homepage/about-us.php" class="text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">About us</a>
                    <a href="/homepage/faq.php" class="text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">FAQ</a>
                    <a href="/homepage/contact.php" class="text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">Contact us</a>
                    
                    <!-- Lógica de usuario integrada en la navegación -->
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        
                        <div x-data="{ open: false }" class="relative">
                            <!-- CAMBIO: Aplicamos los mismos estilos de tamaño y grosor al botón del menú -->
                            <button @click="open = !open" class="flex items-center space-x-2 text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">
                                <?php
                                // Lógica para determinar la ruta del avatar
                                $avatar_path = BASE_URL . '/assets/img/avatars/avatar10.svg'; // Un avatar por defecto
                                if (!empty($_SESSION['user_avatar'])) {
                                    $avatar_path = BASE_URL . '/assets/img/avatars/' . htmlspecialchars($_SESSION['user_avatar']);
                                }
                                ?>
                                <img class="h-8 w-8 rounded-full border-2 border-gray-300 object-cover" src="<?php echo $avatar_path; ?>" alt="Avatar del usuario">
                                <span>My Account</span>
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                            
                            <!-- El dropdown no cambia -->
                            <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10" style="display: none;">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm">Logged as</p>
                                    <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['user_nombre']); ?></p>
                                </div>
                                <a href="/blogging/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Dashboard</a>
                                <a href="/blogging/write_blog.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">New Post</a>
                                <div class="border-t border-gray-100"></div>
                                <a href="/logout.php" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">Log out</a>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="flex items-center space-x-4">
                            <!-- CAMBIO: Aplicamos los mismos estilos de tamaño y grosor a los botones de visitante -->
                            <a href="/login/login.php" class="text-lg font-semibold text-gray-700 hover:text-primary-600 transition-colors">Log in</a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    <main>