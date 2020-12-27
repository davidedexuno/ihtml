if [ ! -f "/tmp/php-cs-fixer" ]; then
	wget https://cs.symfony.com/download/php-cs-fixer-v2.phar -O /tmp/php-cs-fixer
	chmod +x /tmp/php-cs-fixer
fi
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
echo "/tmp/php-cs-fixer fix $DIR/src"
/tmp/php-cs-fixer fix $DIR/src
echo "/tmp/php-cs-fixer fix $DIR/libihtml.php"
/tmp/php-cs-fixer fix $DIR/libihtml.php

