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

* This plugin allows you to keep the list of CSS and JS files in PHP configuration file instead of Twig templates.
* You can inject files from config file into frontend site and into control panel.
CSS and JS files will be injected into proper places - CSS files to end of `<head>` and JS files at end of `<body>`.
* You can also include links to google fonts CSS files, like `https://fonts.googleapis.com/css2?family=Montserrat:wght@100;400&display=swap` - it will be included like other CSS files.
* Plugin also allows for cache busting of files by appending URL parameter to their paths which contains their modification date. This means that files will be cache busted only if they were modified. Files from external servers won't be cache busted. 
* Plugin exposes endpoint that lists all static files in JSON format, so the list can be consumed by frontend build tools. This does not include google fonts files.

## Usage

In order to use plugin, place this code into base template of your project:

```
{% do craft.staticFileManager.outputFiles() %}
```

You can pass false to this function to cancel injection of assets into template:

```
{% do craft.staticFileManager.outputFiles(false) %}
```

Be advised that static files won't be inserted is there is no `<body>` or `<head>` tags in your template.

Files list is set in `config/static-file-manager.php` setting file, under `filesList` key. You can place files there as strings or as anonymous functions, if you want to add some conditional logic. 

To inject files into control panel, place them into `cpFilesList` setting. You dont need any additional Twig tag for that.

For example:

```
<?php
return [
   'filesList' => [
   		'some-file.css',
      'other-file.js',
   		function(){
            $language = Craft::$app->getSites()->currentSite->language;
            if($language == 'en'){
                    return 'english-file.js';
            }else{
                    return 'non-english-file.js';
            }
   		}
   ],
   'cpFilesList' => [
      'some-control-panel-styles.css',
   ]
];
```



Remember that you can access `Craft` object in all config files. You can use that to include different files on different sites or add other conditional logic like that.

```
if(Craft::$app->getSites()->currentSite->handle == 'someSite'){
  $files = [
    'some-file.js',
    'some-file.css',
  ];
}else{
  $files = [
    'other-file.js',
    'other-file.css',
  ];
}

return [
   'filesList' => $files,
];
```

## Twig filter

You can manually bust cache of files within Twig templates using `version` filter:

```
<script src="{{'some_script.js'|version}}"></script>
```

## JSON endpoint

Plugin exposes endpoint that returns asset list in JSON format. The list consists of two arrays - array with key `css` contains CSS files and array with key `js` contains Javascript files. Only files present on the filesystem are listed, so these from external servers are omitted from this list. List is available under `[website url]/actions/static-file-manager` URL, but you first need to enable it using `exposeJsonList` config setting.

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

* `filesList` - array with list of static files paths within web root directory (usually `web` directory). These files will be injected into frontend site.
* `cpFilesList` - array with list of static files within web root directory that will be injected into control panel.
* `bustCache` - if files should be cache busted. Default: `true`.
* `exposeJsonList` - if plugin should expose list of files in JSON format. Default: `false`.

---------------------------

Brought to you by [Piotr Pogorzelski](http://craftsnippets.com)
