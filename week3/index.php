<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

/* Require composer autoloader */
require __DIR__ . '/vendor/autoload.php';

/* Include model.php */
include 'model.php';

/* Set credentials */
$cred = set_cred('ddwt18', 'ddwt18');

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week3', 'ddwt18', 'ddwt18');

/* Create Router instance */
$router = new \Bramus\Router\Router();

// Custom 404 Handler
$router->set404(function () {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo '404, page not found!';
});

// Add routes here
$router->mount('/api', function() use($router, $db, $cred){
    http_content_type('application/json');
    $router->before('GET|POST|PUT|DELETE', '/api/.*', function() use($cred){
        if (!check_cred($cred)){
            echo json_encode([
                'type' => 'warning',
                'message' => 'An error occurred. Authentication failed.'
            ]);
            http_response_code(401);
            exit();
        }
        echo json_encode([
            'type' => 'succes',
            'message' => 'Succesfully authenticated'
        ]);

    });

    /* GET for reading all series */
    $router->get('/series', function() use($db) {
        // Retrieve and output information
        $series_info = get_series($db);
        echo json_encode($series_info);
    });

    /* GET for reading individual series */
    $router->get('/series/(\d+)', function($id) use($db) {
        // Retrieve and output information
        $serie_info = get_serieinfo($db, $id);
        echo json_encode($serie_info);
    });

    /* DELETE for removing individual serie */
    $router->post('/series/(\d+)', function($id) use($db) {
        // Retrieve and output feedback
        $feedback = remove_serie($db, $id);
        echo json_encode($feedback);
    });

    /* POST for adding individual serie */
    $router->post('/series', function() use($db) {
        // Get input form $_POST, process, output feedback
        $feedback = add_serie($db, $_POST);
        echo json_encode($feedback);
    });

    /* PUT for updating an individual serie */
    $router->put('/series/(\d+)', function($id) use($db) {
        $_PUT = array();
        parse_str(file_get_contents('php://input'), $_PUT);

        // Get input form $_PUT, process, output feedback
        $serie_info = $_PUT + ["serie_id" => $id];
        $feedback = update_serie($db, $serie_info);
        echo json_encode($feedback);
    });

});

/* Run the router */
$router->run();
