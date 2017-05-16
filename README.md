# PhantomJS Starter


PHP helper class to start phantomjs sessions on demand. You could use it in functional test frameworks like behat/mink.

## License

The phantomjsstarter is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

## Installation

```composer require mazelab/phantomjsstarter --dev```



```
    "require-dev": {
        "mazelab/phantomjsstarter": "^1.0"
    }
```

## API

The _Starter_ constructor accepts the following parameters:

- `$port` - webdriver port number which is passed to the `--webdriver` option    
- `$options` - other additional options. Defaults to `--proxy-type=none --ignore-ssl-errors=true`
- `$phantomJsPath` - path to the phantomjs executable. Defaults to global `phantomjs`

Example
-------
A _FeatureContext_ file could look like this

```php
    class FeatureContext extends MinkContext
    {
        /** @BeforeSuite */
        public static function setup(SuiteEvent $event)
        {
            // this will set the port
            $phantomjs = new Mazelab\Phantomjs\Starter(8643);
            $phantomjs->up();
        }
        
        /** @BeforeSuite */
        public static function setup2(SuiteEvent $event)
        {
            // this will set the port and tells the starter
            // to use the binary from node_modules/.bin
            $phantomjs = new Mazelab\Phantomjs\Starter(8643, null, 'node_modules/.bin/phantomjs');
            $phantomjs->up();
        }
        ...
```
And then your config file `behat.yml`

```yaml
    default:
        context:
            class:  'FeatureContext'
        formatter:
            name:               pretty
            parameters:
                output_path:    null
        extensions:
            Behat\MinkExtension\Extension:
                default_session: selenium2
                javascript_session: selenium2
                base_url: 'https://dev.myproject.com'
                selenium2:
                    wd_host: "http://localhost:8643/wd/hub"
```
