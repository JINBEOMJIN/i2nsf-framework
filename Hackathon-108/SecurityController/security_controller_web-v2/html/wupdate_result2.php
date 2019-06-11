<?php
function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ){
                $key = 'Policy_web';
            }
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild($key, htmlspecialchars($value));
        }
    }
}

$servername="localhost";
$username="root";
$password="secu";
$conn=mysql_connect(localhost, $username, $password) or die ("Error connecting to mysql server: ".mysql_error());
            
$dbname = 'I2NSF';
mysql_select_db($dbname, $conn) or die ("Error selecting specified database on mysql server: ".mysql_error());

$Rule_id = $_POST['Rule_id'];
$Rule_name = $_POST['Rule_name'];
$Position = $_POST['Position'];
$Web = $_POST['Website'];
$Start_time = $_POST['Starting_Time'];
$End_time = $_POST['Ending_Time'];
$Action = $_POST['Action'];

$updatesql="UPDATE Policy_web SET Rule_name='$Rule_name', Position='$Position', Web='$Web', Start_time='$Start_time', End_time='$End_time', Action='$Action' WHERE Rule_id='$Rule_id';";
mysql_query($updatesql) or die("Query to update record in Policy_Web failed with this error: ".mysql_error()); 


$result = mysql_query("SELECT * FROM Policy_web WHERE Rule_id=$Rule_id");

$rows = array();
#while($r = mysql_fetch_assoc($result)){

#$rows = $r;
#}
$Rule_id = mysql_fetch_row($result)[0];

$container = array();
$ruleData = array();
$ruleData["rule-id"] = $Rule_id;
$ruleData["rule-name"] = $Rule_name;
$ruleData["rule-case"] = "web";
$timeinfo = array("start-time" => $Start_time, "end-time" => $End_time);
$eventData = array();
$eventData["time-information"] = [$timeinfo];
$ruleData["event"] = [$eventData];
$condData = [array("source" => $Position, "destination" => $Web)];
$ruleData["condition"] = $condData;
$actData = [array("action-name" => $Action)];
$ruleData["action"] = $actData;
$groupData["i2nsf:rule"] = array();
$groupData["i2nsf:rule"] = $ruleData;
file_put_contents("test.json", json_encode($groupData, JSON_PRETTY_PRINT));

$CLIENT_CERT = "/works/jetconf/data/example-client.pem";

$POST_DATA='{ "jetconf:input": {"name": "Edit 1", "options": "config"} }';
$URL = "https://127.0.0.1:8443/restconf/operations;jetconf:conf-start";
$log = shell_exec("curl --ipv4 --http2 -k --cert-type PEM -E $CLIENT_CERT -X POST -d $POST_DATA $URL");

$POST_DATA = "@test.json";
$URL = "https://localhost:8443/restconf/data/i2nsf:Policy";
$log = shell_exec("curl --ipv4 --http2 -k --cert-type PEM -E $CLIENT_CERT -X POST -d $POST_DATA $URL");

// Creating an XML File //

$data_str = file_get_contents('test.json');
 
$xml_data = new SimpleXMLElement('<?xml version="1.0"?><I2NSF></I2NSF>');
array_to_xml(json_decode($data_str, true), $xml_data);
$result = $xml_data -> asXML();
 
header('Content-Type: text/xml; charset=UTF-8');
print_r($result);

//$url="http://192.168.115.130:8000/restconf/config/sc/nsf/firewall/policy/testPolicy/".$jsond;
//header('Location:'.$url);

//echo $jsond;

mysql_close($conn);

//$host = "127.0.0.1";
//$TCP_PORT = 6000;
//$output= "web,update,".$result;
//$socket = socket_create(AF_INET, SOCK_STREAM,0);
//socket_connect ($socket , $host,$TCP_PORT );
//socket_write($socket, $output, strlen ($output));
//socket_close($socket);
header( "refresh:2;url=select_page.php" );
//echo $Rule_id;
//echo $Rule_name;
//echo $Position;
//echo $Web;
//echo $Start_time;
//echo $End_time;
//echo $Action;

?>
