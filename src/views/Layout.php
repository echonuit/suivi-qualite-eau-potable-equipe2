<?php

namespace EauPotable\views;

class Layout
{
    /**
     * @param array<string> $styles Chemins des feuilles de style CSS.
     */
    public function __construct(
        private string $titre,
        private string $description,
        private string $contenu,
        private array $styles = [],
    ) {}

    public function afficher(): string
    {
        ob_start();?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="description" content="<?php echo htmlspecialchars($this->description) ?>"/>
    <title><?php echo htmlspecialchars($this->titre) ?></title>
    <link rel="stylesheet" href="/styles/global.css">
    <?php foreach ($this->styles as $style): ?>
        <link rel="stylesheet" href="/src/styles/<?php echo htmlspecialchars($style) ?>.css">
    <?php endforeach; ?>
</head>
<body>
<header>
    <!-- HEADER -->
</header>
<main>
    <?php echo '<h1>' . $this->titre . '</h1>' ?>
    <?php echo $this->contenu; ?>
</main>
<footer class="footer">
    <!-- FOOTER -->
</footer>
</body>
</html>
    <?php
    return ob_get_clean();
    }
}
