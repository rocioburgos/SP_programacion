<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use \Firebase\JWT\JWT as jwt; 
    require_once './vendor/autoload.php';
    require_once './Clases/media.php';
    require_once './Clases/usuario.php';
    require_once './Clases/mw.php';
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;

    $app = new \Slim\App(["settings" => $config]);

    //A nivel de aplicacion un post
    $app->post('[/]', function (Request $request, Response $response) {   
       Media::AgregarMedia($request,$response);   
        return $response;
    });//->add(\Media:: class ."::AgregarMedia");

    $app->get('/medias', function (Request $request, Response $response) {   
        
        $json= Media::TraerTodasMedias($request,$response);
        echo $json;
     
        return $response;

    });

    $app->post('/usuarios', function (Request $request, Response $response) {   
          
       $nuevoresponse=  Usuario::AgregarUsuario($request,$response);
        return $nuevoresponse;
    });//->add(\Usuario:: class ."::AgregarUsuario");
    
    $app->get('[/]', function (Request $request, Response $response) {   
        
       echo Usuario::TraerTodosUsuarios($request,$response);
        
        return $response;

    });


    //"::funcion"->cuando es de instancia
    //":funcion"->cuando es static
    $app->group('/login',function(){

        $this->post('[/]', function (Request $request, Response $response) {   
          
           echo Usuario::LoginUsuario($request,$response);
            return $response;
        })->add(\MW:: class ."::VerificarBD")->add(\MW:: class .":VerificarCamposVacios")->add(\MW:: class ."::VerificarSeteados");

        $this->get('[/]', function (Request $request, Response $response) {   
          
           $response=(Usuario::VerificarTokenUsuario($request,$response));
           
            return $response;
        })->add(\MW:: class .":VerificarCamposVacios")->add(\MW:: class ."::VerificarSeteados");

    });
    
    $app->delete("[/]",function(Request $request, Response $response){
        //Borrar medias por ID si es PROPIERARIO        
        $response= Media::EliminarMedia($request,$response);
        echo $response;
        return $response;
    })->add(\MW::class."::VerificarToken")->add(\MW::class .":VerificarPropietario");
//

    $app->put("[/]",function(Request $request, Response $response){
        
     /*   $esPropietario= json_decode(MW::VerificarPropietario($request,$response));
        var_dump(MW::VerificarEncargado($request,$response));
        $esEncargado;
        echo "encargado ".$esEncargado;
        if(MW::VerificarPropietario($request,$response)==true || MW::VerificarEncargado($request,$response)==true){
           
            return Media::ModificarMedia($request,$response);
        }else{
            return $response->withJson("Error de permisos.",409);
        }

        return $response;*/
        return Media::ModificarMedia($request,$response);
    })->add(MW::class."::VerificarPerfiles")->add(\MW::class."::VerificarToken");

   

$app->run();
?>