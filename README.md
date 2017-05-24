# StMarksSmarty

[![Latest Version](https://img.shields.io/packagist/v/smtech/stmarkssmarty.svg)](https://packagist.org/packages/smtech/stmarkssmarty)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smtech/stmarks-bootstrapsmarty/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smtech/stmarks-bootstrapsmarty/?branch=master)

A wrapper for Smarty to provide a consistent UI for our scripts and apps.

## Install

Because this makes use of front-end files managed via Bower, as well as the back-end managed by Composer, it is _really, really, super-helpful_ to run the following command before trying to work with this package:

```BASH
composer global require "fxp/composer-asset-plugin:^1.1"
```

Find out more about [`fxp/composer-asset-plugin`](https://github.com/francoispluchino/composer-asset-plugin) and [Bower](http://bower.io/).

And then, include in `composer.json`:

```
"require": {
  "smtech/stmarkssmarty": "dev-master"
}
```

## Use

If you have no templates of your own:

```
$smarty = StMarksSmarty::getSmarty();

// ...app logic...

$smarty->assign('content', '<p>whatever content you want displayed</p>');
$smarty->display();
```

If you have your own templates directory:

```
$smarty->addTemplateDir('<path-to-your-templates-dir>');
```

If your app will be presented as an embedded `iframe`:

```
$smarty = StMarksSmarty::getSmarty(true);
```

Complete [API documentation](http://smtech.github.io/stmarkssmarty/index.html) is included in the repo and the [Smarty API documentation](http://www.smarty.net/docs/en/) is also online.
