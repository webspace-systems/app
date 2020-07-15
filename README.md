
# Index #


- [__router.php__](#routerphp)

- [__app.php__](#appphp)

- - [__config.php__](#configphp)

- - [__template__](#template)

- - [__plugins/asset_load_controller__](#pluginsasset_load_controller)

- - [__public__](#public)

- [__error/trait.php__](#errortraitphp)

- [__error/error.js__](#errorerrorjs): front-end error handling

- [__sql/trait.php__](#sqltraitphp): 'sql' and wrapper funct.

- [__user/trait.php__](#usertraitphp): login funct. with method 'user']

- [__sh__](#sh) Bash scripts

- [__sh/test__](#scriptstestsh)

- - [__sh/tests/php_syntax.sh__](#scriptstestsphp_syntaxsh)

- - [__sh/tests/php_tests.sh__](#scriptstestsphp_testssh)

- [__Notes__](#notes)

- - [__About PHP "Traits"__](#about-php-traits)







# router.php #

[...]






# app.php #

See [index.php](src/index.php)

 - See component [user/login](src/user/login) for example of component working as page module.

 - A component can also supply php traits like sql and error handling, or js like functions for ajax communication.


## config.php ##

[...]


## template ##

Template front-end [...]


## plugins/asset_load_controller ##

JS feature to ensuring complete loading of all css and js assets,
Compiled and inserted directly in the header.
[https://github.com/theiscnp/Asset_Load_Controller](https://github.com/theiscnp/Asset_Load_Controller)



## public ##

Thought as a creative name for the default public frontpage website.





# error/trait.php #

[Probably outdated]

function __error__ ( $msg = "", $add_data = [], $die = true, $is_user_fault = false, $code = 500 )
- just proxy for static \_error:

*static* function __\_error__($msg = "", $add_data = [], $die = true, $is_user_fault = false, $code = 500)
- Arguments $die and $code may switch places

*static* function __\_on_error__($errno, $errstr, $errfile, $errline, $in_shutdown = false){
- to handle php errors and funct. 'trigger_error'. Note btw that trigger_error() only supports the E_USER_* class of warnings.
- just require error/trait.php && set_error_handler(['_error','\_on_error']);
- prevents default error handling

*static* function __\_on_shutdown__(){ Pass error_get_last() to __\_on_error__ }
- to handle fatal errors, make sure the error/trait.php is required and `register_on_shutdown(['_error','_on_shutdown']);`


# error/error.js #

front-end error handling





# sql/trait.php #

always returning sql instance


# user/trait.php #

Simple user system incl. trait function 'user' & 'user_require'.





# sh #

Scripts for misc. automatization of special tasks like testing & compiling
Fx. maybe we can also make JS unit tests using Babel...?!
`node ./node_modules/babel-cli/bin/babel-node.js tests/error/trait.js`


- [__sh/lint__](#sh_lint)
- [__sh/test__](#sh_test)
- [__sh/test_php_tests__](#sh_test_php_tests)
- [__sh/test_php_syntax__](#sh_test_php_syntax)




If you get: `-bash: sh/build: Permission denied`, just $`chmod 777 sh/build`



## sh/lint ##

$`sh/lint`



## sh/test ##

$`sh/test`

to

1. [sh/tests/php_tests.sh](sh/tests/php_tests.sh)
2. [sh/tests/php_syntax.sh](sh/tests/php_syntax.sh)


## sh/test_php_tests.sh ##

Runing php test scripts in dir 'tests'.

If the script outputs "OK", the test is considered successfull

See [sh/test_php_tests.sh](sh/test_php_tests.sh)



## sh/test_php_syntax.sh ##

Syntax checking .php files in dir 'src' (plugins excl.)

See [sh/test_php_tests.sh](sh/test_php_syntax.sh)







# sh/build #

$`sh/build`:

```
bash

babel src -d dist -D -x [.js] --no-comments --ignore [plugins/*] --verbose

```


 - ¿ Also test.. ?







# Notes #

- pluralisation rules

- `const findComponentByPath = (path, routes) => routes.find(r => r.path.match(new RegExp(`^\\${path}$`, 'gm'))) || undefined;`



## About PHP "Traits" ##

Traits extend the class applied to.
The methods of the trait are protected agains overwriting if not extended like `parent::__construct();`.
Apply using the 'use'-statement like this:

```php

trait _connection_trello {

	function connect_trello(){

		return "Fancy shit";
	}
}

class kanban extends app {

	use _connection_trello;

	// ...
}

print((new kanban())->connect_trello()); // "Fancy shit"

```

*
Traits are a mechanism for code reuse in single inheritance languages such as PHP. A Trait is intended to reduce some limitations of single inheritance by enabling a developer to reuse sets of methods freely in several independent classes living in different class hierarchies. The semantics of the combination of Traits and classes is defined in a way which reduces complexity, and avoids the typical problems associated with multiple inheritance and Mixins.

A Trait is similar to a class, but only intended to group functionality in a fine-grained and consistent way. It is not possible to instantiate a Trait on its own. It is an addition to traditional inheritance and enables horizontal composition of behavior; that is, the application of class members without requiring inheritance.
*
[See php.net/manual/en/language.oop5.traits.php](https://www.php.net/manual/en/language.oop5.traits.php)

