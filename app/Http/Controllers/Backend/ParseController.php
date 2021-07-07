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

        if(!$request->session()->has('source_info')) {
            $request->session()->put('source_info', $parser->parseSourceInfo());
            $request->session()->save();
        }

        return view(
            "backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action')
        );
    }

    public function section_parse(Request $request) 
    {
        $parser = new ParseService;
        $articles = $parser->parseArticles($request->date_from, $request->date_to, $request->source_name, $request->url_section);
        
        if($request->source_name == "Reuters" || $request->source_name == "Yandex_zen") {
            $tags = '';
        } else {
            $tags = $parser->parseTags($articles);
            $tags = view('backend.ajax.tags', compact('tags'))->render();
        }

        return response()->json(['articles' => view('backend.ajax.table', compact('articles'))->render(), 'tags' => $tags]);
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
        //dd($request->session());
        return view("backend.$module_path.parse_articles", compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action'));
    }

    public function parse_article_ajax(Request $request) {

        $parser = new ParseService;
        
        $article_arr = json_decode($request->session()->get('articles')[$request->article]);
        $img_name = explode('?', array_slice(explode('/', $article_arr->img->url[0]), -1)[0])[0];
        
        $ch = curl_init($article_arr->img->url[0]);
        $fp = fopen('img/parse/'.$img_name, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
        fclose($fp);

        $img = Img::create([
            'alt' => $article_arr->img->alt[0],
            'original' => $img_name,
        ]);

        $article = ParseArticle::create([
            'title' => $article_arr->title[0],
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
            $key = $request->article;
    
            $arr = json_decode($arr_articles[$key]);
            
            $arr->status = 2;
            
            $arr_articles[$key] = json_encode($arr);
    
            $request->session()->put('articles', $arr_articles);
            $request->session()->save();
        }

        return response()->json(['request' => $request->article]);

    }

    public function stats(Request $request) {

        $articles = ParseArticle::where('created_at','>=', $request->date_from)
            ->where('created_at', '<=', $request->date_to)
            ->get();

        $url_arr = [
            'rt' => 0, 
            'aif' => 0, 
            'reuters' => 0, 
            'yandex' => 0, 
            'huffpost' => 0,
        ];

        foreach ($articles as $article) {
            $name_source = explode('.', $article->url)[1];

            $url_arr[$name_source]++;
        }

        $count = count($articles) / 100;

        foreach ($url_arr as $key => $value) {
            $url_arr[$key] = $value / $count;
        }

        return response()->json(view('backend.ajax.stat', ['percent' => $url_arr, 'count' => count($articles)])->render());
    }

}