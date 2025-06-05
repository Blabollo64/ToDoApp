<?php
// Set limit and get offset from query string
$limit = 20;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

// Fetch data from PokéAPI
$api_url = "https://pokeapi.co/api/v2/pokemon?limit=$limit&offset=$offset";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

// Get list of Pokémon
$pokemon_list = $data['results'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pokémon List</title>
  <style>
    body { font-family: Arial; background: #f0f0f0; text-align: center; }
    .pokemon-container { display: flex; flex-wrap: wrap; justify-content: center; }
    .pokemon { margin: 10px; padding: 10px; background: #fff; border-radius: 8px; width: 150px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
    img { width: 100px; height: 100px; }
    a { text-decoration: none; color: #0077cc; margin: 10px; }
  </style>
</head>
<body>
  <h1>Pokémon List</h1>
  <div class="pokemon-container">
    <?php foreach ($pokemon_list as $pokemon): ?>
      <?php
        // Extract ID from the Pokémon URL
        preg_match('/\/pokemon\/(\d+)\//', $pokemon['url'], $matches);
        $id = $matches[1];
        $image_url = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/$id.png";
      ?>
      <div class="pokemon">
        <img src="<?= $image_url ?>" alt="<?= $pokemon['name'] ?>">
        <p><?= ucfirst($pokemon['name']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div>
    <?php if ($offset > 0): ?>
      <a href="?offset=<?= $offset - $limit ?>">&laquo; Previous</a>
    <?php endif; ?>
    <a href="?offset=<?= $offset + $limit ?>">Next &raquo;</a>
  </div>
</body>
</html>
