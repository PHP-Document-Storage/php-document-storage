help:                                                                           ## shows this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

static-analysis: ## Execute static analysis
	./vendor/bin/phpstan --configuration=etc/phpstan.dist.neon

unit-tests: ## Execute unit tests
	./vendor/bin/phpunit -c etc/phpunit.xml