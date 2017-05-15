phantomjsstarter
================

Helper to start phantomjs sessions. We use it in functional test frameworks like behat/mink.

Installation
------------
```javascript
    "require": {
        "mazelab/phantomjsstarter": "~1.0"
    }
```
Example
-------
features/bootstrap/FeatureContext.php 

```php
    class FeatureContext extends MinkContext
    {

        /** @BeforeSuite */
        public static function setup(SuiteEvent $event)
        {
            $phantomjs = new Mazelab\Phantomjs\Starter(8643);
            $phantomjs->up();
        }

        ...
```
behat.yml
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
