

#  components  #



- [__router.php__](#routerphp)

- [__config.php__](#configphp)

- [__app.php__](#appphp)

- [__template__](#template)

- [__plugins/asset_load_controller__](#pluginsasset_load_controller)

- [__error/trait.php__](#errortraitphp)

- [__error/error.js__](#errorerrorjs)

- [__sql/trait.php__](#sqltraitphp)

- [__user/trait.php__](#usertraitphp)

- [__sh__](#sh)

- [__sh/test__](#scriptstestsh)

- - [__sh/tests/php_syntax.sh__](#scriptstestsphp_syntaxsh)

- - [__sh/tests/php_tests.sh__](#scriptstestsphp_testssh)

- [__Notes__](#notes)

- - [__About PHP "Traits"__](#about-php-traits)








#  router.php  #


[...]





#  config.php  #

[...]





#  app.php  #


Implemented by [index.php](src/index.php)

 

 - See component [user/login](src/user/login) for example of component working as page module.

 - A component can also supply php traits like sql and error handling, or js like functions for ajax communication.



#  template  #


[...]





#  plugins/asset_load_controller  #

JS feature to ensuring complete loading of all css and js assets,
Compiled and inserted directly in the header.
[https://github.com/theiscnp/Asset_Load_Controller](https://github.com/theiscnp/Asset_Load_Controller)








#  error/trait.php  #

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



#  error/error.js  #


[...]




#  sql/trait.php  #


[...]





#  user/trait.php  #


Simple user system incl. trait function 'user' & 'user_require'.







#  sh  #


Bash scripts to:


- __Validate syntax__

- - [__sh/test_php_syntax__](#sh_test_php_syntax)


- __Enforce code style__

- - [__sh/lint__](#sh_lint)

- - [__sh/format__](#sh_format)


- __Unit test__

- - [__sh/test_php_tests__](#sh_test_php_tests)

- - [__sh/test_js_tests__](#sh_test_js_tests)





##  sh/lint  ##


  $ `sh/lint`


to

 1. [sh/lint_js](sh/lint_js)

 2. [sh/lint_php](sh/lint_php)



## # sh/lint_js # ##

[...]



## # sh/lint_php # ##

[...]







##  sh/test  ##


  $ `sh/test`


to

 1. [sh/tests/php_tests.sh](sh/tests/php_tests.sh)

 2. [sh/tests/php_syntax.sh](sh/tests/php_syntax.sh)


![image](data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAEYAAAAUCAAAAAAVAxSkAAABrUlEQVQ4y+3TPUvDQBgH8OdDOGa+oUMgk2MpdHIIgpSUiqC0OKirgxYX8QVFRQRpBRF8KShqLbgIYkUEteCgFVuqUEVxEIkvJFhae3m8S2KbSkcFBw9yHP88+eXucgH8kQZ/jSm4VDaIy9RKCpKac9NKgU4uEJNwhHhK3qvPBVO8rxRWmFXPF+NSM1KVMbwriAMwhDgVcrxeMZm85GR0PhvGJAAmyozJsbsxgNEir4iEjIK0SYqGd8sOR3rJAGN2BCEkOxhxMhpd8Mk0CXtZacxi1hr20mI/rzgnxayoidevcGuHXTC/q6QuYSMt1jC+gBIiMg12v2vb5NlklChiWnhmFZpwvxDGzuUzV8kOg+N8UUvNBp64vy9q3UN7gDXhwWLY2nMC3zRDibfsY7wjEkY79CdMZhrxSqqzxf4ZRPXwzWJirMicDa5KwiPeARygHXKNMQHEy3rMopDR20XNZGbJzUtrwDC/KshlLDWyqdmhxZzCsdYmf2fWZPoxCEDyfIvdtNQH0PRkH6Q51g8rFO3Qzxh2LbItcDCOpmuOsV7ntNaERe3v/lP/zO8yn4N+yNPrekmPAAAAAElFTkSuQmCC)



##  sh/test_php_tests.sh  ##

Runing php test scripts in dir 'tests'.

If the script outputs "OK", the test is considered successfull

See [sh/test_php_tests.sh](sh/test_php_tests.sh)



##  sh/test_php_syntax.sh  ##

Syntax checking .php files in dir 'src' (plugins excl.)

See [sh/test_php_tests.sh](sh/test_php_syntax.sh)



##  sh/test_js_syntax  ##

[...]


##  sh/test_js_tests  ##

[$ `node ./node_modules/babel-cli/bin/babel-node.js tests/error/trait.js `]








##  sh/build  ##

  
  $ `sh/build`


```bash

babel src -d dist -D -x [.js] --no-comments --ignore [plugins/*] --verbose

```


















#  Notes  #

- pluralisation rules

- ```js  const findComponentByPath = (path, routes) => routes.find(r => r.path.match(new RegExp(`^\\${path}$`, 'gm'))) || undefined;```

- Consider by comparing __*jshint*__ vs *eslint*





##  About PHP "Traits"  ##

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

* [php.net/manual/en/language.oop5.traits.php](https://www.php.net/manual/en/language.oop5.traits.php)



