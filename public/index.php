<?php

require 'get-release.php';

$uri = $_SERVER['REQUEST_URI'];
$routes = ['/', '/download'];

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
    </head>
    <body>
        <h1>Millesime.phar</h1>
        <p>Build your PHP application into Phar archive(s).</p>
        <form action="/download" method="POST">
            <button>Download <span><?php echo $release->name; ?></span></button>
            <span>or</span>
            <a href="https://github.com/millesime/millesime">Discover on Github.</a>
        </form>
        <h2>Installation</h2>
        <pre><code>wget -O millesime.phar https://millesime.io/download
chmod +x millesime.phar
sudo mv millesime.phar /usr/local/bin/millesime</code></pre>

        <h2>Basic usage</h2>
        <p>To start using Millesime in your project, all you need is a <code>millesime.json</code> file. This file describes what files of your project will contain your <code>.phar</code> and may contain other metadata as well.</p>
        <p>You could use the <code>init</code> command to start with a quick <code>millesime.json</code> manifest.</p>
        <pre><code>cd /your/project/path
millesime init</code></pre>

        <p>Then use the <code>build</code> command each time you want to create <code>.phar</code> archives based on your project.</p>

        <pre><code>millesime build</code></pre>

        <p>With the <code>--watch</code> option, Millesime watches for changes to files and runs a build when a change occurs.</p>
    </body>
</html>