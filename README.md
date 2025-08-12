# 💼 Jenbury Financial - Financial Education Platform

A comprehensive web-based financial education platform developed by **BARTA Tech** for **Jenbury Financial**, led by Andrea Jenkins. This platform delivers modular financial courses with multimedia content, secure user management, and robust administrative capabilities.

## 🌐 Live System Access

**Production URL**: https://review.jenburyfinancialcourses.u24s2109.iedev.org

### Quick Access Accounts

**👑 Administrator Account**
- **Email**: `admin@jenburyfinancial.com`
- **Password**: `Admin123!@#`

**👤 Demo User Account**
- **Email**: `user@example.com`
- **Password**: `User123!@#`

> ⚠️ **Important**: Change these passwords immediately in production environments.

## 🎯 What This Platform Does

Jenbury Financial's online learning platform helps people learn about finance through:

- **📚 Easy-to-Follow Courses**: Financial education broken into bite-sized modules
- **🛒 Flexible Purchasing**: Buy entire courses or just the modules you need
- **📱 Works Everywhere**: Use on your phone, tablet, or computer
- **👥 Community Forum**: Ask questions and discuss topics with other learners
- **📊 Track Your Progress**: See how much you've completed and what's next
- **💳 Secure Payments**: Safe checkout powered by Stripe

## 🏗️ How It's Built

### Core Technology
- **CakePHP 5.0.6**: Modern PHP framework for web applications
- **MySQL**: Database to store all information securely
- **Stripe Integration**: Secure payment processing
- **Bootstrap**: Responsive design that works on all devices

### Key Features
- **User Registration & Login**: Secure account creation and access
- **Course Management**: Add, edit, and organize educational content
- **Payment System**: Integrated with Stripe for safe transactions
- **Forum System**: Community discussions with moderation tools
- **Progress Tracking**: Monitor learning advancement
- **Admin Dashboard**: Complete control over content and users

## 🚀 Getting Started (For Developers)

### What You Need
- PHP 8.1 or newer
- MySQL 5.7 or newer
- Composer (PHP package manager)
- Web server (Apache or Nginx)

### Quick Setup

1. **Get the Code**
   ```bash
   git clone 
   cd 
   ```

2. **Install Required Packages**
   ```bash
   composer install
   ```

3. **Set Up Database**
   ```bash
   # Create database
   mysql -u root -p -e "CREATE DATABASE jenbury_financial;"
   
   # Import sample data
   mysql -u username -p jenbury_financial < config/sample_data.sql
   ```

4. **Configure Database Connection**
   - Copy `config/app_local.example.php` to `config/app_local.php`
   - Edit the database settings with your credentials

5. **Start the Application**
   ```bash
   bin/cake server
   ```

6. **Access Your Site**
   - Frontend: http://localhost:8765/
   - Admin Panel: http://localhost:8765/admin

## 👥 How to Use the Platform

### For Students

**Getting Started**
1. Visit the website and click "Register"
2. Fill in your details and create a secure password
3. Check your email and log in

**Learning Process**
1. Browse available financial courses
2. Add courses or individual modules to your cart
3. Complete secure checkout with Stripe
4. Access your purchased content immediately
5. Track your progress as you learn

**Community Features**
- Join forum discussions about financial topics
- Ask questions and get help from other users
- Share your learning experiences

### For Administrators

**Managing Content**
1. Log into the admin panel at `/admin`
2. Create new courses with detailed descriptions
3. Add modules and lessons with multimedia content
4. Set pricing for courses and individual modules
5. Monitor user progress and engagement

**User Management**
- View all registered users
- Edit user information and permissions
- Track user statistics and learning progress
- Handle customer support inquiries

**Forum Moderation**
- Create discussion categories
- Approve/disapprove posts and threads
- Lock threads when necessary
- Maintain community standards

## 📁 How the Code is Organized

```
jenbury-financial/
├── 📂 src/                    # Main application code
│   ├── 📂 Controller/         # Handles user requests
│   ├── 📂 Model/             # Database interactions
│   └── 📂 View/              # Not used (see templates/)
├── 📂 templates/             # Web pages users see
│   ├── 📂 Admin/             # Admin panel pages
│   └── 📂 Pages/             # Public website pages
├── 📂 webroot/               # Images, CSS, and JavaScript
│   ├── 📂 css/               # Styling files
│   ├── 📂 js/                # Interactive features
│   └── 📂 img/               # Pictures and icons
├── 📂 config/                # Settings and database setup
└── 📂 logs/                  # Error logs and debugging
```

## 🔧 Key System Features

### Course Management
- **Modular Structure**: Courses → Modules → Individual Lessons
- **Multimedia Support**: Videos (MP4, WebM), documents (PDF, DOCX), images
- **Rich Text Editor**: CKEditor for formatting lesson content
- **Progress Tracking**: Automatic progress monitoring per user

### Payment System
- **Stripe Integration**: Secure credit card processing
- **Flexible Pricing**: Course bundles or individual module purchases
- **Discount Codes**: Promotional pricing with percentage discounts
- **Purchase History**: Complete transaction records

### User Management
- **Secure Authentication**: Password hashing and session management
- **Role-Based Access**: Different permissions for students and admins
- **Profile Management**: Users can update their information
- **Password Reset**: Secure password recovery via email

### Forum System
- **Category Organization**: Structured discussion topics
- **Thread Management**: Create, edit, lock, and sticky threads
- **Post Moderation**: Approve/disapprove posts for quality control
- **User Permissions**: Users can edit their own posts and threads

## 🛡️ Security Features

- **Secure Passwords**: Minimum 8 characters with special characters required
- **HTTPS Ready**: Secure data transmission
- **SQL Injection Protection**: Safe database queries
- **Payment Security**: PCI-compliant via Stripe
- **Session Security**: Automatic logout and secure session handling

## 📊 System Statistics

The admin dashboard provides:
- Total number of registered users
- Course and module completion rates
- Revenue tracking and purchase analytics
- User engagement metrics
- Forum activity monitoring

## 🚀 Deployment (For System Administrators)

### Using cPanel Hosting

1. **Upload Files**: Use cPanel File Manager or FTP
2. **Set Up Database**: Create MySQL database via cPanel
3. **Configure Domain**: Point domain to project folder
4. **Install Dependencies**: Run `composer install` via terminal
5. **Initialize Database**: Import schema and sample data

### Database Migrations
```bash
# Run content management migrations
bin/cake migrations migrate --plugin=ContentBlocks
bin/cake migrations seed --seed ContentBlocksSeed
```

## 🔄 Backup and Recovery

### Creating Backups (via cPanel)
1. Log into cPanel dashboard
2. Navigate to "Backup" in Files section
3. Click "Download a Full Account Backup"
4. Store backup files securely

### Restoring from Backup
1. Access cPanel Backup section
2. Upload backup file
3. Select restore options (files, database, etc.)
4. Confirm restoration process

> 💡 **Tip**: Schedule automatic backups and always keep local copies

## 🔍 Troubleshooting

### Common Issues

**Can't Log In**
- Check email and password spelling
- Use "Forgot Password" if needed
- Clear browser cache and cookies

**Payment Problems**
- Verify card details are correct
- Check if discount code is valid and active
- Contact admin if payment fails after multiple attempts

**Course Not Accessible**
- Confirm purchase was completed successfully
- Check if course is published and active
- Contact administrator for access issues

**Admin Panel Issues**
- Ensure you're using admin credentials
- Check if account has proper permissions
- Review error logs in `/logs` directory

### For Developers
```bash
# Clear application cache
bin/cake cache clear_all

# Check error logs
tail -f logs/error.log

# Run database migrations
bin/cake migrations migrate
```

## 👨‍💻 Development Team

**BARTA Tech - Team 109**
*FIT3048 Intelligent Mobile Applications, Monash University*

This platform was developed as part of an educational project, showcasing modern web development practices and real-world application deployment.

## 📞 Support

For technical issues or questions:
- Review this documentation first
- Check error logs for technical problems
- Contact the development team through proper academic channels
- For production issues, contact system administrator

## 📄 License

This project is **proprietary and confidential** to Jenbury Financial.

- Educational use within FIT3048 is permitted
- Commercial distribution requires explicit permission
- Code review by authorized academic staff is allowed
- All rights reserved to the development team and client

## 🎓 Learning Outcomes

This project demonstrates:
- Modern PHP web development with CakePHP
- Database design and management
- Payment system integration
- User authentication and authorization
- Content management systems
- Forum and community features
- Responsive web design
- Security best practices

---

**Created by BARTA Tech for Jenbury Financial**  
*Empowering financial education through technology*

📧 **Client**: Andrea Jenkins - Jenbury Financial  
🏫 **Institution**: Monash University  
📚 **Unit**: FIT3048 - Intelligent Mobile Applications

*Last Updated: June 2025 | Version 1.1*
