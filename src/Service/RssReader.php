<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Podcast;
use Carbon\Carbon;
use GuzzleHttp\Client;
use RuntimeException;
use SimpleXMLElement;

class RssReader
{
    private SimpleXMLElement $simpleXmlDocument;

    public function __construct(string $rssFeed)
    {
        $client = new Client();
        $resp = $client->get($rssFeed);

        $body = $resp->getBody()->getContents();

        $simpleXmlDocument = simplexml_load_string($body);
        if (!$simpleXmlDocument) {
            throw new RuntimeException("cannot parse rss feed");
        }

        $this->simpleXmlDocument = $simpleXmlDocument;
    }

    public function getEpisodes(): array
    {
        $episodes = [];
        foreach ($this->simpleXmlDocument->channel->item as $item) {
            $episode = new Episode();
            $episode->setTitle($item->title);
            $episode->setGuid($item->guid);
            $episode->setUrl($item->enclosure->attributes()->url);
            $episode->setType($item->enclosure->attributes()->type);
            $episode->setLength((int)$item->enclosure->attributes()->length);
            $pubDate = Carbon::parse($item->pubDate)->toDateTimeImmutable();
            $episode->setPubDate($pubDate);
            $episode->setDescription($item->description);
            $episodes[] = $episode;
        }

        return $episodes;
    }

    public function getPodcast(): Podcast
    {
        $podcast = new Podcast();

        $podcast->setTitle($this->simpleXmlDocument->channel->title);
        $podcast->setDescription($this->simpleXmlDocument->channel->description);

        $pubDate = (string) $this->simpleXmlDocument->channel->pubDate;
        $lastBuildDate = (string) $this->simpleXmlDocument->channel->lastBuildDate;

        if ($pubDate === '') {
            $pubDate = $lastBuildDate; // TODO: not sure this is wise?
        }

        $pubDatetime = Carbon::parse($pubDate)->toDateTimeImmutable();

        $podcast->setPubDate($pubDatetime);

        return $podcast;
    }
}
