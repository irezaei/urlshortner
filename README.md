URL Shortener

A simple and functional URL shortening service built with PHP and MySQL.

Features:

    Shorten long URLs.

    Custom short codes for URLs.

    Generate QR Codes for shortened URLs.

    Simple and elegant user interface using Materialize CSS.

Prerequisites:

    PHP version 7 or higher.

    MySQL.

    A web server (e.g., Apache or Nginx).

Setup Instructions:

    Installation:

        Clone the repository:
        git clone https://github.com/your-username/your-repo-name.git
        cd your-repo-name

        Set up the database:

            Create a new MySQL database.

            Create the links table using the following SQL command:
            CREATE TABLE links (
            id INT AUTO_INCREMENT PRIMARY KEY,
            original_url VARCHAR(2048) NOT NULL,
            short_code VARCHAR(255) NOT NULL UNIQUE,
            redirect_count INT DEFAULT 0
            );

        Create the config.php file:

            Create a file named config.php in the root directory and add your database credentials:
            <?php // Database connection settings $dbHost = 'localhost'; // Database host $dbUser = 'your-db-username'; // Database username $dbPass = 'your-db-password'; // Database password $dbName = 'your-db-name'; // Database name 

    Running the Project:

        Place the project in your web server directory (e.g., htdocs or www).

        Open your browser and navigate to the project URL (e.g., http://localhost/your-repo-name).

    Using the Project:

        On the homepage, enter your URL and click the "Shorten URL" button.

        The shortened URL and its QR Code will be displayed.

Advanced Configuration:

    Password: The default password for accessing the service is yamahdi. You can change this in the PHP code.

    Custom Domain: If you want to use your own domain, replace https://url.com/surl/?c= in the PHP code with your domain.

Contributing:
If you'd like to contribute to this project, please follow these steps:

    Fork the repository.

    Make your changes.

    Submit a Pull Request.

License:
This project is licensed under the MIT License.

Developer:

    Your Name

        Email: rezaei1374@gmail.com

        GitHub: irezaei