# document-storage

Provides adapters to various storage services.

[![Author](http://img.shields.io/badge/author-@baruica-blue.svg?style=flat-square)](https://twitter.com/baruica)
[![Build Status](https://img.shields.io/travis/baruica/document-storage.svg?style=flat-square)](https://travis-ci.org/baruica/document-storage)
[![Quality Score](https://img.shields.io/scrutinizer/g/baruica/document-storage.svg?style=flat-square)](https://scrutinizer-ci.com/g/baruica/document-storage/?branch=master)

## Install

Via composer
```bash
composer require baruica/document-storage
```

## Storage adapters

All storage adapters implement the ```DocumentStorage\Storage``` interface:
- ```DocumentStorage\Adapter\Storage\Composite```
- ```DocumentStorage\Adapter\Storage\Filesystem```
- ```DocumentStorage\Adapter\Storage\S3```

**To store a document:**
```php
$docUrl = $storage->store('body of a doc', 'docName');
```
The method returns the document's url.

**To get the document's url**
```php
$docUrl = $storage->getUrl('docName');
```
If the document doesn't exist, it will throw a ```DocumentStorage\Exception\DocumentNotFoundException```

## Code License

[LICENSE](https://github.com/baruica/document-storage/blob/master/LICENSE)
