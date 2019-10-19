<?php
/**
 * User: zidni
 * Date: 2019-10-18
 * Time: 18:42
 */

namespace App\Transformer\Linkshortener;


use App\ShortLink;
use League\Fractal\TransformerAbstract;

class LinkShortenerTransformer extends TransformerAbstract
{

    public function transformStoreSucces(ShortLink $shortLink)
    {
        $response = [
            'shortened_url' => url('api/' . $shortLink->code)
        ];

        return $response;
    }

    public function transformUpdateSucces(ShortLink $shortLink)
    {
        $response = [
            'shortened_url' => url('api/' . $shortLink->code),
            'link' => $shortLink->link
        ];

        return $response;
    }
}
