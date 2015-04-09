# simple wiki

This is a simple wiki I made to store notes etc. It's written in PHP because
I tend to keep a web server running on my Mac and PHP runs on there.

## Get started

1. Clone repo
2. `composer install`
3. `php c.php install`
4. Open a web browser

## Features

Some things it does have is a list of all pages, simple image support and
the ability to completely change the type of page in use.

To access this you'll need a "YAML Front" which looks like so:

```
---
type: sms
---
something something something
```

These extra formats can be found in the [docs](docs).

## TODO

- Editor on the web (maybe)
