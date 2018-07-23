<?php
 use \Firebase\JWT\JWT as jwt; 
require_once "./Clases/media.php";
    class MW{

        public function VerificarSeteados($request,$response,$netx){
           
            if($request->isPost()){
                //devuelve TRUE si var existe y tiene un valor distinto de NULL, FALSE de lo contrario.
                if(isset($request->getParsedBody()['correo']) && isset($request->getParsedBody()['clave'])){ 
                    //$response= MW::VerificarCamposVacios($request,$response,$netx);
                    $response= $netx($request,$response);      
                }
                else
                { 
                    //return $response->withJson(array(["error"=>"valores no seteados"]));
                    return $response->withJson("Error valores no seteados",409);
                }

                return $response;

            }else if($request->isGet()){
                $token = $request->getHeader('token');
                if(isset($token[0])){
                    $response= $netx($request,$response);
                    
                }
                else
                {
                   
                    return $response->withJson("Error valores no seteados",409);
                }
                    return $response;
            }
        }

        public static function VerificarCamposVacios($request,$response,$netx){
            
            if($request->isPost()){
                $correo = $request->getParsedBody()['correo'];
                $clave = $request->getParsedBody()['clave'];

                if(empty($correo) || empty($clave) ){ 

                return $response->withJson("valores vacios",409);
                
                }
                else
                {
                    
                //  $response=  MW::VerificarBD($request,$response,$netx);
                    $response= $netx($request,$response);
                
                }
                    return $response;
            }else if($request->isGet()){
                    $token = $request->getHeader('token');
                    if(empty($token[0])){

                        return $response->withJson("valores vacios",409);
                    }
                    else
                    {
                        $response= $netx($request,$response);
                    }
                        return $response;
            }
            
        }


        public function VerificarBD($request,$response,$netx){

            $correo = $request->getParsedBody()['correo'];
            $clave = $request->getParsedBody()['clave'];

            $objetoPDO = new PDO('mysql:host=localhost;dbname=merceriabd;charset=utf8', "root", "");
                  
                    $sql =$objetoPDO->prepare("SELECT * FROM usuarios WHERE correo = :correo AND clave = :clave");
                    $sql->bindValue(':correo', $correo);
                    $sql->bindValue(':clave', $clave);
                    $sql->execute();
                
                    $result = $sql->rowCount();
                

                if($result==1){

                    $response = $netx($request, $response);
                    return $response;

                }else
                {
                  //  return $response->withJson(array(["error"=>"ERROR no se encuentra en la BD"]));
                  
                  return $response->withJson("No se encuentra en la BD",409);
                 
                }
        }


        public function VerificarToken($request,$response,$netx){
            $token=null;

            if($request->isGet()){
                $token= $request->getHeader('token'); //$token[0]
            }else{
                $token= $request->getParsedBody()['token'];
            }
            
            if(empty($token)|| $token=== "")
            {
                echo "el token esta vacio";
            }
            try
            {
                $jwtDecode = JWT::decode($token,'miClave',array('HS256'));  
              
                $response = $netx($request, $response);
                return $response;
             }
             catch(Exception $e){

              
                return $response->withJson("token invalido",409);
             }

           
            
        }

            public static function VerificarPropietario($request,$response,$next){
                $token=null;

                if($request->isGet()){
                    $token= $request->getHeader('token');
                }else{
                    $token= $request->getParsedBody()['token'];
                }

                    if(empty($token) || $token === "")
                    {
                        echo "el token esta vacio";
                    }
                    try
                    {
                        $jwtDecode = JWT::decode($token,'miClave',array('HS256'));
          
                        if($jwtDecode->perfil=="propietario"){
                    
                          //  $response = $next($request, $response);
                          $response->withJson("Ok");
                          $response = $next($request, $response);
                          return $response;
                        
                             
                        }else{
                           
                            return $response->withJson("Error",409);
                        }
                     }
                     catch(Exception $e){
                     
                        return $response->withJson("Error",409);
                     }
            }


            public function VerificarEncargado($request,$response,$next){
                $token=null;

                if($request->isGet()){
                    $token= $request->getHeader('token');
                }else{
                    $token= $request->getParsedBody()['token'];
                }

                    if(empty($token) || $token == "")
                    {
                        echo "el token esta vacio";
                    }
                    try
                    {
                        $jwtDecode = JWT::decode($token,'miClave',array('HS256'));
          
                        if($jwtDecode->perfil=="encargado"){

                            $response->withJson("Ok");
                            $response = $next($request, $response);
                            return $response;                       
            
                        }else{
                           
                            return $response->withJson("Error",409);
                        
                        }
                     }
                     catch(Exception $e){
                        
                        return $response->withJson("Error",409);
                        
                     }
            }

            

            public function VerificarPerfiles($request,$response,$next){
                $token= $request->getParsedBody()['token'];
                $encargado=Media::EsEncargado($token);
                $propietario= Media::EsPropietario($token);
              //  echo "<br>".$encargado."<br>";
          
                if ($encargado|| $propietario){
                 //  echo "okkkk";
                    $response= $next($request,$response);
                    return $response;
                }else{
                    return $response->withJson("Error de permisos",409);
                }
            }

            }
 
?>