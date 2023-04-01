<?php

namespace App\Http\Controllers;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Post;
use App\Models\Cart;
use App\Models\Brand;
use App\User;
use Auth;
use Session;
use Newsletter;
use DB;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Helper;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Transaction;
class FrontendController extends Controller
{
    protected $product=null;
    public function __construct(Product $product){
        $this->product=$product;
    }

    public function index(Request $request){
        return redirect()->route($request->user()->role);
    }

    public function home(){
        $featured=Product::where('status','active')->where('is_featured',1)->orderBy('price','DESC')->limit(2)->get();
        $posts=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $banners=Banner::where('status','active')->limit(3)->orderBy('id','DESC')->get();
        // return $banner;
        $products=Product::where('status','active')->orderBy('id','DESC')->limit(8)->get();
        $category=Category::where('status','active')->where('is_parent',1)->orderBy('title','ASC')->get();
        // dd($category);
        // return $category;
        return view('frontend.index')
                ->with('featured',$featured)
                ->with('posts',$posts)
                ->with('banners',$banners)
                ->with('product_lists',$products)
                ->with('category_lists',$category);
    }

    public function aboutUs(){
        return view('frontend.pages.about-us');
    }

    public function contact(){
        return view('frontend.pages.contact');
    }

    public function productDetail($slug){
        $product_detail= Product::getProductBySlug($slug);
        // dd($product_detail);
        return view('frontend.pages.product_detail')->with('product_detail',$product_detail);
    }

    public function productGrids(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids);
            // return $products;
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));

            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(9);
        }
        // Sort by name , price, category


        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }
    public function productLists(){
        $products=Product::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=Category::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // dd($cat_ids);
            $products->whereIn('cat_id',$cat_ids)->paginate;
            // return $products;
        }
        if(!empty($_GET['brand'])){
            $slugs=explode(',',$_GET['brand']);
            $brand_ids=Brand::select('id')->whereIn('slug',$slugs)->pluck('id')->toArray();
            return $brand_ids;
            $products->whereIn('brand_id',$brand_ids);
        }
        if(!empty($_GET['sortBy'])){
            if($_GET['sortBy']=='title'){
                $products=$products->where('status','active')->orderBy('title','ASC');
            }
            if($_GET['sortBy']=='price'){
                $products=$products->orderBy('price','ASC');
            }
        }

        if(!empty($_GET['price'])){
            $price=explode('-',$_GET['price']);
            // return $price;
            // if(isset($price[0]) && is_numeric($price[0])) $price[0]=floor(Helper::base_amount($price[0]));
            // if(isset($price[1]) && is_numeric($price[1])) $price[1]=ceil(Helper::base_amount($price[1]));

            $products->whereBetween('price',$price);
        }

        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // Sort by number
        if(!empty($_GET['show'])){
            $products=$products->where('status','active')->paginate($_GET['show']);
        }
        else{
            $products=$products->where('status','active')->paginate(6);
        }
        // Sort by name , price, category


        return view('frontend.pages.product-lists')->with('products',$products)->with('recent_products',$recent_products);
    }
    public function productFilter(Request $request){
            $data= $request->all();
            // return $data;
            $showURL="";
            if(!empty($data['show'])){
                $showURL .='&show='.$data['show'];
            }

            $sortByURL='';
            if(!empty($data['sortBy'])){
                $sortByURL .='&sortBy='.$data['sortBy'];
            }

            $catURL="";
            if(!empty($data['category'])){
                foreach($data['category'] as $category){
                    if(empty($catURL)){
                        $catURL .='&category='.$category;
                    }
                    else{
                        $catURL .=','.$category;
                    }
                }
            }

            $brandURL="";
            if(!empty($data['brand'])){
                foreach($data['brand'] as $brand){
                    if(empty($brandURL)){
                        $brandURL .='&brand='.$brand;
                    }
                    else{
                        $brandURL .=','.$brand;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL="";
            if(!empty($data['price_range'])){
                $priceRangeURL .='&price='.$data['price_range'];
            }
            if(request()->is('e-shop.loc/product-grids')){
                return redirect()->route('product-grids',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
            else{
                return redirect()->route('product-lists',$catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
    }
    public function productSearch(Request $request){
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $products=Product::orwhere('title','like','%'.$request->search.'%')
                    ->orwhere('slug','like','%'.$request->search.'%')
                    ->orwhere('description','like','%'.$request->search.'%')
                    ->orwhere('summary','like','%'.$request->search.'%')
                    ->orwhere('price','like','%'.$request->search.'%')
                    ->orderBy('id','DESC')
                    ->paginate('9');
        return view('frontend.pages.product-grids')->with('products',$products)->with('recent_products',$recent_products);
    }

    public function productBrand(Request $request){
        $products=Brand::getProductByBrand($request->slug);
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productCat(Request $request){
        $products=Category::getProductByCat($request->slug);
        // return $request->slug;
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->products)->with('recent_products',$recent_products);
        }

    }
    public function productSubCat(Request $request){
        $products=Category::getProductBySubCat($request->sub_slug);
        // return $products;
        $recent_products=Product::where('status','active')->orderBy('id','DESC')->limit(3)->get();

        if(request()->is('e-shop.loc/product-grids')){
            return view('frontend.pages.product-grids')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }
        else{
            return view('frontend.pages.product-lists')->with('products',$products->sub_products)->with('recent_products',$recent_products);
        }

    }

    public function blog(){
        $post=Post::query();

        if(!empty($_GET['category'])){
            $slug=explode(',',$_GET['category']);
            // dd($slug);
            $cat_ids=PostCategory::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            return $cat_ids;
            $post->whereIn('post_cat_id',$cat_ids);
            // return $post;
        }
        if(!empty($_GET['tag'])){
            $slug=explode(',',$_GET['tag']);
            // dd($slug);
            $tag_ids=PostTag::select('id')->whereIn('slug',$slug)->pluck('id')->toArray();
            // return $tag_ids;
            $post->where('post_tag_id',$tag_ids);
            // return $post;
        }

        if(!empty($_GET['show'])){
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate($_GET['show']);
        }
        else{
            $post=$post->where('status','active')->orderBy('id','DESC')->paginate(9);
        }
        // $post=Post::where('status','active')->paginate(8);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogDetail($slug){
        $post=Post::getPostBySlug($slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        // return $post;
        return view('frontend.pages.blog-detail')->with('post',$post)->with('recent_posts',$rcnt_post);
    }

    public function blogSearch(Request $request){
        // return $request->all();
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        $posts=Post::orwhere('title','like','%'.$request->search.'%')
            ->orwhere('quote','like','%'.$request->search.'%')
            ->orwhere('summary','like','%'.$request->search.'%')
            ->orwhere('description','like','%'.$request->search.'%')
            ->orwhere('slug','like','%'.$request->search.'%')
            ->orderBy('id','DESC')
            ->paginate(8);
        return view('frontend.pages.blog')->with('posts',$posts)->with('recent_posts',$rcnt_post);
    }

    public function blogFilter(Request $request){
        $data=$request->all();
        // return $data;
        $catURL="";
        if(!empty($data['category'])){
            foreach($data['category'] as $category){
                if(empty($catURL)){
                    $catURL .='&category='.$category;
                }
                else{
                    $catURL .=','.$category;
                }
            }
        }

        $tagURL="";
        if(!empty($data['tag'])){
            foreach($data['tag'] as $tag){
                if(empty($tagURL)){
                    $tagURL .='&tag='.$tag;
                }
                else{
                    $tagURL .=','.$tag;
                }
            }
        }
        // return $tagURL;
            // return $catURL;
        return redirect()->route('blog',$catURL.$tagURL);
    }

    public function blogByCategory(Request $request){
        $post=PostCategory::getBlogByCategory($request->slug);
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post->post)->with('recent_posts',$rcnt_post);
    }

    public function blogByTag(Request $request){
        // dd($request->slug);
        $post=Post::getBlogByTag($request->slug);
        // return $post;
        $rcnt_post=Post::where('status','active')->orderBy('id','DESC')->limit(3)->get();
        return view('frontend.pages.blog')->with('posts',$post)->with('recent_posts',$rcnt_post);
    }

    // Quick checkout
    public function quickCheckout(Product $product_detail){
        return view('frontend.pages.quick-checkout', compact('product_detail'));
    }

    public function paymentFaspay(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                Rule::unique('transactions', 'order_id')
            ],
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'card_number' => 'required|numeric',
            'expiration_month' => 'required|regex:/^\d{2}\/\d{2}$/',
            'expiration_year' => [
                'required',
                'numeric',
                'digits:4',
                'after_or_equal:' . date('Y')
            ],
            'card_cvv' => 'required|numeric',
            'cardholder_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('error', $validator->errors()->first());
            return redirect()->back()->withInput();
        }

        $merchant_id	= '35258';
        $user_id 		= 'bot'.$merchant_id;
        $passw 			= 'jQbpI1mv';
        $bill_no 		= date('Ymdhis');
        $bill_date 		= date('Y-m-d H:i:s');
        $bill_expired 	= date("Y-m-d H:i:s", strtotime ("+3 hour"));
        $payment_channel = '402';
        $signature		= sha1(md5(($user_id.$passw.$bill_no)));
        $env			= 'dev';


        $data = array('request' 			=> 'Transmisi Info Detil Pembelian' ,
                        'merchant_id' 		=> $merchant_id ,
                        'merchant'			=> 'Internet Digital Media' ,
                        'bill_no'			=> $bill_no,
                        'bill_reff'			=> 'AZ'.$bill_no ,
                        'bill_date'			=> $bill_date,
                        'bill_expired'		=> $bill_expired,
                        "bill_desc"			=> "Pembayaran #".$bill_no,
                        "bill_currency"		=> "IDR",
                        "bill_gross"		=> "1000000",
                        "bill_miscfee"		=> "500000",
                        "bill_total"		=> "1500000",
                        "cust_no"			=> "A001",
                        "cust_name"			=> "faspay",
                        "cust_lastname"		=> "test",
                        "payment_channel"	=> $payment_channel,
                        "pay_type"			=> "1",
                        "bank_userid"		=> "",
                        "msisdn"			=> "08123456789",
                        "email"				=> "test@test.com",
                        "terminal"			=> "10",
                        "billing_name"		=> "test faspay",
                        "billing_lastname"	=> "0",
                        "billing_address"	=> "jalan pintu air raya",
                        "billing_address_city"		=> "Jakarta Pusat",
                        "billing_address_region"	=> "DKI Jakarta",
                        "billing_address_state"		=> "Indonesia",
                        "billing_address_poscode"	=> "10710",
                        "billing_msisdn"			=> "08123456789",
                        "billing_address_country_code"	=> "ID",
                        "receiver_name_for_shipping"	=> "Faspay Test",
                        "shipping_lastname"				=> "",
                        "shipping_address"				=> "jalan pintu air raya",
                        "shipping_address_city"			=> "Jakarta Pusat",
                        "shipping_address_region"		=> "DKI Jakarta",
                        "shipping_address_state"		=> "Indonesia",
                        "shipping_address_poscode"		=> "10710",
                        "shipping_msisdn"				=> "08123456789",
                        "shipping_address_country_code"	=> "ID",
                        "item" => array('id' 			=> "XYZ001" ,
                                        "product"		=> "Iphone 12",
                                        "qty"			=> "1",
                                        "amount"		=> "1000000",
                                        "payment_plan"	=> "01",
                                        "merchant_id"	=> "BC001",
                                        "tenor"			=> "00",
                                        "type"			=> "Smartphone",
                                        "url"			=> "https://merchant_website/product",
                                        "image_url"		=> "https://merchant_image_url/Mffc35PH77Dq7USrHb4qNm-1200-80.jpg"

                         ),

                        "reserve1"						=> "",
                        "reserve2"						=> "",
                        "signature"						=> $signature


         );

        $request1 = json_encode($data);


        // if ($env == 'dev') {
            $url='https://dev.faspay.co.id/cvr/300011/10';
        // }else{
            // $url='https://web.faspay.co.id/cvr/300011/10';
        // }


        $c = curl_init ($url);
        curl_setopt ($c, CURLOPT_POST, true);
        curl_setopt ($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt ($c, CURLOPT_POSTFIELDS, $request1);
        curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec ($c);


        curl_close($c);
        $data_response = json_decode($response);
        // dd($data_response);

        $redirect_url = $data_response->redirect_url;

        /* ======= redirect to faspay URL =======*/
        // header("Location:$redirect_url");

        // $redirectUrl = 'https://fpgdev.faspay.co.id/payment';
        // $queryString = http_build_query($payload);
        // $redirectUrl .= '?'.$queryString;
        return Redirect::away($redirect_url);

        // $merchant_id	= '35258';
        // $user_id 		= 'bot'.$merchant_id;
        // $passw 			= 'jQbpI1mv';
        // $bill_no 		= date('Ymdhis');
        // $bill_date 		= date('Y-m-d H:i:s');
        // $bill_expired 	= date("Y-m-d H:i:s", strtotime ("+1 hour"));
        // $payment_channel = '402';
        // $signature		= sha1(md5(($user_id.$passw.$bill_no)));

        // dd($bill_no);

        // $merchant_id = env('FP_PROD_MERCHANT_ID');
        // $merchant_name = env('FP_PROD_MERCHANT_NAME');
        // $merchant_password = env('FP_PROD_PASSWORD');
        // $request_time = date('YmdHis');
        // $transaction_id = uniqid();
        // $transaction_amount = number_format($request->amount, 2, '.', '');
        // $payment_method = 1; // Credit Card
        // $signature = md5($merchant_id.$payment_method.$transaction_id.$transaction_amount.$request_time.$merchant_password);

        // // Data yang dibutuhkan untuk request pembayaran
        // $payload = [
        //     'MERCHANTID' => $merchant_id,
        //     'TXNPASSWORD' => $merchant_password,
        //     'CURRENCYCODE' => 'IDR',
        //     'AMOUNT' => $transaction_amount,
        //     'MERCHANT_TRANID' => $transaction_id,
        //     'BILLING_NAME' => $request->recardholder_name,
        //     'BILLING_EMAIL' => $request->email,
        //     'BILLING_PHONE' => '1234567890',
        //     'PAYMENT_METHOD' => $payment_method,
        //     'SIGNATURE' => $signature,
        //     'REDIRECT_URL' => env('FP_PROD_REDIRECT_URL'), //route('faspay.response'),
        //     'BILLING_ADDRESS' => 'Jl. Pintu Air Raya No.20',
        //     'BILLING_ADDRESS_CITY' => 'Jakarta',
        //     'BILLING_ADDRESS_REGION' => 'DKI Jakarta',
        //     'BILLING_ADDRESS_STATE' => 'Jakarta',
        //     'BILLING_ADDRESS_POSCODE' => '10710',
        //     'BILLING_ADDRESS_COUNTRY_CODE' => 'ID',
        //     'SHIPPING_NAME' => $request->cardholder_name,
        //     'SHIPPING_ADDRESS' => 'Jl. Pintu Air Raya No.20',
        //     'SHIPPING_ADDRESS_CITY' => 'Jakarta',
        //     'SHIPPING_ADDRESS_REGION' => 'DKI Jakarta',
        //     'SHIPPING_ADDRESS_STATE' => 'Jakarta',
        //     'SHIPPING_ADDRESS_POSCODE' => '10710',
        //     'SHIPPING_ADDRESS_COUNTRY_CODE' => 'ID',
        //     'DESCRIPTION' => $request->description,
        //     'EXPIRYMONTH' => $request->expiration_month,
        //     'EXPIRYYEAR' => $request->expiration_year,
        //     'CARDCVC' => $request->card_cvv,
        //     'CARDNAME' => $request->cardholder_name,
        //     'CARDTYPE' => 'V',
        //     'CUSTNAME' => $request->cardholder_name,
        //     'CUSTEMAIL' => $request->email,
        //     'PHONE_NO' => '1234567890',
        //     'CARD_NUMBER' => $request->card_number,
        //     'PYMT_IND' => '',
        //     'PYMT_CRITERIA' => '',
        //     'PYMT_TOKEN' => '',
        //     'CUSTOMER_REF' => '',
        //     'NAME_ON_CARD' => $request->cardholder_name,
        // ];

        // $redirectUrl = 'https://fpgdev.faspay.co.id/payment';
        // $queryString = http_build_query($payload);
        // $redirectUrl .= '?'.$queryString;
        // return Redirect::away($redirectUrl);


        // $amount = $request->amount;
        // $description = $request->description;
        // $cardholder_name = $request->cardholder_name;
        // $email = $request->email;
        // $card_number = $request->card_number;
        // $expiration_month = $request->expiration_month;
        // $expiration_year = $request->expiration_year;
        // $card_cvv = $request->card_cvv;

        // // Kirim request ke API Faspay
        // $response = $this->executePaymentAPI(
        //     $amount,$description,$cardholder_name,$email,$card_number,$expiration_month,$expiration_year,$card_cvv
        // );

        // Redirect user ke halaman payment gateway Faspay
        // return redirect()->away($response['payment_page']);

    }

    private function executePaymentAPI($amount,$description,$cardholder_name,$email,$card_number,$expiration_month,$expiration_year,$card_cvv)
    {
        // Data yang diperlukan untuk koneksi ke API Faspay
        $merchant_id = env('FP_PROD_MERCHANT_ID');
        $merchant_name = env('FP_PROD_MERCHANT_NAME');
        $merchant_password = env('FP_PROD_PASSWORD');
        $request_time = date('YmdHis');
        $transaction_id = uniqid();
        $transaction_amount = number_format($amount, 2, '.', '');
        $payment_method = 1; // Credit Card
        $signature = md5($merchant_id.$payment_method.$transaction_id.$transaction_amount.$request_time.$merchant_password);

        // Data yang dibutuhkan untuk request pembayaran
        $payload = [
            'MERCHANTID' => $merchant_id,
            'TXNPASSWORD' => $merchant_password,
            'CURRENCYCODE' => 'IDR',
            'AMOUNT' => $transaction_amount,
            'MERCHANT_TRANID' => $transaction_id,
            'BILLING_NAME' => $cardholder_name,
            'BILLING_EMAIL' => $email,
            'BILLING_PHONE' => '1234567890',
            'PAYMENT_METHOD' => $payment_method,
            'SIGNATURE' => $signature,
            'REDIRECT_URL' => env('FP_PROD_REDIRECT_URL'), //route('faspay.response'),
            'BILLING_ADDRESS' => 'Jl. Pintu Air Raya No.20',
            'BILLING_ADDRESS_CITY' => 'Jakarta',
            'BILLING_ADDRESS_REGION' => 'DKI Jakarta',
            'BILLING_ADDRESS_STATE' => 'Jakarta',
            'BILLING_ADDRESS_POSCODE' => '10710',
            'BILLING_ADDRESS_COUNTRY_CODE' => 'ID',
            'SHIPPING_NAME' => $cardholder_name,
            'SHIPPING_ADDRESS' => 'Jl. Pintu Air Raya No.20',
            'SHIPPING_ADDRESS_CITY' => 'Jakarta',
            'SHIPPING_ADDRESS_REGION' => 'DKI Jakarta',
            'SHIPPING_ADDRESS_STATE' => 'Jakarta',
            'SHIPPING_ADDRESS_POSCODE' => '10710',
            'SHIPPING_ADDRESS_COUNTRY_CODE' => 'ID',
            'DESCRIPTION' => $description,
            'EXPIRYMONTH' => $expiration_month,
            'EXPIRYYEAR' => $expiration_year,
            'CARDCVC' => $card_cvv,
            'CARDNAME' => $cardholder_name,
            'CARDTYPE' => 'V',
            'CUSTNAME' => $cardholder_name,
            'CUSTEMAIL' => $email,
            'PHONE_NO' => '1234567890',
            'CARD_NUMBER' => $card_number,
            'PYMT_IND' => '',
            'PYMT_CRITERIA' => '',
            'PYMT_TOKEN' => '',
            'CUSTOMER_REF' => '',
            'NAME_ON_CARD' => $cardholder_name,
        ];


        // // Kirim request ke API Faspay
        // $client = new \GuzzleHttp\Client();
        // $response = $client->post('https://fpg.faspay.co.id/payment', [
        //     'form_params' => $data
        // ]);

        // $result = json_decode($response->getBody(), true);

        // // Redirect user ke halaman payment gateway Faspay
        // return redirect()->away($result['payment_page']);
        // Generate signature
        // $signature = md5(config('services.faspay.user_id').config('services.faspay.password').$payload['merchant_id'].$payload['bill_no'].$payload['bill_reff'].$payload['bill_total'].$payload['bill_currency'].$payload['cc_number'].$payload['cc_exp_month'].$payload['cc_exp_year'].$payload['cc_cvv']);
        // $payload['signature'] = $signature;
        // Redirect
        $redirectUrl = 'https://fpgdev.faspay.co.id/payment';
        $queryString = http_build_query($payload);
        $redirectUrl .= '?'.$queryString;
        return Redirect::away($redirectUrl);
    }


    public function paymentFaspay2(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => [
                'required',
                Rule::unique('transactions', 'order_id')
            ],
            'amount' => 'required|numeric',
            'description' => 'required|string|max:255',
            'card_number' => 'required|numeric',
            'expiration_month' => 'required|regex:/^\d{2}\/\d{2}$/',
            'expiration_year' => [
                'required',
                'numeric',
                'digits:4',
                'after_or_equal:' . date('Y')
            ],
            'card_cvv' => 'required|numeric',
            'cardholder_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            $request->session()->flash('error', $validator->errors()->first());
            return redirect()->back()->withInput();
        }


        $tranid = date("YmdGis");
        $signaturecc=sha1('##'.strtoupper('aggregator_tes').'##'.strtoupper('ejeussad').'##'.$tranid.'##1000.00##'.'0'.'##');

        $postData = array(
            "TRANSACTIONTYPE"               => '1',
            "RESPONSE_TYPE"                 => '2',
            "LANG"                          => '',
            "MERCHANTID"                    => 'aggregator_tes',
            "PAYMENT_METHOD"                => '1',
            "TXN_PASSWORD"                  => 'ejeussad',
            "MERCHANT_TRANID"               => $tranid,
            "CURRENCYCODE"                  => 'IDR',
            "AMOUNT"                        => '1000.00',
            "CUSTNAME"                      => 'merhcant test CC',
            "CUSTEMAIL"                     => 'testing@faspay.co.id',
            "DESCRIPTION"                   => 'transaski test',
            "RETURN_URL"                    => 'http://localhost/creditcard/merchant_return_page.php',
            "SIGNATURE"                     => $signaturecc,
            "BILLING_ADDRESS"               => 'Jl. pintu air raya',
            "BILLING_ADDRESS_CITY"          => 'Jakarta',
            "BILLING_ADDRESS_REGION"        => 'DKI Jakarta',
            "BILLING_ADDRESS_STATE"         => 'DKI Jakarta',
            "BILLING_ADDRESS_POSCODE"       => '10710',
            "BILLING_ADDRESS_COUNTRY_CODE"  => 'ID',
            "RECEIVER_NAME_FOR_SHIPPING"    => 'Faspay test',
            "SHIPPING_ADDRESS"              => 'Jl. pintu air raya',
            "SHIPPING_ADDRESS_CITY"         => 'Jakarta',
            "SHIPPING_ADDRESS_REGION"       => 'DKI Jakarta',
            "SHIPPING_ADDRESS_STATE"        => 'DKI Jakarta',
            "SHIPPING_ADDRESS_POSCODE"      => '10710',
            "SHIPPING_ADDRESS_COUNTRY_CODE" => 'ID',
            "SHIPPINGCOST"                  => '0.00',
            "PHONE_NO"                      => '0897867688989',
            "MPARAM1"                       => '',
            "MPARAM2"                       => '',
            "PYMT_IND"                      => '',
            "PYMT_CRITERIA"                 => '',
            "PYMT_TOKEN"                    => '',

            /* ==== customize input card page ===== */
            "style_merchant_name"         => 'black',
            "style_order_summary"         => 'black',
            "style_order_no"              => 'black',
            "style_order_desc"            => 'black',
            "style_amount"                => 'black',
            "style_background_left"       => '#fff',
            "style_button_cancel"         => 'grey',
            "style_font_cancel"           => 'white',
            /* ==== logo directly to your url source ==== */
            "style_image_url"           => 'http://url_merchant/image.png',
        );

        $url = "https://fpg.faspay.co.id/payment";
        $fields = http_build_query($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;


        // $merchant_id	= '12345';
        // $user_id 		= 'bot'.$merchant_id;
        // $passw 			= 'p@ssw0rd';
        // $bill_no 		= date('Ymdhis');
        // $bill_date 		= date('Y-m-d H:i:s');
        // $bill_expired 	= date("Y-m-d H:i:s", strtotime ("+1 hour"));
        // $payment_channel = '402';
        // $signature		= sha1(md5(($user_id.$passw.$bill_no)));
        // $env			= 'dev';


        // $data = array('request' 			=> 'Transmisi Info Detil Pembelian' ,
        //                 'merchant_id' 		=> $merchant_id ,
        //                 'merchant'			=> 'Faspay Store' ,
        //                 'bill_no'			=> $bill_no,
        //                 'bill_reff'			=> 'AZ'.$bill_no ,
        //                 'bill_date'			=> $bill_date,
        //                 'bill_expired'		=> $bill_expired,
        //                 "bill_desc"			=> "Pembayaran #".$bill_no,
        //                 "bill_currency"		=> "IDR",
        //                 "bill_gross"		=> "1000000",
        //                 "bill_miscfee"		=> "500000",
        //                 "bill_total"		=> "1500000",
        //                 "cust_no"			=> "A001",
        //                 "cust_name"			=> "faspay",
        //                 "cust_lastname"		=> "test",
        //                 "payment_channel"	=> $payment_channel,
        //                 "pay_type"			=> "1",
        //                 "bank_userid"		=> "",
        //                 "msisdn"			=> "08123456789",
        //                 "email"				=> "test@test.com",
        //                 "terminal"			=> "10",
        //                 "billing_name"		=> "test faspay",
        //                 "billing_lastname"	=> "0",
        //                 "billing_address"	=> "jalan pintu air raya",
        //                 "billing_address_city"		=> "Jakarta Pusat",
        //                 "billing_address_region"	=> "DKI Jakarta",
        //                 "billing_address_state"		=> "Indonesia",
        //                 "billing_address_poscode"	=> "10710",
        //                 "billing_msisdn"			=> "08123456789",
        //                 "billing_address_country_code"	=> "ID",
        //                 "receiver_name_for_shipping"	=> "Faspay Test",
        //                 "shipping_lastname"				=> "",
        //                 "shipping_address"				=> "jalan pintu air raya",
        //                 "shipping_address_city"			=> "Jakarta Pusat",
        //                 "shipping_address_region"		=> "DKI Jakarta",
        //                 "shipping_address_state"		=> "Indonesia",
        //                 "shipping_address_poscode"		=> "10710",
        //                 "shipping_msisdn"				=> "08123456789",
        //                 "shipping_address_country_code"	=> "ID",
        //                 "item" => array('id' 			=> "XYZ001" ,
        //                                 "product"		=> "Iphone 12",
        //                                 "qty"			=> "1",
        //                                 "amount"		=> "1000000",
        //                                 "payment_plan"	=> "01",
        //                                 "merchant_id"	=> "BC001",
        //                                 "tenor"			=> "00",
        //                                 "type"			=> "Smartphone",
        //                                 "url"			=> "https://merchant_website/product",
        //                                 "image_url"		=> "https://merchant_image_url/Mffc35PH77Dq7USrHb4qNm-1200-80.jpg"

        //                 ),

        //                 "reserve1"						=> "",
        //                 "reserve2"						=> "",
        //                 "signature"						=> $signature


        // );

        // $request = json_encode($data);


        // if ($env == 'dev') {
        //     $url='https://dev.faspay.co.id/cvr/300011/10';
        // }else{
        //     $url='https://web.faspay.co.id/cvr/300011/10';
        // }


        // $c = curl_init ($url);
        // curl_setopt ($c, CURLOPT_POST, true);
        // curl_setopt ($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        // curl_setopt ($c, CURLOPT_POSTFIELDS, $request);
        // curl_setopt ($c, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt ($c, CURLOPT_SSL_VERIFYPEER, false);
        // $response = curl_exec ($c);


        // curl_close($c);
        // $data_response = json_decode($response);
        // dd($data_response);
        // $redirect_url = $data_response->redirect_url;

        // /* ======= redirect to faspay URL =======*/
        // header("Location:$redirect_url");


        // $payload = [
        //     'request' => 'Post Data Transaction',
        //     'merchant_id' => config('services.faspay.merchant_id'),
        //     'merchant' => config('services.faspay.merchant_name'),
        //     'bill_no' => $request->order_id,
        //     'bill_reff' => $request->order_id,
        //     'bill_date' => date('Y-m-d H:i:s'),
        //     'bill_expired' => date('Y-m-d H:i:s', strtotime('+1 day')),
        //     'bill_desc' => $request->description,
        //     'bill_currency' => 'IDR',
        //     'bill_gross' => $request->amount,
        //     'bill_miscfee' => 0,
        //     'bill_total' => $request->amount,
        //     'cust_no' => '0000000000000000',
        //     'cust_name' => $request->cardholder_name,
        //     'payment_channel' => 15,
        //     'bank_userid' => '',
        //     'msisdn' => '',
        //     'email' => $request->email,
        //     'terminal' => '10',
        //     'billing_address' => $request->address1,
        //     'billing_address_city' => 'Kuta',
        //     'billing_address_region' => 'Bali',
        //     'billing_address_state' => $request->country,
        //     'billing_address_poscode' => $request->post_code,
        //     'billing_mall_name' => '',
        //     'billing_telkom' => '',
        //     'billing_email' => $request->email,
        //     'billing_sent_email' => '1',
        //     'response_type' => '3',
        //     'return_url' => config('services.faspay.redirect_url'),
        //     'timeout' => '',
        //     'signature' => '',
        //     'cc_number' => $request->card_number,
        //     'cc_exp_month' => substr($request->expiration_month, 0, 2),
        //     'cc_exp_year' => substr($request->expiration_year, 3, 2),
        //     'cc_cvv' => $request->card_cvv,
        // ];


        // // Generate signature
        // $signature = md5(config('services.faspay.user_id').config('services.faspay.password').$payload['merchant_id'].$payload['bill_no'].$payload['bill_reff'].$payload['bill_total'].$payload['bill_currency'].$payload['cc_number'].$payload['cc_exp_month'].$payload['cc_exp_year'].$payload['cc_cvv']);
        // $payload['signature'] = $signature;
        // dd(json_encode($payload));
        // // Redirect
        // $redirectUrl = 'https://fpgdev.faspay.co.id/payment';
        // $queryString = http_build_query($payload);
        // $redirectUrl .= '?'.$queryString;
        // return Redirect::away($redirectUrl);
    }

    public function handleFaspayCallback(Request $request)
    {

        dd($request);
        // $merchant_id	= '35258';
        // $user_id 		= 'bot'.$merchant_id;
        // $passw 			= 'jQbpI1mv';
        // $bill_no 		= date('Ymdhis');
        // $bill_date 		= date('Y-m-d H:i:s');
        // $bill_expired 	= date("Y-m-d H:i:s", strtotime ("+1 hour"));
        // $payment_channel = '402';
        // $signature		= sha1(md5(($user_id.$passw.$bill_no)));
        // $env			= 'dev';

        // Validate response signature
        $signature = md5($request->response.$request->trx_id.config('services.faspay.merchant_id').config('services.faspay.merchant_name').$request->bill_no.$request->payment_reff.$request->payment_date.$request->payment_status.$request->payment_total.$request->payment_channel.config('services.faspay.user_id').config('services.faspay.password'));
        if ($signature !== $request->signature) {
            return response('Invalid signature', 400);
        }

        // Handle payment status
        // $orderId = $request->bill_no;
        $paymentStatus = $request->payment_status;
        dd($paymentStatus);
        // Generate trx_id
        $trxId = uniqid();

        // Save transaction to database
        $transaction = new Transaction([
            'transaction_id' => $request->trx_id,
            'order_id' => $orderId,
            'amount' => $request->payment_total,
            'payment_status' => $paymentStatus,
            // Add other attributes here
        ]);
        $transaction->save();

        if ($paymentStatus == '2') { // Payment success
            // Update order status and do other necessary actions
            // ...
            return view('payment.success');
        } else if ($paymentStatus == '3') { // Payment failed
            // Update order status and do other necessary actions
            // ...
            return view('payment.failed');
        } else { // Payment pending
            // Update order status and do other necessary actions
            // ...
            return view('payment.pending');
        }
    }

    // Login
    public function login(){
        return view('frontend.pages.login');
    }
    public function loginSubmit(Request $request){
        $data= $request->all();
        if(Auth::attempt(['email' => $data['email'], 'password' => $data['password'],'status'=>'active'])){
            Session::put('user',$data['email']);
            request()->session()->flash('success','Successfully login');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Invalid email and password pleas try again!');
            return redirect()->back();
        }
    }

    public function logout(){
        Session::forget('user');
        Auth::logout();
        request()->session()->flash('success','Logout successfully');
        return back();
    }

    public function register(){
        return view('frontend.pages.register');
    }
    public function registerSubmit(Request $request){
        // return $request->all();
        $this->validate($request,[
            'name'=>'string|required|min:2',
            'email'=>'string|required|unique:users,email',
            'password'=>'required|min:6|confirmed',
        ]);
        $data=$request->all();
        // dd($data);
        $check=$this->create($data);
        Session::put('user',$data['email']);
        if($check){
            request()->session()->flash('success','Successfully registered');
            return redirect()->route('home');
        }
        else{
            request()->session()->flash('error','Please try again!');
            return back();
        }
    }
    public function create(array $data){
        return User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'password'=>Hash::make($data['password']),
            'status'=>'active'
            ]);
    }
    // Reset password
    public function showResetForm(){
        return view('auth.passwords.old-reset');
    }

    public function subscribe(Request $request){
        if(! Newsletter::isSubscribed($request->email)){
                Newsletter::subscribePending($request->email);
                if(Newsletter::lastActionSucceeded()){
                    request()->session()->flash('success','Subscribed! Please check your email');
                    return redirect()->route('home');
                }
                else{
                    Newsletter::getLastError();
                    return back()->with('error','Something went wrong! please try again');
                }
            }
            else{
                request()->session()->flash('error','Already Subscribed');
                return back();
            }
    }

}
