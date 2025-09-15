<?php
// seed_posts.php

// Load Composer's autoloader
require_once 'vendor/autoload.php';
// Include your database connection
require_once 'config/conexion.php';

// Initialize Faker for the 'en_US' locale (English)
$faker = Faker\Factory::create('en_US');


// --- CONFIGURATION ---
$numberOfPosts = 10; // How many posts to create?
$authorId = 5; // The ID of the user who will be the author of these posts
echo "Starting to seed the database with {$numberOfPosts} posts...\n";

// This is better than a fixed array. It gets real category IDs from your DB.
$categoryIdsResult = $conn->query("SELECT id FROM categorias");
$categoryIds = [];
if ($categoryIdsResult) {
    while ($row = $categoryIdsResult->fetch_assoc()) {
        $categoryIds[] = $row['id'];
    }
}

if (empty($categoryIds)) {
    die("Error: No categories found in the database. Please add some categories first.\n");
}

// This list should match the files in your /uploads/default-stock/ folder
$default_images = [
    'default-stock/alex-knight-2EJCSULRwC8-unsplash.jpg',
    'default-stock/alexandre-debieve-FO7JIlwjOtU-unsplash.jpg',
    'default-stock/gertruda-valaseviciute-xMObPS6V_gY-unsplash.jpg',
    'default-stock/glenn-carstens-peters-npxXWgQ33ZQ-unsplash.jpg',
    'default-stock/google-deepmind-LaKwLAmcnBc-unsplash.jpg',
    'default-stock/google-deepmind-Oy2yXvl1WLg-unsplash.jpg',
    'default-stock/luca-bravo-XJXWbfSo2f0-unsplash.jpg',
    'default-stock/marvin-meyer-SYTO3xs06fU-unsplash.jpg',
    'default-stock/nasa-Q1p7bh3SHj8-unsplash.jpg',
    'default-stock/possessed-photography-U3sOwViXhkY-unsplash.jpg',
];
// --- SEEDING LOGIC ---
$sql = "INSERT INTO articulos (titulo, contenido, imagen_destacada, categoria_id, autor_id, fecha_publicacion) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

for ($i = 0; $i < $numberOfPosts; $i++) {
     $title = $faker->catchPhrase();
    
    // Generate structured HTML content with English text
    $content = "<h2>" . $faker->sentence(5) . "</h2>" .
               "<p>" . $faker->paragraphs(4, true) . "</p>" .
               "<h3>" . $faker->sentence(6) . "</h3>" .
               "<blockquote><p>" . $faker->sentence(12) . "</p></blockquote>" .
               "<p>" . $faker->paragraphs(3, true) . "</p>" .
               "<ul><li>" . $faker->words(3, true) . "</li><li>" . $faker->words(3, true) . "</li><li>" . $faker->words(3, true) . "</li></ul>";

    // Randomly select a featured image from your list
    $featured_image = $faker->randomElement($default_images);
    
    // Randomly select a category ID from the ones you have in your database
    $category_id = $faker->randomElement($categoryIds);
    
    // Generate a random publication date within the last year
    $publication_date = $faker->dateTimeThisYear()->format('Y-m-d H:i:s');

    // Bind parameters and execute the query
    $stmt->bind_param("sssiis", $title, $content, $featured_image, $category_id, $authorId, $publication_date);
    $stmt->execute();
    
    echo "Created post # " . ($i + 1) . ": " . $title . "\n";
}

$stmt->close();
$conn->close();

echo "\nSeeding complete! {$numberOfPosts} posts have been added to the database.\n";
?>