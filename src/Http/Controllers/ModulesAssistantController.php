<?php

namespace Dorcas\ModulesAssistant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesAssistant\Models\ModulesAssistant;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModulesAssistantController extends Controller {

    public function __construct()
    {
        parent::__construct();
        /*$this->data = [
            'page' => ['title' => config('modules-assistant.title')],
            'header' => ['title' => config('modules-assistant.title')],
            'selectedMenu' => 'sales'
        ];*/
        $this->data = [
            'header_message' => ['message' => '', 'alert' => 'alert-primary'],
            'assistant_assistant' => ['assistant_1_title' => 'Overview', 'assistant_1_body' => '', 'assistant_2_title' => '', 'assistant_2_body' => ''],
            'assistant_docs' => ['docs_header' => '', 'docs_main' => [], 'docs_footer' => ''],
            'assistant_help' => ['help_1_title' => '', 'help_1_body' => '', 'help_2_title' => '', 'help_2_body' => ''],
            'page_data' => []
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-assistant::index', $this->data);
    }

    /**
     * @param Request $request
     * @param string  $module
     * @param string  $url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generate(Request $request, string $module = "", string $url = "")
    {
        $page_info = false;
        if (!empty($url)) {
            $this->data['page_info'] = $this->getPageInfo($url);
            $page_info = true;
        }
        $pageinfo = $this->data['page_info'];

        switch ($module) {
            case 'mcu':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-customers.title').' Module</strong>! ' : 'You are currently using the <strong>'.config('modules-customers.title').' Module</strong>!';
                break;
            
            default:
            $this->data['header_message']['message'] = 'Thanks for using the <strong>Dorcas Hub</strong>!';
                break;
        }

        #get docs
        $docs_module = $this->generateDocs($module);
        $docs_url = $this->generateDocs($url);

        $docs = $this->generateDocs("digitize");

        $this->data['assistant_docs']['docs_header'] = $docs["header"];
        $this->data['assistant_docs']['docs_body'] = $docs["body"];
        $this->data['assistant_docs']['docs_footer'] = $docs["footer"];


        return response()->json($this->data);
    }


    public function getPageInfo(string $url)
    {
        $info = ["title" => "", "description" => ""];

        switch ($url) {
            case 'customers-main':
            $info = ["title" => "Main Page", "description" => "You can choose how to manage your customers", "docs_tag" => 1];
                break;
            case 'customers-customers':
            $info = ["title" => "Customers List section", "description" => "It displays a list of all your customers"];
                break;
            
        }

        return $info;
    }


    public function getPageComponents(string $url) {

        $components = [
            'customers-main' => ['video' => 'https://youtu.be/zbNnbKtkVbM'],
            'customers-customers' => ['video' => 'https://youtu.be/zbNnbKtkVbM']
        ];

        return !empty($components[$url]) ? $components[$url] : [];


    }


    public function generateAssistant(string $url) {

        

    }


    public function generateDocs(string $tag) {

        $header = 'Find below some documentation related to';
        $footer = 'Still can\'t find what you are looking for? Check our full documentation website OR use the help section';
        $body = [];

        if (!empty($tag) ) { //&& is_numeric($tag)
            $docs_url = 'https://blog.smartbusiness.com.ng/wp-json/wp/v2/posts?search='.$tag;
            //$docs_url = 'http://docs.dorcas.io/wp-json/wp/v2/posts?tags='.$tag;
            $client = new \GuzzleHttp\Client();
            $request = $client->get($docs_url);
            $response = json_decode($request->getBody()->getContents());
            //dd($response);
            foreach ($response as $key => $value) {
                $body[] = [
                    'post_id' => $value->id,
                    'post_title' => $value->title->rendered,
                    'post_body' => str_replace("\n","<br>",$value->content->rendered),
                    'post_excerpt' => str_replace("\n","<br>",$value->excerpt->rendered),
                    'post_featured_media' => $value->featured_media ?: '',
                    'post_guid' => $value->guid->rendered,
                    'post_link' => $value->link,
                    'post_slug' => $value->slug
                ];
            }
        }
        
        return ['header' => $header, 'body' => $body, 'footer' => $footer];
    }


    public function generateHelp(string $url) {

        

    }





}