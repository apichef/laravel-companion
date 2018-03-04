<?php

namespace MilroyFraser\JsonApiCompanions\Response;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\JsonApiSerializer;
use League\Fractal\TransformerAbstract;

class JsonApiResponse implements Responsable
{
    /** @var TransformerAbstract */
    private $transformer;

    /** @var string */
    private $resourceKey;

    private $data;

    /**
     * JsonApiResponses constructor.
     * @param $data
     * @param TransformerAbstract $transformer
     * @param string $resourceKey
     */
    public function __construct($data, TransformerAbstract $transformer, string $resourceKey)
    {
        $this->data = $data;
        $this->transformer = $transformer;
        $this->resourceKey = $resourceKey;
    }

    public function toResponse($request)
    {
        $data = $this->transform($request);

        return Response::create($data);
    }

    private function transform($request)
    {
        $manager = new Manager();
        $manager->setSerializer(new JsonApiSerializer());

        if ($this->data instanceof LengthAwarePaginator) {
            $resource = new ResourceCollection($this->data->getCollection(), $this->transformer, $this->resourceKey);
            $this->preparePagination($request, $this->data, $resource);
        } elseif ($this->data instanceof Collection) {
            $resource = new ResourceCollection($this->data, $this->transformer, $this->resourceKey);
        } else {
            $resource = new Item($this->data, $this->transformer, $this->resourceKey);
        }

        $manager->parseIncludes($request->get('include'));
        $data = $manager->createData($resource)->toArray();

        return $this->sparseFields($data, $request->get('fields', []));
    }

    private function preparePagination($request, $data, $resource)
    {
        $data->setPageName('page[number]');
        $this->appendQueryStringToPaginationLinks($request, $data);
        $resource->setPaginator(new IlluminatePaginatorAdapter($data));
    }

    private function appendQueryStringToPaginationLinks($request, $data)
    {
        $queryString = urldecode($request->getQueryString());

        collect(explode('&', $queryString))->each(function ($param) use ($data) {
            if (! starts_with($param, 'page[number]')) {
                $param = explode('=', $param);
                $value = isset($param[1]) ? $param[1] : '';
                $data->appends($param[0], $value);
            }
        });
    }

    private function sparseFields(array $data, array $fields): array
    {
        if (! $fields) {
            return $data;
        }

        $data['data'] = $this->sparseData($data['data'], $fields);

        if (isset($data['included'])) {
            $data['included'] = $this->sparseIncludes(collect($data['included']), $fields);
        }

        return $data;
    }

    private function sparseData($data, array $fields): array
    {
        if ($this->isSingleResource($data)) {
            return $this->sparseSingle($data, $fields);
        }

        return $this->sparseCollection(collect($data), $fields);
    }

    private function isSingleResource($data)
    {
        return isset($data['type']) && isset($data['id']);
    }

    private function sparseIncludes(Collection $data, array $fields)
    {
        $includes = [];

        $data->groupBy('type')->each(function ($chunk) use ($fields, &$includes) {
            $includes = array_merge($includes, $this->sparseCollection($chunk, $fields));
        });

        return $includes;
    }

    private function sparseSingle(array $data, array $fields)
    {
        if (isset($fields[$data['type']])) {
            $data['attributes'] = array_only($data['attributes'], explode(',', $fields[$data['type']]));
        }

        return $data;
    }

    private function sparseCollection(Collection $data, array $fields)
    {
        $first = $data->first();

        if (isset($fields[$first['type']])) {
            $only = explode(',', $fields[$first['type']]);
            $data = $data->map(function ($resource) use ($only, $first) {
                $resource['attributes'] = array_only($resource['attributes'], $only);

                return $resource;
            });
        }

        return $data->all();
    }
}
