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

## Credits

* [Dan Phillimore](http://github.com/asmblah) - Author
* [Stash caching library](https://github.com/tedious/Stash)
* [Stash bundle for Symfony](https://github.com/tedious/TedivmStashBundle)
