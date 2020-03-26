# Array Cache

This is a helper to save cache looking at keys as dotted arrays. So we are able to clear any sub-array we want. example :
I have three cache keys `fields.object1.value1` , `fields.object1.value2` and  `fields.object2.value1`, if we look at them as dotted arrays it would look like this :

- fields
    - object1
        - value1
        - value2
    - object2
        - value1

calling `\ArrayCache::forget('fields.object1')` will forget `fields.object1.value1` and `fields.object1.value2`.
calling `\ArrayCache::forget('fields')` will forget the 3 keys.

## Available methods

All methods for retrieving/forgetting keys from the Laravel cache repository are available

## Installation

Install package through composer : `composer require ryssbowh/laravel-array-cache`

Register the service provider `Ryssbowh\ArrayCacheServiceProvider`