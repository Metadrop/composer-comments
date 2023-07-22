# Composer Comments Plugin

This Composer plugins allows to add comments on Composer packages in the `composer.json` file. Those comments are displayed when the package is installed, updated or removed. Also, a `comments` command is provided to check if there are any comments.

## Install

Just add the plugin to your Composer project:

```
composer require metadrop/composer-comments
```

## Limitations

The comments are not displayed when using `--dry-run` because the packages are not actually being installed, removed or updated. This is due how Composer emits events: Composer does not emit update/intall/remove event so this plugin can't display the comments associated to a package.

### Local development

CLone this repository and start hacking!

However, you will need to run it and test it. For this, can add the following code to the `composer.json` file on another directory where you want to run and test this composer plugin:

```json
  "repositories": [
    {
      "type": "path",
      "url": "/path/to/composer-comments"
    }
```

`/path/to/composer-comments` should be a folder where you have cloned this repository.

## Adding comments

Add commments in your `composer.json` using the `extra` property:

```json
  "extra": {
    "package-comments": {
      "vendor/package1": "A comment about package1",
      "vendor/package2": "A comment about package2",
      "vendor/package3": "A comment about package3",
    }
  }
```

Packages that have comments don't need to be required. In other words, you can add comments for packages that are not present in your project.

## Displaying comments

To display all comments run:

```
composer comments
```

To display comments for a given package:

```
composer comments vendor/package
```


## What for?

Sometimes packages are required and later it is not clear why. Or maybe certain package release is requried or you know that certain package should not be used. Using this plugin you can add that information directly to the `composer.json` file, increasing the chances a developer sees the message when they are dealing with packages avoiding issues in the future.

It is recommended to add only information that is not obvious from the `composer.json`.

For example:

```json
  "extra": {
    "package-comments": {
      "vendor/bogus_package": "This package was tested but the peformance was not good enough and was discarded",
      "vendor/bogus_release": "On certain installations this package triggered a mysterious error. We decided to stick to releaase 1.2.3 because the bug as not present there until we found the root cause.",
      "vendor/good_package": "This package is used to provide X functionality"
    }
  }
```

Please, don't do this:

```json
  "extra": {
    "package-comments": {
      "vendor_x/package": "This package is provided by Vendor X",
      "vendor/fancy_package": "This is the fancy package",
      "vendor/package": "Using version 1.2.3"
    }
  }
```

