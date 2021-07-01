<?php
namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;
use Carbon\Carbon;

class ParseService 
{
    public function __construct() {
        $this->arr = [
            'RT' => [
                'link' => 'https://russian.rt.com',
                'parser_for_menu' => 'a.nav__link_header',
                'parser_articles' => [
                    ''
                ],
            ],
            'AIF' => [
                'link' => 'https://spb.aif.ru',
                'parser_for_menu' => 'li.menuItem > a',
            ],
            'NY_Times' => [
                'link' => 'https://www.nytimes.com',
                'parser_for_menu' => 'a.css-1wjnrbv',
            ],
            'Financial_Times' => [
                'link' => 'https://www.ft.com',
                'parser_for_menu' => 'a.o-header__drawer-menu-link',
            ],
            'Yandex_Zen' => [
                'link' => 'https://zen.yandex.ru',
                'parser_for_menu' => 'a.nav-menu-item',
            ],
        ];
    }

    public function parseSourceInfo() {
        $new_arr = [];

        foreach($this->arr as $title_source => $val) {
            $new_arr += [$title_source => $this->parseData($val['parser_for_menu'], $val['link'])];
        }

        return collect($new_arr);
    }

    public function parseArticles($date_from, $date_to, $name_source, $url) {
        $newvals = [];

        if($name_source == "RT") {

            $section = explode('/', $url)[count(explode('/', $url))-1];

            $link_main = 'https://russian.rt.com/listing/type.Article.category.'.$section.'/prepare/sections/1/';
            //return $link_main;
            $i = 2;
            $data = @file_get_contents($link_main.''.$i);
            while($data) {
    
                $link = $link_main.''.$i;
                
                $html = file_get_contents($link);

                $crawler = new Crawler(null, $link);
                $crawler->addHtmlContent($html, 'UTF-8');

                if(count(explode(' ', $crawler->filter('div.card__date')->text())) == 1) {
                    $date = Carbon::now();
                } else {
                    $date = explode(' ', $crawler->filter('time.date')->attr('datetime'))[0];
                }

                if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                    $newvals[] = [
                        'title' => $crawler->filter('div.card__heading > a')->text(),
                        'keyWord' => $section != 'news' ? $crawler->filter('div.card__trend > span > a')->text() : '-',
                        'url' => $crawler->filter('div.card__heading > a')->link()->getUri(),
                        'text' => $crawler->filter('div.card__summary')->text()
                    ];
                    //dd($newvals);
                }
                if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_from)->getTimestamp()) {
                    break;
                }
                $i++;
            }
        } else if($name_source == "NY_Times") {
            $link = $url;
            $html = file_get_contents($link);
            
            $crawler = new Crawler(null, $link);
            $crawler->addHtmlContent($html, 'UTF-8');
            
            //for ($i=0; $i < 40; $i++) { 
                $newvals = $crawler->filter('li.css-ye6x8s')->each(function(Crawler $node, $i) {
                    return [
                        'title' => $node->filter('h2.css-1j9dxys')->text(),
                        'text' => $node->filter('p')->text(),
                        'url' => $node->filter('div.css-1l4spti > a')->link()->getUri(),
                        'keyWord' => '-'
                    ];
                });
            //}
        } else if($name_source == "AIF") {
            $i = 1;
            while(1) {
                $link = $url.'?page='.$i;

                $html = file_get_contents($link);
            
                $crawler = new Crawler(null, $link);
                $crawler->addHtmlContent($html, 'UTF-8');


                $newvals = $crawler->filter('div.list_item')->each($crawler, function(Crawler $node, $i) {
                    $date = explode(' ', $crawler->filter('span.text_box__date')->text())[0];
                    if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                        return [
                            'title' => $node->filter('div.box_info > a > h3')->text(),
                            'text' => $node->filter('div.text_box > span')->text(),
                            'keyWord' => '-',
                            'url' => $node->filter('div.box_info > a')->link()->getUri(),
                        ];
                    }
                });
                if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_from)->getTimestamp()) {
                    break;
                }

                $i++;
            }
        }

        return collect($newvals);
    }

    private function parseData($parser, $link) {
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');
        
        $vals = $crawler->filter($parser)->each(function (Crawler $node, $i) {
            if($node->text() != '.link:hover .Covid19-icon { background-color: transparent; } Евро-2020')
            return [$node->text() => $node->link()->getUri()];
        });

        return $vals;
    }
}
