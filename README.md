# Report generator for Laravel Dusk

[![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-dusk-reporter?color=%234dc71f)](https://github.com/yaroslawww/laravel-dusk-reporter/blob/master/LICENSE.md)
[![Packagist Version](https://img.shields.io/packagist/v/yaroslawww/laravel-dusk-reporter)](https://packagist.org/packages/yaroslawww/laravel-dusk-reporter)
[![Total Downloads](https://img.shields.io/packagist/dt/yaroslawww/laravel-dusk-reporter)](https://packagist.org/packages/yaroslawww/laravel-dusk-reporter)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-dusk-reporter/?branch=master)

Report will be saved in `.md` files.

## Installation

You can install the package via composer:

```bash
composer require --dev yaroslawww/laravel-dusk-reporter
```

## Usage

#### Purge old data

When testing, the package adds content files, so you need to clear the directories before starting tests again

```shell
php artisan dusk-reporter:purge
# or
php artisan dusk-reporter:purge -y
# or
php artisan dusk-reporter:purge --path="/my/project/report"
```

#### 1. Use trait `TestWithDuskReport` in your dusk test case. This is optional, but it will be easy to rename the file.

```injectablephp
//...
use LaravelDuskReporter\TestWithDuskReport;

abstract class DuskTestCase extends BaseTestCase
{
    use TestWithDuskReport;
    
    //...
    
}
```

#### 2. You can override default configuration

Change store report folder, by default package use "base_path('storage/laravel-dusk-reporter')"

```injectablephp
Reporter::$storeBuildAt = app_path('reports/dusk-report');
```

or

```shell
DUSK_REPORT_PATH=reports/browser-tests  php artisan dusk  --stop-on-failure
```

Change store screenshots folder, by default package use field "$storeBuildAt"

```injectablephp
Reporter::$storeScreenshotAt = app_path('reports/dusk-screenshots');
```

By default package save link in `.md` files as relative path, you can change it

```injectablephp
Reporter::$screenshotRelativePath = false;
```

Change element to fit content (by default package use "body" tag)

```injectablephp
Reporter::$getBodyElementCallback = function ($browser) {
    return $browser->driver->findElement(WebDriverBy::id('someId'));
};
```

#### 3. Create report

```injectablephp
namespace Tests\Browser\CPD\Marketing;

//...

class HomePageTest extends DuskTestCase {
    /** @test */
    public function open_by_not_logged_user() {
        $REPORT = $this->newDuskReportFile()
                       ->h1( 'Home marketing page' )
                       ->h2( 'Initial data' )
                       ->p( "Open page by not logged user" );
    
        $this->browse( function ( Browser $browser ) use ( $REPORT ) {
            $browser->visit( new HomePage() )
                    // ...
                    ->assertPresent( '@header' )
                    ->assertPresent( '@footer' );
    
            $REPORT->screenshot( $browser, ReportScreenshot::RESIZE_COMBINE )
                // or
                ->screenshotWithVisibleScreen( $browser )
                // or
                ->screenshotWithFitScreen( $browser )
                // or
                ->screenshotWithCombineScreen( $browser )
                // or
                ->screenshotWithCombineScreen( $browser,  $suffix = 'additional_screen', $newLine = false);
        } );
        
        $REPORT->h3('Conclusion')->p('Test passed.');
    }
}
```

#### 4. Create report for one test file

By default, the package is designed to create one report for one test (since the phpunit re-creates the structure for
each test). But you can create one file for multiple tests using method `duskReportFile`

```injectablephp
abstract class Page extends BasePage
{
    use HasDuskReporter;

}
```

```injectablephp
namespace Tests\Browser;

use Database\Seeders\DatabaseDuskCPDSeeder;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use LaravelDuskReporter\Reporter;
use Tests\Browser\Pages\Page;
use Tests\DuskTestCase;

class ReportableDuskTestCase extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        Page::withDuskReporter( $this->setUpReportFileForClass() );
    }
}
```

```injectablephp
namespace Tests\Browser\Marketing;

class HomePageTest extends ReportableDuskTestCase {

    $duskReportClassFilePath = 'Marketing';

    /**  @test */
    public function open_by_not_logged_user() {
        $this->browse( function ( Browser $browser ) {
            $browser->visit( new HomePage() )
                    ->assertPresent( '@header' )
                    ->assertPresent( '@footer' )
                    ->reportUserSeePage()
                    ->reportAppend( function ( $reporter ) {
                        $reporter->p( 'My additional note.' );
                    } );
        } );
    }

    public function testPageHasVideo() {
        $this->browse( function ( Browser $browser ) {
            $browser->visit( new HomePage() )
                    ->assertPresent( '@marketing-video' );

            // $browser->reportUserSeePage();
            $this->duskReportFile()->h2( "Open page with video" )
                         ->screenshotWithFitScreen( $browser )
                         ->p('Some note');
        } );
    }

}
```

#### 5. Disable reporting

Sometimes you need to cancel the creation of a report (for example, you want to make a quick test without generating a
report). Then just add a global variable `DUSK_REPORT_DISABLED=1` or add it to `.env.dusk.local` file. Also package
supports disabling only screenshots `DUSK_SCREENSHOTS_DISABLED=1`

```shell
DUSK_REPORT_DISABLED=1 php artisan dusk tests/Browser/CPD/Marketing/HomePageTest.php --stop-on-failure
DUSK_SCREENSHOTS_DISABLED=1 php artisan dusk tests/Browser/CPD/Marketing/HomePageTest.php --stop-on-failure
```

## Frontend generation

In some cases, you will need to show the report in HTML format. Usually it is convenient to use md->html compilers for
this. For example, you can use [vuepress](https://vuepress.vuejs.org/)

```shell
# Add vuepress if not exists
yarn add -D vuepress
```

Then add command to your package.json

```
# package.json
{
    ...
    "scripts": {
        ...
        "testsPreview:build": "cp -r vendor/yaroslawww/laravel-dusk-reporter/assets/.vuepress storage/laravel-dusk-reporter/ && VUEPRESS_BASE='/laravel-dusk-reporter-html/' VUEPRESS_DEST='public/laravel-dusk-reporter-html' vuepress build storage/laravel-dusk-reporter",
        ...
    },
    "devDependencies": {
        ...
        "vuepress": "^1.5.4",
        ...
    },
    ...
}

```

Then just run

```shell
yarn testsPreview:build
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/)
