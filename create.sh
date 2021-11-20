docker create -it -p 80:80 --name "lamp" -h "localhost" -v /home/vadim/lamp/html:/var/www/html -P denisovvsh/lamp
