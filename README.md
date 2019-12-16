# Static file manager

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require craftsnippets/static-file-manager

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for asset manifest.

## Overview

* This plugin allows you to keep the list of static files in PHP configuration file instead of Twig.
CSS and JS files will be injected into proper places - CSS files to end of `<head>` and JS files at end of `<body>`.
* Plugin also allows for cache busting of files by appending URL parameter to their paths with their modification date. Files from external servers won't be cache busted. 
* Plugin exposes endpoint that lists all static files in JSON format, so the list can be consumed by frontend build tools. 



## Usage

In order to use plugin, place this variable into base template of your project:

```
{{craft.staticFileManager.outputFiles()}}
```

You can pass false to this function to cancel injection of assets into template:

```
{{craft.staticFileManager.outputFiles(false)}}
```

Be advised that static files won't be inserted is there is no`<body>` or `<head>` tags in your template.

Files list is set in `config/static-file-manager.php` setting file, under `filesList` key. You can place files there as strings or as anonymous functions, if you want to add some conditional logic. For example:

```
<?php
return [
   'filesList' => [
   		'some-lib.css',
      'jquery.js',
   		function(){
            $language = Craft::$app->getSites()->currentSite->language;
            if($language == 'en'){
                    return 'english-file.js';
            }else{
                    return 'non-english-file.js';
            }
   		}
   ],
];
```

## JSON endpoint

Plugin exposes endpoint that returns asset list in JSON format. The list consists of two arrays - array with key `css` contains CSS files and array with key `js` contains Javascript files. Only files present on the filesystem are listed, so these from external servers are omitted from this list. List is available under `[website url]/actions/static-file-manager` URL.

Here's an example gulp task that loads list of Javascript files from endpoint and minifies them into one file. This config assumes that you use xampp and your project lives in `htdocs` directory.

```
var gulp = require('gulp');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
const https = require('http');

gulp.task('production', ['sass:production', 'uglify:production']);

gulp.task('uglify:production', function() {

// set endpoint url
let file_path = __dirname;
let explode = file_path.split('htdocs');
let url = 'http://localhost'+explode[1]+'/web/actions/static-file-manager';

// load data

https.get(url,(res) => {
    let body = "";

    res.on("data", (chunk) => {
        body += chunk;
    });

    res.on("end", () => {

        let json = JSON.parse(body);
        let js = json['js'];
        
        // gulp task
        return gulp.src(js)
        .pipe(concat('main.min.js'))
        .pipe(uglify())
        .on('error', swallowError)
        .pipe(gulp.dest('web/static'));

    });

});

});
``` 


## Settings

Place these settings in `config/static-file-manager.php` file.

* `filesList` - array consisting list of static files paths within web root directory (usually `web` directory).
* `bustCache` - if files should be cache busted. Default: `true`.
* `exposeJsonList` - if plugin should expose list of files in JSON format. Default: `true`.



Brought to you by [Piotr Pogorzelski](http://craftsnippets.com)
