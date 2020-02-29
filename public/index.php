<?php

require __DIR__.'/get-release.php';

$uri = $_SERVER['REQUEST_URI'];
$routes = ['/', '/getting-started', '/executable-archives', '/signed-archives', '/download'];

if (!in_array($uri, $routes)) {
    header("HTTP/1.0 404 Not Found");
    exit(404);
}

$release = get_release();
$asset = get_asset($release, 'millesime.phar');

if ('/download' === $uri) {
    header("HTTP/1.1 302 Found");
    header("Location: ".$asset->browser_download_url);
    exit(302);
}

?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Millesime.phar - Build you PHP application into Phar archive(s).">
        <title>Millesime.phar - Build your PHP application into Phar archive(s).</title>
        <style type="text/css">
            body {
                width: 80%;
                margin: 0 auto;
            }
        </style>
    </head>
    <body>
    <?php if ('/' === $uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>Create Phar archives from your PHP project.</p>
        </header>
        <nav aria-labelledby="mainmenulabel">
            <h2 id="mainmenulabel">Main menu</h2>
            <li><span>Current page :</span> Home</li>
            <li><a href="/getting-started">Getting started</a></li>
            <li><a href="/executable-archives">Executable archives</a></li>
            <li><a href="/signed-archives">Signed archives</a></li>
        </nav>
        <form action="/download" method="POST">
            <p>
            <button>Download <span><?= $release->name ?></span></button>
            <span>(<?= $asset->name ?>, <?= round($asset->size/1048576, 1) ?> MB)</span>
            </p>
        </form>
        <main>
            <article id="what-is-millesime">
                <h2>What is Millesime?</h2>
                <p>Millesime is a tool to create Phar archives in PHP.<br>
                A single configuration file allows to specify the files to be packaged and to be ignored.</p>
                <aside>
                    <nav>
                        <a href="/getting-started">Getting started →</a>
                    </nav>
                </aside>
                <h3>What is "Phar archive"?</h3>
                <blockquote>
                    <p>A Phar artiche can be characterized as a convenient way to group several files into a single bundle.<br> 
                    It's a simple way to distribute and to run a complete PHP application from a single file.<br>
                    Additionally, phar archives can be executed by PHP as standard PHP files, 
                    both from command line and web server. <br>
                    You can think of it as the executable thumb drive for your PHP applications.</p>
                    <footer>What is phar? in 
                        <a href="https://www.php.net/manual/en/intro.phar.php"><cite>PHP Manual</cite></a>.
                    </footer>
                </blockquote>
            </article>
        </main>
    <?php elseif('/getting-started' === $uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool to create Phar archives in PHP.</p>
        </header>
        <nav aria-labelledby="mainmenulabel">
            <h2 id="mainmenulabel">Main menu</h2>
            <li><a href="/">Home</a></li>
            <li><span>Current page :</span> Getting started</a></li>
            <li><a href="/executable-archives">Executable archives</a></li>
            <li><a href="/signed-archives">Signed archives</a></li>
        </nav>
        <main>
            <article>
                <h2>Getting started</h2>
                <nav aria-labelledby="tocheader">
                    <h3 id="tocheader">Table of Contents</h3>
                    <ol>
                        <li>
                            <a href="#installation">Installation</a>
                            <ol>
                                <li>Requirements</li>
                                <li>Install via composer</li>
                            </ol>
                        </li>
                        <li>
                            <a href="#manifest">The <code>millesime.json</code> manifest</a>
                            <ol>
                                <li>The packages key</li>
                                <li>The package.finder key</li>
                                <li>Other config options</li>
                            </ol>
                        </li>
                        <li>
                            <a href="#build">Start creating archives</a>
                            <ol>
                                <li>Create archives from CLI</li>
                                <li>Creating archives from code</li>
                            </ol>
                        </li>
                    </ol>
                </nav>
            </article>
            <article id="installation">
                <h3>Installation</h3>
                <p>Simply <a href="/download">download</a> the <em>millesime.phar</em> archive and make it executable.</p>
                <pre><code>wget -O millesime.phar https://millesime.io/download
    chmod +x millesime.phar</code></pre>
                <p>Move it into <code>/usr/local/bin</code> if you want to use it globaly.</p>
                <pre><code>sudo mv millesime.phar /usr/local/bin/millesime</code></pre>
                <h4>Requirements</h4>
                <ul>
                    <li>PHP 7.4.1 and above (at least 7.4.0 recommended to avoid potential bugs),</li>
                    <li>Phar extension with <a href="https://www.php.net/manual/fr/phar.configuration.php#ini.phar.readonly">phar.readonly</a> directive set to Off,</li>
                    <li>OpenSSL enabled if you want to sign your <code>.phar</code> archives.</li>
                </ul>
                <h4>Install via composer</h4>
                <pre><code>composer require millesime/millesime</code></pre>
                <pre><code>php vendor/bin/millesime</code></pre>
                <h4>Install globally via composer</h4>
                <p>To install Millesime, install Composer and issue the following command:</p>
                <pre><code>composer global require millesime/millesime</code></pre>
                <p>Then make sure you have the global Composer binaries directory in your <code>PATH</code>.
                    This directory is platform-dependent, see Composer documentation for details. Example for some Unix systems:</p>
                <pre><code>$ export PATH="$PATH:$HOME/.composer/vendor/bin"</code></pre>
            </article>

            <article id="manifest">
                <h3>The <code>millesime.json</code> manifest</h3>
                <p>Millesime can be configured with a <code>millesime.json</code> manifest. <br>
                This is were the name of the archive, the files to include or exclude from the phar archives will configured.</p>
                <p>Use the <code>init</code> command to create a default <code>millesime.json</code> manifest.</p>
                <pre><code>cd /your/project/path
    millesime init</code></pre>
                <h4>The <code>packages</code> section</h4>
                <p>This the main section of the manifest. This is where each archives to be created will be configured.<br>
                    This section contains an array of package objects.</p>
                <p>The <code>name</code> indicates the output path of the phar archive.</p>
                <pre><code>{
    "packages": [
        {
            "name": "yourpackage.phar",
            // ...        
        }
    ]
    }</code></pre>
                
                <p>The <code>stub</code> is the entrypoint of your application. It's the file that is loaded when the phar is run.</p>
                            <pre><code>{
                "packages": [
                    {
                        "name": "yourpackage.phar",
                        "name": "public/index.php"
                        // ...
                    }
                ]
                }</code></pre>

                <p>The <code>finder</code> object indicates the files and directories to include in the phar archive.</p>
                <pre><code>{
    "packages": [
        {
            "name": "yourpackage.phar",
            "finder": {
                "in": ["src", "vendor"],
                "name": ["*.php", "*.yml"],
                "ignoreVCS": true
            }
        }
    ]
    }</code></pre>

                <h4>Configuration options</h4>
                <dl>
                    <dt>name <em>(required)</em></dt>
                    <dd>Output path of the generated archive</dd>
                    <dt>stub (required)</dt>
                    <dd>Entrypoint of the phar package. 
                        It's the file that is exectued when the archive is run.</dd>
                    <dt>finder <em>(required)</em></dt>
                    <dt>finder.in</dt>
                    <dd>Directories to include</dd>
                    <dt>finder.name</dt>
                    <dd>Pattern of files to include</dd>
                    <dt>finder.ignoreVSC</dt>
                    <dd>Excludes all git files from package.</dd>
                    <dt>web-based</dt>
                    <dd>Indicates if your package will be executed on a webserver.</dd>
                    <dt>signature</dt>
                    <dd>Configuration of the phar signature method.</dd>
                    <dt>scripts</dt>
                    <dd>List of commands to execute before building the archive.</dd>
                </dl>
            </article>

            <article id="build">
                <h3>Start creating archives</h3>
                <h4>From CLI</h4>
                <pre><code>millesime build</code></pre>
                <p>Create the <code>.phar</code> archives in the directories specified in the `packages.name` section of the <code>millesime.json</code> manifest.</p>
                <h5>Parameters</h5>
                <p>The build command takes two optional parameters: <code>source</code> and <code>destination</code>. These arguments can be used to run Millesime from outside the project directory.</p>
                <pre><code>millesime build source destination</code></pre>
                <h5>Options</h5>
                <p><code>--watch</code> (or <code>-w</code>): automatically build the archives when a change occurs.<br>
                    <code>--no-scripts</code>: prevent the scripts defined in the manifest to be executed.<br>
                    <code>--passphrase</code> (or <code>-p</code>): passphrase of the OpenSSL private key. If left empty, the input will be interactive.</p>

                <h4>From PHP</h4>
                <p>Millesime is available as PHP library when installed using Composer.</p>
                <pre><code>&lt;?php
    require 'vendor/autoload.php';

    use Millesime\Millesime;
    use Psr\Log\NullLogger;

    $logger = new NullLogger; // Disable millesime output
    $passphrase = null; // eq. --passphrase option
    $noScripts = false; // eq. --no-scripts option

    $millesime = new Millesime($logger, $passphrase, $noScripts);
    $packages = $millesime('/path/to/project', '/where/you/want/phar/archives');

    foreach ($packages as $package) {
    echo "$package->getName() created\n";
    }
    </code></pre>
                <aside>
                    <nav>
                        <a href="/executable-archives">Executable archives →</a>
                    </nav>
                </aside>
            </article>
        </main>
    <?php elseif('/executable-archives' === $uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool to create Phar archives in PHP.</p>
        </header>
        <nav aria-labelledby="mainmenulabel">
            <h2 id="mainmenulabel">Main menu</h2>
            <li><a href="/">Home</a></li>
            <li><a href="/getting-started">Getting started</a></li>
            <li><span>Current page :</span> Executable archives</a></li>
            <li><a href="/signed-archives">Signed archives</a></li>
        </nav>
        <main>
            <article>
                <h2>Executable archives</h2>
            </article>
            <article>
                <h3>Run from CLI</h3>
                <pre><code>&lt;?php
    // command.php

    printf('Hello %s!', $argv[0]);
    </code></pre>
                <pre><code>{
    "packages": [
        {
            "name": "hello-world.phar"
            "stub": "command.php",
            "web-based": false,
            // ...
        }
    ]
    }</code></pre>
                <pre><code>php hello-world.phar Simon</code></pre>
                <p>The command above will output <em>Hello Simon!</em>.</p>
                <h3>Make for Web</h3>
                <pre><code>&lt;?php
    // index.php

    printf('Hello %s!', $_GET['name']);
    </code></pre>
                <pre><code>{
    "packages": [
        {
            "name": "hello-world.phar"
            "stub": "index.php",
            "web-based": true,
            // ...
        }
    ]
    }</code></pre>
                <pre><code>php -S localhost:80 hello-world.phar</code></pre>
                <p>Your webapp running on the port 80 will output <em>Hello Garfunkel</em> to <a href="http://localhost:80/?name=Garfunkel" rel="nofollow">http://localhost:80/?name=Garfunkel</a></p>
            </article>
        </main>
    <?php elseif('/signed-archives'===$uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool to create Phar archives in PHP.</p>
        </header>
        <nav aria-labelledby="mainmenulabel">
            <h2 id="mainmenulabel">Main menu</h2>
            <li><a href="/">Home</a></li>
            <li><a href="/getting-started">Getting started</a></li>
            <li><a href="/executable-archives">Executable archives</a></li>
            <li><span>Current page :</span> Signed archives</a></li>
        </nav>
        <main>
        </main>
    <?php endif; ?>

        <footer>
            <hr>
            <p>
                Latest release published on <time datetime="<?= $release->published_at ?>"><?= $release->published_at ?></time><br>
                <a href="https://github.com/millesime/millesime/issues/">Report an issue</a>
            </p>
        </footer>
    </body>
</html>