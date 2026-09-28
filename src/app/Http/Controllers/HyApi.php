<?php

namespace App\Http\Controllers;



use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\ClassObject;
use App\Models\ClassList;
use App\Models\MyPage;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Library\HttpClient;
use App\Library\AES_Encryption;

use Illuminate\Support\Facades\Hash;

$key = "";
$iv = "";

class HyApi extends Controller
{
    function login(){
        return redirect('https://api.hanyang.ac.kr/oauth/authorize?client_id=379d5938cbb71bb3d6a360da4ea05a&response_type=code&redirect_uri=https://pblboard.com/hy/callback&scope=35');
    }

    function callback(Request $request){
        try {
            $apiBaseUrl = "https://api.hanyang.ac.kr/rs";
            $result = "";
            $ext = "json";
            $status = false;
            $code = $this->token($request->code);

//            return dd($request->suupYear);

            $user = Auth::user();
            if ($user) {
                if ($user->authority == 3) {
                    $access_token = null;
                    if($request->code){
                        $access_token = $code->access_token;
                        Session::put(['access_token' => $code->access_token]);
                    }

                    $this->dataAPI($request, $access_token);
                    return redirect()->route('admin.apiButton');
                }
                else {
                    return dd('에러');
                    return redirect()->back();
                }
            }


            $access_token = $code->access_token;

            $user = json_decode($this->api($access_token, "GET", $apiBaseUrl, "/user/loginList", null, $ext))->response->list[0];


//            $hyEmail = $user->loginId.'@hanyang.ac.kr';
            $userExist = User::where('uuid', $user->uuid)->where('social', 'hanyang')->first();

            if (!$userExist) {
                $userInfo = [
                    'name' => $user->userNm,
                    'email' => $user->gaeinNo,
                    'password' => Hash::make($user->uuid),
                    'contact' => null,
                    'code' => null,
//                    'authority' => $user->userGb == '0010' ? 2 : 1,
                    'authority' => $user->userGb ==  '0010' ? 2 : ( $user->userGb == '0020' ? 2 : 1),
                    'social' => 'hanyang',
                    'uuid' => $user->uuid,

                    'jikwiGb'=>$user->jikwiGb,
                    'jaejikYn'=>$user->jaejikYn,
                    'daepyoUserGbYn'=>$user->daepyoUserGbYn,
                    'daehakNm'=>$user->daehakNm,
                    'jikjongGb'=>$user->jikjongGb,
                    'gaeinNo'=>$user->gaeinNo,
                    'sinbunGbNm'=>$user->sinbunGbNm,
                    'sosokNm'=>$user->sosokNm,
                    'iphakYear'=>$user->iphakYear,
                    'userNm'=>$user->userNm,
                    'sinbunGb'=>$user->sinbunGb,
                    'userGb'=>$user->userGb,
                    'sinbunGbEnm'=>$user->sinbunGbEnm,
                    'sosokCd'=>$user->sosokCd,
                    'sosokEnm'=>$user->sosokEnm,
                    'userGbNm'=>$user->userGbNm,
                    'sosokId'=>$user->sosokId,
                ];

                $newUser = User::create($userInfo);

                if (in_array($newUser->authority, [1, 2])) {
                    $myPage = MyPage::where('userId', $newUser->id)->first();
                    if (!$myPage) {
                        MyPage::create([
                            'userId' => $newUser->id,
                        ]);
                    }
                }

                ClassList::where('hakbun', $newUser->gaeinNo)->update(['userId' => $newUser->id]);

                Auth::login($newUser);
                return redirect()->route('home');
            } else if ($userExist['uuid'] != $user->uuid) {
                // If social account and registered account is not matched each other
                return dd('앗몰랑');
            } else {
                // Already exist user
                Auth::login($userExist);

                return redirect()->route('dash');
            }
        } catch (\Exception $e) {
            return dd($e);
            return redirect()->route('home');
        }
    }

    function dataAPI(Request $request, $access_token = null){

        if(Session::get('access_token') || !is_null($access_token)){
            $validator = Validator::make($request->all(), [
                'suupYear' => ['required', 'string'],
                'suupTerm' => ['required', 'string'],
            ]);

            if ($validator->fails()) {
//                모달 
//                return redirect()->back()->withErrors($validator->errors())->withInput($request->input());
            }

            $apiBaseUrl = "https://api.hanyang.ac.kr/rs";
            $access_token = Session::get('access_token');
            $suupYear = $request->suupYear ? $request->suupYear : Session::get('suupYear');
            $suupTerm = $request->suupTerm ? $request->suupTerm : Session::get('suupTerm');
            $ext = "json";

//            return dd($request->all());

//            return dd(json_decode($this->api($access_token, "GET", $apiBaseUrl, "/haksa/findPBLSuupList",
//                ['suupYear'=>$suupYear, 'suupTerm'=>$suupTerm]
//                , $ext)));

            $suupLists = json_decode($this->api($access_token, "GET", $apiBaseUrl, "/haksa/findPBLSuupList",
                ['suupYear'=>$suupYear, 'suupTerm'=>$suupTerm]
                , $ext))->response->list;

            $suupLists_ = [];
            foreach ($suupLists as $suupList) {
                $suupLists_[] =[
                    'suupTermNm' => $suupList->suupTermNm,
                    'pyegangYn' => $suupList->pyegangYn,
                    'suupTimes' => '2021-01-01',
                    'sincheongInwon' => $suupList->sincheongInwon,
                    'hakjeom' => $suupList->hakjeom,
                    'gnjHakgwaNm' => $suupList->gnjHakgwaNm,
                    'isuGrade' => $suupList->isuGrade,
                    'suupYear' => $suupList->suupYear,
                    'suupTerm' => $suupList->suupTerm,
                    'daepyoGangsaNm' => $suupList->daepyoGangsaNm,
                    'gnjSosokNm' => $suupList->gnjSosokNm,
                    'daepyoGangsaHp' => $suupList->daepyoGangsaHp,
                    'daepyoGangsaNo' => $suupList->daepyoGangsaNo,
                    'gwamokNm' => $suupList->gwamokNm,
                    'daepyoGangsaHakgwa' => $suupList->daepyoGangsaHakgwa,
                    'suupTypeGb' => $suupList->suupTypeGb,
                    'onlineGb' => $suupList->onlineGb,
                    'haksuNo' => $suupList->haksuNo,
                    'daepyoGangsaDaehak' => $suupList->daepyoGangsaDaehak,
                    'gnjDaehakNm' => $suupList->gnjDaehakNm,
                    'gyogangsa' => $suupList->gyogangsa,
                    'teuksuSuupInfo' => $suupList->teuksuSuupInfo,
                    'isuGbNm' => $suupList->isuGbNm,
                    'suupNo' => $suupList->suupNo,
                    'daepyoGangsaEmail' => $suupList->daepyoGangsaEmail,
                    'ganguiRoom' => $suupList->ganguiRoom,
                    'daepyoGangsaJikjong' => $suupList->daepyoGangsaJikjong,
                    'campusCd' => $suupList->campusCd,
                ];
            }

            ClassObject::upsert($suupLists_, ['suupNo'], [
                'suupTermNm',
                'pyegangYn',
                'suupTimes',
                'sincheongInwon',
                'hakjeom',
                'gnjHakgwaNm',
                'isuGrade',
                'suupYear',
                'suupTerm',
                'daepyoGangsaNm',
                'gnjSosokNm',
                'daepyoGangsaHp',
                'daepyoGangsaNo',
                'gwamokNm',
                'daepyoGangsaHakgwa',
                'suupTypeGb',
                'onlineGb',
                'haksuNo',
                'daepyoGangsaDaehak',
                'gnjDaehakNm',
                'gyogangsa',
                'teuksuSuupInfo',
                'isuGbNm',
                'daepyoGangsaEmail',
                'ganguiRoom',
                'daepyoGangsaJikjong',
                'campusCd']);

            $myClassObjects = ClassObject::where('suupYear',$suupYear)->where('suupTerm',$suupTerm)->get();
            
            $Users = User::where('authority', 1)->get();

            foreach ($myClassObjects as $suupList) {
                $studentsLists = json_decode($this->api($access_token, "GET", $apiBaseUrl, "/haksa/findPBLStudentsList",
                    ['suupYear'=>$suupYear, 'suupTerm'=>$suupTerm, 'suupNo' => $suupList->suupNo]
                    , $ext))->response->list;

                $studentsLists_ = [];
                foreach ($studentsLists as $key => $studentsList) {
                    $userHakbun = $Users->where('gaeinNo', $studentsList->hakbun)->first();

                    $studentsLists_[] =[
                        'userId' => $userHakbun ? $userHakbun->id : null,
                        'classObjectId' => $suupList->id,
                        'name' => $studentsList->name,
                        'suupTermNm' => $studentsList->suupTermNm,
                        'daehakNm' => $studentsList->daehakNm,
                        'gwamokNm' => $studentsList->gwamokNm,
                        'hakgwaNm' => $studentsList->hakgwaNm,
                        'haksuNo' => $studentsList->haksuNo,
                        'jeonggonggbnm' => $studentsList->jeonggonggbnm,
                        'suupNo' => $studentsList->suupNo,
                        'hpNo' => $studentsList->hpNo,
                        'sosokCd' => $studentsList->sosokCd,
                        'email' => $studentsList->email,
                        'hakbun' => $studentsList->hakbun,
                        'suupYear' => $studentsList->suupYear,
                        'suupTerm' => $studentsList->suupTerm,
                        'grade' => $studentsList->grade,
                        'campusCd' => $studentsList->campusCd,
                    ];
                }

                ClassList::upsert($studentsLists_, ['suupNo', 'hakbun'], [
                    'userId',
                    'classObjectId',
                    'name',
                    'suupTermNm',
                    'daehakNm',
                    'gwamokNm',
                    'hakgwaNm',
                    'haksuNo',
                    'jeonggonggbnm',
                    'hpNo',
                    'sosokCd',
                    'email',
                    'suupYear',
                    'suupTerm',
                    'grade',
                    'campusCd',
                ]);
            }

            if(Session::get('access_token')){
                return redirect()->route('admin.apiButton');
            }
            return true;

            return redirect()->back();
        }
        else{
            //리다이렉트 한양대 로그인으로
            Session::put(['suupYear' => $request->suupYear, 'suupTerm' => $request->suupTerm]);
            return redirect()->route('auth.hy.login');
        }
    }

    function token($code) {
        $data = array('client_id'=>env('HY_CLIENT_ID', null), 'client_secret'=>env('HY_CLIENT_SECRET', null)
        , 'code'=>$code, 'scope'=>35
        , 'redirect_uri'=>env('HY_REDIRECT_URL', null), 'grant_type'=>'authorization_code');
        $client = new HttpClient(env('HY_OAUTH_URL', null));
        $client->get('oauth/token', $data);
        $result = json_decode($client->getContent());

        return $result;
    }

    function expireToken($accessToken) {
        $client_id = env('HY_CLIENT_ID', null);
        $data = array('client_id'=>$client_id, 'token'=>$accessToken);
        $oauth_url = env('HY_OAUTH_URL', null);
        $client = new HttpClient($oauth_url);
        $client->get('oauth/expireToken', $data);
        $cont = json_decode($client->getContent());
        $result = $cont->{'app'}->{'result'};

        if (strcasecmp($result, "success") == 0) return true;
        else return false;
    }

    function paramEncKey($swapKey) {
        $data = array('client_id'=>env('HY_CLIENT_ID', null), 'swap_key'=>$swapKey);
        $client = new HttpClient(env('HY_OAUTH_URL', null));
        $client->post('oauth/get_param_enc_key', $data);
        $result = json_decode($client->getContent());
        // $GLOBALS['key'] = $result->{'body'}->{'key'};
        // $GLOBALS['iv'] = $result->{'body'}->{'iv'};

        $GLOBALS['key'] = $result->body->key;
        $GLOBALS['iv'] = $result->body->iv;
    }

    function api($accessToken, $method, $baseUrl, $subUrl, $param, $responseExt) {
        // $swapKey = getMillis();
        $swapKey = strval($this->getMillis());
        $encodedString = "";
        if (strcasecmp($method, "GET") == 0 || strcasecmp($method, "DELETE") == 0) {
            $keyAndVal = "";
            if (is_null($param) == false) {
//                $jsonArray = json_decode($param);

                foreach($param as $key => $value) {
                    $keyAndVal = $keyAndVal."&".$key."=".urlencode($value);
                }
                $keyAndVal = substr($keyAndVal, 1);
            }
            $this->paramEncKey($swapKey);
            $encodedString = $this->encode($GLOBALS['key'], $GLOBALS['iv'], $keyAndVal);
        }

        $realSubURL = $subUrl.".".$responseExt;
        if (strcasecmp($method, "GET") == 0) {
            $client = new HttpClient($baseUrl);
            $data = "?enc=".$encodedString;
            $headers = array ('Content-Type'=>'application/json','client_id'=>env('HY_CLIENT_ID', null), 'access_token'=>$accessToken, 'swap_key'=>$swapKey);
            $realSubURL = $realSubURL."".$data;
            $result = $client->get($realSubURL, null, $headers);

            return $client->getContent();
        }
        elseif (strcasecmp($method, "POST") == 0) {
            $client = new HttpClient($baseUrl);
            $headers = array ('Content-Type'=>'application/json','client_id'=>env('HY_CLIENT_ID', null), 'access_token'=>$accessToken, 'swap_key'=>$swapKey);
            $result = $client->post($realSubURL, $param, $headers);
            return $client->getContent();
        }
        elseif (strcasecmp($method, "DELETE") == 0) {
            $client = new HttpClient($baseUrl);
            $data = "?enc=".$encodedString;
            $headers = array ('Content-Type'=>'application/json','client_id'=>env('HY_CLIENT_ID', null), 'access_token'=>$accessToken, 'swap_key'=>$swapKey);
            $result = $client->delete($realSubURL, $data, $headers);
            return $client->getContent();
        }
        elseif (strcasecmp($method, "PUT") == 0) {
            $client = new HttpClient($baseUrl);
            $headers = array ('Content-Type'=>'application/json','client_id'=>env('HY_CLIENT_ID', null), 'access_token'=>$accessToken, 'swap_key'=>$swapKey);
            $result = $client->put($realSubURL, $param, $headers);
            return $client->getContent();
        }

    }

    function encode($paramKey, $paramIv, $paramInput) {
//        $AES = new AES_Encryption($paramKey, $paramIv, 'PKCS7', 'cbc');
//        $result = $AES->encrypt($paramInput);

//        $result = $this->strToHex($result);
//        return $result;
        $result = openssl_encrypt($this->pkcs7_pad($paramInput, 16), 'AES-256-CBC', $paramKey, OPENSSL_NO_PADDING, $paramIv);

        return $this->strToHex($result);
    }

    function pkcs7_pad($data, $size) {
        $length = $size - strlen($data) % $size;
        return $data . str_repeat(chr($length), $length);
    }

    function strToHex($string){
        $hex = '';
        for ($i=0; $i<strlen($string); $i++){
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0'.$hexCode, -2);
        }
        return strToUpper($hex);
    }



    function getMillis(){
        list($usec, $sec) = explode(' ', microtime());
        return abs((int) ((int) $sec * 1000 + ((float) $usec * 1000)));
    }

}
