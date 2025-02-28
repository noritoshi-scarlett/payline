# Payline App

## Biblioteka ta spełnia nastepujace kryteria:
- obsługuje logi w sposób generyczny, np. logi płatności, logi dostawy produktów, logi zdarzeń itd.
- logi są modelowane za pomocą techniki Event Sourcingu. Aktualny status zawiwera najnowszy log, aktualnośc logów mierzona jest czasem powstania wg. danych ze źródła
- zawiera system cachowania, w tym wypadku zaimplementoweany jest Redis, choć interfejs jest ogólny
- ma z zamierzeniu korzystać jak najmniej z zasobó zewnetrznych
- generycnzosć ma być realizowana w każdym możliwym miejscu

#### projekt jest bardziej zadaniem akademickim niż implementacja faktycznie mogąca służyć komercyjnemu zastosowaniu, choć jedno nie wyklucza drugiego ;)

## inne informacje:
- używany z Dockerem
- posiada testy napisane w PHPUnit
- *jest w fazie rozwoju*

## Uruchamianie tetsów:
```
XDEBUG_MODE=coverage php vendor/bin/phpunit --display-warnings --coverage-html payline/test-build/coverage
```
