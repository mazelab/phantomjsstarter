phantomjsstarter
================

Helper to start phantomjs sessions. We are using it in functional test frameworks like behat/mink.

Installation
------------

    "require": {
        "mazelab/phantomjsstarter": "~1.0"
    }

Example
-------

    class FeatureContext extends MinkContext
    {

        /** @BeforeSuite */
        public static function setup(SuiteEvent $event)
        {
            $phantomjs = new Mazelab\Phantomjs\Starter(8643);
            $phantomjs->up();
        }

        ...
