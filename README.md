# Onion Search

Onion Search is a minimalist, brutalist-designed search engine for `.onion` links, leveraging Ahmia.fi for data synchronization. This project is intended for educational purposes and as a demonstration of a simple PHP-based search interface.

## Disclaimer

This project is for **educational purposes only**. The developer is not responsible for any misuse of this software. Accessing `.onion` sites may carry risks, and users should exercise caution and adhere to all applicable laws and regulations.

## Credits

Inspired by and adapted from the [Deep-Index](https://github.com/BryanApolonio/Deep-Index) project by Bryan Apolonio.

## Installation on Debian/Ubuntu

This guide assumes you have a fresh Debian or Ubuntu server. We will install Nginx as the web server and PHP-FPM to process PHP files.

### 1. Install Nginx

Install Nginx, a high-performance web server:

```bash
sudo apt install nginx -y
```

Start Nginx and enable it to start on boot:

```bash
sudo systemctl start nginx
sudo systemctl enable nginx
```

### 2. Install PHP and PHP-FPM

Install PHP (version 8.1 or higher is recommended) and the PHP-FPM (FastCGI Process Manager) package, along with the SQLite extension:

```bash
sudo apt install php-fpm php-sqlite3 php-curl -y
```

Start PHP-FPM and enable it to start on boot:

```bash
sudo systemctl start php8.1-fpm
sudo systemctl enable php8.1-fpm
```

(Adjust `php8.1-fpm` to your installed PHP version if different, e.g., `php8.2-fpm`).

### 3. Clone the Repository

Clone the Onion Search repository to your web server's root directory. For Nginx, this is typically `/var/www/html/`.

```bash
sudo git clone https://github.com/YOUR_GITHUB_USERNAME/Onion-Search.git /var/www/html/onion-search
```

**Note:** Replace `https://github.com/YOUR_GITHUB_USERNAME/Onion-Search.git` with the actual URL of your repository.

### 4. Configure Nginx

Create a new Nginx server block configuration file for Onion Search. For example, `/etc/nginx/sites-available/onion-search`:

```bash
sudo nano /etc/nginx/sites-available/onion-search
```

Add the following content to the file:

```nginx
server {
    listen 80;
    server_name your_domain_or_ip;
    root /var/www/html/onion-search;

    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    error_log /var/log/nginx/onion-search_error.log;
    access_log /var/log/nginx/onion-search_access.log;
}
```

**Important:**
- Replace `your_domain_or_ip` with your actual domain name or server IP address.
- Ensure `fastcgi_pass` points to the correct PHP-FPM socket for your PHP version (e.g., `php8.1-fpm.sock`).

Enable the new server block by creating a symbolic link to `sites-enabled`:

```bash
sudo ln -s /etc/nginx/sites-available/onion-search /etc/nginx/sites-enabled/
```

Test the Nginx configuration for syntax errors:

```bash
sudo nginx -t
```

If the test is successful, restart Nginx to apply the changes:

```bash
sudo systemctl restart nginx
```

### 5. Set Directory Permissions

Ensure the web server has appropriate permissions to read files and write to the `data` directory for the SQLite database:

```bash
sudo chown -R www-data:www-data /var/www/html/onion-search
sudo chmod -R 755 /var/www/html/onion-search
sudo mkdir -p /var/www/html/onion-search/data
sudo chmod 775 /var/www/html/onion-search/data
```

## Usage

After installation, navigate to `http://your_domain_or_ip` in your web browser. You can then:

- **Search:** Enter a query in the search bar to find `.onion` links.
- **Synchronize Data:** Click the "SYNC AHMIA" link in the footer to fetch the latest `.onion` links from Ahmia.fi. This will populate or update your local SQLite database.

## Configuration

- **`engine.php`:**
    - `DB_PATH`: Path to the SQLite database file. Default is `./data/onion.db`.
    - `CACHE_TIME`: Time in seconds before attempting to re-synchronize data from Ahmia. Default is 3600 seconds (1 hour).

- **`style.css`:** Modify this file to customize the visual appearance of the search engine.
