Millesime
=========

[![Build Status](https://secure.travis-ci.org/millesime/millesime.svg?branch=master)](http://travis-ci.org/millesime/millesime) [![Code Coverage](https://codecov.io/gh/millesime/millesime/branch/master/graph/badge.svg)](https://codecov.io/gh/millesime/millesime) [![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/millesime/millesime/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/millesime/millesime) [![Total Downloads](https://poser.pugx.org/millesime/millesime/downloads.png)](https://packagist.org/packages/millesime/millesime) [![Latest Stable Version](https://poser.pugx.org/millesime/millesime/v/stable.png)](https://packagist.org/packages/millesime/millesime)

Compile you PHP application into Phar archive(s).

```bash
mkdir hello-world && cd hello-world

echo "<?php echo 'Hello, word !';" > index.php

millesime init hello-world

millesime compile

php -S localhost:8080 hello-world.phar
```

Read the documentation and discover **Millesime** with a _Hello World_ application to [getting started](https://github.com/millesime/millesime/wiki/Getting-Started).

## Installation

```bash
wget https://millesime.io/download

chmod +x millesime.phar

mv millesime.phar /usr/local/bin/millesime
```

Alternatively, you may use [Composer](https://getcomposer.org/) to download and install **Millesime** as well as its dependencies. Please refer to the [documentation](https://github.com/millesime/millesime/wiki/Installation) for details on how to do this.