<?php
/**
 * Jenbury Financial - About Page
 */
$this->assign('title', 'About Us');
?>

<div class="about-page">
    <div class="page-header">
        <h1>About Jenbury Financial</h1>
    </div>

    <div class="row">
        <div class="column">
            <h2>Our Story</h2>
            <p>Jenbury Financial was founded by Andrea Jenkins, a seasoned financial advisor with over 15 years of
                experience in the industry. Throughout her career, Andrea noticed a significant gap in financial
                education that was accessible and affordable for everyday people.</p>

            <p>After years of providing personalized financial planning services, Andrea realized that many of the
                principles and strategies she taught her clients could be shared more broadly through structured
                educational content. This realization led to the birth of Jenbury Financial's online learning platform.
            </p>

            <p>Today, Jenbury Financial is a trusted name in financial education, helping thousands of individuals gain
                the knowledge and confidence they need to make informed financial decisions.</p>
        </div>
        <div class="column">
            <div class="card">
                <img src="https://via.placeholder.com/400x300" alt="Andrea Jenkins" class="img-responsive">
                <div class="card-body">
                    <h3>Andrea Jenkins</h3>
                    <p class="subtitle">Founder & Lead Financial Advisor</p>
                    <p>With a background in finance and a passion for education, Andrea has dedicated her career to
                        making financial knowledge accessible to everyone.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Our Mission</h2>
        <p>At Jenbury Financial, our mission is to empower individuals with the knowledge and tools they need to take
            control of their financial future. We believe that financial education should be:</p>

        <div class="row">
            <div class="column">
                <div class="feature-item">
                    <h3>Accessible</h3>
                    <p>Available to everyone, regardless of their background or current financial situation.</p>
                </div>
            </div>
            <div class="column">
                <div class="feature-item">
                    <h3>Affordable</h3>
                    <p>Priced fairly to ensure that financial education is not a luxury but a necessity within reach.
                    </p>
                </div>
            </div>
            <div class="column">
                <div class="feature-item">
                    <h3>Practical</h3>
                    <p>Focused on real-world applications that can be implemented immediately to improve financial
                        outcomes.</p>
                </div>
            </div>
            <div class="column">
                <div class="feature-item">
                    <h3>Comprehensive</h3>
                    <p>Covering all aspects of personal finance, from budgeting to retirement planning and everything in
                        between.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <h2>Our Approach</h2>
        <p>We believe in a modular approach to financial education. Our courses are broken down into focused modules
            that allow you to learn at your own pace and focus on the areas that are most relevant to your current
            financial situation.</p>

        <p>Each module is designed by financial experts and includes:</p>
        <ul>
            <li>Clear, jargon-free explanations of financial concepts</li>
            <li>Practical examples and case studies</li>
            <li>Interactive tools and calculators</li>
            <li>Actionable steps you can take to implement what you've learned</li>
            <li>Resources for further learning and exploration</li>
        </ul>

        <p>Our goal is not just to provide information, but to facilitate transformation—helping you develop the
            knowledge, skills, and habits that lead to financial well-being.</p>
    </div>

    <div class="section">
        <h2>Our Values</h2>

        <div class="row">
            <div class="column">
                <div class="value-item">
                    <h3>Integrity</h3>
                    <p>We are committed to providing honest, unbiased financial education that puts your interests
                        first.</p>
                </div>
            </div>
            <div class="column">
                <div class="value-item">
                    <h3>Empowerment</h3>
                    <p>We believe in giving you the knowledge and tools to make your own informed financial decisions.
                    </p>
                </div>
            </div>
            <div class="column">
                <div class="value-item">
                    <h3>Inclusivity</h3>
                    <p>We design our content to be accessible and relevant to people from all walks of life.</p>
                </div>
            </div>
            <div class="column">
                <div class="value-item">
                    <h3>Excellence</h3>
                    <p>We strive for the highest quality in all our educational materials and continuously update our
                        content to reflect current best practices.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <div class="cta-content">
            <h2>Ready to Start Your Financial Journey?</h2>
            <p>Join thousands of students who are taking control of their financial future.</p>
            <?= $this->Html->link('Explore Our Courses', ['controller' => 'Courses', 'action' => 'index'], ['class' => 'button']) ?>
        </div>
    </div>
</div>

<style>
    :root {
        --primary: #7B2FF7;
        --primary-light: #C084FC;
        --background: #F9F8FF;
        --text: #2D1E46;
        --text-light: #7D6DAE;
        --card-bg: #ffffff;
        --button-bg: #7B2FF7;
        --button-text: #ffffff;
        --border-radius: 12px;
    }

    .about-page {
        background: var(--background);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--text);
        line-height: 1.6;
    }

    .about-page .page-header {
        text-align: center;
        padding: 4rem 1rem 2rem;
        animation: fadeInDown 1s ease;
    }

    .about-page .page-header h1 {
        font-size: 2.8rem;
        color: var(--primary);
        margin: 0;
    }

    .about-page .row {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        justify-content: space-between;
        margin: 2rem 0;
        padding: 0 1rem;
    }

    .about-page .column {
        flex: 1 1 45%;
        min-width: 280px;
    }

    .about-page .card {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease;
        animation: fadeInUp 1s ease;
    }

    .about-page .card:hover {
        transform: translateY(-5px);
    }

    .about-page .card img {
        width: 100%;
        display: block;
        height: auto;
    }

    .about-page .card-body {
        padding: 1.5rem;
    }

    .about-page .card-body h3 {
        margin-top: 0;
        color: var(--primary);
    }

    .about-page .card-body .subtitle {
        font-weight: 600;
        color: var(--text-light);
        margin-bottom: 1rem;
    }

    .about-page .section {
        padding: 3rem 1rem;
        background: var(--card-bg);
        margin-bottom: 2rem;
        border-radius: var(--border-radius);
        animation: fadeIn 1.5s ease;
    }

    .about-page .section h2 {
        color: var(--primary);
        margin-bottom: 1rem;
        letter-spacing: 0.5px;
        word-spacing: 1px;
    }

    .about-page .feature-item,
    .about-page .value-item {
        background: var(--background);
        border-left: 5px solid var(--primary);
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 16px rgba(123, 47, 247, 0.05);
        transition: transform 0.3s ease;
        animation: fadeInUp 1.2s ease;
    }

    .about-page .feature-item:hover,
    .about-page .value-item:hover {
        transform: scale(1.02);
    }

    .about-page ul {
        padding-left: 1.5rem;
    }

    .about-page ul li {
        margin-bottom: 0.5rem;
    }

    .about-page .cta-section {
        text-align: center;
        padding: 4rem 2rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        color: var(--button-text);
        border-radius: var(--border-radius);
        margin: 2rem 1rem;
        animation: fadeInUp 1.5s ease;
    }

    .about-page .cta-section h2 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .about-page .cta-section p {
        font-size: 1.2rem;
        margin-bottom: 2rem;
    }

    .about-page .cta-section .button {
        display: inline-block;
        padding: 0.75rem 2rem;
        background: var(--button-text);
        color: var(--primary);
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .about-page .cta-section .button:hover {
        background: #fff;
        color: #6b00b3;
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .about-page .row {
            flex-direction: column;
        }

        .about-page .cta-section {
            padding: 3rem 1rem;
        }
    }
</style>