<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\XmlController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../App/XmlController.php';

$app = AppFactory::create();

$app->post('/okb-report', function (Request $request, Response $response) {
    $params = (array)$request->getParsedBody();
    $userId = $params['person_id'];
    if($userId){
        try {
            $db = new DB();
            $userData = $db->getUserData($userId);
            $db = null;
            $response->getBody()->write(json_encode($userData));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } catch(PDOException $e) {
            $errors = array(
                "message" => $e->getMessage(),
            );
    
            $response->getBody()->write(json_encode($errors));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
        }
    } else {
        $response->getBody()->write(json_encode(array("message" => "user is not exsist. ID: ". $userId)));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(500);
    }
       
});

$app->get('/user-data', function (Request $request, Response $response) {
    try {
        $xmlString = file_get_contents(__DIR__ . '/../storage/files/file.xml');
        $xml = new XmlController();
        //$userData = $xml->getDataFromXml(__DIR__ . '/../storage/files/translate.json');
        $userData = $xml->parseXml($xmlString);
        $response->getBody()->write(json_encode($userData));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);

    } catch(Exception $e) {
        $errors = array(
            "message" => $e->getMessage(),
        );

        $response->getBody()->write(json_encode($errors));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
    

});

$app->run();