<?php
/**
 * Jenbury Financial - FAQ Page
 */
$this->assign('title', 'Frequently Asked Questions');
?>

<div class="page-header">
    <h1>Frequently Asked Questions</h1>
</div>

<div class="faq-section">
    <h2>General Questions</h2>
    
    <div class="faq-item">
        <h3>What is Jenbury Financial?</h3>
        <div class="faq-answer">
            <p>Jenbury Financial is an online financial education platform founded by Andrea Jenkins. We offer modular financial courses designed to help individuals gain the knowledge and confidence they need to make informed financial decisions.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Who are your courses designed for?</h3>
        <div class="faq-answer">
            <p>Our courses are designed for anyone who wants to improve their financial literacy, regardless of their current knowledge level. Whether you're just starting your financial journey or looking to deepen your understanding of specific financial topics, we have courses that can help you achieve your goals.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Do I need any prior financial knowledge to take your courses?</h3>
        <div class="faq-answer">
            <p>No, you don't need any prior financial knowledge. Our courses are designed to be accessible to beginners while still providing value to those with more experience. Each course starts with the fundamentals before moving on to more advanced concepts.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Are your courses accredited?</h3>
        <div class="faq-answer">
            <p>Our courses are designed for educational purposes and personal development. While they are not formally accredited, they are created by financial professionals with extensive industry experience and knowledge.</p>
        </div>
    </div>
</div>

<div class="faq-section">
    <h2>Course Structure & Content</h2>
    
    <div class="faq-item">
        <h3>How are the courses structured?</h3>
        <div class="faq-answer">
            <p>Our courses are divided into modules, each focusing on a specific aspect of financial education. Modules contain various types of content, including video lessons, text-based explanations, interactive tools and downloadable resources.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Can I purchase individual modules or do I need to buy the entire course?</h3>
        <div class="faq-answer">
            <p>You have the flexibility to purchase either individual modules or complete courses. This allows you to focus on the specific areas that are most relevant to your financial needs and goals.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>How long does it take to complete a course?</h3>
        <div class="faq-answer">
            <p>The time required to complete a course varies depending on the course's complexity and your pace of learning. On average, a module takes 2-3 hours to complete, and a full course might consist of 5-8 modules. Since all courses are self-paced, you can take as much time as you need.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Are the courses updated regularly?</h3>
        <div class="faq-answer">
            <p>Yes, we regularly review and update our course content to ensure it reflects current financial best practices, regulations, and market conditions. When you purchase a course, you get access to all future updates to that course.</p>
        </div>
    </div>
</div>

<div class="faq-section">
    <h2>Purchasing & Access</h2>
    
    <div class="faq-item">
        <h3>How do I purchase a course or module?</h3>
        <div class="faq-answer">
            <p>To purchase a course or module, simply browse our course catalog, select the course or module you're interested in, and click the "Purchase" button. You'll need to create an account (if you don't already have one) and complete the payment process.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>What payment methods do you accept?</h3>
        <div class="faq-answer">
            <p>We accept all major credit cards (Visa, Mastercard, American Express) and PayPal for course purchases.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>How long do I have access to a course after purchase?</h3>
        <div class="faq-answer">
            <p>Once you purchase a course or module, you have lifetime access to the content. You can revisit the material as many times as you need.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Can I access courses on mobile devices?</h3>
        <div class="faq-answer">
            <p>Yes, our platform is fully responsive and works on desktop computers, laptops, tablets, and smartphones. You can access your courses from any device with an internet connection.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Do you offer refunds?</h3>
        <div class="faq-answer">
            <p>Yes, we offer a 30-day money-back guarantee for all our courses. If you're not satisfied with your purchase, please contact us within 30 days, and we'll process your refund.</p>
        </div>
    </div>
</div>

<div class="faq-section">
    <h2>Support & Community</h2>
    
    <div class="faq-item">
        <h3>What kind of support is available if I have questions?</h3>
        <div class="faq-answer">
            <p>We offer email support for all course-related questions. Simply reach out to our support team, and we'll get back to you as soon as possible. For more personalized guidance, you can also book a consultation with one of our financial advisors (additional fees may apply).</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Is there a community or forum where I can connect with other students?</h3>
        <div class="faq-answer">
            <p>Yes, we have a community forum where you can connect with other students, share insights, ask questions, and discuss financial topics. The forum is moderated by our team to ensure a supportive and helpful environment.</p>
        </div>
    </div>
    
    <div class="faq-item">
        <h3>Do you offer personalized financial advice?</h3>
        <div class="faq-answer">
            <p>Our courses provide educational content and general financial guidance, not personalized financial advice. However, if you're looking for personalized advice, you can book a consultation with one of our financial advisors who can provide tailored recommendations based on your specific situation.</p>
        </div>
    </div>
</div>

<div class="section">
    <h2>Still Have Questions?</h2>
    <p>If you couldn't find the answer to your question, please don't hesitate to reach out to us. We're here to help!</p>
    <div class="text-center">
        <?= $this->Html->link('Contact Us', ['controller' => 'Pages', 'action' => 'contact'], ['class' => 'button']) ?>
    </div>
</div>