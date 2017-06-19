<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App;

function getDB()
{
    $dbhost = "localhost";
    $dbuser = "";
    $dbpass = "";
    $dbname = "";
 
    $mysql_conn_string = "mysql:host=$dbhost;dbname=$dbname";
    $dbConnection = new PDO($mysql_conn_string, $dbuser, $dbpass); 
    $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbConnection;
}


$app->get('/AllGov', function($request,$response){
    $conn = getDB();
    $stmt = $conn->prepare("SELECT * FROM governorate"); 
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $i=0;
    $data=[];
    foreach($stmt->fetchAll() as $key=>$value) { 
   $data[$i++] = $value;
    }
$response->getBody()->write(json_encode($data));
});

///////////////
 $app->get('/Insert/{govern}/{city}', function($request,$response,$args){
    $govern = $args['govern'];
    $city = $args['city'];
    $conn = getDB();
     $STH = $conn->prepare("select GovernID from governorate WHERE GovernName='$govern'");
     $STH->execute();
     $result=$STH->fetch();
     $id=$result["GovernID"];
     $sql = "INSERT INTO city (GovernID, CityName) VALUES ('$id', '$city')";
     $conn->exec($sql);
     $response->getBody()->write("Inserted");	
});

//////////////////////
$app->get('/Join', function($request,$response){
$conn = getDB();
    $stmt = $conn->prepare("SELECT city.CityID,city.CityName, governorate.GovernName,governorate.GovernID FROM city JOIN governorate ON city.	GovernID=governorate.GovernID"); 
    $stmt->execute();
    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    $i=0;
    $data=[];
    foreach($stmt->fetchAll() as $key=>$value) { 
   $data[$i++] = $value;
    }
$response->getBody()->write(json_encode($data));
});

/////////////////
$app->get('/', function() use($app) {
    echo "Welcome to Slim based API";
});

////////////////////
    $app->get('/todos', function ($request, $response, $args) {
          $db = getDB();
         $sth = $db->prepare("SELECT * FROM films");
        $sth->execute();
        $todos = $sth->fetchAll();
        return $response->withJson($todos);
    });

$app->run();


// php -S localhost:9090


?>