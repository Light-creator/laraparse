<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Log;
use Symfony\Component\DomCrawler\Crawler;

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
    public function index()
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

        $source_info = collect($this->getSourceInfo());
        
        return view(
            "backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action', 'source_info')
        );
    }

    public function section_parse(Request $request) 
    {
        dd($request);

        return back();
    }

    private function getSourceInfo()
    {
        $arr = [
            'RT' => [
                'link' => 'https://russian.rt.com',
                'parser' => 'a.nav__link_header',
            ],
            'AIF' => [
                'link' => 'https://spb.aif.ru',
                'parser' => 'li.menuItem > a',
            ],
            'NY_Times' => [
                'link' => 'https://www.nytimes.com',
                'parser' => 'a.css-1wjnrbv',
            ],
            'Financial_Times' => [
                'link' => 'https://www.ft.com',
                'parser' => 'a.o-header__drawer-menu-link',
            ],
            'Yandex_Zen' => [
                'link' => 'https://zen.yandex.ru',
                'parser' => 'a.nav-menu-item',
            ],
        ];

        $new_arr = [];

        foreach($arr as $title_source => $val) {
            $new_arr += [$title_source => $this->getData($val['parser'], $val['link'])];
        }

        return $new_arr;
    }

    private function getData($parser, $link) {
        $html = file_get_contents($link);

        $crawler = new Crawler(null, $link);
        $crawler->addHtmlContent($html, 'UTF-8');

        $vals = $crawler->filter($parser)->each(function (Crawler $node, $i) {
            return [$node->text() => $node->link()->getUri()];
        });

        return $vals;
    }

}
