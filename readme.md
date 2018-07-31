# AppSero - Client

- [Installation](#installation)
- [Insights](#insights)


## Installation

You can install AppSero Client in two ways, via composer and manually.

### 1. Composer Installation

Add dependency in your project (theme/plugin):

```
composer require appsero/client dev-master
```

Now add `autoload.php` in your file if you haven't done already.

```php
require __DIR__ . '/vendor/autoload.php';
```

### 2. Manual Installation

Clone the repository in your project.

```
cd /path/to/your/project/folder
git clone https://github.com/AppSero/client.git appsero
```

Now include the dependencies in your plugin/theme.

```php
require __DIR__ . '/appsero/src/insights.php';
```

## Insights

AppSero can be used in both themes and plugins.

The `AppSero\Insights` class has *five* parameters:

```php
new AppSero\Insights( $hash, $name, $file, $theme, $notice );
```

- **hash** (*string*, *required*) - The unique identifier for a plugin or theme.
- **name** (*string*, *required*) - The name of the plugin or theme.
- **file** (*string*, *required*) - The **main file** path of the plugin. For theme, path to `functions.php`
- **theme** (*boolean*, *optional*) - Indicate wheather the current usage for a theme or plugin. Defaults to `false`.
- **notice** (*string*, *optional*) - If needs to override the default admin notice message, you can pass a string of your own.

### Usage Example

Please refer to the **installation** step before start using the class. 

You can obtain the **hash** for your plugin for the [AppSero Dashboard](https://dashboard.appsero.com). The 3rd parameter **must** have to be the main file of the plugin.

#### For Plugins

Example code that needs to be used on your main plugin file.

```php
/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function appsero_init_tracker_appsero_test() {

    if ( ! class_exists( 'AppSero\Insights' ) ) {
        require_once __DIR__ . '/appsero/src/insights.php';
    }

    new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044891', 'Akismet', __FILE__ );
}

add_action( 'init', 'appsero_init_tracker_appsero_test' );
```

#### For Themes

Example code that needs to be used on your themes `functions.php` file.

```php
/**
 * Initialize the theme tracker
 *
 * @return void
 */
function appsero_init_tracker_twenty_twelve() {

    if ( ! class_exists( 'AppSero\Insights' ) ) {
        require_once __DIR__ . '/appsero/src/insights.php';
    }

    new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044892', 'Twenty Twelve', __FILE__, true );
}

add_action( 'init', 'appsero_init_tracker_twenty_twelve' );
```

## Credits

Created and maintained by [AppSero](https://appsero.com).
