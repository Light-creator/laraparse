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
            $new_arr += [$title_source => $this->parseData($val['parser_for_menu'], $val['link'], $title_source)];
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

                if(Carbon::parse($date)->getTimestamp() < Carbon::parse($date_from)->getTimestamp()) {
                    return collect($newvals);
                }
                if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                    $newvals[] = [
                        'title' => $crawler->filter('div.card__heading > a')->text(),
                        'keyWord' => $section != 'news' ? $crawler->filter('div.card__trend > span > a')->text() : '-',
                        'url' => $crawler->filter('div.card__heading > a')->link()->getUri(),
                        'text' => $crawler->filter('div.card__summary')->text(),
                        'source_name' => $name_source,
                    ];
                    //dd($newvals);
                }
                $i++;
            }
        } else if($name_source == "NY_Times") {
            $link = $url;
            $html = file_get_contents($link);
            
            $crawler = new Crawler(null, $link);
            $crawler->addHtmlContent($html, 'UTF-8');
            
            foreach($crawler->filter('li.css-ye6x8s') as $DOM) {
                $node = new Crawler($DOM, $link);

                $newvals[] = [
                    'title' => $node->filter('h2.css-1j9dxys')->text(),
                    'text' => $node->filter('p')->text(),
                    'url' => $node->filter('div.css-1l4spti > a')->link()->getUri(),
                    'keyWord' => '-',
                    'source_name' => $name_source,
                ];
            }
        } else if($name_source == "AIF") { 
            $i = 1;
            while(1) {
                $link = $url.'?page='.$i;

                $html = file_get_contents($link);
                
                $crawler = new Crawler(null, $link);
                $crawler->addHtmlContent($html, 'UTF-8');

                foreach($crawler->filter('div.list_item') as $DOM) {
                    $node = new Crawler($DOM);
                    
                    $date = explode(' ', $node->filter('span.text_box__date')->text())[0];

                    if(Carbon::parse($date)->getTimestamp() < Carbon::parse($date_from)->getTimestamp()) {
                        return collect($newvals);
                    }
                    if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                        $newvals[] = [
                            'title' => $node->filter('div.box_info > a > h3')->text(),
                            'text' => $node->filter('div.text_box > span')->text(),
                            'keyWord' => mb_strtolower($node->filter('a.rubric_link')->text()),
                            'url' => $node->filter('div.box_info > a')->link()->getUri(),
                            'source_name' => $name_source,
                        ];
                    }
                    
                }
                
                $i++;
            }
        } else if($name_source == "Financial_Times") {
            $i = 1;
            while(1) {
                $link = $url.'?page='.$i;

                $html = file_get_contents($link);
                
                $crawler = new Crawler(null, $link);
                $crawler->addHtmlContent($html, 'UTF-8');
                
                $z = 17;
                foreach($crawler->filter('li.o-teaser-collection__item.o-grid-row') as $DOM) {
                    $node = new Crawler($DOM, $link);
                    $date = explode('T', $crawler->filter('time')->attr('datetime'))[0];
                    //$date = explode('T', $crawler->filterXPath('//*[@id="stream"]/div[1]/ul/li['.$z.']/div[1]/div/time')->attr('datetime'))[0];

                    if(Carbon::parse($date)->getTimestamp() < Carbon::parse($date_from)->getTimestamp()) {
                        return collect($newvals);
                    }
                    if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                        if($node->filter('a.js-teaser-heading-link')->count()) {
                            $newvals[] = [
                                'title' => $node->filter('a.js-teaser-heading-link')->text(),
                                'text' => $node->filter('a.js-teaser-standfirst-link')->count() ? $node->filter('a.js-teaser-standfirst-link')->text() : '-',
                                'keyWord' => $node->filter('a.o-teaser__tag')->text(),
                                'url' => $node->filter('a.js-teaser-heading-link')->link()->getUri(),
                                'source_name' => $name_source,
                            ];
                        }
                    }
                    $z++;
                }
                
                $i++;
            }
        } else if('Yandex_zen') {
            $c = curl_init($url);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);

            $html = curl_exec($c);
            $crawler = new Crawler(null, $url);
            $crawler->addHtmlContent($html, 'UTF-8');

            dd($html);
            
        }

        return collect($newvals);
    }

    private function parseData($parser, $link, $title_source) {
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $vals = [];
        
        if($title_source == "AIF") {
            foreach($crawler->filter('li.top_level_item_js')->first() as $DOM) {
                $node = new Crawler($DOM);
                $count = $node->filter('a')->count()-1;
            }
        } else {
            $count = 0;
        }
        $i = 0;
        foreach($crawler->filter($parser) as $DOM) {
            $node = new Crawler($DOM, $link);
            if($node->text() != '.link:hover .Covid19-icon { background-color: transparent; } Евро-2020' && $i >= $count) {
                $vals[] = [
                    $node->text() => $node->link()->getUri(),
                ];
            }
            $i++;
        }

        return $vals;
    }

    public function parseTags($link, $title_source) {
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        if($title_source == "RT") {
            $arrTags = $crawler->filter('li.nav__row-item_popular-trends')->each(function (Crawler $node, $i) {
                return $node->filter('a')->text();
            });
        } else if($title_source == "AIF") {

        }
        return $arrTags;
    }

    protected function parseArticleTags($link, $title_source) {

    }

    public function parseArticle($arr_article) {
        
        $html = file_get_contents($arr_article->link);

        $crawler = new Crawler(null, $arr_article->link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $text = '';

        if($arr_article->source_name == "RT") {
            if($crawler->filter('div.article__text_article-page')->text() == "") {
                $text = $crawler->filter('div.article__summary.article__summary_article-page.js-mediator-article')->html();
            } else {
                foreach ($crawler->filter('div.article__text_article-page')->children() as $DOM) {
                    $node = new Crawler($DOM, $arr_article->link);
                    if($node->filter('div.read-more__title')->count() == 0 && $node->filter('img.article__cover-image ')->count() == 0) {
                        $text .= $DOM->ownerDocument->saveHTML($DOM);
                    }
                }
            }

            $desc = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'));
            $title = $crawler->filterXpath("//meta[@property='og:title']")->extract(array('content'));
            $keyWords = $crawler->filterXpath("//meta[@property='mediator_theme']")->extract(array('content'));
            $img_url = $crawler->filterXpath("//meta[@property='og:image']")->extract(array('content'));

        } else if($arr_article->source_name == "AIF") {
            if($crawler->filter('div.article_text')->count() == 0) {
                $text = $crawler->filter('div.lead')->html();
            } else {
                foreach ($crawler->filter('div.article_text')->children() as $DOM) {
                    $node = new Crawler($DOM, $arr_article->link);
                    if($node->filter('div.inj_link_box')->count() == 0 && $node->filter('img')->count() == 0) {
                        $text .= $DOM->ownerDocument->saveHTML($DOM);
                    }
                }
            }

            $desc = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'));
            $title = $crawler->filterXpath("//meta[@property='og:title']")->extract(array('content'));
            $keyWords = $crawler->filterXpath("//meta[@name='keywords']")->extract(array('content'));
            $img_url = $crawler->filterXpath("//meta[@property='og:image']")->extract(array('content'));

        }   

        return [
            'title' => $arr_article->title,
            'url' => $arr_article->link,
            'text' => $text,
            'meta_tags_article' => [
                'desc' => $desc, 
                'title' => $title, 
                'keyWords' => $keyWords, 
            ],
            'img' => [
                'url' => $img_url,
                'alt' => $arr_article->title,
            ],
        ];
    }
}
