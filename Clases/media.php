<?php
  use \Firebase\JWT\JWT as jwt;
    class Media{
        public function AgregarMedia($request,$response)//para el post
        {
           
                    $color = $request->getParsedBody()['color'];
                    $marca = $request->getParsedBody()['marca'];
                    $precio = $request->getParsedBody()['precio'];
                    $talle = $request->getParsedBody()['talle'];
                
                    $objetoPDO = new PDO('mysql:host=localhost;dbname=merceriabd;charset=utf8', "root", "");
       
                        $consulta =$objetoPDO->prepare("INSERT INTO medias (color, marca, precio,talle) VALUES(:color, :marca, :precio, :talle)");
                    
                       
                        $consulta->bindValue(':color', $color, PDO::PARAM_STR);
                        $consulta->bindValue(':marca', $marca, PDO::PARAM_STR);
                        $consulta->bindValue(':precio', $precio, PDO::PARAM_STR);
                        $consulta->bindValue(':talle',$talle,PDO::PARAM_STR);

                        $consulta->execute(); 
                        $response->getBody()->write("Elemento agregado con exito");

                       // $response = $next($request, $response);
            
                return $response;

        }

        public function TraerTodasMedias($request,$response){
            $arrayDeMedias=array();
            $objetoPDO = new PDO('mysql:host=localhost;dbname=merceriabd;charset=utf8', "root", "");
            $sql = $objetoPDO->prepare('SELECT * FROM medias');
            $sql->execute();
            
            while($result = $sql->fetchObject())
            {
             /*PARA MOSTRAR PROLIJO  
             echo $result->color." ".$result->talle." ".$result->precio." ".$result->marca."<br>";
             */
                array_push($arrayDeMedias,$result);
            }
            $cant= count($arrayDeMedias);
           /* for($i=0;$i<$cant;$i++){
             $response->getBody()->write(  $arrayDeMedias[$i]->color." ".$arrayDeMedias[$i]->marca ." ".$arrayDeMedias[$i]->talle." ".$arrayDeMedias[$i]->precio."<br>");
            }*/
            $nuevoResponse=$response->withJson($arrayDeMedias,200);
            return $nuevoResponse;
           
        }


        public function EliminarMedia($request,$response){
                $token = $request->getParsedBody()['token'];
                $id = $request->getParsedBody()['id'];
             //   if(Media::EsPropietario($token)){

                $objetoPDO2 = new PDO('mysql:host=localhost;dbname=merceriabd;charset=utf8', "root", "");
                    //Veo si existe el cd en la BD, para en un futuro pedir confirmacion antes de eliminar.
                $sql =$objetoPDO2->prepare("SELECT * FROM medias WHERE id = :id");
                $sql->bindValue(':id', $id);
                $sql->execute();
              
                $result = $sql->rowCount();

               
               if($result==1){//suponiendo que no TIENEN QUE EXIsTIR DOS CDS IGUALES
                   
                    //¿Como saber el ID del CD resultado de la consulta?
                    $resultado=$sql->fetch();
                    $id= $resultado[0];
                   // $pathFoto=$resultado[4];
                   
                   
                    $sql =$objetoPDO2->prepare("DELETE FROM medias WHERE id = :id");
                    $sql->bindValue(':id', $id);
                    $sql->execute();
                    

                   // return   $response->withJson("Media eliminado");
                 return  $response->getBody()->write("Elemento eliminado con exito");
                } else
                {
                    
                    return $response->getBody()->write("Elemento inexistente");
                }
        //    }else{

                $response->getBody()->write("Error.");
               // return $response->withJson("Error");
                //$response->withJson("Solo propietario");
          //  }
            return $response;

        }

        public static function EsPropietario($token){
          //desencriptar token, verificar el perfil, retronar true o false
          if(empty($token) || $token === "")
          {
              echo "el token esta vacio";
          }
          try
          {
              $jwtDecode = JWT::decode($token,'miClave',array('HS256'));

              if($jwtDecode->perfil=="propietario"){
                    return true;
              }else{
                  return false;
              }
           }
           catch(Exception $e){

              return false;
           }
         

        }

        public static function EsEncargado($token){
            if(empty($token) || $token === "")
            {
                echo "el token esta vacio";
            }
            try
            {
                $jwtDecode = JWT::decode($token,'miClave',array('HS256'));
  
                if($jwtDecode->perfil=="encargado" || $jwtDecode->perfil=="Encargado"){
                      return true;
                }else{
                    return false;
                }
             }
             catch(Exception $e){
  
                return false;
             }
        }
        public static function ModificarMedia($request,$response){
            $token = $request->getParsedBody()['token'];
            $id = $request->getParsedBody()['id'];
            $color=$request->getParsedBody()['color'];
            $talle= $request->getParsedBody()['talle'];
            $marca= $request->getParsedBody()['marca'];
            $precio= $request->getParsedBody()['precio'];
         //   if(Media::EsPropietario($token) || Media::EsEncargado($token)){
                $objetoPDO2 = new PDO('mysql:host=localhost;dbname=merceriabd;charset=utf8', "root", "");
                    //Veo si existe el cd en la BD, para en un futuro pedir confirmacion antes de eliminar.
                $sql =$objetoPDO2->prepare("SELECT * FROM medias WHERE id = :id");
                $sql->bindValue(':id', $id);
                $sql->execute();
              
                $result = $sql->rowCount();

               
               if($result==1){//suponiendo que no TIENEN QUE EXIsTIR DOS CDS IGUALES
                   
                    //¿Como saber el ID del CD resultado de la consulta?
                    $resultado=$sql->fetch();
                    $id= $resultado[0];
                   // $pathFoto=$resultado[4];
                   
// UPDATE `medias` SET `id`=[value-1],`color`=[value-2],`talle`=[value-3],`marca`=[value-4],`precio`=[value-5] WHERE 1
                    $sql =$objetoPDO2->prepare("UPDATE medias SET color=:color,talle=:talle, marca=:marca , precio=:precio WHERE id = :id");
                    $sql->bindValue(':color',$color);
                    $sql->bindValue(':talle',$talle);
                    $sql->bindValue(':marca',$marca);
                    $sql->bindValue(':precio',$precio);
                    $sql->bindValue(':id', $id);
                    $sql->execute();

                   // return   $response->withJson("Media eliminado");
                 return  $response->getBody()->write("Elemento modificado con exito");
                } else
                {
                    
                    $response->getBody()->write("Elemento inexistente");
                }
           // }else{

                $response->getBody()->write("Error.");
             
             
                // return $response->withJson("Error");
                //$response->withJson("Solo propietario");
            //}
            return $response;
            }
        }


    
    
?>