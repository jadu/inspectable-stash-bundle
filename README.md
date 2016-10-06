Inspectable Stash bundle
========================

[![Build Status](https://secure.travis-ci.org/jadu/inspectable-stash-bundle.png?branch=master)](http://travis-ci.org/jadu/inspectable-stash-bundle)

Symfony bundle to allow inspecting data cached with Stash.

## Usage
Add the bundle to your kernel to install the cache driver proxy and inspection console command:

```php
class AppKernel
{
    ...
    
    public function registerBundles()
    {
        $bundles = array(
            ...
            new Tedivm\StashBundle\TedivmStashBundle(),
            new Jadu\InspectableStashBundle\InspectableStashDriverBundle(),
            ...
        );

        return $bundles;
    }
    
    ...
}
```

## Inspecting the cached data

You can use the console command:
```shell
app/console dump:stash:cache
```
to fetch a list of all keys stored by Stash. If you need to filter this list
for only keys matching a certain pattern, you may provide a regular expression like this:
```shell
app/console dump:stash:cache --grep 'some/prefix.*later'
```
. Finally, if you need to see the values for each cache entry too, just add the `--with-values` option, like this:
```shell
app/console dump:stash:cache --grep 'some/prefix.*later' --with-values
```
. `--with-values` may be used with or without `--grep`.

## Credits

* [Dan Phillimore](http://github.com/asmblah) - Author
* [Stash caching library](https://github.com/tedious/Stash)
* [Stash bundle for Symfony](https://github.com/tedious/TedivmStashBundle)
