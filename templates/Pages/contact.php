<?php
/**
 * Jenbury Financial - Contact Page
 */
$this->assign('title', 'Contact Us');
?>

<div class="page-header">
    <h1>Contact Us</h1>
</div>

<div class="row">
    <div class="column">
        <div class="card">
            <div class="card-body">
                <h2>Get in Touch</h2>
                <p>Have questions about our courses or need assistance? We're here to help. Fill out the form below, and one of our team members will get back to you as soon as possible.</p>
                
                <?= $this->Form->create(null, ['url' => ['controller' => 'Pages', 'action' => 'contact'], 'class' => 'needs-validation', 'novalidate' => true]) ?>
                    <div class="row">
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('name', [
                                    'label' => 'Your Name*',
                                    'required' => true,
                                    'placeholder' => 'Enter your full name',
                                    'minlength' => 2,
                                    'maxlength' => 100, // Arbitrary length for contact form name
                                    'pattern' => "^[A-Za-z\\s'-]+$", // Allow letters, spaces, hyphens, apostrophes
                                    'title' => 'Please enter a valid name (letters, spaces, hyphens, apostrophes only, 2-100 characters).'
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                        <div class="column">
                            <div class="input">
                                <?= $this->Form->control('email', [
                                    'type' => 'email',
                                    'label' => 'Email Address*',
                                    'required' => true,
                                    'placeholder' => 'Enter your email address',
                                    'maxlength' => 100 // Match register form
                                ]) ?>
                                <?php // Removed validation-message div ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input">
                        <?= $this->Form->control('subject', [
                            'label' => 'Subject*',
                            'required' => true,
                            'placeholder' => 'What is your message about?',
                            'maxlength' => 50
                        ]) ?>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="input">
                        <?= $this->Form->control('message', [
                            'type' => 'textarea',
                            'label' => 'Your Message*',
                            'required' => true,
                            'placeholder' => 'How can we help you?',
                            'maxlength' => 300,
                            'rows' => 5
                        ]) ?>
                        <?php // Removed validation-message div ?>
                    </div>
                    
                    <div class="submit">
                        <?= $this->Form->button('Send Message', ['class' => 'button']) ?>
                    </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
    
    <div class="column">
        <div class="card">
            <div class="card-body">
                <h2>Contact Information</h2>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <h3>Email</h3>
                        <p><a href="mailto:info@jenburyfinancial.com">info@jenburyfinancial.com</a></p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Phone</h3>
                        <p><a href="tel:+61399999999">+61 3 9999 9999</a></p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Office Hours</h3>
                        <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                        <p>Saturday - Sunday: Closed</p>
                    </div>
                    
                    <div class="contact-item">
                        <h3>Address</h3>
                        <p>123 Financial Street</p>
                        <p>Melbourne, VIC 3000</p>
                        <p>Australia</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h2>Book a Free Consultation</h2>
                <p>Interested in personalized financial advice? Schedule a free 15-minute consultation with one of our financial advisors.</p>
                <?= $this->Html->link('Book a Call', '#', ['class' => 'button button-outline']) ?>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <h2>Frequently Asked Questions</h2>
    
    <div class="faq-item">
        <h3>How do I access my purchased courses?</h3>
        <p>Once you've purchased a course or module, you can access it by logging into your account and navigating to "My Courses" in your dashboard. From there, you'll see all the courses and modules you've purchased.</p>
    </div>
    
    <div class="faq-item">
        <h3>Can I get a refund if I'm not satisfied with a course?</h3>
        <p>We offer a 30-day money-back guarantee for all our courses. If you're not satisfied with your purchase, please contact us within 30 days, and we'll process your refund.</p>
    </div>
    
    <div class="faq-item">
        <h3>How long do I have access to a course after purchase?</h3>
        <p>Once you purchase a course or module, you have lifetime access to the content. You can revisit the material as many times as you need.</p>
    </div>
    
    <div class="faq-item">
        <h3>Are the courses self-paced?</h3>
        <p>Yes, all our courses are self-paced. You can work through the material at your own speed and on your own schedule.</p>
    </div>
    
    <div class="faq-item">
        <h3>Do you offer corporate training?</h3>
        <p>Yes, we offer corporate financial education packages. Please contact us directly to discuss your organization's needs.</p>
    </div>
</div>