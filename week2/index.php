<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt18_week2', 'ddwt18','ddwt18');

/* Get number of series and number of users and display */
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');

/* Render Navigation */
$navigation_template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT18/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT18/week2/overview/'
    ),
    3 => Array(
        'name' => 'Add',
        'url' => '/DDWT18/week2/add/'
    ),

    4 => Array(
        'name' => 'My Account',
        'url' => '/DDWT18/week2/myaccount/'
    ),
    5 => Array(
        'name' => 'Register',
        'url' => '/DDWT18/week2/register/'
    ),
    6 => Array(
        'name' => 'Login',
        'url' => '/DDWT18/week2/login/'
    ),
    7 => Array(
        'name' => 'Logout',
        'url' => '/DDWT18/week2/logout/'
    ));

/* Landing page */
if (new_route('/DDWT18/week2/', 'get')) {
    /* Get error msg */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Home' => na('/DDWT18/week2/', True)
    ]);
    $navigation = get_navigation($navigation_template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT18/week2/overview/', 'get')) {
    /* Get error msg from remove POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview', True)
    ]);
    $navigation = get_navigation($navigation_template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table($db);

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT18/week2/serie/', 'get')) {
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Get error msg from edit POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Overview' => na('/DDWT18/week2/overview/', False),
        $serie_info['name'] => na('/DDWT18/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($navigation_template, 2);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];
    $added_by = get_names($db, $serie_info['user']);
    $display_buttons = check_allowance($serie_info['user']);

    /* Choose Template */
    include use_template('serie');

}

/* Add serie GET */
elseif (new_route('/DDWT18/week2/add/', 'get')) {

    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Get error msg from add POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Add Series' => na('/DDWT18/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_template, 3);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT18/week2/add/';

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT18/week2/add/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Add serie to database and get feedback */
    $feedback = add_serie($db, $_POST);

    /* Redirect to add serie GET route */
    redirect(sprintf('/DDWT18/week2/add/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT18/week2/edit/', 'get')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT18/week2/new/', True)
    ]);
    $navigation = get_navigation($navigation_template, NULL);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT18/week2/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT18/week2/edit/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Add serie to database and get feedback */
    $feedback = update_serie($db, $_POST);
    $serie_id = $_POST['serie_id'];

    /* Redirect to add serie GET route */
    redirect(sprintf('/DDWT18/week2/serie/?serie_id=%s&error_msg=%s',
        $serie_id , json_encode($feedback)));
}

/* Remove serie */
elseif (new_route('/DDWT18/week2/remove/', 'post')) {
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
    $feedback = remove_serie($db, $serie_id);

    /* Redirect to add serie GET route */
    redirect(sprintf('/DDWT18/week2/overview/?error_msg=%s'
        , json_encode($feedback)));

}

/* My account page */
elseif (new_route('/DDWT18/week2/myaccount/', 'get')){
    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT18/week2/login/');
    }

    /* Get error msg from registration POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'My Account' => na('/DDWT18/week2/myaccount', True)
    ]);
    $navigation = get_navigation($navigation_template, 4);

    /* Page content */
    $page_subtitle = 'Overview';
    $page_content = 'Overview of your account';
    $user =  get_names($db, $_SESSION['user_id']);

    /* Choose Template */
    include use_template('account');
}

/* Registration form GET*/
elseif (new_route('/DDWT18/week2/register/', 'get')){
    /* Check if logged in */
    if ( check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Register' => na('/DDWT18/week2/register', True)
    ]);
    $navigation = get_navigation($navigation_template, 5);

    /* Page content */
    $page_subtitle = 'Register a new account';

    /* Choose Template */
    include use_template('register');
}

/* Registration form POST*/
elseif (new_route('/DDWT18/week2/register/', 'post')){
    /* Register user */
    $feedback = register_user($db, $_POST);

    /* Redirect to my account page */
    redirect(sprintf('/DDWT18/week2/register/?error_msg=%s', json_encode($feedback)));

}

/* Login GET*/
elseif (new_route('/DDWT18/week2/login/', 'get')){
    /* Check if logged in */
    if (check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Login';
    $breadcrumbs = get_breadcrumbs([
        'DDWT18' => na('/DDWT18/', False),
        'Week 2' => na('/DDWT18/week2/', False),
        'Login' => na('/DDWT18/week2/login', True)
    ]);
    $navigation = get_navigation($navigation_template, 6);

    /* Page content */
    $page_subtitle = 'Use your username and password to login';

    /* Choose Template */
    include use_template('login');
}

/* Login POST*/
elseif (new_route('/DDWT18/week2/login/', 'post')){
    /* Register user */
    $feedback = login_user($db, $_POST);

    /* Redirect to my account page */
    redirect(sprintf('/DDWT18/week2/login/?error_msg=%s', json_encode($feedback)));
}

/* Logout GET*/
elseif (new_route('/DDWT18/week2/logout/', 'get')){
    if ( !check_login() ) {
        redirect('/DDWT18/week2/myaccount/');
    }

    /* Logout user */
    $feedback = logout_user();

    /* Redirect to my account page */
    redirect(sprintf('/DDWT18/week2/?error_msg=%s',
        json_encode($feedback)));
}

else {
    http_response_code(404);
}