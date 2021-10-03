# MVC Introduction
## Controllers
#### Naming conventions:
- verb + thing + Controller => "ShowProductController"

MUST NOT:
- contain any code that should be shown in the browser, like HTML and CSS;
- deal with the database or the filesystem.
MUST DO:
- receive the HTTP requests;
- send back a response to browser.

### DB:
- only in dev | LOCAL environment you will see DB error!

## Model
#### Naming conventions:
- thing(singular) => "Product" + Model 

Its function:
- talks to DB;
- works with filesystem.

## Events:
#### Naming conventions:
- thing + verb (past tense) => "ProductCreated"

# Roman's Framework

## Project setup
```
composer install
```

## Update project deps
```
composer update
```

### Migrations
```
php migrations.php
```

### Compiles and minifies for production
```
Go To Config and change LOCAL => REMOTE
```

### Composer Scripts
```

```

### Customize configuration
See [Configuration Reference](https://getcomposer.org/).


### Developers
- UtmostCreator
