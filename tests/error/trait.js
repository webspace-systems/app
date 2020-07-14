
import { tests } from '../tests.js'

new tests ( { 'file': 'router.php', 'function': 'router::route' }, [

	{ 'parameters': [''],				'must_result': [ 'null', null ]  },
	{ 'parameters': ['.'],				'must_result': [ 'null', null ]  },
	{ 'parameters': ['index'],			'must_result': [ 'null', null ]  },
	{ 'parameters': ['router'],		'must_result': [ 'null', null ]  },
	{ 'parameters': ['router.php'],	'must_result': [ 'null', null ]  },
	{ 'parameters': ['config'],		'must_result': [ 'null', null ]  },
	{ 'parameters': ['config.php'],	'must_result': [ 'null', null ]  },
	{ 'parameters': ['sql'],			'must_result': [ 'null', null ]  },
	{ 'parameters': ['sql.php'],		'must_result': [ 'null', null ]  },
	{ 'parameters': ['_sql'],			'must_result': '_sql'  }

])
