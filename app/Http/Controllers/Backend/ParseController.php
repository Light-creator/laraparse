<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Str;
use Log;

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

        return view(
            "backend.$module_path.index",
            compact('module_title', 'module_name', "$module_name", 'module_icon', 'module_action')
        );
    }

}