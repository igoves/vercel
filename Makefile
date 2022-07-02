.PHONY: deploy
.PHONY: php

deploy:
	cd ${PROJECT} && vc -c -n ${PROJECT} -S xorg ${NOW}

deploy-all:
	$(MAKE) php

php:
	PROJECT=php $(MAKE) deploy

php-composer:
	PROJECT=php-composer $(MAKE) deploy