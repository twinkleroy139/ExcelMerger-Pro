# Use an official lightweight PHP image with Apache
FROM php:8.2-apache

# Install system dependencies, Python 3, and pip
RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    python3-pandas \
    python3-openpyxl \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite if needed
RUN a2enmod rewrite

# Set working directory inside container
WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html/

# Install any extra python requirements if present
RUN if [ -f python_scripts/requirements.txt ]; then pip3 install --no-cache-dir -r python_scripts/requirements.txt --break-system-packages; fi

# Create required writable directories and set permissions
RUN mkdir -p database outputs uploads \
    && chmod -R 777 database outputs uploads includes

# Render requires web services to listen on port 10000
RUN sed -i 's/80/10000/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Expose port 10000
EXPOSE 10000

# Start Apache in the foreground
CMD ["apache2-foreground"]