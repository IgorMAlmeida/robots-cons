<?php

namespace App\Services;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Services\Curl;

class GovConsultService extends Curl{

    private string $userPortal;
    private string $passPortal;
    private string $anticaptchakey;
    private string $portalConsignadoBase;
    private string $portalConsignadoAdm;
       
    public  function __construct()
    {
        $this->userPortal = env('USER_PORTAL');
        $this->passPortal = env('PASS_PORTAL');
        $this->anticaptchakey = env('ANTICAPTCHA_KEY');
        $this->portalConsignadoBase = env('URL_PORTAL_CONSIGNADO_BASE');
        $this->portalConsignadoAdm = env('URL_PORTAL_CONSIGNADO_ADMINISTRATIVO');

    }

   public function Consult($values):array
   {
        try{
            $location = "Referer: ". $values["pageContent"]["location"];
            $ajaxBase = "Wicket-Ajax-BaseURL: ". basename($values["pageContent"]["location"])."";

            $secToken = 'SECURITYTOKEN:'.$values['token'];
            $headers = [
                'Accept: application/xml, text/xml, */*; q=0.01',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Origin: https://www.portaldoconsignado.com.br',
                'Pragma: no-cache',
                $location,
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'Wicket-Ajax: true',
                $ajaxBase,
                'Wicket-FocusedElementId: id14',
                'X-Requested-With: XMLHttpRequest, CSRF Prevention',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
                $secToken,
            ];

            $followUrl = $values["pageContent"]["ajaxResponse"]["u"];
            $followUrl = str_replace('./', '/', $followUrl);
            $method = $values["pageContent"]["ajaxResponse"]["m"];
            $formButton = $values["pageContent"]["ajaxResponse"]["f"]."_hd_0";

            $formData = [
                $formButton    => "",
                "radioGroup"    => "radio2",
                "SECURITYTOKEN" => $values['token'],
                "acessar"       => "true",
            ];
            $formData = http_build_query($formData);

            $url = $this->portalConsignadoBase.$followUrl;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, -1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $values['cookieFile']);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $values['cookieFile']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            $response = curl_exec($ch);
            curl_close($ch);


            $getPageContent = (new GetPageContent())->getContent($response);
            $cookie = explode(';', $getPageContent["cookies"]);
            $cookie = $cookie[0];

            $redirectUrl = $getPageContent["ajaxLocation"];

            $location = "Referer: ". $redirectUrl;
            $headers = [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                $location,
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: same-origin',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/autenticado');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: same-origin',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            $response = curl_exec($ch);
            $referUrl = "Referer: ".curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);
            ///////////////////////////////////////////// ATÉ AQUI TA FUNCIONANDO CORRETAMENTE /////////////////////////////////////////////

            // var_dump($response);exit;
            /////// REQUISITAR TOKENS ////////////




            $token = $values['token'];
            $headerToken = "SECURITYTOKEN:". $token;


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/csrfTokenS');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: */*',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                'Referer: https://www.portaldoconsignado.com.br/consignatario/autenticado?5',
                'Sec-Fetch-Dest: script',
                'Sec-Fetch-Mode: no-cors',
                'Sec-Fetch-Site: same-origin',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            
            $response = curl_exec($ch);
            
            curl_close($ch);
    
            $response = curl_exec($ch);

            if (preg_match('/\("SECURITYTOKEN",\s*"([^"]+)"\);/', $response, $matches)) {
                $tokenBusca = $matches[1];
                // echo "Token: " . $token;
            }            
            
            // curl_close($ch);
            // var_dump($response);
            // exit;


            /////// FIM REQUISITAR TOKENS ////////////
            // $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Pragma: no-cache',
                // 'Referer: https://www.portaldoconsignado.com.br/consignatario/autenticado?5',
                $referUrl,
                'Sec-Fetch-Dest: document',
                'Sec-Fetch-Mode: navigate',
                'Sec-Fetch-Site: same-origin',
                'Sec-Fetch-User: ?1',
                'Upgrade-Insecure-Requests: 1',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            $response = curl_exec($ch);
            $referUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            curl_close($ch);

            // var_dump($response);
            // exit;

            // $token = (new TokenService())->getToken(["response"=>$response]);

            // var_dump($token);
            // exit;
            // echo "teste";exit;
            $getPageContent = (new GetPageContent())->getContent($response);
            // echo "teste";exit;

            // var_dump($getPageContent);exit;
            $followUrl = $getPageContent["ajaxResponse"]["u"];
            $followUrl = str_replace('./', '/', $followUrl);
            $method = $getPageContent["ajaxResponse"]["m"];
            $formButton = $getPageContent["ajaxResponse"]["f"]."_hd_0";
            $referUrl = "Referer: $referUrl";
            
            
            // var_dump($referUrl);
            // var_dump($followUrl);
            // var_dump($method);
            // var_dump($formButton);
            // exit;
            // echo($tokenBusca)exit;
            $headerToken = "SECURITYTOKEN: $tokenBusca";
            $formData = [
                $formButton => "",
                "cpfServidor" => "100.619.708-75",
                "matriculaServidor" => "5866005",
                "selectOrgao" => "",
                "selectProduto" => "",
                "selectEspecie" => "",
                "SECURITYTOKEN" => $tokenBusca,
                "botaoPesquisar" => "1",
            ];
            $formData = http_build_query($formData);
            // var_dump($formData);exit;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/'.$followUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/xml, text/xml, */*; q=0.01',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Origin: https://www.portaldoconsignado.com.br',
                'Pragma: no-cache',
                $referUrl,
                $headerToken,
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'Wicket-Ajax: true',
                'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?7',
                'Wicket-FocusedElementId: id23',
                'X-Requested-With: XMLHttpRequest, CSRF Prevention',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 'id22_hf_0=&cpfServidor=100.619.708-75&matriculaServidor=5866005&selectOrgao=&selectProduto=&selectEspecie=&SECURITYTOKEN=A0G7-IEJ7-N2LQ-4H29-JM52-RWRL-J0M6-BZ4Q-TIXN-G3L0-ANR3-D523-67TR-UK9E-HIX5-HUOS&botaoPesquisar=1');
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            $response = curl_exec($ch);

            var_dump($response);exit;

            curl_close($ch);


            $formData = [
                $formButton => "",
                "cpfServidor" => "100.619.708-75",
                "matriculaServidor" => "5866005",
                "selectOrgao" => "",
                "selectProduto" => "",
                "selectEspecie" => "",
                "SECURITYTOKEN" => $token,
                "botaoPesquisar" => "1",
            ];
            // var_dump($formData);exit;
            $formData = http_build_query($formData);
        

            $url =  "https://www.portaldoconsignado.com.br/consignatario".$followUrl;
            $referer = "Referer: https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?7";
            $cookieHeader = "Cookie: ". $cookie;
            // var_dump($referer);exit;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_MAXREDIRS, -1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/xml, text/xml, */*; q=0.01',
                'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                'Cache-Control: no-cache',
                'Connection: keep-alive',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Origin: https://www.portaldoconsignado.com.br',
                'Pragma: no-cache',
                // 'Referer: https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6',
                $referer,
                $headerToken,
                // $cookieHeader,
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
                'Wicket-Ajax: true',
                'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?7',
                'Wicket-FocusedElementId: id23',
                'X-Requested-With: XMLHttpRequest, CSRF Prevention',
                'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
                'sec-ch-ua-mobile: ?0',
                'sec-ch-ua-platform: "Linux"',
            ]);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, 'id24_hf_0=&cpfServidor=100.619.708-75&matriculaServidor=5866005&selectOrgao=&selectProduto=&selectEspecie=&SECURITYTOKEN=L19R-6CLQ-5YZ6-5HK8-VE7X-XACT-3ERG-164O-CNZB-D4N4-O848-UTCL-D9PD-38DV-HIU1-QQ26&botaoPesquisar=1');

            $response = curl_exec($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);
            // var_dump($info);
            var_dump($response);

            exit;

            // if (preg_match('/<redirect><!\[CDATA\[(.*?)\]\]><\/redirect>/', $response, $matches)) {
            //     $redirectUrl = $matches[1];
            
            //     var_dump($redirectUrl);exit;
            //     // Executar uma nova requisição cURL para a URL de redirecionamento
            
            //     // Processar a resposta da URL de redirecionamento
            //     var_dump($response);
            // }


            // var_dump($response);
            echo "finalizou";
            exit;

// $ch = curl_init('https://www.portaldoconsignado.com.br/selecaoPerfil?2-1.IBehaviorListener.0-form-acessar');
// // curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/selecaoPerfil?2-1.IBehaviorListener.0-form-acessar');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'Accept: application/xml, text/xml, */*; q=0.01',
//     'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
//     'Cache-Control: no-cache',
//     'Connection: keep-alive',
//     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//     'Origin: https://www.portaldoconsignado.com.br',
//     'Pragma: no-cache',
//     'Referer: https://www.portaldoconsignado.com.br/selecaoPerfil?2',
//     'SECURITYTOKEN: 42DV-JQKE-80VN-QB7Y-MO4K-XWP0-7BEE-320U-Z217-PPHN-459D-OYVF-4A87-S638-TUJ2-8R49',
//     'Sec-Fetch-Dest: empty',
//     'Sec-Fetch-Mode: cors',
//     'Sec-Fetch-Site: same-origin',
//     'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
//     'Wicket-Ajax: true',
//     'Wicket-Ajax-BaseURL: selecaoPerfil?2',
//     'Wicket-FocusedElementId: id14',
//     'X-Requested-With: XMLHttpRequest, CSRF Prevention',
//     'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
//     'sec-ch-ua-mobile: ?0',
//     'sec-ch-ua-platform: "Linux"',
// ]);
// curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000ONfO5nrMeU4c9xCRckhiU6R:18guqmp5m');
// curl_setopt($ch, CURLOPT_POSTFIELDS, 'id13_hf_0=&radioGroup=radio2&SECURITYTOKEN=42DV-JQKE-80VN-QB7Y-MO4K-XWP0-7BEE-320U-Z217-PPHN-459D-OYVF-4A87-S638-TUJ2-8R49&acessar=1');
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
// curl_setopt($ch, CURLOPT_MAXREDIRS, 100);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

// $response = curl_exec($ch);

// curl_close($ch);


// var_dump($response);


//             exit;
           

// echo "CONSULTA";
//             // var_dump($values);
//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/selecaoPerfil?3-1.IBehaviorListener.0-form-acessar');
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
//             curl_setopt($ch, CURLOPT_HTTPHEADER, [
//                 'Accept: application/xml, text/xml, */*; q=0.01',
//                 'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
//                 'Cache-Control: no-cache',
//                 'Connection: keep-alive',
//                 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//                 'Origin: https://www.portaldoconsignado.com.br',
//                 'Pragma: no-cache',
//                 'Referer: https://www.portaldoconsignado.com.br/selecaoPerfil?3',
//                 'SECURITYTOKEN: 20YE-28SG-493O-YPOR-VQMO-RZ2V-U58X-UNSH-G52G-1HNT-REAL-0C0Q-72Z1-Q0G3-MI4Q-S7SS',
//                 'Sec-Fetch-Dest: empty',
//                 'Sec-Fetch-Mode: cors',
//                 'Sec-Fetch-Site: same-origin',
//                 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
//                 'Wicket-Ajax: true',
//                 'Wicket-Ajax-BaseURL: selecaoPerfil?3',
//                 'Wicket-FocusedElementId: id16',
//                 'X-Requested-With: XMLHttpRequest, CSRF Prevention',
//                 'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
//                 'sec-ch-ua-mobile: ?0',
//                 'sec-ch-ua-platform: "Linux"',
//             ]);
//             $cookiePath = getcwd() . '/Cookies';

//             // Nome do arquivo de cookies com base na data e hora atuais
//             $cookieFile = $cookiePath . '/cookie2_' . date('Y_m_d_H_i_s');
            
//             // Configuração do curl
//             curl_setopt($ch, CURLOPT_COOKIE, $values['cookie']);
//             curl_setopt($ch, CURLOPT_COOKIEFILE, $values['cookieFile']); // Arquivo de onde os cookies são lidos
//             curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);  // Novo arquivo onde os cookies serão salvos
//             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecionamentos
//             curl_setopt($ch, CURLOPT_MAXREDIRS, 100); // Número máximo de redirecionamentos
//             curl_setopt($ch, CURLOPT_POSTFIELDS, 'id15_hf_0=&radioGroup=radio2&SECURITYTOKEN=20YE-28SG-493O-YPOR-VQMO-RZ2V-U58X-UNSH-G52G-1HNT-REAL-0C0Q-72Z1-Q0G3-MI4Q-S7SS&acessar=1');
            
//             // Execução da solicitação curl
//             $response = curl_exec($ch);
//             curl_close($ch);
            
//             // sleep(5);
//             // var_dump($cookieFile);
//             // // Ler o conteúdo do novo arquivo de cookies
//             // // $cookieContent = file_get_contents($cookieFile);
//             // $cookieContent = file_get_contents($values['cookieFile']);

//             // // Usar expressão regular para encontrar o valor do JSESSIONID
//             // if (preg_match('/JSESSIONID\s+([^\s]+)/', $cookieContent, $matches)) {
//             //     $sessionValue = $matches[1];
//             //     $cookie = "JSESSIONID={$sessionValue}";
//             // }

//             // echo "NOVOS VALORES PT 2";

//             // var_dump($cookie);
//             echo "TELA";
// // exit;
//             var_dump($response);


// // exit;

// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6-1.IBehaviorListener.0-form-botaoPesquisar=null');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
//     'sec-ch-ua-mobile: ?0',
//     'Wicket-FocusedElementId: id23',
//     'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
//     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//     'Accept: application/xml, text/xml, */*; q=0.01',
//     'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?6',
//     'X-Requested-With: XMLHttpRequest, CSRF Prevention',
//     'Wicket-Ajax: true',
//     'SECURITYTOKEN: HDRB-59FE-36Z3-O969-D8P2-FSP7-3T0K-58XR-ABCU-PI35-MB4R-F8K5-P785-H5CX-90QH-M6GY',
//     'sec-ch-ua-platform: "Linux"',
//     'Sec-Fetch-Site: same-origin',
//     'Sec-Fetch-Mode: cors',
//     'Sec-Fetch-Dest: empty',
//     'host: www.portaldoconsignado.com.br',
// ]);
// // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Seguir redirecionamentos

// // curl_setopt($ch, CURLOPT_MAXREDIRS, 100); // Número máximo de redirecionamentos

// curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000Xo22RqOpEv7UFZrvmvx9ZlG:18guqmoe6');

// curl_setopt($ch, CURLOPT_POSTFIELDS, 'SECURITYTOKEN=HDRB-59FE-36Z3-O969-D8P2-FSP7-3T0K-58XR-ABCU-PI35-MB4R-F8K5-P785-H5CX-90QH-M6GY&botaoPesquisar=1&cpfServidor=100.619.708-75&id22_hf_0=&matriculaServidor=5866005&selectEspecie=&selectOrgao=&selectProduto=');
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// $response = curl_exec($ch);

// curl_close($ch);
// $response = curl_exec($ch);

// curl_close($ch);

//             var_dump($response);
// exit;




//             $urlCounter = 0;
//             $urlCounter++;
            // echo "EADSSDAD";
//             var_dump($values);exit;

//             $ch = curl_init();
//             curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/autenticado?5');
//             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//             curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
//             curl_setopt($ch, CURLOPT_HTTPHEADER, [
//                 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
//                 'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
//                 'Cache-Control: no-cache',
//                 'Connection: keep-alive',
//                 'Pragma: no-cache',
//                 'Referer: https://www.portaldoconsignado.com.br/wicket/page?3',
//                 'Sec-Fetch-Dest: document',
//                 'Sec-Fetch-Mode: navigate',
//                 'Sec-Fetch-Site: same-origin',
//                 'Upgrade-Insecure-Requests: 1',
//                 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
//                 'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
//                 'sec-ch-ua-mobile: ?0',
//                 'sec-ch-ua-platform: "Linux"',
//             ]);
//             curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000_botsbETAKo3XcYXssKQIv9:18guqmoe6');
//             // JSESSIONID=00009qh2NWS-UKq85hIBFmdqpcz:18guqmoe6
//             // JSESSIONID=00009qh2NWS-UKq85hIBFmdqpcz:18guqmoe6
//             // JSESSIONID=0000Re-c0E7IyrHu_N1FKbycWbL:18guqmoe6
//             // JSESSIONID=0000Re-c0E7IyrHu_N1FKbycWbL:18guqmoe6
//             // JSESSIONID=0000Re-c0E7IyrHu_N1FKbycWbL:18guqmoe6

//             // curl_setopt($ch, CURLOPT_COOKIE, $values['cookie']);
//             // curl_setopt($ch, CURLOPT_COOKIEFILE, $values['cookieFile']);
//             // curl_setopt($ch, CURLOPT_COOKIEJAR, $values['cookieFile']);
//             // $cookie = (new CookieService())->getCookie();

//             $response = curl_exec($ch);

//             curl_close($ch);
//             // $response = $this->get($params);

//             var_dump($response);

// var_dump($values);
// exit;
//             $values['token'] = "GUD6-YRNC-7YZP-HUVE-CRT0-4WKO-QKBW-4L8X-Z4VY-G040-W9LG-KB93-GG4M-X6X5-AHOE-60HZ";
//             $formData = [
//                 "id22_hf_0" => "",
//                 "cpfServidor" => "100.619.708-75",
//                 "matriculaServidor" => "5866005",
//                 "selectOrgao" => "",
//                 "selectProduto" => "",
//                 "selectEspecie" => "",
//                 "SECURITYTOKEN" => $values['token'],
//                 "botaoPesquisar" => "1",
//             ];

//             // var_dump($loginData);
//             $formData = http_build_query($formData);
//             // var_dump($values['cookieFile']);
// // exit;
//             $params = [
//                 "url"            => $this->portalConsignadoBase."/consignatario/pesquisarMargem?6-1.IBehaviorListener.0-form-botaoPesquisar",
//                 "formDataString" => $formData,
//                 "cookies"        => "JSESSIONID=0000dTnQ4JrCvlc1bKvdCJW5kdW:18guqmp5m",
//                 "cookieFile"     => $values['cookieFile'],
//                 "method"         => "POST",
//                 "followLocation" => true,
//                 "headers"        => [
//                     'Accept: application/xml, text/xml, */*; q=0.01',
//                     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//                     'SECURITYTOKEN:'.$values['token'],
//                     'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?6',
//                 ],
//             ];
//             var_dump($params);

//             $response = $this->get($params);



//TODOS OS ITENS AQUI SAO NECESSÁRIOS PRA CONSULTA DE MARGEM
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6-1.IBehaviorListener.0-form-botaoPesquisar');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'Accept: application/xml, text/xml, */*; q=0.01',
//     'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
//     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
//     'Origin: https://www.portaldoconsignado.com.br',
//     'Referer: https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6',
//     'SECURITYTOKEN: W1CR-MR1M-8Y33-07HG-6J1G-2CGS-YOEX-R1UV-HVER-Z1HV-XW5I-MEHF-SOO2-A1G7-LVER-8QJS',
//     'Wicket-Ajax: true',
//     'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?6',
//     'Wicket-FocusedElementId: id23',
//     'X-Requested-With: XMLHttpRequest, CSRF Prevention',
// ]);
// curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000zonepxzTtLW_5ID4vW6V7K6:18guqmoe6');
// curl_setopt($ch, CURLOPT_POSTFIELDS, 'id22_hf_0=&cpfServidor=100.619.708-75&matriculaServidor=5866005&selectOrgao=&selectProduto=&selectEspecie=&SECURITYTOKEN=W1CR-MR1M-8Y33-07HG-6J1G-2CGS-YOEX-R1UV-HVER-Z1HV-XW5I-MEHF-SOO2-A1G7-LVER-8QJS&botaoPesquisar=1');

// $response = curl_exec($ch);

// curl_close($ch);

// var_dump($values);exit;
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, 'https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6-1.IBehaviorListener.0-form-botaoPesquisar');
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            // curl_setopt($ch, CURLOPT_HTTPHEADER, [
            //     'Accept: application/xml, text/xml, */*; q=0.01',
            //     'Accept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
            //     'Cache-Control: no-cache',
            //     'Connection: keep-alive',
            //     'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            //     'Origin: https://www.portaldoconsignado.com.br',
            //     'Pragma: no-cache',
            //     'Referer: https://www.portaldoconsignado.com.br/consignatario/pesquisarMargem?6',
            //     'SECURITYTOKEN: RHOM-XFEG-Q8OU-HI8M-CVZ5-LB8N-PH09-TBA1-GIA1-1NGM-O7X8-BGQ5-R21M-APND-NCTO-HXTD',
            //     'Sec-Fetch-Dest: empty',
            //     'Sec-Fetch-Mode: cors',
            //     'Sec-Fetch-Site: same-origin',
            //     'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 Edg/124.0.0.0',
            //     'Wicket-Ajax: true',
            //     'Wicket-Ajax-BaseURL: consignatario/pesquisarMargem?6',
            //     'Wicket-FocusedElementId: id23',
            //     'X-Requested-With: XMLHttpRequest, CSRF Prevention',
            //     'sec-ch-ua: "Chromium";v="124", "Microsoft Edge";v="124", "Not-A.Brand";v="99"',
            //     'sec-ch-ua-mobile: ?0',
            //     'sec-ch-ua-platform: "Linux"',
            // ]);
            // // curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000Ows0wkrsSqP1Z7h8ueq6gZ7:18guqmp5m');
            // curl_setopt($ch, CURLOPT_COOKIE, 'JSESSIONID=0000_botsbETAKo3XcYXssKQIv9:18guqmoe6');
            // // curl_setopt($ch, CURLOPT_COOKIE, $values['cookie']);

            // curl_setopt($ch, CURLOPT_POSTFIELDS, 'id22_hf_0=&cpfServidor=100.619.708-75&matriculaServidor=5866005&selectOrgao=&selectProduto=&selectEspecie=&SECURITYTOKEN=RHOM-XFEG-Q8OU-HI8M-CVZ5-LB8N-PH09-TBA1-GIA1-1NGM-O7X8-BGQ5-R21M-APND-NCTO-HXTD&botaoPesquisar=1');

            // $response = curl_exec($ch);

            // curl_close($ch);

            // var_dump($response);
            // exit;

            return [
                "erro"       =>  false,
                "response"   =>  $response['response'],
            ];

        }catch (\Exception $e){
                return [
                "erro"     =>  true,
                "response" =>  $e->getMessage()
            ];
        }
    }
}