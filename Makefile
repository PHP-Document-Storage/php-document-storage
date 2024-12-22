help: ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

static-analysis: ## Execute static analysis
	./vendor/bin/phpstan --configuration=etc/phpstan.dist.neon

unit-tests: ## Execute unit tests
	./vendor/bin/phpunit -c etc/phpunit.xml

coding-style-check: ## Validates coding style rules
	./vendor/bin/php-cs-fixer fix --diff --dry-run --verbose --config etc/.php-cs-fixer.php

coding-style-fix: ## Fixes coding style automiatically
	./vendor/bin/php-cs-fixer fix --verbose --config etc/.php-cs-fixer.php

install: ## Install project dependencies
	composer install