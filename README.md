# Report generator for Laravel Dusk

Report will be saved in `.md` files.

## Installation

You can install the package via composer:

```bash
composer require --dev laravel/dusk
composer require --dev yaroslawww/laravel-dusk-reporter
```

## Usage

1. Use trait `WithDuskReport` in your dusk test case. This is optional, but it will be easy to rename the file.

```injectablephp
//...
use ThinkOne\LaravelDuskReporter\WithDuskReport;

abstract class DuskTestCase extends BaseTestCase
{
    use WithDuskReport;
    
    //...
    
}
```

2. You can override default configuration

```injectablephp
//...
use ThinkOne\LaravelDuskReporter\Reporter;
use ThinkOne\LaravelDuskReporter\WithDuskReport;

abstract class DuskTestCase extends BaseTestCase
{
    use WithDuskReport;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Change store report folder, by default package use "storage_path('laravel-dusk-reporter')"
        Reporter::$storeBuildAt = app_path('reports/dusk-report');
        
        // Change store screenshots folder, by default package use field "$storeBuildAt"
        Reporter::$storeScreenshotAt = app_path('reports/dusk-screenshots');
        
        // By default package save link in `.md` files as relative path, you can change it
        Reporter::$screenshotRelativePath = false;
         
        // Change element to fit content (by default package use "body" tag)
        Reporter::$getBodyElementCallback = function ($browser) {
            return $browser->driver->findElement(WebDriverBy::id('root'));
        };
    }
    
    //...
    
}
```

3. Create report

```injectablephp
/** @test */
public function open_not_logged_user() {
    $REPORT = $this->newDuskReportFile()
                   ->h1( 'Home marketing page' )
                   ->br()->h2( 'Initial data' )->br()
                   ->raw( "Open page by not logged user" )->br()
                   ->h2( 'Result' )->br();

    $this->browse( function ( Browser $browser ) use ( $REPORT ) {
        $browser->visit( new HomePage() )
                // ...
                ->assertPresent( '@header' )
                ->assertPresent( '@footer' );

        $REPORT->screenshot( $browser, 1, ReportScreenshot::RESIZE_COMBINE );
    } );
    
    $REPORT->br()->h3('Conclusion')->br()->raw('Test passed.');
}
```

By default you will see report directories tree like this:

```
- storage
-- laravel-dusk-reporter
--- MarketingHomePageTest
---- open_not_logged_user.md
---- open_not_logged_user_1.png
```

File `open_not_logged_user.md` will contain this data:

```
# Home marketing page

## Initial data

Open page by not logged user
## Result

![MarketingHomePageTest/open_not_logged_user_1](open_not_logged_user_1.png)

### Conclusion

Test passed.
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
