<?php
// -- Bloque de depuración --
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Incluir el header.
require_once __DIR__ . '/_header.php';

?>

<!-- ======================================================= -->
<!-- ========= CONTENIDO "SOBRE MÍ" - DISEÑO ASIMÉTRICO ======== -->
<!-- ======================================================= -->
<div class="bg-white">
    <!-- CAMBIO: Se reduce el padding vertical (py-12) para acercar el contenido al header -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-5xl mx-auto">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-start">
                
                <!-- Columna de la Imagen (más ancha) -->
                <div class="md:col-span-4 flex justify-center md:justify-start">
                    <!-- CAMBIO: Foto mucho más grande (h-64 w-64) y cuadrada (rounded-xl) -->
                    <img class="h-64 w-64 rounded-xl object-cover shadow-lg" 
                         src="/blog_educativo_local/assets/img/foto_perfil.jpeg" 
                         alt="Foto de perfil del autor">
                </div>

                <!-- Columna del Texto (ahora todo alineado a la izquierda) -->
                <div class="md:col-span-8">
                    <!-- Encabezado de la Sección -->
                    <div class="mb-8">
                        <!-- CAMBIO: Título y subtítulo alineados a la izquierda -->
                        <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                            Sobre Mí
                        </h1>
                        <p class="mt-4 text-xl text-gray-600">
                            Mi trayectoria profesional y áreas de investigación.
                        </p>
                    </div>

                    <!-- Texto Biográfico -->
                    <div class="prose prose-lg text-gray-700 max-w-none">
                        <!-- Reemplaza este texto de ejemplo con tu biografía -->
                        <p>
                            Obtuvo el grado de Licenciatura en Ingeniería Cibernética en la Universidad La Salle, México en 1998 y los grados en Maestro y Doctor en Ciencias en la especialidad de Control Automático en CINVESTAV-IPN en 1999 y 2003, respectivamente. Sus áreas de investigación incluyen las Redes Neuronales Artificiales aplicadas a la identificación y control de sistemas, Visión por Computadora, Mecatrónica y la implementación sobre FPGAs de este tipo de algoritmos.
                        </p>
                        
                    </div>

                    <!-- Botón de Llamado a la Acción (Google Scholar) -->
                    <!-- CAMBIO: Botón alineado a la izquierda -->
                    <div class="mt-10">
                        <a href="https://scholar.google.com.mx/citations?user=zkApqcAAAAAJ&hl=en" 
                           target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-x-2 px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-transform hover:scale-105">
                             <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M12.14 11.372a.5.5 0 0 1-.235-.956l1.34-1.34a.5.5 0 0 1 .707.707l-1.34 1.34a.5.5 0 0 1-.472.249zM9.738 12.56a.5.5 0 0 1-.235-.956l.95-1.748a.5.5 0 0 1 .866.472l-.95 1.748a.5.5 0 0 1-.631.484zm-3.41-1.956a.5.5 0 0 1 0-.707l3.536-3.535a.5.5 0 0 1 .707.707L6.328 11.31a.5.5 0 0 1-.707 0z"/>
                                <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM2.047 5.106c.272.18.58.333.905.464l1.325 1.325A3.992 3.992 0 0 1 3.5 8c0 .542.109 1.054.308 1.523l-1.42 1.42A6.01 6.01 0 0 1 2.047 5.106zM6.257 4.29A4.017 4.017 0 0 1 8 3.5c.507 0 1.002.094 1.459.268l-1.388 1.388c-.28-.14-.577-.23-.889-.23-.312 0-.61.09-.889.23L6.257 4.29zm3.52 1.566c.28.14.577.23.889.23.312 0 .61-.09.889-.23l1.388-1.388A3.984 3.984 0 0 1 12.5 8c0 .542-.109 1.054-.308 1.523l-1.21-1.21c.14-.28.23-.577.23-.889s-.09-.61-.23-.889L9.777 5.856zM8 11.5c.312 0 .61-.09.889-.23l1.325 1.325c-.272.18-.58.333-.905.464L8 11.5zm-2.047-3.406c0-.312.09-.61.23-.889L4.973 5.996C4.833 6.276 4.743 6.58 4.743 6.9c0 .32.09.624.23.899l1.21 1.21c-.14.28-.23.577-.23.889s.09.61.23.889l-1.21 1.21c-.14-.28-.23-.577-.23-.889s.09-.61.23-.889l-1.42-1.42A3.984 3.984 0 0 1 3.5 8c0-.542.109-1.054.308-1.523L2.447 7.79A6.01 6.01 0 0 1 8 11.5c.312 0 .61-.09.889-.23l1.42-1.42c.14-.28.23-.577.23-.889s-.09-.61-.23-.889l1.21-1.21c.14.28.23.577.23.889s-.09.61-.23.889l-1.21-1.21A3.992 3.992 0 0 1 8 4.5c-.312 0-.61.09-.889.23L5.79 3.22A6.01 6.01 0 0 1 8 2.5c1.379 0 2.65.465 3.672 1.22l-1.325 1.325A3.992 3.992 0 0 1 8 4.5c-.312 0-.61.09-.889.23L5.79 3.22z"/>
                            </svg>
                            Ve mis Publicaciones en Google Scholar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Incluir el footer
require_once __DIR__ . '/_footer.php';
?>