# Compiler

Compile you PHP application into Phar archive.

```
cd /your/project/
millesime init
millesime compile
```

This program will creates a phar archive from your project.


## `millesime.json` project setup

You could define more options to millesime by using a millesime.json file.

### The `distrib` key

```json
{
	"name": "app",
    "distrib": [
        {
            "name": "ditrib",
            "stub": "bin/app"
        }
    ]
}
```

- `name` will be the name of your phar archive. Exemple `millesime.phar`,
- `stub` is the path who will be called when you will try to execute your archive as pphp script.
