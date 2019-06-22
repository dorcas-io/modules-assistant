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

        $overviewVideo = "https://www.youtube.com/embed/zbNnbKtkVbM";

        switch ($module) {
            case 'dashboard':
                $this->data['header_message']['message'] = '<strong>Welcome to Dorcas Hub Dashboard</strong>. It contains vital statistics about your business operations as well as quick shortcuts to other functions';
                $pageinfo["docs_tag"] = 35;
                break;
            case 'mcu':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-customers.title', 'Customers').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-customers.title', 'Customers').' Module</strong>!';
                $pageinfo["docs_tag"] = 13;
                $pageinfo["video"] = "https://www.youtube.com/embed/zbNnbKtkVbM";
                break;
            case 'mpe':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-people.title', 'People').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-people.title', 'Customers').' People</strong>!';
                $pageinfo["docs_tag"] = 21;
                break;
            case 'mli':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-library.title', 'Library').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-library.title', 'Library').' Module</strong>!';
                $pageinfo["docs_tag"] = 28;
                break;
            case 'map':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-app-store.title', 'App Store').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-app-store.title', 'App Store').' Module</strong>!';
                $pageinfo["docs_tag"] = 30;
                break;
            case 'mit':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-integrations.title', 'Integrations').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-integrations.title', 'Integrations').' Module</strong>!';
                $pageinfo["docs_tag"] = 27;
                break;
            case 'mpa':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-access-requests.title', 'Access Requests').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-access-requests.title', 'Access Requests').' Module</strong>!';
                break;
                $pageinfo["docs_tag"] = 36;
            case 'mps':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-service-requests.title', 'Service Requests').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-service-requests.title', 'Service Requests').' Module</strong>!';
                break;
                $pageinfo["docs_tag"] = 37;
            case 'mpp':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-service-profile.title', 'Service Profile').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-service-profile.title', 'Service Profile').' Module</strong>!';
                $pageinfo["docs_tag"] = 38;
                break;
            case 'mec':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-ecommerce.title', 'eCommerce').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-ecommerce.title', 'eCommerce').' Module</strong>!';
                $pageinfo["docs_tag"] = 39;
                break;
            case 'mfn':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-finance.title', 'Finance').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-finance.title', 'Finance').' Module</strong>!';
                $pageinfo["docs_tag"] = 40;
                break;
            case 'mmp':
                $this->data['header_message']['message'] = $page_info ? 'You are currently using the '.$pageinfo["title"].' of the <strong>'.config('modules-marketplace.title', 'Marketplace').' Module</strong>! '. $pageinfo["description"] : 'You are currently using the <strong>'.config('modules-marketplace.title', 'Marketplace').' Module</strong>!';
                $pageinfo["docs_tag"] = 41;
                break;
            
            default:
            $this->data['header_message']['message'] = 'Thanks for using the <strong>Dorcas Hub</strong>!';
                $pageinfo["docs_tag"] = 42;
                break;
        }


        $this->data['assistant_assistant']['assistant_1_body'] = $this->generateOverviewVideo($pageinfo["video"], $pageinfo["overview_msg"]);

        #get docs
        //$docs_module = $this->generateDocs($module);
        //$docs_url = $this->generateDocs($url);

        $docs = $this->generateDocs($pageinfo["docs_tag"], $pageinfo["title"]);

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
        $info = ["title" => "", "description" => "", "docs_tag" => "", 'video' => 'https://www.youtube.com/embed/zbNnbKtkVbM', 'overview_msg' => 'Watch the video below to get started!'];

        switch ($url) {

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
                break;

            case 'customers-new':
            $info["title"] = "new customer section";
            $info["description"] = "Here you can add details for a new customer";
            $info["docs_tag"] = 1;
                break;

            case 'customers-custom-fields':
            $info["title"] = "custom fields section";
            $info["description"] = "Here you can add custom fields such as <em>customer website, age</em> or other peculiar customer characteristics";
            $info["docs_tag"] = 1;

                break;
            case 'customers-groups':
            $info["title"] = "customer groups section";
            $info["description"] = "Here you can create special groups to use in categorizing customers such as <em>VIP status, location, age group</em> or other peculiar segments";
            $info["docs_tag"] = 1;
                break;
            
            case 'marketplace-main':
            $info["title"] = "main page";
            $info["description"] = "It contains listings individual and businesses <em>such as professionals and vendors</em> from whom you can buy services and products.";
            $info["docs_tag"] = 1;
                break;

            case 'library-main':
            $info["title"] = "main page";
            $info["description"] = "It contains several resources <em>in form of videos, audio and text</em> that will be of immense benefit to your business operations";
            $info["docs_tag"] = 1;
                break;

            case 'app-store-main':
            $info["title"] = "main page";
            $info["description"] = "It features great applications the offer more comprehensive functionality to improve a specific area of your business";
            $info["docs_tag"] = 1;
                break;

            case 'integrations-main':
            $info["title"] = "main page";
            $info["description"] = "It offer connectivity with existing 3rd party applications and platforms that you may already use";
            $info["docs_tag"] = 1;
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
                break;

            case 'ecommerce-website':
            $info["title"] = "website section";
            $info["description"] = "Here you can build a functioning website using a friendly drag-n-drop builder. You can then export the website or publish it automatically with paid hub account";
            $info["docs_tag"] = 1;
                break;

            case 'ecommerce-emails':
            $info["title"] = "emails section";
            $info["description"] = "Having a custom email account <em>such as info@yourdomain.com</em> shows a professional brand. Here you can add and delete email-accounts with a few clicks";
            $info["docs_tag"] = 1;
                break;

            case 'ecommerce-blog':
            $info["title"] = "blogs section";
            $info["description"] = "Blogs are an essential part of marketing &amp; customer support. Setting up a blog and managing articles is a breeze with the Blog Manager";
            $info["docs_tag"] = 1;
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
                break;

            case 'finance-accounts':
            $info["title"] = "accounts &amp; journals section";
            $info["description"] = "Here you can either use existing <em>credit and debit</em> accounts and sub-accounts for categorizing your financial transactions or create custom ones in a way that better suits your business practice.";
            $info["docs_tag"] = 1;
                break;

            case 'finance-entries':
            $info["title"] = "transactions &amp; entries section";
            $info["description"] = "Here you can enter your accounting transactions including debits, credits, payables, receivables and more.";
            $info["docs_tag"] = 1;
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
                break;

            case 'marketplace-products':
            $info["title"] = "vendor products section";
            $info["description"] = "This includes a listing of various physical products offered for sale by small businesses around you from which you can pick and choose and purchase for delivery";
            $info["docs_tag"] = 1;
                break;

            case 'marketplace-contacts-main':
            $info["title"] = "preferred contacts section";
            $info["description"] = "This page contains a list of professionals and vendors that you probably want to do business with later on.";
            $info["docs_tag"] = 1;
                break;

            case 'people-employees':
            $info["title"] = "employees section";
            $info["description"] = "This page contains a list of your employees. From here you can add, edit or delete employees";
            $info["docs_tag"] = 1;
                break;

            case 'people-departments':
            $info["title"] = "department section";
            $info["description"] = "Your business operations are probably handled by one or more functional units called departments. You can create and manage departments here";
            $info["docs_tag"] = 1;
                break;

            case 'people-teams':
            $info["title"] = "teams section";
            $info["description"] = "Beyond departments, you sometimes need to assemble ad-hoc teams of employees, usually on per-projcet basis. You can create and manage teams here";
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


    public function generateDocs(string $tag, string $title) {

        $header = 'Find below some documentation related to <strong>' . $title . '</strong>';
        $footer = 'Still can\'t find what you are looking for? Contact us via the Help section';
        $body = [];

        if (!empty($tag) && is_numeric($tag)) { //&& is_numeric($tag)
            //$docs_url = 'https://blog.smartbusiness.com.ng/wp-json/wp/v2/posts?search='.$tag;
            $docs_url = 'https://docs.dorcas.io/wp-json/wp/v2/posts?tags='.$tag;
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

        /*$this->validate($request, [
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:6144',
        ], [
            'attachment.max' => 'The attachment should not be greater than 6Mb, you can compress the file into an archive.'
        ]);*/
        # validate the request

        try {

            $name = $request->input('customer-name', '');
            $email = $request->input('customer-email', '');
            $phone = $request->input('customer-phone', '');
            $message = $request->input('help-message', '');
            $area = $request->input('help-area', '');
            $attachment = $request->file('attachment', null);


            $partner = null;
            $user = null;
            $appUiSettings = [];

            $dorcasUser = $request->user();
            if (!empty($dorcasUser)) {
                if (!empty($dorcasUser->partner) && !empty($dorcasUser->partner['data'])) {
                    $partner = (object) $dorcasUser->partner['data'];
                    $configuration = (array) $partner->extra_data;
                    $appUiSettings = $configuration['hubConfig'] ?? [];
                    $appUiSettings['product_logo'] = !empty($this->partner->logo) ? $this->partner->logo : null;
                    $user = $dorcasUser;
                }
            }
            

            $subject = ($appUiSettings['product_name'] ?? 'Dorcas Hub') . 'Message from' . $this->user->firstname .' '. $this->user->lastname;
            $subdomain = null;
            if (empty($request->session()->get('domain')) && !empty($partner->domain_issuances)) {
                $domain = $this->partner->domain_issuances['data'][0] ?? null;
            }
            if (!empty($domain)) {
                $subdomain = 'https://' . $this->domain->prefix . '.' . $this->domain->domain['data']['domain'];
            }


            $help_data = array(
                "name" => $name,
                "email" => $email,
                "phone" => $phone,
                "message" => $message,
                "user" => $user,
                "appUiSettings" => $appUiSettings,
                "partner" => $partner,
                "subdomain" => $subdomain
            );


            
            /*Mail::to($request->user())
            ->queue(new HelpEmail($order));*/

            Mail::send('modules-assistant::help-email', $help_data, function($message) {
                $message->to('ifeoluwa.olawoye@gmail.com', 'Test Email')->subject('Message from Access Hub');
                if (!empty($attachment)) {
                    $message->attach($attachment->getRealPath(),
                        [
                            'as' => $attachment->getClientOriginalName(),
                            'mime' => $attachment->getClientMimeType(),
                        ]);
                }
                $message->from('hello@dorcas.io','Dorcas Hub');
            });

            /*Mail::to($request->user())
                ->cc($moreUsers)
                ->bcc($evenMoreUsers)
                ->send(new OrderShipped($order));*/

            /*Mail::to($request->user())
                ->cc($moreUsers)
                ->bcc($evenMoreUsers)
                ->queue(new OrderShipped($order));*/


        } catch (\Exception $e) {
            $response = "Error". $e->getMessage();
        }

        $response = "Success";
        return $response;
    }

}