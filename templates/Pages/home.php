<?php
// Disable layout
$this->disableAutoLayout();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $this->Html->meta('icon') ?>
    <title><?= $this->ContentBlock->text('home-page-title-tag') ?></title>
    <meta name="description" content="<?= $this->ContentBlock->text('home-meta-description') ?>">
    
    <!-- Custom styles -->
    <style>
        :root {
            --jenbury-blue: rgb(45, 177, 210);
            --jenbury-dark: rgb(12, 162, 200);
            --jenbury-purple: rgb(90, 50, 150);
            --jenbury-teal: rgb(20, 130, 150);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: system-ui, -apple-system, sans-serif;
            overflow-x: hidden;
            background-color: var(--jenbury-teal);
            color: white;
        }
        
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        
        .auth-buttons {
            display: flex;
            gap: 1rem;
        }
        
        .login-button, .signup-button, .logout-button {
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .login-button {
            background-color: transparent;
            color: white;
            border: 1px solid white;
        }
        
        .login-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        
        .logout-button {
            background-color: var(--jenbury-teal);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        
        .logout-button:hover {
            background-color: rgba(20, 130, 150, 0.8);
            transform: translateY(-2px);
        }
        
        .signup-button {
            background-color: var(--jenbury-purple);
            color: white;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        
        .signup-button:hover {
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            transform: translateY(-2px);
        }
        .container {
            position: relative;
            min-height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .background-container {
            position: absolute;
            inset: 0;
            z-index: 0;
        }
        
        .background-svg {
            width: 100%;
            height: 100%;
        }
        
        .path {
            stroke-linecap: round;
            opacity: 0.6;
        }
        
        .content {
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            text-align: center;
        }
        
        .title-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .title-main {
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: bold;
            line-height: 1.1;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .title-secondary {
            font-size: clamp(1.8rem, 6vw, 3.5rem);
            font-weight: 600;
            line-height: 1.1;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 0;
        }
        
        .title-word {
            display: inline-block;
            margin-right: 0.3rem;
        }
        
        .title-word:last-child {
            margin-right: 0;
        }
        
        .letter {
            display: inline-block;
            opacity: 0;
            transform: translateY(100px);
            letter-spacing: -0.02em;
            animation: letterAnimation 0.5s ease-out forwards;
            /* Animation delay is set inline for each letter */
        }
        
        .subtitle {
            font-size: clamp(1.2rem, 3vw, 1.5rem);
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            opacity: 0;
            animation: fadeInUp 1s ease-out 0.8s forwards;
        }
        
        .button-container {
            display: inline-block;
            background-color: var(--jenbury-purple);
            padding: 1px;
            border-radius: 1.15rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .button-container:hover {
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255,255,255,0.95);
            color: var(--jenbury-teal);
            font-size: 1.125rem;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 1.15rem;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid rgba(0,0,0,0.1);
            backdrop-filter: blur(4px);
        }
        
        .button:hover {
            background-color: white;
            transform: translateY(-2px);
        }
        
        .button span {
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        
        .button:hover span {
            opacity: 1;
        }
        
        .button .arrow {
            margin-left: 0.75rem;
            opacity: 0.7;
            transition: all 0.3s;
        }
        
        .button:hover .arrow {
            opacity: 1;
            transform: translateX(6px);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeInUp {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes letterAnimation {
            0% { transform: translateY(100px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes buttonAnimation {
            0% { transform: translateY(20px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
        
        @keyframes pathAnimation {
            0% { stroke-dashoffset: 1000; }
            100% { stroke-dashoffset: 0; }
        }
        
        @keyframes pathOffset {
            0% { stroke-dashoffset: 0; }
            50% { stroke-dashoffset: 500; }
            100% { stroke-dashoffset: 0; }
        }
        
    </style>
</head>
<body>
    <header class="header">
        <div class="auth-buttons">
            <?php if ($this->request->getAttribute('identity')): ?>
                <?= $this->Html->link(
                    $this->ContentBlock->text('home-logout-button-text'), // Use text()
                    ['controller' => 'Users', 'action' => 'logout'],
                    ['class' => 'logout-button']
                ) ?>
            <?php else: ?>
                <?= $this->Html->link(
                    $this->ContentBlock->text('home-login-button-text'), // Use text()
                    ['controller' => 'Users', 'action' => 'login'],
                    ['class' => 'login-button']
                ) ?>
                <?= $this->Html->link(
                    $this->ContentBlock->text('home-signup-button-text'), // Use text()
                    ['controller' => 'Users', 'action' => 'register'],
                    ['class' => 'signup-button']
                ) ?>
            <?php endif; ?>
        </div>
    </header>

    <div class="container">
        <div class="background-container">
            <svg class="background-svg" width="100%" height="100%" viewBox="0 0 696 316" fill="none">
                <title>Background Paths</title>
                <?php for($i = 0; $i < 36; $i++): ?>
                    <path
                        class="path path-<?= $i ?>"
                        d="M-<?= 380 - $i * 5 ?> -<?= 189 + $i * 6 ?>C-<?= 380 - $i * 5 ?> -<?= 189 + $i * 6 ?> -<?= 312 - $i * 5 ?> <?= 216 - $i * 6 ?> <?= 152 - $i * 5 ?> <?= 343 - $i * 6 ?>C<?= 616 - $i * 5 ?> <?= 470 - $i * 6 ?> <?= 684 - $i * 5 ?> <?= 875 - $i * 6 ?> <?= 684 - $i * 5 ?> <?= 875 - $i * 6 ?>"
                        stroke="<?= sprintf('rgb(255, 255, 255)') ?>"
                        stroke-width="<?= 0.5 + $i * 0.03 ?>"
                        stroke-opacity="<?= 0.1 + $i * 0.01 ?>"
                        style="stroke-dasharray: 1000; stroke-dashoffset: 1000;"
                    />
                <?php endfor; ?>
                
                <?php for($i = 0; $i < 36; $i++): ?>
                    <path
                        class="path path-reverse-<?= $i ?>"
                        d="M-<?= 380 - $i * -5 ?> -<?= 189 + $i * 6 ?>C-<?= 380 - $i * -5 ?> -<?= 189 + $i * 6 ?> -<?= 312 - $i * -5 ?> <?= 216 - $i * 6 ?> <?= 152 - $i * -5 ?> <?= 343 - $i * 6 ?>C<?= 616 - $i * -5 ?> <?= 470 - $i * 6 ?> <?= 684 - $i * -5 ?> <?= 875 - $i * 6 ?> <?= 684 - $i * -5 ?> <?= 875 - $i * 6 ?>"
                        stroke="<?= sprintf('rgb(255, 255, 255)') ?>"
                        stroke-width="<?= 0.5 + $i * 0.03 ?>"
                        stroke-opacity="<?= 0.05 + $i * 0.005 ?>"
                        style="stroke-dasharray: 1000; stroke-dashoffset: 1000;"
                    />
                <?php endfor; ?>
            </svg>
        </div>
        <div class="content">
            <div class="title-container">
                <h1 class="title-main">
                    <?php
                    // Use content block for the first part
                    $firstPart = $this->ContentBlock->text('home-main-heading-1'); // Use text()
                    $words = explode(" ", $firstPart); // Assuming spaces separate words for animation

                    foreach ($words as $wordIndex => $word) {
                        echo '<span class="title-word">';
                        $letters = str_split($word);
                        foreach ($letters as $letterIndex => $letter) {
                            $delay = ($wordIndex * 0.1) + ($letterIndex * 0.03);
                            echo '<span class="letter" style="animation-delay: ' . $delay . 's;">' . h($letter) . '</span>'; // Added h() for safety
                        }
                        echo '</span>';
                    }
                    ?>
                </h1>
                <h2 class="title-secondary">
                    <?php
                    // Use content block for the second part
                    $secondPart = $this->ContentBlock->text('home-main-heading-2'); // Use text()
                    $words = explode(" ", $secondPart); // Assuming spaces separate words for animation
                    $firstPartWordCount = count(explode(" ", $this->ContentBlock->text('home-main-heading-1'))); // Use text() & Recalculate for delay offset

                    foreach ($words as $wordIndex => $word) {
                        // Adjust word index to continue from the first part
                        $adjustedWordIndex = $wordIndex + $firstPartWordCount;

                        echo '<span class="title-word">';
                        $letters = str_split($word);
                        foreach ($letters as $letterIndex => $letter) {
                            $delay = ($adjustedWordIndex * 0.1) + ($letterIndex * 0.03);
                            echo '<span class="letter" style="animation-delay: ' . $delay . 's;">' . h($letter) . '</span>'; // Added h() for safety
                        }
                        echo '</span>';
                    }
                    ?>
                </h2>
            </div>

            <p class="subtitle">
                <?= $this->ContentBlock->text('home-subtitle') ?> <!-- Use text() -->
            </p>

            <div class="button-container" style="animation: buttonAnimation 1s ease-out forwards; animation-delay: 1s;">
                <?= $this->Html->link(
                    $this->ContentBlock->text('home-cta-button-text'), // Use text()
                    // Consider making the link target configurable via another block if needed
                    ['controller' => 'Courses', 'action' => 'index'],
                    ['class' => 'button', 'escape' => false] // Assuming button text might contain HTML like the arrow
                ) ?>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate paths
            const paths = document.querySelectorAll('.path');
            paths.forEach((path, index) => {
                // Initial animation to draw the path
                path.style.animation = `pathAnimation 3s ease-out forwards ${index * 0.05}s`;
                
                // After drawing, start the infinite animation
                setTimeout(() => {
                    path.style.animation = `pathOffset ${20 + Math.random() * 10}s linear infinite`;
                    path.style.opacity = '0.6';
                }, 3000 + (index * 50));
            });
            
            // Fade in the content container
            document.querySelector('.content').style.animation = 'fadeIn 2s ease-out forwards';
        });
    </script>
</body>
</html>
