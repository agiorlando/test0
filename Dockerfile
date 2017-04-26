FROM rknol/php7-symfony
MAINTAINER Ruben Knol <c.minor6@gmail.com>

# Copy the project into the directory that php-fpm
# serves files from
COPY . /srv/symfony

# Set file permissions so that
RUN chmod -R 777 /srv/symfony

# Set the database connection details
ENV APP_DATABASE_HOST test0-mysql
ENV APP_DATABASE_NAME test0
ENV APP_DATABASE_USERNAME test0
ENV APP_DATABASE_PASSWORD test0_pw

# Install library dependencies with Composer
RUN composer install --no-interaction