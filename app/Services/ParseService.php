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
                'parser_for_tags' => 'a.tags-trends__link.link.link_underline_color',
            ],
            'AIF' => [
                'link' => 'https://spb.aif.ru',
                'parser_for_menu' => 'li.menuItem > a',
                'parser_for_tags' => 'span.item-prop-span',
            ],
            'Reuters' => [
                'link' => 'https://www.reuters.com',
                'parser_for_menu' => 'li.LinkGroup__item___2lsBAV > a.Text__text___3eVx1j.Text__dark-grey___AS2I_p.Text__medium___1ocDap.Text__default___1Xh7Yh.Link__underline_on_hover___3-iv5a.LinkGroup__link___Q30Q4E',
            ],
            'HuffingtonPost' => [
                'link' => 'https://www.huffpost.com',
                'parser_for_menu' => 'a.subnav__item',
                'parser_for_tags' => 'a.tag.yr-tag',
            ],
            'Yandex_zen' => [
                'link' => 'https://www.zen.yandex.ru',
                'parser_for_menu' => 'a.nav-menu-item',
            ],
        ];
    }
    
    // Parsing data for menu
    public function parseSourceInfo() {
        $new_arr = [];

        foreach($this->arr as $title_source => $val) {
            $new_arr += [$title_source => $this->parseData($val['parser_for_menu'], $val['link'], $title_source)];
        }

        return collect($new_arr);
    }

    protected function parseData($parser, $link, $title_source) {
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
                if($title_source == 'Reuters' && $i < 8) {
                    $vals[] = [
                        $node->text() => $node->link()->getUri(),
                    ];
                } else if($title_source != 'Reuters') {
                    $vals[] = [
                        $node->text() => $node->link()->getUri(),
                    ];
                }
            }
            $i++;
        }

        return $vals;
    }


    // Parsing Articles
    public function parseArticles($date_from, $date_to, $name_source, $url) {
        $newvals = [];

        if($name_source == "RT") {

            $section = explode('/', $url)[count(explode('/', $url))-1];

            $link_main = 'https://russian.rt.com/listing/type.Article.category.'.$section.'/prepare/sections/1/';
            
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
                    $newvals[] = $this->parseArticle($crawler->filter('div.card__heading > a')->link()->getUri(), $name_source);
                }
                $i++;
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
                        $newvals[] = $this->parseArticle($node->filter('div.box_info > a')->link()->getUri(), $name_source);
                    }
                    
                }
                
                $i++;
            }
        } else if($name_source == "Reuters") {
            $section = explode('/', $url)[3];

            $z = 0;
            while(1) {
                $link = 'https://www.reuters.com/pf/api/v3/content/fetch/articles-by-section-alias-or-id-v1?query={"id":"/'.$section.'","offset":'.$z.',"orderby":"last_updated_date:desc","size":1,"website":"reuters"}&d=42&_website=reuters';
            
                $response = file_get_contents($link);
                $data = json_decode($response);
                
                $date = explode('T', $data->result->articles[0]->published_time)[0];
                $url = 'https://www.reuters.com'. $data->result->articles[0]->canonical_url;
                
                if(Carbon::parse($date)->getTimestamp() < Carbon::parse($date_from)->getTimestamp() || $z == 15) {
                    
                    return collect($newvals);
                }
                if(Carbon::parse($date)->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                    
                    $newvals[] = $this->parseArticle($url, $name_source);
                    $z++;
                }
            }
        } else if($name_source == "HuffingtonPost") {
            
            $z = 1;
            while(1) {
                $link = $url.'?page='.$z;
                
                $html = file_get_contents($link);
                
                $crawler = new Crawler(null, $link);
                $crawler->addHtmlContent($html, 'UTF-8');

                foreach($crawler->filter('a.card__headline.card__headline--long') as $DOM) {
                    $node = new Crawler($DOM, $link);
                    $article = $this->parseArticle($node->link()->getUri(), $name_source);
                    
                    if($article['date'] != '' && Carbon::parse($article['date'])->getTimestamp() < Carbon::parse($date_from)->getTimestamp() || $z == 15) {
                        return $newvals;
                    }
                    if($article['date'] != '' || Carbon::parse($article['date'])->getTimestamp() <= Carbon::parse($date_to)->getTimestamp()) {
                    
                        $newvals[] = $article;
                    }
                    $z++;
                }

            }
        } else if($name_source == "Yandex_zen") {
            
            $url = 'https://zen.yandex.ru/api/v3/launcher/export?clid=300&country_code=ru&interest_name='.urlencode(explode('?', explode('/', $url)[4])[0]);
            $response = json_decode(file_get_contents($url));

            foreach ($response->items as $article) {
                if(explode('/', $article->link)[2] == 'zen.yandex.ru') {
                    $newvals[] = $this->parseArticle($article->link, $name_source);
                }
            }
        }

        return $newvals;
    }

    public function parseArticle($link, $source_name) {
        
        $html = file_get_contents($link);
        
        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $text = '';

        if($source_name == "RT") {
            if($crawler->filter('div.article__text_article-page')->count() == 0) {
                $text = $crawler->filter('div.article__summary.article__summary_article-page.js-mediator-article')->html();
            } else {
                if($crawler->filter('div.article__text_article-page')->text() != "") {
                    foreach ($crawler->filter('div.article__text_article-page')->children() as $DOM) {
                        $node = new Crawler($DOM, $link);
                        if($node->filter('div.read-more__title')->count() == 0 && $node->filter('img.article__cover-image ')->count() == 0) {
                            $text .= $DOM->ownerDocument->saveHTML($DOM);
                        }
                    }
                } else {
                    $text = $crawler->filter('div.article__summary.article__summary_article-page.js-mediator-article')->html();
                }
            }

            $tags = $crawler->filterXpath("//meta[@property='mediator_theme']")->extract(array('content'));
            $keyWords = $this->parseArticleTags($link, $source_name);
            $date = '';

        } else if($source_name == "AIF") {
            if($crawler->filter('div.article_text')->count() == 0) {
                $text = $crawler->filter('div.lead')->html();
            } else {
                foreach ($crawler->filter('div.article_text')->children() as $DOM) {
                    $node = new Crawler($DOM, $link);
                    if($node->filter('div.inj_link_box')->count() == 0 && $node->filter('img')->count() == 0) {
                        $text .= $DOM->ownerDocument->saveHTML($DOM);
                    }
                }
            }

            $tags = $crawler->filterXpath("//meta[@name='keywords']")->extract(array('content'));
            $keyWords = $this->parseArticleTags($link, $source_name);
            $date = '';
            
        } else if($source_name == "Reuters") {
            foreach ($crawler->filter('div.ArticleBody__content___2gQno2.paywall-article')->children() as $DOM) {
                $node = new Crawler($DOM, $link);
                if($node->filter('div.AdSlot__container___vv9J1U')->count() == 0) {
                    $text .= $DOM->ownerDocument->saveHTML($DOM);
                }
            }

            $tags = $crawler->filterXpath("//meta[@name='article:tag']")->extract(array('content'));
            $keyWords = '';
            $date = '';

        }  else if($source_name == "HuffingtonPost") {
            if($crawler->filter('div.entry__text.js-entry-text.yr-entry-text')->count() != 0) {
                foreach ($crawler->filter('div.entry__text.js-entry-text.yr-entry-text')->children() as $DOM) {
                    $node = new Crawler($DOM, $link);
                    if($node->filter('div.app-download-interstitial__container')->count() == 0 && $node->filter('span.advertisement__label')->count() == 0 && $node->filter('div.flex-video-container')->count() == 0) {
                        $text .= $DOM->ownerDocument->saveHTML($DOM);
                    }
                }
            } else {
                $text = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'))[0];
            }

            $tags = $crawler->filterXpath("//meta[@name='keywords']")->extract(array('content'));
            $keyWords = $this->parseArticleTags($link, $source_name);
            $date = $crawler->filter('span.timestamp__date--published > span')->count() != 0 ? explode(' ', $crawler->filter('span.timestamp__date--published > span')->text())[0] : '';

        } else if($source_name == "Yandex_zen") {
            if($crawler->filter('div.article-render')->count() != 0) {
                foreach ($crawler->filter('div.article-render')->children() as $DOM) {
                    $node = new Crawler($DOM, $link);
                    if($node->filter('div.article-image-with-viewer__image')->count() == 0 && $node->filter('a.article-link.article-link_theme_undefined')->count() == 0) {
                        $text .= $DOM->ownerDocument->saveHTML($DOM);
                    }
                }
            } else {
                $text = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'))[0];
            }

            $tags = $crawler->filterXpath("//meta[@name='keywords']")->extract(array('content'));
            $keyWords = '';
            $date = '';

        }      

        $desc = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'));
        $title = $crawler->filterXpath("//meta[@property='og:title']")->extract(array('content'));
        $img_url = $crawler->filterXpath("//meta[@property='og:image']")->extract(array('content'));

        return [
            'date' => $date,
            'title' => $title,
            'source_name' => $source_name,
            'keyWords' => $keyWords,
            'status' => 1,
            'url' => $link,
            'text' => $text,
            'meta_tags_article' => [
                'desc' => $desc, 
                'title' => $title, 
                'keyWords' => $tags, 
            ],
            'img' => [
                'url' => $img_url,
                'alt' => $title,
            ],
        ];
    }


    // Parsing Tags
    public function parseTags($articles) {
        $tags = [];

        foreach($articles as $article) {
            $arr_tags = $this->parseArticleTags($article['url'], $article['source_name']);

            $tags = array_merge($tags, $arr_tags);
        }

        return array_values(array_unique($tags));
    }

    protected function parseArticleTags($link, $title_source) {
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $arr_tags = $crawler->filter($this->arr[$title_source]['parser_for_tags'])->each(function (Crawler $node, $i) {
            return $node->text();
        });
        
        return $arr_tags;
    }

}
