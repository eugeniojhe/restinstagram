<?php 
	global $routes;
	$routes = array(); 
	$routes['/users/login'] = '/users/login'; 
	$routes['/users/new'] = '/users/new'; 
	$routes['/users/feed'] = '/users/feed';
	$routes['/users/{id}'] = '/users/view/:id'; 
	$routes['/users/{id}/photos'] = '/users/photos/:id'; 
	$routes['/users/{id}/follow'] = '/users/follow/:id'; 

	$routes['/photos/random/'] = "/photos/random"; 
	$routes['/photos/{id}'] = '/photos/view/:id'; 
	$routes['/photos/{id}/comment'] = '/photos/comment/:id'; 
	$routes['/photos/{id}/like'] = "/photos/like/:id"; 
	$routes['/comments/{id}'] =  "/photos/delete_comment/:id"; 

