Installation
============

Add this to composer.json in "require" section:

.. code-block:: json

	"drastus/simplegrid": "dev-master"

Then in config/app.php in 'providers' array:

.. code-block:: php

	'Drastus\SimpleGrid\SimpleGridServiceProvider',

and in 'aliases' array:

.. code-block:: php

	'Grid' => 'Drastus\SimpleGrid\Facades\SimpleGridFacade',
