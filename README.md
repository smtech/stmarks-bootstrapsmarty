# StMarksSmarty

A wrapper for Smarty to provide a consistent UI for our scripts and apps.

## Install

Include in `composer.json`:

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

Complete [API documentation](http://htmlpreview.github.io/?https://github.com/smtech/stmarkssmarty/blob/master/doc/index.html) is included in the repo and the [Smarty API documentation](http://www.smarty.net/docs/en/) is also online.
