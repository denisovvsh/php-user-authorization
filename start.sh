docker start lamp && \
docker exec -it lamp systemctl restart apache2 && \
docker exec -it lamp systemctl restart mysql && \
docker exec -it lamp chown -R $(id -nu):$(id -ng) /var/www
#docker exec -it lamp echo '<?php phpinfo(); ?>' >> /var/www/html/phpinfo.php
