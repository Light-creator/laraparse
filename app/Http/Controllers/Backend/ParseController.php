<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Log;

use App\Services\ParseService;
use App\Models\ParseArticle;
use App\Models\Img;

class ParseController extends Controller
{
    
    public function __construct()
    {
        // Page Title
        $this->module_title = 'Parse';

        // module name
        $this->module_name = 'parse';

        // directory path of the module
        $this->module_path = 'parse';

        // module icon
        $this->module_icon = 'c-icon fas fa-sitemap';

        // module model name, path
        $this->module_model = "App\Models\ParseArticle";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';
        $$module_name = $module_model::with('permissions')->paginate();

        Log::info(label_case($module_title.' '.$module_action).' | User:'.auth()->user()->name.'(ID:'.auth()->user()->id.')');

        $parser = new ParseService;
        //$request->session()->forget('articles');
        //dd($request->session()->get('articles'));
        dd($parser->parseArticles('2021-07-3', '2021-07-5', 'Reuters', 'https://www.reuters.com/world/'));
        //dd($articles);
        //dd($parser->parseTags('https://russian.rt.com/world', "RT"));
        if(!$request->session()->has('source_info')) {
            $request->session()->put('source_info', $parser->parseSourceInfo());
            $request->session()->save();
        }
        // https://www.washingtonpost.com/pb/api/v2/render/feature/section/story-list?content_origin=prism-query&url=prism://prism.query/site-articles-only,/politics&offset=20&limit=15
        //dd($request->session());

        return view(
            "backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action')
        );
    }

    public function section_parse(Request $request) 
    {
        $parser = new ParseService;
        $articles = $parser->parseArticles($request->date_from, $request->date_to, $request->source_name, $request->url_section);
        $tags = $parser->parseTags($articles);
        //dd($tags);
        return response()->json(['articles' => view('backend.ajax.table', compact('articles'))->render(), 'tags' => view('backend.ajax.tags', compact('tags'))->render()]);
    }

    public function parse_tags(Request $request) 
    {
        $parser = new ParseService;
        $tags = $parser->parseTags($request->url_section, $request->source_name);
        
        return response()->json(view('backend.ajax.tags', compact('tags'))->render());
    }

    public function session_article(Request $request) {
        if($request->is_checked == 1) {
            if($request->session()->get('articles')) {
                if(!in_array($request->article, $request->session()->get('articles'))) {
                    $request->session()->push('articles', $request->article);
                    $request->session()->save();

                    return response()->json(['message' => 2]);
                } else {
                    return response()->json(['message' => 3]);
                }
            } else {
                $request->session()->push('articles', $request->article);
                $request->session()->save();

                return response()->json(['message' => 2]);
            }
        } else {
            $arr_articles = $request->session()->get('articles');
            $key = array_search($request->article, $arr_articles);

            unset($arr_articles[$key]);

            $request->session()->put('articles', $arr_articles);
            $request->session()->save();

            return response()->json(['message' => 1]);
        }
    }

    public function parse_articles(Request $request) {

        $module_title = $this->module_title;
        $module_name = $this->module_name;
        $module_path = $this->module_path;
        $module_icon = $this->module_icon;
        $module_model = $this->module_model;
        $module_name_singular = Str::singular($module_name);

        $module_action = 'List';
        $$module_name = $module_model::with('permissions')->paginate();

        $parser = new ParseService;
        
        return view("backend.$module_path.parse_articles", compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action'));
    }

    public function parse_article_ajax(Request $request) {

        $parser = new ParseService;

        $article_arr = json_decode($request->session()->get('articles')[$request->article]);
        $img_name = array_slice(explode('/', $article_arr->img->url[0]), -1)[0];
        
        $ch = curl_init($article_arr->img->url[0]);
        $fp = fopen('img/parse/'.$img_name, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $img = Img::create([
            'alt' => $article_arr->img->alt,
            'original' => $img_name,
        ]);

        $article = ParseArticle::create([
            'title' => $article_arr->title,
            'url' => $article_arr->url,
            'project' => 'test_project',
            'categorie' => 'test_categorie',
            'tags' => 'test',
            'img' => $img->id,
            'author' => 'test_author',
            'desc' => $article_arr->text,
            'meta-tag-img' => json_encode($article_arr->img),
            'meta-tags' => json_encode($article_arr->meta_tags_article)
        ]);

        if($article) {
            $arr_articles = $request->session()->get('articles');
            $key = array_search($request->article, $arr_articles);
    
            $arr = json_decode($arr_articles[$key]);
            
            $arr->status = 2;
            
            $arr_articles[$key] = json_encode($arr);
    
            $request->session()->put('articles', $arr_articles);
            $request->session()->save();
        }

        return response()->json(['req' => $request->article]);

    }

}





// public function parseArticle($arr_article) {
        
    //     $html = file_get_contents($arr_article->link);

    //     $crawler = new Crawler(null, $arr_article->link);
    //     $crawler->addHtmlContent($html, 'UTF-8');

    //     $text = '';

    //     if($arr_article->source_name == "RT") {
    //         if($crawler->filter('div.article__text_article-page')->text() == "") {
    //             $text = $crawler->filter('div.article__summary.article__summary_article-page.js-mediator-article')->html();
    //         } else {
    //             foreach ($crawler->filter('div.article__text_article-page')->children() as $DOM) {
    //                 $node = new Crawler($DOM, $arr_article->link);
    //                 if($node->filter('div.read-more__title')->count() == 0 && $node->filter('img.article__cover-image ')->count() == 0) {
    //                     $text .= $DOM->ownerDocument->saveHTML($DOM);
    //                 }
    //             }
    //         }

    //         $desc = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'));
    //         $title = $crawler->filterXpath("//meta[@property='og:title']")->extract(array('content'));
    //         $keyWords = $crawler->filterXpath("//meta[@property='mediator_theme']")->extract(array('content'));
    //         $img_url = $crawler->filterXpath("//meta[@property='og:image']")->extract(array('content'));

    //     } else if($arr_article->source_name == "AIF") {
    //         if($crawler->filter('div.article_text')->count() == 0) {
    //             $text = $crawler->filter('div.lead')->html();
    //         } else {
    //             foreach ($crawler->filter('div.article_text')->children() as $DOM) {
    //                 $node = new Crawler($DOM, $arr_article->link);
    //                 if($node->filter('div.inj_link_box')->count() == 0 && $node->filter('img')->count() == 0) {
    //                     $text .= $DOM->ownerDocument->saveHTML($DOM);
    //                 }
    //             }
    //         }

    //         $desc = $crawler->filterXpath("//meta[@name='description']")->extract(array('content'));
    //         $title = $crawler->filterXpath("//meta[@property='og:title']")->extract(array('content'));
    //         $keyWords = $crawler->filterXpath("//meta[@name='keywords']")->extract(array('content'));
    //         $img_url = $crawler->filterXpath("//meta[@property='og:image']")->extract(array('content'));

    //     }   

    //     $keyWords = $this->parseArticleTags($arr_article->link, $arr_article->source_name);

    //     return [
    //         'title' => $arr_article->title,
    //         'url' => $arr_article->link,
    //         'text' => $text,
    //         'meta_tags_article' => [
    //             'desc' => $desc, 
    //             'title' => $title, 
    //             'keyWords' => $keyWords, 
    //         ],
    //         'img' => [
    //             'url' => $img_url,
    //             'alt' => $arr_article->title,
    //         ],
    //     ];
    // }
