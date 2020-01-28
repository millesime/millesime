<?php

require 'get-release.php';

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
<?php if ('/'===$uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>Create Phar archive(s) from your PHP project.</p>
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
                <p>Millesime is a tool for creating Phar archives in PHP.<br>
                It allows you to declare the files of your project that you want to be packaged and ignore those you 
                want to be ignored based on a single configuration file.</p>
                <aside>
                    <nav>
                        <a href="/getting-started">Getting started →</a>
                    </nav>
                </aside>
                <h3>What is "Phar archive"?</h3>
                <blockquote>
                    <p>Phar archives are best characterized as a convenient way to group several files into a single file. 
                    As such, a phar archive provides a way to distribute a complete PHP application in a single file and 
                    run it from that file without the need to extract it to disk. Additionally, phar archives can be executed 
                    by PHP as easily as any other file, both on the commandline and from a web server. Phar is kind of like a 
                    thumb drive for PHP applications.</p>
                    <footer>What is phar? in 
                        <a href="https://www.php.net/manual/en/intro.phar.php"><cite>PHP Manual</cite></a>.
                    </footer>
                </blockquote>
            </article>
        </main>
<?php elseif('/getting-started'===$uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool for creating Phar archives in PHP.</p>
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
                                <li>Install as a composer dependency</li>
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
                    <li>PHP 7.4.1 or above (at least 7.4.0 recommended to avoid potential bugs),</li>
                    <li>Phar extension with <a href="https://www.php.net/manual/fr/phar.configuration.php#ini.phar.readonly">phar.readonly</a> directive set to Off,</li>
                    <li>OpenSSL enabled if you want to sign your <code>.phar</code> archives.</li>
                </ul>
                <h4>Install as a composer dependency</h4>
                <pre><code>composer require millesime/millesime</code></pre>
                <pre><code>php vendor/bin/millesime</code></pre>
            </article>

            <article id="manifest">
                <h3>The <code>millesime.json</code> manifest</h3>
                <p>To start using Millesime in your project, all you need is a <code>millesime.json</code> file. This file describes what files of your project will contain your <code>.phar</code> and may contain other metadata as well.</p>
                <p>You could use the <code>init</code> command to start with a quick <code>millesime.json</code> manifest.</p>
                <pre><code>cd /your/project/path
millesime init</code></pre>
                <h4>The <code>packages</code> key</h4>
                <p>The goal of Millesime is to create phar archives. So one of the two minimal things that your <code>millesime.json</code> should contains is the archives names.</p>
                <pre><code>{
    "packages": [
        {
            "name": "yourpackage.phar",
            // ...
        }
    ]
}</code></pre>
                <p>As you see, you could create any phar archive you want from a single project.</p>
                <h4>The <code>package.finder</code> key</h4>
                <p>The second things yous has to do is to describe wich files wiil be included from your project into your phar archive.</p>
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
                <h4>Other config options</h4>
                <dl>
                    <dt>stub</dt>
                    <dd>Indicates the file inside your phar archive that yould be executed when someone will try to execute your phar package.</dd>
                    <dt>web-based</dt>
                    <dd>Indicates if your package will be executed by a webserver.</dd>
                    <dt>signature</dt>
                    <dd>Information upon your phar signature method.</dd>
                    <dt>scripts</dt>
                    <dd>A list of command that will be executed before build your archive.</dd>
                </dl>
            </article>


            <article id="build">
                <h3>Start creating archives</h3>
                <h4>Create archives from CLI</h4>
                <p>Then use the <code>build</code> command each time you want to create <code>.phar</code> archives based on your project.</p>
                <pre><code>millesime build</code></pre>
                <p>You will get the created .phar filenames as an output.</p>
                <h5>Parameters</h5>
                <p>The build command takes two optional parameters : <code>source</code> and <code>destination</code>. They could be usefull if you want to execute millesime from different working directory than your project.</p>
                <h5>Options</h5>
                <p>With the <code>--watch</code> (or <code>-w</code>) option, Millesime watches for changes to files and runs a build when a change occurs. If you want to avoid executing the scripts defined in the manifest, you could use the <code>--no-scripts</code> option. In case that your OpenSSL private key needs a passphrase, you could indicate it to millesime in the <code>--passphrase</code> (or <code>-p</code>) option. It is recommendanded to do so interractively.</p>
                <h4>Creating archives from code</h4>
                <p>I you had installed Millesime as a Composer dependency, you could use it as a library. For example :</p>
                <pre><code>&lt;?php
require 'vendor/autoload.php';

use Millesime\Millesime;
use Psr\Log\NullLogger;

$logger = new NullLogger; // millesime output
$passphrase = null; // eq. --passphrase option
$noSripts = false; // eq. --no-scripts option

$millesime = new Millesime($logger, $passphrase, $noSripts);
$packages = $millesime('/path/to/project', '/where/you/want/phar/archives');

foreach ($packages as $package) {
    echo $package->getName(). " has been created\n";
}
</code></pre>
                <aside>
                    <nav>
                        <a href="/executable-archives">Executable archives →</a>
                    </nav>
                </aside>
            </article>
        </main>
<?php elseif('/executable-archives'===$uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool for creating Phar archives in PHP.</p>
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
                <h3>Make for CLI</h3>
                <pre><code>&lt;?php
// command.php

printf('Hello %s!', $argv[0]);
</code></pre>
                <pre><code>{
    "packages" : [
        {
            "name": "hello-world.phar"
            "stub": "command.php",
            "web-based": false,
            // ...
        }
    ]
}</code></pre>
                <pre><code>hello-world.phar Simon</code></pre>
                <p>The command above will repond <em>Hello Simon!</em>.</p>
                <h3>Make for Web</h3>
                <pre><code>&lt;?php
// index.php

printf('Hello %s!', $_GET['name']);
</code></pre>
                <pre><code>{
    "packages" : [
        {
            "name": "hello-world.phar"
            "stub": "index.php",
            "web-based": true,
            // ...
        }
    ]
}</code></pre>
                <pre><code>php -S localhost:80 hello-world.phar</code></pre>
                <p>You webapp running on 80 will respond <em>Hello Garfunkel</em> to <a href="http://localhost:80/?name=Garfunkel" rel="nofollow">http://localhost:80/?name=Garfunkel</a></p>
            </article>
        </main>
<?php elseif('/signed-archives'===$uri): ?>
        <header>
            <h1>Millesime.phar</h1>
            <p>A tool for creating Phar archives in PHP.</p>
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