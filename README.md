# Compiler

Compile you PHP application into Phar archive.

```
cd /your/project/
compiler init
compiler compile
```

This program will creates a phar archive from your project.


## `compiler.json` project setup

You could define more options to compiler by using a compiler.json file.

### The `distrib` key

```json
{
	"name": "Phar Compiler",
    "distrib": [
        {
            "name": "compiler",
            "stub": "bin/compiler"
        }
    ]
}
```

- `name` will be the name of your phar archive. Exemple `compiler.phar`,
- `stub` is the path who will be called when you will try to execute your archive as pphp script.
