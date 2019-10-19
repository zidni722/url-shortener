<?php
/**
 * User: zidni
 * Date: 2019-10-19
 * Time: 08:53
 */

namespace App\Repositories\Linkshortener;


use App\ShortLink;

class LinkshortenerRepository
{
    public function store(array $data)
    {
        $shortLink = ShortLink::create($data);
        return $shortLink;
    }

    public function findByColumn($column, $data)
    {
        return ShortLink::where($column, $data)->first();
    }
}
