<?php
// NOVA VERSAO - 17/07/2023
include_once __DIR__ . "/../config.php";

// helio 26012023 18:10
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function retornaheader($code)
{
    if (isset($code)) {
        switch ($code) {
            case 200:
                $text = 'OK';
                break;
            case 201:
                $text = 'Created';
                break;
            case 202:
                $text = 'Accepted';
                break;
            case 203:
                $text = 'Non-Authoritative Information';
                break;
            case 204:
                $text = 'No Content';
                break;
            case 205:
                $text = 'Reset Content';
                break;
            case 206:
                $text = 'Partial Content';
                break;
            case 300:
                $text = 'Multiple Choices';
                break;
            case 301:
                $text = 'Moved Permanently';
                break;
            case 302:
                $text = 'Moved Temporarily';
                break;
            case 303:
                $text = 'See Other';
                break;
            case 304:
                $text = 'Not Modified';
                break;
            case 305:
                $text = 'Use Proxy';
                break;
            case 400:
                $text = 'Bad Request';
                break;
            case 401:
                $text = 'Unauthorized';
                break;
            case 402:
                $text = 'Payment Required';
                break;
            case 403:
                $text = 'Forbidden';
                break;
            case 404:
                $text = 'Not Found';
                break;
            case 405:
                $text = 'Method Not Allowed';
                break;
            case 406:
                $text = 'Not Acceptable';
                break;
            case 407:
                $text = 'Proxy Authentication Required';
                break;
            case 408:
                $text = 'Request Time-out';
                break;
            case 409:
                $text = 'Conflict';
                break;
            case 410:
                $text = 'Gone';
                break;
            case 411:
                $text = 'Length Required';
                break;
            case 412:
                $text = 'Precondition Failed';
                break;
            case 413:
                $text = 'Request Entity Too Large';
                break;
            case 414:
                $text = 'Request-URI Too Large';
                break;
            case 415:
                $text = 'Unsupported Media Type';
                break;
            case 500:
                $text = 'Internal Server Error';
                break;
            case 501:
                $text = 'Not Implemented';
                break;
            case 502:
                $text = 'Bad Gateway';
                break;
            case 503:
                $text = 'Service Unavailable';
                break;
            case 504:
                $text = 'Gateway Time-out';
                break;
            case 505:
                $text = 'HTTP Version not supported';
                break;
            default:
                exit('Unknown http status code "' . htmlentities($code) . '"');
                break;
        }

        $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
        header($protocol . ' ' . $code . ' ' . $text);
        $GLOBALS['http_response_code'] = $code;
    } else {
        $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
    }

    return $code;
}

function pegaparam($varname)
{
    $v = (isset($_GET[$varname])) ? $_GET[$varname] : ((isset($_POST[$varname])) ? $_POST[$varname] : '');
    //if(!$v) $v = $_SESSION[$varname];
    //else $_SESSION[$varname] = $v;
    return ($v);
}

//header
$aplicacao = null;
$funcao = null; //Param

$metodo = $_SERVER["REQUEST_METHOD"]; //POST, PUT, DELETE and GET
$uri = $_SERVER["REQUEST_URI"];
$data = null;

$versao = pegaparam("versao");
$log = pegaparam("log");


$uri_parse = parse_url($uri, PHP_URL_PATH);

$conteudoEntrada = file_get_contents('php://input');

$jsonEntrada = json_decode($conteudoEntrada, TRUE); // Transforma um texto formato json, numa array json

//$data = array($conteudoEntrada);
//parse_str($conteudoEntrada,$data);

$unsetCount = 10;
/**/
//TRATA A URI
$parametro = "";
$ex = explode("/", $uri_parse);
$inicioUrl = false;
$i = 0;
foreach ($ex as $value) {
    //echo $i . "-" . $ex[$i] . "\n";
    if ($value == "api" || $value == "apilebes" || $value == "apitsweb") {
        $inicioUrl = true;
        unset($ex[$i]);
    }
    if (substr($value, 0, 2) == 'V.') { // alterado para substring
        $versao = substr($value, 2, strlen($value));
        unset($ex[$i]);
    }

    if ($inicioUrl == false || $value == "") {
        unset($ex[$i]);
    }
    $i = $i + 1;
}
/*
for ($i = 0; $i < $unsetCount; $i++) {
    echo $i . "-" . $ex[$i] . "\n";
} 
*/
$ex = array_filter(array_values($ex));
//echo json_encode($ex);



if (isset($ex[0])) {
    $aplicacao = $ex[0];
}
if (isset($ex[1])) {
    $funcao = $ex[1];
    if (isset($ex[3])) {
        $funcao = $funcao . "/" . $ex[3];
    }
}
if (isset($ex[2])) {
    $parametro = $ex[2];
    if (isset($ex[4])) {
        $parametro = $parametro . "/" . $ex[4];
    }
}



/**/
$hml = false;
if ($_SERVER['SERVER_ADDR'] == "10.2.0.233" || $_SERVER['SERVER_ADDR'] == "10.2.0.44" ||
    $_SERVER['SERVER_ADDR'] == "10.145.0.60") {
    $hml = true;
}


/** 
$log_datahora_ini = date("dmYHis");
$acao="segurosH";  
$arqlog = "/home/tsplaces/tmp/apilog/apits_".date("dmY").".log";
$arquivo = fopen($arqlog,"a");
fwrite($arquivo,$log_datahora_ini."$acao"."-aplicacao->".json_encode($aplicacao)."\n");   
fwrite($arquivo,$log_datahora_ini."$acao"."-funcao->".json_encode($funcao)."\n");   
fwrite($arquivo,$log_datahora_ini."$acao"."-parametro->".json_encode($parametro)."\n");   
fwrite($arquivo,$log_datahora_ini."$acao"."-jsonEntrada->".json_encode($jsonEntrada)."\n");   
fwrite($arquivo,$log_datahora_ini."$acao"."-metodo->".json_encode($metodo)."\n");   
fclose($arquivo);
 **/



/* echo 'host='.$_SERVER['SERVER_ADDR']."\n";
echo "aplicacao=".$aplicacao."\n";
echo "versao=".$versao."\n";
echo "funcao=".$funcao."\n";
echo "parametro=".$parametro."\n";
echo "metodo=".$metodo."\n";
echo "log=".$log."\n";
echo "hml=".$hml."\n";
 */



switch ($aplicacao) {

    case "services":
        // NOVA VERSAO - MOVER app/servicos para servicos/app
        include __DIR__ . "/../services/app/versao.php";
        //include "app/servicos/versao.php";
        break;

    case "servicos":
        // NOVA VERSAO - MOVER app/servicos para servicos/app
        include __DIR__ . "/../servicos/app/versao.php";
        //include "app/servicos/versao.php";
        break;

    case "sistema":
        // NOVA VERSAO - MOVER app/sistema para sistema/app
        include __DIR__ . "/../sistema/app/versao.php";
        //include "app/sistema/versao.php";        
        break;

    case "vendas":
        // NOVA VERSAO - MOVER app/vendas para vendas/app
        include __DIR__ . "/../vendas/app/versao.php";
        //include "app/vendas/versao.php";
        break;

    case "relatorios":
        // NOVA VERSAO - MOVER app/relatorios para relatorios/app
        include __DIR__ . "/../relatorios/app/versao.php";
        //include "app/relatorios/versao.php";
        break;

    case "crediario": // helio 28032023
        // NOVA VERSAO - MOVER app/crediario para crediario/app
        include __DIR__ . "/../crediario/app/versao.php";
        //include "app/crediario/versao.php";
        break;

    case "impostos":
        // NOVA VERSAO - MOVER app/fiscal para fiscal/app
        include __DIR__ . "/../impostos/app/versao.php";
        //include "app/fiscal/versao.php";        
        break;

    case "paginas": 
        include  __DIR__ . "/../paginas/app/versao.php";
        break;

    case "cadastros":
        include  __DIR__ . "/../cadastros/app/versao.php";
        break;

    case "notas":
        include  __DIR__ . "/../notas/app/versao.php";
        break;

    case "financeiro":
        include  __DIR__ . "/../financeiro/app/versao.php";
        break;

    case "admin":
        include  __DIR__ . "/../admin/app/versao.php";
        break;

    default:
        $jsonSaida = json_decode(
            json_encode(
                array(
                    "status" => 400,
                    "retorno" => "Aplicacao " . $aplicacao . " Invalida"
                )
            ),
            TRUE
        );
        break;
}


// Pega Saida



if ($log == "true") {
    echo json_encode(
        array(
            "api" => array(
                "tipo" => $metodo,
                // transforma um array , num texto formatado em json
                "URI" => $uri,
                "Uri_Parse" => $uri_parse,
                "aplicacao" => $aplicacao,
                "versao" => $versao,
                "funcao" => $funcao,
                "parametro" => $parametro,
                "jsonEntrada" => $jsonEntrada
                //,"jsonSaida" => $jsonSaida

            ),
            "return" => $jsonSaida
        )
    );
} else {
    if (isset($jsonSaida)) {


        if (isset($jsonSaida->status)) {
            //echo "\nstatus=".$jsonSaida->status."-"."\n";
            retornaheader($jsonSaida->status);
        }
        if (isset($jsonSaida["status"])) {
            //echo "\nstatus="."-".$jsonSaida["status"]."\n";
            retornaheader($jsonSaida["status"]);
        }
        echo json_encode($jsonSaida) . "\n";
    }
}
?>
