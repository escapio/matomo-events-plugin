# Matomo Plugin: EventsExporter 

This plugin allows to export matomo event data with some custom filters like

- filter by category
- filter by action
- filter by label

## Usage

Just send HTTP requests to your Matomo instance.

```http request
GET "https://{MATOMO_BASE_URL}?module=API&method=EventsExporter.getEvents&idSite=2&date=2023-03-01,2023-03-21&token_auth={TOKEN}&format=json"
```

### Mandatory Parameters

| param      | description                                                                                                                                                                                                 | example value                     |
|------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------------------|
| module     | parameter to access an matomo API                                                                                                                                                                           | `API`                             |
| method     | parameter to specify the API method                                                                                                                                                                         | `EventsExporter.getEvents`        |
| idSite     | the site  id which events should be exports                                                                                                                                                                 | `1`, `2`, ...                     |
| token_auth | a token generated within the matomo dashboard <br/>to access Matomo via an API. <br/> [See docs](https://developer.matomo.org/api-reference/reporting-api#authenticate-to-the-api-via-token_auth-parameter) |                                   |
| date       | Param to define the date (range). Also relative dates are supported [See docs](https://developer.<br/>matomo.org/api-reference/reporting-api#standard-api-parameters)                                       | `1892-07-25,2022-07-25`, `last10` |
| period     | Param to define the date period [See docs](https://developer.<br/>matomo.org/api-reference/reporting-api#standard-api-parameters)                                                                           | `1892-07-25,2022-07-25`           |

### Optional parameters

| param          | description                                                                                                                                                                                       | example value         |
|----------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|-----------------------|
| category       | Filter by category name                                                                                                                                                                           | `SOME_CATEGORY`       |
| action_name    | Filter by action name                                                                                                                                                                             | `SOME_ACTION`         |
| action_pattern | Filter events by an action pattern. Any [MySQL compatible pattern](https://dev.mysql.com/doc/refman/8.0/en/regexp.html) works.                                                                    | `^[a-z]+`             |
| order_by_names | By default the result is sorted by the amount of each unique event in descending order. <br/> By setting this parameter the resuls will be sorted by <br/> category-name, action-name, label-name | `1`                   |
| lang_id        | With this parameter the results are filtered by the prefix of the `idaction_url` which is in most <br/>cases a lang id within an url like `de.example.com`                                        | `de`, `en`, `es`, ... |

## License

This plugin is released under the GPL v3 license, see [LICENSE](LICENSE).

The template for plugin was generated with the CLI helper tools from [Matomo](https://github.com/matomo-org/matomo).

## Contributing

### Development

To properly run and test this plugin, this project must be integrated inside a running matomo instance.

#### Project setup

Clone the [Matomo repository](https://github.com/matomo-org/matomo).
Change into the cloned directory.
Add this plugin repo as a Git submodule:

```shell
git submodule add git@github.com:escapio/matomo-events-plugin.git plugins/EventsExporter
```

Change into the plugin's directory.

```shell
cd plugins/EventsExporter
```

Add the `.env`-file and fill in the environment variables:
```shell
cp .env.example .env
```

Start the containers using Docker Compose
```shell
docker compose up -d
```

Next the matomo instance must be set up. To do that open a browser and visit the page using your `MATOMO_PORT`:
```shell
http://localhost:MATOMO_PORT
```

Follow the wizard and fill in the database connection variables according to the values in the `.env` file.

After completing the wizard, matomo will show a warning for an untrusted host and unsecure connection.
To fix this just follow the hint by adding `trusted_hosts[] = "localhost:<YOUR_MATOMO_PORT>"` and 
`enable_trusted_host_check=0` to the `config.ini.php` file located in the `config`-directory of the root directory 
inside the matomo project.

After this step the matomo instance is ready to use.

#### Tests

To run the tests locally, the following need to added to the `config/config.ini.php`:

```
[database_tests]
host = test_db
dbname = test_db
username = test_db_user
password = test_db_pw
tables_prefix = "matomo_"

[tests]
request_uri = "/"
port = 9000

[Development]
enabled = 1
disable_merged_assets = 1
```

After that you can execute the plugin's tests by running following command

```shell
docker compose exec php /var/www/html/console tests:run EventsExporter
```

## Sources

- https://github.com/matomo-org/matomo
- https://developer.matomo.org/develop
- https://developer.matomo.org/guides/tests
- https://developer.matomo.org/guides/tests-php
- https://developer.matomo.org/guides/tests-system
- https://developer.matomo.org/guides/tests-in-depth-faq
- https://developer.matomo.org/api-reference/reporting-api
