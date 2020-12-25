if [ ! -f "/tmp/php-cs-fixer" ]; then
	wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O /tmp/php-cs-fixer
	chmod +x /tmp/php-cs-fixer
fi
/tmp/php-cs-fixer fix ihtml-php/src
/tmp/php-cs-fixer fix ihtml-php/libihtml.php

