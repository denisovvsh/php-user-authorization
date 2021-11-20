FROM ubuntu:groovy

ARG USER_ID
ARG GROUP_ID
ARG USER_NAME
ARG GROUP_NAME

ENV USER_ID $USER_ID
ENV GROUP_ID $GROUP_ID
ENV USER_NAME $USER_NAME
ENV GROUP_NAME $GROUP_NAME

ENV TZ=Europe/Moscow

RUN ln -snf /usr/share/zoneinfo/${TZ} /etc/localtime && echo ${TZ} > /etc/timezone && \
	groupadd -r -g ${GROUP_ID} ${GROUP_NAME} || true && \
	useradd --no-log-init -r -u ${USER_ID} -g ${GROUP_ID} ${USER_NAME}

RUN apt update && \
	yes | apt install systemctl && \
	yes | apt install vim && \
	yes | apt install apache2 && \
	yes | apt install mysql-server && \
	yes | apt install php7.4 libapache2-mod-php7.4 php7.4-mysql && \
	yes | apt install php-curl php-json php-cgi php-gd php-zip php-mbstring php-xml php-xmlrpc && \
	a2enmod rewrite

ENTRYPOINT bash

EXPOSE 80
WORKDIR /var/www/html
VOLUME ["/var/www/html"]