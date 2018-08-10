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

The `AppSero\Insights` class has *three* parameters:

```php
$insights = new AppSero\Insights( $hash, $name, $file );
```

- **hash** (*string*, *required*) - The unique identifier for a plugin or theme.
- **name** (*string*, *required*) - The name of the plugin or theme.
- **file** (*string*, *required*) - The **main file** path of the plugin. For theme, path to `functions.php`

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

    $insights = new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044891', 'Akismet', __FILE__ );
    $insights->init_plugin();
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

    $insights = new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044892', 'Twenty Twelve', __FILE__ );
    $insights->init_theme();
}

add_action( 'init', 'appsero_init_tracker_twenty_twelve' );
```

## More Usage

Sometimes you wouldn't want to show the notice, or want to customize the notice message. You can do that as well.

```php
$insights = new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044892', 'Twenty Twelve', __FILE__ );
```

#### 1. Hiding the notice

```php
$insights->hide_notice();
```

#### 2. Customizing the notice message

```php
$insights->notice('My Custom Notice Message');
```

#### 3. Adding extra data

You can add extra metadata from your theme or plugin. In that case, the **keys** has to be whitelisted from the AppSero dashboard.

```php
$insights->add_extra(array(
    'key'     => 'value',
    'another' => 'another_value'
));
```

#### Finally, initialize

After you instantiate the plugin, without calling `init_theme()` or `init_plugin()`, the required hooks will not be fired and nothing will work. So you must have to do this.

```php
$insights->init_plugin();

// or
$insights->init_theme();
```

#### Method Chaining

You can chain the methods as well.

```php
$insights = new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044892', 'Twenty Twelve', __FILE__ );

$insights->notice('Please allow us to track the usage')
    ->add_extra([
        'key'   => 'value',
        'value' => 'key'
    ])
    ->init_plugin();
```

---

### Dynamic Usage

In some cases you wouldn't want to show the optin message, but forcefully opt-in the user and send tracking data.

```php
$insights = new AppSero\Insights( 'a4a8da5b-b419-4656-98e9-4a42e9044892', 'Twenty Twelve', __FILE__ );

$insights->hide_notice()
    ->init_plugin();

// somewhere in your code, opt-in the user forcefully
// execute this only once
$insights->optin();
```

## Credits

Created and maintained by [AppSero](https://appsero.com).
