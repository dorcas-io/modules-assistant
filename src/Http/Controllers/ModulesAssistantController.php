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

use App\Mail\HelpEmail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Artisan;

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
            'assistant_assistant' => ['assistant_1_title' => 'Overview', 'assistant_1_body' => '', 'assistant_2_title' => 'Actions', 'assistant_2_body' => ''],
            'assistant_docs' => ['docs_header' => '', 'docs_main' => [], 'docs_footer' => ''],
            'assistant_help' => ['help_1_title' => 'Send Us Message', 'help_1_body' => '', 'help_2_title' => 'Contact Centre', 'help_2_body' => ''],
            'page_info' => []
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-assistant::index', $this->data);
    }


    /**
     * @param Request $request
     * @param array  $params
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandAssistant(Request $request, array $params = []) {

        $response = [
            "status" => false,
            "message" => ""
        ];

        $command = isset($params["command"]) && !empty($params["command"]) ? $params["command"] : $request->query('command', null);

        if (empty($command)) {

            $response["message"] = "Invalid Command";
            
        } else {

            $arguments = isset($params["arguments"]) && !empty($params["arguments"]) ? $params["arguments"] : [];

            $exitCode = Artisan::call($command, $arguments);
    
            if ($exitCode === 0) {
                $response["status"] = true;
                $response["message"] = "Command successfully executed";
            } else {
                $response["message"] = "Command encountered an error";
            }

        }

        return $response;

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
        $this->data['page_info'] = $this->getPageInfo($url);
        $pageinfo = $this->data['page_info'];

        $page_info = !empty($pageinfo) && !empty($pageinfo["title"]) ? true : false;

        $overviewVideo = "https://www.youtube.com/embed/SqBXm0acWNQ";

        switch ($module) {
            case 'dashboard':
                $this->data['header_message']['message'] = '<strong>Welcome to Dorcas Hub Dashboard</strong>. It contains vital statistics about your business operations as well as quick shortcuts to other functions';
                $pageinfo["docs_tag"] = "dashboard-module";
                $pageinfo["docs_type"] = "chapter";
                $pageinfo["video"] = "https://www.youtube.com/embed/Ge2Q-XWKdk8";
                break;
            case 'mcu':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-customers.title', 'Customers').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-customers.title', 'Customers').' Module</strong>!';
                $pageinfo["docs_tag"] = "customers-module";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mpe':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-people.title', 'People').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-people.title', 'Customers').' People</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mli':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-library.title', 'Library').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-library.title', 'Library').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'map':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-app-store.title', 'App Store').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-app-store.title', 'App Store').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mit':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-integrations.title', 'Integrations').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-integrations.title', 'Integrations').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mpa':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-access-requests.title', 'Access Requests').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-access-requests.title', 'Access Requests').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mps':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-service-requests.title', 'Service Requests').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-service-requests.title', 'Service Requests').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mpp':
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-service-profile.title', 'Service Profile').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-service-profile.title', 'Service Profile').' Module</strong>!';
                break;
            case 'mec':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-ecommerce.title', 'eCommerce').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-ecommerce.title', 'eCommerce').' Module</strong>!';
                $pageinfo["docs_tag"] = "ecommerce-module";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mfn':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-finance.title', 'Finance').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-finance.title', 'Finance').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mmp':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-marketplace.title', 'Marketplace').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-marketplace.title', 'Marketplace').' Module</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'msl':
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-sales.title', 'Sales').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-sales.title', 'Sales').' Module</strong>!';
                $pageinfo["docs_tag"] = "sales-module";
                $pageinfo["docs_type"] = "chapter";
                break;
            case 'mse':
                $pageinfo["docs_tag"] = "settings-module";
                $pageinfo["docs_type"] = "chapter";
                $this->data['header_message']['message'] = $page_info ? 'You are currently on the <strong>'.$pageinfo["title"].'</strong> of the <strong>'.config('modules-settings.title', 'Settings').' Module</strong>! '. $pageinfo["description"] : 'You are currently on the <strong>'.config('modules-settings.title', 'Settings').' Module</strong>!';
                break;
            
            default:
                $this->data['header_message']['message'] = 'Thanks for using the <strong>Dorcas Hub</strong>!';
                $pageinfo["docs_tag"] = "";
                $pageinfo["docs_type"] = "chapter";
                break;
        }


        $this->data['assistant_assistant']['assistant_1_body'] = $this->generateOverviewVideo($pageinfo["video"], $pageinfo["overview_msg"]);

        $docs = $this->generateDocs($pageinfo["docs_type"], $pageinfo["docs_tag"], $pageinfo["title"]);

        $this->data['assistant_docs']['docs_header'] = $docs["header"];
        $this->data['assistant_docs']['docs_body'] = $docs["body"];
        $this->data['assistant_docs']['docs_footer'] = $docs["footer"];

        $help_sections = $this->generateHelp($pageinfo);
        $this->data['assistant_help']['help_1_body'] = $help_sections["section_message"];
        $this->data['assistant_help']['help_2_body'] = $help_sections["section_contact"];

        return response()->json($this->data);
    }


    public function getPageInfo(string $url)
    {
        $info = ["title" => "", "description" => "", "docs_type" => "", "docs_tag" => "", 'video' => 'https://www.youtube.com/embed/SqBXm0acWNQ', 'overview_msg' => 'Watch the video below to get started!'];

        switch ($url) {

            case 'setup':
            $info["title"] = "setup section";
            $info["description"] = "Here you can setup the Hub for use";
            $info["docs_tag"] = 1;
                break;

            case 'setup':
            $info["title"] = "setup section";
            $info["description"] = "Here you can setup the Hub for use";
            $info["docs_tag"] = 1;
                break;

            case 'overview':
            $info["title"] = "overview section";
            $info["description"] = "It displays a list of available features and what they do";
            $info["docs_tag"] = 1;
                break;

            case 'customers-main':
            $info["title"] = "main page";
            $info["description"] = "You can choose how to manage your customers";
            $info["docs_tag"] = 1;
                break;

            case 'customers-main':
            $info["title"] = "main page";
            $info["description"] = "You can choose how to manage your customers";
            $info["docs_tag"] = 1;
                break;

            case 'customers-customers':
            $info["title"] = "customers list section";
            $info["description"] = "It displays a list of all your customers";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/9B2K8HHz2KI';
                break;

            case 'customers-new':
            $info["title"] = "new customer section";
            $info["description"] = "Here you can add details for a new customer";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/qaw3s5akwfA';
                break;

            case 'customers-custom-fields':
            $info["title"] = "custom fields section";
            $info["description"] = "Here you can add custom fields such as <em>customer website, age</em> or other peculiar customer characteristics";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/JA62outfu1E';

                break;
            case 'customers-groups':
            $info["title"] = "customer groups section";
            $info["description"] = "Here you can create special groups to use in categorizing customers such as <em>VIP status, location, age group</em> or other peculiar segments";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/OdoET1arvV0';
                break;
            
            case 'marketplace-main':
            $info["title"] = "main page";
            $info["description"] = "It contains listings individual and businesses <em>such as professionals and vendors</em> from whom you can buy services and products.";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/y3g9lHK7cUU';
                break;

            case 'library-main':
            $info["title"] = "main page";
            $info["description"] = "It contains several resources <em>in form of videos, audio and text</em> that will be of immense benefit to your business operations";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/R5v-m6_at9U';
                break;

            case 'app-store-main':
            $info["title"] = "main page";
            $info["description"] = "It features great applications the offer more comprehensive functionality to improve a specific area of your business";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/37wgw5oMbl0';
                break;

            case 'integrations-main':
            $info["title"] = "main page";
            $info["description"] = "It offer connectivity with existing 3rd party applications and platforms that you may already use";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/7NnHqMiUIHc';
                break;

            case 'service-requests-main':
            $info["title"] = "main page";
            $info["description"] = "It allows you to manage service requests received from other Hub users that may require your professional service(s)";
            $info["docs_tag"] = 1;
                break;

            case 'service-profile-main':
            $info["title"] = "main page";
            $info["description"] = "It allows you to provide additional details <em>such as credentials, experience &amp; social networks</em> that show your professional competence";
            $info["docs_tag"] = 1;
                break;

            case 'access-requests-main':
            $info["title"] = "main page";
            $info["description"] = "It allows you to request access to the Hub account of another Hub user for the purpose of carrying out a service";
            $info["docs_tag"] = 1;
                break;

            case 'ecommerce-domains':
            $info["title"] = "domains section";
            $info["description"] = "Here you can reserve a Dorcas sub-domain, purchase a new domain name or use an existing one that you own";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/C8YQXC14rCs';
                break;

            case 'ecommerce-website':
            $info["title"] = "website section";
            $info["description"] = "Here you can build a functioning website using a friendly drag-n-drop builder. You can then export the website or publish it automatically with paid hub account";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/7qV-PjGWfpk';
                break;

            case 'ecommerce-emails':
            $info["title"] = "emails section";
            $info["description"] = "Having a custom email account <em>such as info@yourdomain.com</em> shows a professional brand. Here you can add and delete email-accounts with a few clicks";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/4ZcjuKuKSxA';
                break;

            case 'ecommerce-blog':
            $info["title"] = "blogs section";
            $info["description"] = "Blogs are an essential part of marketing &amp; customer support. Setting up a blog and managing articles is a breeze with the Blog Manager";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/UYhMgwp66kI';
                break;

            case 'ecommerce-adverts':
            $info["title"] = "adverts section";
            $info["description"] = "It allows you to market, up-sell and cross-sell additional products to visitors to your website and/or blog";
            $info["docs_tag"] = 1;
                break;

            case 'ecommerce-store':
            $info["title"] = "online store section";
            $info["description"] = "Here you activate and setup a fully functioning online store that automatically displays your products and allows customers to explore and purchase them using your custom online store address";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/rPtJNU9JTWc';
                break;

            case 'finance-accounts':
            $info["title"] = "accounts &amp; journals section";
            $info["description"] = "Here you can either use existing <em>credit and debit</em> accounts and sub-accounts for categorizing your financial transactions or create custom ones in a way that better suits your business practice.";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/RrmT6BHkc2Q';
                break;

            case 'finance-entries':
            $info["title"] = "transactions &amp; entries section";
            $info["description"] = "Here you can enter your accounting transactions including debits, credits, payables, receivables and more.";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/bTckQtlkBus';
                break;

            case 'finance-reports':
            $info["title"] = "reports section";
            $info["description"] = "Here you can generate accounting statements and reports such as balance sheet";
            $info["docs_tag"] = 1;
                break;

            case 'library-videos':
            $info["title"] = "videos section";
            $info["description"] = "Here you can explore several informational and learning videos accross multiple categories and economic sectors";
            $info["docs_tag"] = 1;
                break;

            case 'marketplace-services':
            $info["title"] = "professional services section";
            $info["description"] = "This includes a listing of different services offered by professionals, consultants and other service providers in financial, legal, technology and other fields";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/y3g9lHK7cUU';
                break;

            case 'marketplace-products':
            $info["title"] = "vendor products section";
            $info["description"] = "This includes a listing of various physical products offered for sale by small businesses around you from which you can pick and choose and purchase for delivery";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/y3g9lHK7cUU';
                break;

            case 'marketplace-contacts-main':
            $info["title"] = "preferred contacts section";
            $info["description"] = "This page contains a list of professionals and vendors that you probably want to do business with later on.";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/y3g9lHK7cUU';
                break;

            case 'people-employees':
            $info["title"] = "employees section";
            $info["description"] = "This page contains a list of your employees. From here you can add, edit or delete employees";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/_UXmaH2HhVg';
                break;

            case 'people-departments':
            $info["title"] = "department section";
            $info["description"] = "Your business operations are probably handled by one or more functional units called departments. You can create and manage departments here";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/WVuVfFEV8H8';
                break;

            case 'people-teams':
            $info["title"] = "teams section";
            $info["description"] = "Beyond departments, you sometimes need to assemble ad-hoc teams of employees, usually on per-projcet basis. You can create and manage teams here";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/yVHi_1pWqUk';
                break;

            case 'sales-categories':
            $info["title"] = "categories section";
            $info["description"] = "This allows you to group your products into categories that will help buyers (and you, the business) locate products better on your online store";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/sltC-d21Lyk';
                break;

            case 'sales-products':
            $info["title"] = "products section";
            $info["description"] = "Here you can add, modify and delete the product(s) that your business offers. You can also manage product images and stock levels";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/-r05WFAHXlM';
                break;

            case 'sales-orders':
            $info["title"] = "orders section";
            $info["description"] = "Here you can create orders and invoices for your customers. All orders generated on your online store will also show up here";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/9RCQpbBKkWE';
                break;

            case 'settings-personal':
            $info["title"] = "personal section";
            $info["description"] = "Here you can make changes to your personal/bio data";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/NcFVQjg3ySk';
                break;

            case 'settings-business':
            $info["title"] = "business section";
            $info["description"] = "Here you can make changes to your business information such as contact details and more";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/SwNsP2QWY6I';
                break;

            case 'settings-billing':
            $info["title"] = "billing section";
            $info["description"] = "Here you can make changes to your billing preferences";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/qZu_Bcp49fM';
                break;

            case 'settings-security':
            $info["title"] = "security section";
            $info["description"] = "Here you can make changes to your security preferences <em>such as login password</em>";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/LvDoC92e7GI';
                break;

            case 'settings-customization':
            $info["title"] = "personal section";
            $info["description"] = "Here you can change the appearance of your account <em>such as brand logo</em>";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/5Rzb8PbCJ9w';
                break;

            case 'settings-banking':
            $info["title"] = "banking section";
            $info["description"] = "Here you can specify your bank account information";
            $info["docs_tag"] = 1;
            $info["video"] = 'https://www.youtube.com/embed/dqYgBWxhnLI';
                break;

            case 'settings-access-grants':
            $info["title"] = "access grants section";
            $info["description"] = "Here you can approve or reject request from marketplace professionals to access your account";
            $info["docs_tag"] = 1;
                break;

            default:
            $info["title"] = "";
            $info["description"] = "";
            $info["docs_tag"] = 1;
                break;
        }

        // sales
        // settings

        return $info;
    }


    public function generateDocs(string $doc_type, string $tag, string $title) {

        $header = 'Find below some documentation related to <strong>' . $title . '</strong>';
        $footer = 'Still can\'t find what you are looking for? Contact us via the Messages section';
        $body = [];

        if (!empty($tag)) {
            
            try {
                // Lets use the new format
                $base_url = "http://docs.hostville.ng";
                $book = "ecommerce-suite";
                $chapter = $doc_type; // "customers-module";
                $docs_url = "$base_url/books/$book/$chapter/$tag";
                $client = new \GuzzleHttp\Client();
                $request = $client->get($docs_url);

                if ($request->getStatusCode() === 200) {

                    //$html = json_decode($request->getBody()->getContents());
                    $html = $request->getBody()->getContents();

                    // Create a DOMDocument and load the HTML
                    $dom = new \DOMDocument();
                    libxml_use_internal_errors(true); // Disable error reporting for invalid HTML
                    $dom->loadHTML($html);
                    libxml_clear_errors();

                    // Create a DOMXPath object to query the DOM
                    $xpath = new \DOMXPath($dom);

                    // Find the div with class "entity-list"
                    $entityListDiv = $xpath->query('//div[contains(@class, "entity-list")]')->item(0);

                    // Find all the "a" tags under the div with classes "page" and "entity-list-item"
                    $aTags = $xpath->query('.//a[contains(@class, "page") and contains(@class, "entity-list-item")]', $entityListDiv);
                    foreach ($aTags as $aTag) {
                        // Extract the attributes of the "a" tag
                        $href = $aTag->getAttribute('href');
                        $id = $aTag->getAttribute('data-entity-id');
                        // You can extract other attributes similarly if needed

                        // Find the h4 tag with class "entity-list-item-name"
                        $h4Tag = $xpath->query('.//h4[contains(@class, "entity-list-item-name")]', $aTag)->item(0);
                        $h4Text = $h4Tag ? $h4Tag->textContent : '';

                        // Find the p tag under div with class "entity-item-snippet"
                        $pTag = $xpath->query('.//div[contains(@class, "entity-item-snippet")]/p', $aTag)->item(0);
                        $pText = $pTag ? $pTag->textContent : '';

                        $body[] = [
                            'post_id' => $id,
                            'post_title' => $h4Text,
                            'post_body' => $pText,
                            'post_excerpt' => $pText,
                            'post_link' => $href
                        ];
                    }


                } else {
                    $body = [];
                }

            } catch (\Exception $e) {
                $body = [];
            }
        }
        
        return ['header' => $header, 'body' => $body, 'footer' => $footer];
    }


    public function generateHelp(array $pageinfo) {
        $help_sections = array("section_message" => "", "section_contact" => "");

        /*$header_text = "If you have having problems with the <strong>". $pageinfo["title"] . "</strong> or something else, please fill the form below to send us a message and we&apos;'' reply as quickly as we can";
        $help_sections["section_message"] =  '
            <p>'.$header_text.'</p>
        ';*/

        $help_sections["section_message"] = $pageinfo["title"];

        return $help_sections;
    }


    public function generateOverviewVideo(string $video_url, string $header_text) {

        return '
            <p>'.$header_text.'</p>
            <div class="embed-responsive embed-responsive-16by9">
              <iframe class="embed-responsive-item" src="'.$video_url.'" id="assistant-overview-video"  allowscriptaccess="always" allow="autoplay"></iframe>
            </div>
        ';

    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function helpSendMessage(Request $request, Sdk $sdk)
    {

        try {
            
            $message = $request->input('help-message', '');
            $area = $request->input('help-area', '');
            $help_attachment = $request->file('attachment', null);


            $partner = null;
            $user = null;
            $appUiSettings = [];

            $dorcasUser = $request->user();
            if (!empty($dorcasUser)) {
                if (!empty($dorcasUser->partner) && !empty($dorcasUser->partner['data'])) {
                    $partner = (object) $dorcasUser->partner['data'];
                    $configuration = (array) $partner->extra_data;
                    $appUiSettings = $configuration['hubConfig'] ?? [];
                    $appUiSettings['product_logo'] = "";  // Lets use Dorcas' own !empty($partner->logo) ? $partner->logo : null;
                    $user = $dorcasUser;
                }
            }
            

            $help_subject = ($appUiSettings['product_name'] ?? 'Dorcas Hub') . ' Message from ' . $user->firstname .' '. $user->lastname;
            $subdomain = null;
            if (empty($request->session()->get('domain')) && !empty($partner->domain_issuances)) {
                $domain = $partner->domain_issuances['data'][0] ?? null;
            }
            if (!empty($domain)) {
                $subdomain = 'https://' . $domain["prefix"] . '.' . $domain["domain"]["data"]['domain'];
            }

            $help_data = array(
                "name" => $user->firstname .' '. $user->lastname,
                "email" => $user->email,
                "phone" => $user->phone,
                "message_string" => $message,
                "user" => $user,
                "partner" => $partner,
                "appUiSettings" => $appUiSettings,
                "Partner Name" => $partner->name,
                "Parter Address" => $subdomain
            );

            $company = $request->user()->company(true, true);

            Mail::send('modules-assistant::help-email', $help_data, function($message) use ($help_attachment, $help_subject, $partner, $user, $company) {
                $message->to($company->email, $partner->name)->subject($help_subject);
                if (!empty($help_attachment)) {
                    $message->attach($help_attachment->getRealPath(),
                        [
                            'as' => $help_attachment->getClientOriginalName(),
                            'mime' => $help_attachment->getClientMimeType(),
                        ]);
                }
                $message->from('hello@dorcas.io','Dorcas Hub');
                $message->replyTo($user->email, $user->firstname .' '. $user->lastname);
            });

            $response = "Success";

        } catch (\Exception $e) {
            $response = "Error". $e->getMessage();
        }
        
        return $response;
    }

    public function getModules(Request $request) {

        $allModules = array();

        $allModules["dashboard"] = array(
            "name" => "dashboard",
            "menu_group" => "",
            "dashboard" => "all",
            "title" => "Dashboard",
            "description" => "It contains statistics about your business operations as well as quick shortcuts to other functions",
            "docs_tag" => 35,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-dashboard'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mcu"] = array(
            "name" => 'modules-customers',
            "menu_group" => "",
            "dashboard" => "business",
            "title" => config('modules-customers.title', 'Customers').' Module',
            "description" => "Contains management tools to create, edit, categorize and group your esteemed customers",
            "docs_tag" => 13,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-customers'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mec"] = array(
            "name" => 'modules-ecommerce',
            "menu_group" => "",
            "dashboard" => "business",
            "title" => config('modules-ecommerce.title', 'eCommerce').' Module',
            "description" => "Comes with tools to manage domain names, email accounts, build websites, blogs and setup online stores",
            "docs_tag" => 39,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-ecommerce'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mpe"] = array(
            "name" => 'modules-people',
            "dashboard" => "business",
            "menu_group" => "",
            "title" => config('modules-people.title', 'People').' Module',
            "description" => "Allows you to manage your employees&apos; data and organize them using departments and teams",
            "docs_tag" => 21,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-people'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["msl"] = array(
            "name" => 'modules-sales',
            "menu_group" => "",
            "dashboard" => "business",
            "title" => config('modules-sales.title', 'Sales').' Module',
            "description" => "Contains several tools to add, edit and categorize your products as well as invoicing and order management",
            "docs_tag" => "",
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-sales'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mfn"] = array(
            "name" => 'modules-finance',
            "menu_group" => "",
            "dashboard" => "business",
            "title" => config('modules-finance.title', 'Finance').' Module',
            "description" => "Contains tools and interfaces for adding, importing and categorizing your accounting entries",
            "docs_tag" => 40,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-finance'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mli"] = array(
            "name" => 'modules-library',
            "menu_group" => "addons",
            "dashboard" => "business",
            "title" => config('modules-library.title', 'Library').' Module',
            "description" => "Contains a selection of learning resources such as videos on various business topics",
            "docs_tag" => 28,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-library'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mmp"] = array(
            "name" => 'modules-marketplace',
            "menu_group" => "addons",
            "dashboard" => "business",
            "title" => config('modules-marketplace.title', 'Marketplace').' Module',
            "description" => "Discover a pool of service professionals as well as product vendors that are ready to help your business",
            "docs_tag" => 41,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-marketplace'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["map"] = array(
            "name" => 'modules-app-store',
            "menu_group" => "addons",
            "dashboard" => "business",
            "title" => config('modules-app-store.title', 'App Store').' Module',
            "description" => "Contains apps that when installed, provide additional functionality to your digital operations",
            "docs_tag" => 30,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-app-store'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mit"] = array(
            "name" => 'modules-integrations',
            "menu_group" => "addons",
            "dashboard" => "business",
            "title" => config('modules-integrations.title', 'Integrations').' Module',
            "description" => "This allows you to leverage the features of existing 3rd-party platforms",
            "docs_tag" => 27,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-integrations'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mpa"] = array(
            "name" => 'modules-access-requests',
            "menu_group" => "",
            "dashboard" => "professional",
            "title" => config('modules-access-requests.title', 'Access Requests').' Module',
            "description" => "Allows professional service providers to request access to other SME&apos;s Hub accounts",
            "docs_tag" => 36,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-access-requests'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mps"] = array(
            "name" => 'modules-service-requests',
            "menu_group" => "",
            "dashboard" => "professional",
            "title" => config('modules-service-requests.title', 'Service Requests').' Module',
            "description" => "Allows professional service provicers to receive service requests from other SMEs",
            "docs_tag" => 37,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-service-requests'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mpp"] = array(
            "name" => 'modules-service-profile',
            "menu_group" => "",
            "dashboard" => "professional",
            "title" => config('modules-service-profile.title', 'Service Profile').' Module',
            "description" => "Allows professional service providers to properly showcase their credentials and experience",
            "docs_tag" => 38,
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-service-profile'),
            "action_title" => "",
            "action_link" => ""
        );

        $allModules["mse"] = array(
            "name" => 'modules-settings',
            "menu_group" => "",
            "dashboard" => "all",
            "title" => config('modules-settings.title', 'Settings').' Module',
            "description" => "Several settings that allow you to customize several aspects of your experience with the Hub",
            "docs_tag" => "",
            "video" => "",
            "display_image" => "images/overview/dashboard.jpg",
            "action_list" => $this->getModuleSubMenus($request,'modules-settings'),
            "action_title" => "",
            "action_link" => ""
        );

        return $allModules;

    }

    public function getModuleSubMenus(Request $request, $menu) {

        $submenus = [];
        $addons = ['modules-library','modules-integrations','modules-app-store','modules-marketplace'];
        $isAddon = in_array($menu, $addons);
        if ($isAddon) {
            $config = 'navigation-menu.addons.sub-menu.' . $menu;
        } else {
            $config = 'navigation-menu.' . $menu;
        }
        $menuConfig = config($config);
        $submenuConfig = config($config . '.sub-menu');
        //$viewMode = $request->session()->get('viewMode', 'business');
        $overviewSuffix = "?overview_mode=true";

        /*if ($isAddon) {
            $routea = safe_href_route($menuConfig['route']) ? route($menuConfig['route']) : 'javascript:void(0)';
            $submenus[] = '<li><a href="' . $routea . '" class="">' . $menuConfig['title'] . '</a></li>';
        }*/


        if ($menuConfig['navbar']) { // && $menuConfig['dashboard'] == $viewMode -  we'll filter in the view
            if ($menuConfig['clickable']) {
                $routem = safe_href_route($menuConfig['route']) ? route($menuConfig['route']) : 'javascript:void(0)';
                $submenus[] = '<li><a href="' . $routem . $overviewSuffix . '" class="">' . $menuConfig['title'] . '</a></li>';
            }
            if ( count( $submenuConfig ) > 0 ) {
                foreach ( $submenuConfig as $key => $value ) {
                    $route = safe_href_route($value['route']) ? route($value['route']) : 'javascript:void(0)';
                    if ( empty($value['visibility']) || ( isset($value['visibility']) && $value['visibility']==='show' ) ) {
                        $submenus[] = '<li><a href="' . $route . $overviewSuffix .  '" class="">' . $value['title'] . '</a></li>';
                    }
                }
            }
        }
        return $submenus;
    }

}