# Sarala Laravel Companion

> [Laravel](https://github.com/laravel/laravel) support package to easily develop REST API following [JSON API specification](http://jsonapi.org/format/1.1/#document-top-level).

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

## Install

Via Composer

``` bash
$ composer require sarala-io/laravel-companion
```

## Usage
Eloquent model implementation
```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Sarala\Filterable;

class Post extends Model
{
    use Filterable;

    ...
}

```
Dedicated query filter implementation
``` php
namespace App\Filters;

use Sarala\FilterAbstract;

class PostsFilter extends FilterAbstract
{
    protected $lookup = [
        'my' => 'composedByMe'
    ];

    public function composedByMe()
    {
        return $this->builder->composedBy(auth()->user());
    }
    
    ...
}
```
Controller implementation
```php
namespace App\Http\Controllers;

use App\Post;
use App\Filters\PostsFilter;
use App\Http\Transformers\PostTransformer;
use Sarala\JsonApiResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request, PostsFilter $filters)
    {
        $data = Post::filter($filters)->paginateOrGet($request);

        return new JsonApiResponse($data, new PostTransformer(), 'posts');
    }

    public function show(Post $post, PostsFilter $filter)
    {
        $data = Post::filter($filter)->find($post->id);

        return new JsonApiResponse($data, new PostTransformer(), 'posts');
    }
    
    ...
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email milroy.me@gmail.com instead of using the issue tracker.

## Credits

- [Milroy Fraser][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/sarala-io/laravel-companion.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/sarala-io/laravel-companion/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/sarala-io/laravel-companion.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/sarala-io/laravel-companion.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/sarala-io/laravel-companion.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/sarala-io/laravel-companion
[link-travis]: https://travis-ci.org/sarala-io/laravel-companion
[link-scrutinizer]: https://scrutinizer-ci.com/g/sarala-io/laravel-companion/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/sarala-io/laravel-companion
[link-downloads]: https://packagist.org/packages/sarala-io/laravel-companion
[link-author]: https://github.com/milroyfraser
[link-contributors]: ../../contributors
