FROM ubuntu:22.04
RUN apt-get update
RUN DEBIAN_FRONTEND=noninteractive TZ="America/New_York" apt-get -y install tzdata
RUN apt-get install software-properties-common -y
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update
RUN apt-get install php7.4-cli php7.4-fpm php7.4-opcache php7.4-sqlite3 -y
RUN apt-get install nginx -y
RUN mkdir -p /cfp/slides
WORKDIR /cfp
COPY styles.css .   
COPY index.php .
COPY lib.php .
COPY portal.php .
COPY signup.php .
COPY logo.png .
COPY delete.png .
COPY default /etc/nginx/sites-available
COPY php-fpm.conf /etc/php/7.4/fpm
COPY nginx.conf /etc/nginx
COPY run.sh .
RUN chown -R www-data:www-data /cfp
RUN chmod a+x run.sh
CMD ["./run.sh"] 