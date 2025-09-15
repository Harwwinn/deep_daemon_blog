<?php
// Incluir el header, que ya inicia la sesión si es necesario
require_once __DIR__ . '/_header.php';
// Incluir la conexión a la base de datos
require_once __DIR__ . '/../config/conexion.php';

// --- DATOS DINÁMICOS DESDE LA BASE DE DATOS ---

// Obtener TODOS los colaboradores, ordenando a los fundadores primero, y luego alfabéticamente
$sql = "SELECT * FROM colaboradores ORDER BY es_fundador DESC, nombre ASC";
$result = $conn->query($sql);
$team_members = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>

<!-- ======================================================= -->
<!-- ========= CONTENIDO DE "SOBRE NOSOTROS" ======== -->
<!-- ======================================================= -->
<div class="bg-white py-12">
    <div class="container mx-auto px-3 sm:px-6 lg:px-8">
        
        <!-- Encabezado de la página -->
        <div class="max-w-4xl mx-auto text-center mb-16">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
                Sobre Nosotros
            </h1>
            <p class="mt-4 text-xl text-gray-600 text-justify">
                Somos un grupo de trabajo que busca vincular el desarrollo científico con soluciones industriales para generar tecnología de punta y capital humano de alto impacto.
            </p>
            <p class="mt-4 text-xl text-gray-600 text-justify">
                We are a working group that seeks to link scientific development with industrial solutions to generate cutting-edge technology and high-impact human capital.
            </p>
        </div>
        
        <!-- NUEVA SECCIÓN: Misión, Visión y Valores -->
        <div class="max-w-5xl mx-auto mb-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Tarjeta de Misión y Visión -->
                <div class="bg-gray-50 p-8 rounded-lg border border-gray-200 space-y-8">
                    <div>
                        <div class="flex items-center gap-x-3">
                            <div class="flex-shrink-0 bg-primary-100 p-2 rounded-full">
                                <svg class="h-6 w-6 text-primary-600" xmlns="http://www.w.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Visión / Vision</h2>
                        </div>
                        <p class="mt-3 text-gray-700 leading-relaxed text-justify">
                            Que el grupo de trabajo y sus integrantes sean un referente a nivel mundial en el desarrollo de tecnologías de punta a nivel científico, académico y comercial, capacitando a capital humano de excelente calidad y desarrollando proyectos con alto impacto comercial y social.
                        </p>
                        <p class="mt-3 text-gray-700 leading-relaxed text-justify">
                            The working group and its members become a global benchmark in the development of cutting-edge scientific, academic, and commercial technologies, training high-quality human capital, and developing projects with high commercial and social impact.
                        </p>
                    </div>
                    <div>
                        <div class="flex items-center gap-x-3">
                            <div class="flex-shrink-0 bg-primary-100 p-2 rounded-full">
                                <svg class="h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">Misión / Mission</h2>
                        </div>
                        <p class="mt-3 text-gray-700 leading-relaxed text-justify">
                            Desarrollar sistemas inteligentes basados en redes neuronales profundas que puedan ser distribuidos a usuarios reales, con el objetivo de favorecer una educación integral a los estudiantes del grupo de trabajo.
                        </p>
                        <p class="mt-3 text-gray-700 leading-relaxed text-justify">
                            Develop intelligent systems based on deep neural networks that can be deployed to real users, providing comprehensive education to students in the working group.
                        </p>
                    </div>
                </div>

                <!-- Tarjeta de Valores -->
                <div class="bg-primary-600 text-white p-8 rounded-lg shadow-lg">
                    <div class="flex items-center gap-x-3">
                        <div class="flex-shrink-0 bg-white/20 p-2 rounded-full">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" /></svg>
                        </div>
                        <h2 class="text-2xl font-bold">Nuestros Valores / Our Values</h2>
                    </div>
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Integridad:</strong> Actuamos con honestidad y ética en todo lo que hacemos.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Confianza:</strong> Construimos relaciones sólidas basadas en la confianza mutua.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Comunicaciones honestas y abiertas:</strong> Fomentamos un diálogo transparente y constructivo.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Pasión:</strong> Nos impulsa un deseo genuino de trabajar para hacer un cambio en el mundo.</span>
                        </li>
                    </ul>
                    <hr class="border-gray-200 my-6 mt-6">
                    <ul class="mt-6 space-y-4">
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Integrity:</strong> We act with honesty and ethics in everything we do.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Trust:</strong> We build strong relationships based on mutual trust.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Honest and Open Communication:</strong> We foster a transparent and constructive dialogue.</span>
                        </li>
                        <li class="flex items-start">
                            <span class="font-bold text-primary-200 mr-2">✓</span>
                            <span class="text-justify"><strong>Passion:</strong> We are driven by a genuine desire to make a difference in the world.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Sección de Fundadores (se muestra solo si existe al menos uno) -->
        <?php if (!empty($team_members)): ?>
        <!-- Cuadrícula de Miembros del Equipo -->
        <div class="max-w-5xl mx-auto space-y-12">
            
            <?php foreach ($team_members as $member): ?>
            <!-- Tarjeta de Miembro del Equipo -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12 items-center 
                        bg-gray-50 p-8 rounded-2xl border 
                        <?php echo $member['es_fundador'] ? 'border-primary-300' : 'border-gray-200'; // Borde de acento para fundadores ?>">
                
                <!-- Columna de la Imagen -->
                <div class="md:col-span-4 flex justify-center">
                    <div class="relative">
                        <img class="h-48 w-48 sm:h-56 sm:w-56 rounded-full object-cover shadow-lg" 
                             src="<?php echo BASE_URL; ?>/assets/img/<?php echo htmlspecialchars($member['foto']); ?>" 
                             alt="Foto de <?php echo htmlspecialchars($member['nombre']); ?>">
                        
                        <?php if ($member['es_fundador']): ?>
                        <span class="absolute bottom-2 right-2 block h-10 w-10 transform translate-x-1/2 translate-y-1/2 rounded-full border-4 border-white bg-primary-600 text-white flex items-center justify-center" title="Fundador">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10.868 2.884c.321.64.321 1.415 0 2.055L7.83 10.42a1.06 1.06 0 01-1.736.002L4.636 6.962a1.06 1.06 0 011.736-.002l1.03 1.796 2.07-4.141z" clip-rule="evenodd" />
                                <path d="M5.5 13a3.5 3.5 0 100-7 3.5 3.5 0 000 7z" />
                                <path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM4.5 10a5.5 5.5 0 1111 0 5.5 5.5 0 01-11 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna de Texto -->
                <div class="md:col-span-8">
                    <h2 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($member['nombre']); ?></h2>
                    <p class="text-lg font-medium text-primary-600 mt-1"><?php echo htmlspecialchars($member['rol']); ?></p>
                    
                    <?php if (!empty($member['bio'])): ?>
                        <p class="mt-4 text-gray-700 leading-relaxed text-justify"><?php echo nl2br(htmlspecialchars($member['bio'])); ?></p>
                    <?php endif; ?>
                    
                    <!-- Enlaces Sociales y Académicos (Dinámicos) -->
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        
                        <!-- Lógica para Google Scholar -->
                        <?php if (!empty($member['enlace_scholar'])): ?>
                        <a href="<?php echo htmlspecialchars($member['enlace_scholar']); ?>" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-x-2 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M12.14 11.372a.5.5 0 0 1-.235-.956l1.34-1.34a.5.5 0 0 1 .707.707l-1.34 1.34a.5.5 0 0 1-.472.249zM9.738 12.56a.5.5 0 0 1-.235-.956l.95-1.748a.5.5 0 0 1 .866.472l-.95 1.748a.5.5 0 0 1-.631.484zm-3.41-1.956a.5.5 0 0 1 0-.707l3.536-3.535a.5.5 0 0 1 .707.707L6.328 11.31a.5.5 0 0 1-.707 0z"/>
                                <path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM2.047 5.106c.272.18.58.333.905.464l1.325 1.325A3.992 3.992 0 0 1 3.5 8c0 .542.109 1.054.308 1.523l-1.42 1.42A6.01 6.01 0 0 1 2.047 5.106zM6.257 4.29A4.017 4.017 0 0 1 8 3.5c.507 0 1.002.094 1.459.268l-1.388 1.388c-.28-.14-.577-.23-.889-.23-.312 0-.61.09-.889.23L6.257 4.29zm3.52 1.566c.28.14.577.23.889.23.312 0 .61-.09.889-.23l1.388-1.388A3.984 3.984 0 0 1 12.5 8c0 .542-.109 1.054-.308 1.523l-1.21-1.21c.14-.28.23-.577.23-.889s-.09-.61-.23-.889L9.777 5.856zM8 11.5c.312 0 .61-.09.889-.23l1.325 1.325c-.272.18-.58.333-.905.464L8 11.5zm-2.047-3.406c0-.312.09-.61.23-.889L4.973 5.996C4.833 6.276 4.743 6.58 4.743 6.9c0 .32.09.624.23.899l1.21 1.21c-.14.28-.23.577-.23-.889s.09.61.23.889l-1.21 1.21c-.14-.28-.23-.577-.23-.889s.09-.61.23-.889l-1.42-1.42A3.984 3.984 0 0 1 3.5 8c0-.542.109 1.054.308-1.523L2.447 7.79A6.01 6.01 0 0 1 8 11.5c.312 0 .61-.09.889-.23l1.42-1.42c.14-.28.23-.577.23-.889s-.09-.61-.23-.889l1.21-1.21c.14.28.23.577.23.889s-.09.61.23.889l-1.21-1.21A3.992 3.992 0 0 1 8 4.5c-.312 0-.61.09-.889.23L5.79 3.22A6.01 6.01 0 0 1 8 2.5c1.379 0 2.65.465 3.672 1.22l-1.325 1.325A3.992 3.992 0 0 1 8 4.5c-.312 0-.61.09-.889.23L5.79 3.22z"/>
                            </svg>
                            Publicaciones
                        </a>
                        <?php endif; ?>

                        <!-- Lógica para Twitter -->
                        <?php if (!empty($member['enlace_twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($member['enlace_twitter']); ?>" target="_blank" rel="noopener noreferrer" 
                           class="inline-flex items-center gap-x-2 text-sm font-medium text-gray-600 hover:text-primary-600 transition-colors" title="Sitio Web">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" />
                            </svg>
                            Sitio Personal
                        </a>
                        <?php endif; ?>
                        
                        <!-- Lógica para LinkedIn -->
                        <?php if (!empty($member['enlace_linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($member['enlace_linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600" title="LinkedIn">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" /></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
        </div>
        <?php endif; ?>

        <!-- Sección de Colaboradores (se muestra solo si hay colaboradores) -->
        <?php if (!empty($collaborators)): ?>
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900">Conoce al Equipo</h2>
                <p class="mt-3 text-lg text-gray-600">
                    Las personas talentosas que hacen posible este proyecto.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($collaborators as $collab): ?>
                <div class="text-center bg-gray-50 p-8 rounded-lg border border-gray-200 hover:shadow-xl transition-shadow duration-300">
                    <img class="w-32 h-32 rounded-full mx-auto mb-4 object-cover" 
                         src="<?php echo BASE_URL; ?>/assets/img/<?php echo htmlspecialchars($collab['foto']); ?>" 
                         alt="Foto de <?php echo htmlspecialchars($collab['nombre']); ?>">
                    <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($collab['nombre']); ?></h3>
                    <p class="text-md font-medium text-primary-600"><?php echo htmlspecialchars($collab['rol']); ?></p>
                    
                    <!-- Enlaces a Redes Sociales -->
                    <div class="mt-4 flex justify-center space-x-4">
                        <?php if (!empty($collab['enlace_twitter'])): ?>
                        <a href="<?php echo htmlspecialchars($collab['enlace_twitter']); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.71v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($collab['enlace_linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars($collab['enlace_linkedin']); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-gray-600">
                            <span class="sr-only">LinkedIn</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" /></svg>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Incluir el footer
require_once __DIR__ . '/_footer.php';
?>