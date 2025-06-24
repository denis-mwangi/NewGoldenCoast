# Golden-Coast
## Website Overview
![Full Screenshot](website.png)
# Golden Coast

## Project Overview
Golden Coast is a web application designed to promote coastal destinations, allowing users to explore travel options, book accommodations, and discover local activities. Built with XAMPP as the development environment, the project leverages PHP for server-side logic and MySQL for database management, delivering a dynamic and user-friendly experience. The application features a responsive front-end and a robust backend, making it suitable for travelers and administrators alike.

This repository contains the source code, configuration files, and documentation for the Golden Coast website. It is intended for developers, contributors, and enthusiasts interested in building or enhancing a travel-focused web application.

---

## Table of Contents
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## Features
- **Destination Explorer**: Browse coastal destinations with rich descriptions, images, and travel tips.
- **Booking System**: Book accommodations or activities directly through the platform (if implemented).
- **Responsive Design**: Optimized for desktop and mobile devices using HTML5, CSS3, and Bootstrap (or similar).
- **User Authentication**: Secure login and registration for personalized experiences (if implemented).
- **Search and Filter**: Find destinations or activities based on user preferences.
- **Admin Panel**: Manage destinations, bookings, and user data via a backend interface.
- **Dynamic Content**: Integration with MySQL for real-time data management.

---

## Technologies Used
The Golden Coast project is built with the following technologies, configured to run in a XAMPP environment:
- **Frontend**:
  - HTML5, CSS3, JavaScript
  - [Bootstrap](https://getbootstrap.com/) (or specify other CSS frameworks if used)
  - jQuery (optional, for dynamic front-end interactions)
- **Backend**:
  - [PHP](https://www.php.net/) (v7.x or higher, compatible with XAMPP)
  - [MySQL](https://www.mysql.com/) for database management
- **Development Environment**:
  - [XAMPP](https://www.apachefriends.org/) (cross-platform Apache, MySQL, PHP, and Perl stack)
- **Version Control**:
  - [Git](https://git-scm.com/) for source code management
- **APIs** (optional):
  - Third-party APIs for maps, weather, or booking services (e.g., Google Maps, OpenWeatherMap)

---

## Installation
Follow these steps to set up the Golden Coast project locally using XAMPP:

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (latest version, including Apache, MySQL, and PHP)
- [Git](https://git-scm.com/) for cloning the repository
- A modern web browser (e.g., Chrome, Firefox)
- (Optional) Text editor or IDE (e.g., VS Code, PhpStorm)

### Steps
1. **Install XAMPP**:
   - Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/) for your operating system (Windows, macOS, or Linux).
   - Start the Apache and MySQL modules from the XAMPP Control Panel.

2. **Clone the Repository**:
   ```bash
   git clone https://github.com/denis-mwangi/Golden-Coast.git
   ```

3. **Move Project to XAMPP**:
   - Copy the `Golden-Coast` folder to the XAMPP `htdocs` directory (e.g., `C:\xampp\htdocs` on Windows or `/opt/lampp/htdocs` on Linux).
   - Example:
     ```bash
     mv Golden-Coast /path/to/xampp/htdocs/
     ```

4. **Set Up the Database**:
   - Open `phpMyAdmin` by navigating to `http://localhost/phpmyadmin` in your browser.
   - Create a new database (e.g., `golden_coast_db`).
   - Import the database schema (if provided in the repository, e.g., `database.sql`):
     - In phpMyAdmin, select the database, go to the "Import" tab, and upload the `.sql` file.
   - Alternatively, run the SQL commands manually if no schema file exists:
     ```sql
     CREATE DATABASE golden_coast_db;
     ```

5. **Configure Environment Variables**:
   - Locate or create a configuration file (e.g., `config.php` or `.env`) in the project root.
   - Update database connection settings. Example for `config.php`:
     ```php
     <?php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', ''); // Default XAMPP MySQL password is empty
     define('DB_NAME', 'golden_coast_db');
     ?>
     ```

6. **Start the Application**:
   - Ensure Apache and MySQL are running in the XAMPP Control Panel.
   - Navigate to `http://localhost: [port]/Golden-Coast` (e.g., `http://localhost/Golden-Coast`) in your browser.
   - If a specific port is used, check the Apache configuration in XAMPP.

7. **Test the Setup**:
   - Verify that the homepage loads correctly.
   - Test key features like browsing destinations or user login (if implemented).

---

## Usage
Once the application is running in XAMPP:
- **Access the Website**: Open `http://localhost/Golden-Coast` to explore destinations, search for activities, or book accommodations.
- **Admin Panel**: If implemented, access the admin interface (e.g., `http://localhost/Golden-Coast/admin`) to manage content or bookings.
- **Database Management**: Use phpMyAdmin (`http://localhost/phpmyadmin`) to view or modify database records.
- **Debugging**: Check PHP error logs in XAMPP (`xampp/apache/logs/error.log`) if issues arise.

For detailed usage instructions, refer to the [user guide](#) (add link if a separate guide exists).

---

## Project Structure
The repository is organized as follows:
```
Golden-Coast/
├── css/                      # CSS styles (e.g., Bootstrap, custom styles)
├── js/                       # JavaScript files (e.g., jQuery, custom scripts)
├── images/                   # Static assets (e.g., destination photos)
├── includes/                 # PHP includes (e.g., config.php, database connection)
├── admin/                    # Admin panel files (if applicable)
├── database.sql              # Database schema (if provided)
├── index.php                 # Main entry point for the application
├── .htaccess                 # Apache configuration (if used)
├── README.md                 # This file
└── (other PHP files)         # Pages like login.php, booking.php, etc.
```

---

## Contributing
We welcome contributions to the Golden Coast project! To contribute:
1. **Fork the Repository**:
   Fork the repository on GitHub to create your own copy.
2. **Create a Branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```
3. **Make Changes**:
   Implement your feature or bug fix, following PHP coding standards and testing in the XAMPP environment.
4. **Test Your Changes**:
   Test the application locally in XAMPP to ensure functionality.
5. **Commit and Push**:
   ```bash
   git commit -m "Add your commit message here"
   git push origin feature/your-feature-name
   ```
6. **Create a Pull Request**:
   Open a pull request on GitHub with a clear description of your changes.

For detailed guidelines, refer to [CONTRIBUTING.md](#) (add link if it exists).

---

## License
This project is licensed under the [MIT License](LICENSE). See the `LICENSE` file for details.

---

## Contact
For questions, suggestions, or support, please contact:
- **Author**: Denis Mwangi
- **Email**: [your-email@example.com](mailto:your-email@example.com)
- **GitHub**: [denis-mwangi](https://github.com/denis-mwangi)
- **Issues**: Open an issue on the [GitHub Issues page](https://github.com/denis-mwangi/Golden-Coast/issues)

Thank you for exploring Golden Coast! 🌊
