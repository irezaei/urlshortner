# URL Shortener

A simple and functional URL shortening service built with PHP and MySQL.

## Features
- Shorten long URLs.
- Custom short codes for URLs.
- Generate QR Codes for shortened URLs.
- Simple and elegant user interface using Materialize CSS.

## Prerequisites
- PHP version 7 or higher.
- MySQL.
- A web server (e.g., Apache or Nginx).

## Setup Instructions

### 1. Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/irezaei/urlshortner
   cd urlshortner

    Configure the database:

        Create a new MySQL database (if not already created).

        Update the database credentials in the index.php file.

    Run the project:

        Place the project in your web server directory (e.g., htdocs or www).

        Open your browser and navigate to the project URL (e.g., http://localhost/urlshortner).

2. Automatic Database Setup

    The script will automatically create the necessary database table (links) when you run the project for the first time.

    No manual SQL commands are required.

3. Using the Project

    On the homepage, enter your URL and click the Shorten URL button.

    The shortened URL and its QR Code will be displayed.

Advanced Configuration

    Password: The default password for accessing the service is yamahdi. You can change this in the PHP code.

    Custom Domain: If you want to use your own domain, replace https://url.com/surl/?c= in the PHP code with your domain.

Contributing

If you'd like to contribute to this project, please follow these steps:

    Fork the repository.

    Make your changes.

    Submit a Pull Request.

License

This project is licensed under the MIT License.
Developer

    Mohammad Reza Rezaei

        Email: rezaei1374@gmail.com

        GitHub: irezaei