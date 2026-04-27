<?php
require 'config.php';
session_start();

$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/film-rating-app', '', $request);
$path = parse_url($request, PHP_URL_PATH);

switch (true) {
    case ($path === '/' || $path === '/home' || $path === '/home.php'):
        require 'views/home.php';
        break;

    case (preg_match('/^\/page\/([0-9]+)$/', $path, $matches)):
        $_GET['page'] = $matches[1];
        require 'views/home.php';
        break;
		
	case (preg_match('/^\/film\/(\d+)$/', $path, $matches) ? true : false):
		$film_id = $matches[1];
		require 'views/film.php';
		break;

    case (preg_match('/^\/admin_panel$/', $path) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin'):
        require 'views/admin_panel.php';
        break;
		
	case (preg_match('/^\/register(\/(success|error\/[a-z_]+))?$/', $path, $matches) ? true : false):
        $registerStatus = isset($matches[2]) ? $matches[2] : null;
        require 'views/register.php';
        break;
		
	case preg_match('#^/user/([^/]+)$#', $path, $matches) === 1:
		$_GET['username'] = $matches[1];
		require 'views/user_panel.php';
		break;

		
    case ($path === '/login'):
        require 'views/login.php';
        break;

    case ($path === '/register'):
        require 'views/register.php';
        break;

    case ($path === '/logout'):
        session_destroy();
        header("Location: /film-rating-app/");
        exit;

    case ($path === '/login_action'):
        require 'actions/login_action.php';
        break;

    case ($path === '/register_action'):
        require 'actions/register_action.php';
        break;

    case ($path === '/get_reviews'):
        require 'actions/get_reviews.php';
        break;

    case ($path === '/delete_review'):
        require 'actions/delete_review.php';
        break;

    case ($path === '/add_film_action'):
        require 'actions/add_film_action.php';
        break;
		
	case ($path === '/add_review'):
        require 'actions/add_review.php';
        break;

    case ($path === '/delete_film_action'):
        require 'actions/delete_film_action.php';
        break;

    case ($path === '/edit_film_action'):
        require 'actions/edit_film_action.php';
        break;

    case ($path === '/delete_user_action'):
        require 'actions/delete_user_action.php';
        break;

    default:
        http_response_code(404);
        echo "Strona nie znaleziona!";
        break;
}
