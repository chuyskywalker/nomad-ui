docker run -ti --rm \
 -v `pwd`/html:/var/www/html/ \
 -e 'NOMAD_BASEURL=http://consul.service.consul:4646' \
 php:5.6-apache