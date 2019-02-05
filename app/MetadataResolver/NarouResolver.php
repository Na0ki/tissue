<?php

namespace App\MetadataResolver;

use GuzzleHttp\Cookie\CookieJar;

class NarouResolver implements Resolver
{
    public function resolve(string $url): Metadata
    {
        $cookieJar = CookieJar::fromArray(['over18' => 'yes'], '.syosetu.com');

        $client = new \GuzzleHttp\Client();
        $res = $client->get($url, ['cookies' => $cookieJar]);
        if ($res->getStatusCode() === 200) {
            $ogpResolver = new OGPResolver();
            $metadata = $ogpResolver->parse($res->getBody());
            $metadata->description = '';

            $dom = new \DOMDocument();
            @$dom->loadHTML(mb_convert_encoding($res->getBody(), 'HTML-ENTITIES', 'ASCII,JIS,UTF-8,eucJP-win,SJIS-win'));
            $xpath = new \DOMXPath($dom);

            $description = [];

            // 作者名
            $writerNodes = $xpath->query('//*[contains(@class, "novel_writername")]');
            if ($writerNodes->length !== 0 && !empty($writerNodes->item(0)->textContent)) {
                $description[] = trim($writerNodes->item(0)->textContent);
            }

            // あらすじ
            $exNodes = $xpath->query('//*[@id="novel_ex"]');
            if ($exNodes->length !== 0 && !empty($exNodes->item(0)->textContent)) {
                $summary = trim($exNodes->item(0)->textContent);
                $description[] = mb_strimwidth($summary, 0, 101, '…'); // 100 + '…'(1)
            }

            $metadata->description = implode(' / ', $description);

            return $metadata;
        } else {
            throw new \RuntimeException("{$res->getStatusCode()}: $url");
        }
    }
}
