<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker\Generator as Faker;

class IndexController extends Controller
{
    public function index(Request $request,Faker $faker)
    {
        $params = $request->query();
        $filter=false;
        //select demanda para mes 1, año 1 de la BD
        //Iniciar asignación de tareas
        $mes = 1;
        while ($mes<=12 && array_key_exists("capacidad",$params)) 
        {
            $filter=true;
            $demanda = \App\DemandMonthly::where('id',$mes)->where('idAnio',1)->get();
            
            $demanda=$demanda->first()->demanda;
            
            //capacidad*8=capacidad_real
            $capacidadReal = $params['capacidad']*8;
            //capacidad_real/horas por tarea=no_tareas
            $cantidadTareas = $capacidadReal/$params['horas'];

            //-demanda/30=demanda diaria
            $dailyDemand = $demanda/30;

            //-demanda diaria/no_tareas=cantidad_operadores
            $cantidadOperadores = ceil($dailyDemand/$cantidadTareas);
            //-guardar operadores en la base
            
            if($cantidadOperadores>0)
            {
                $team = factory(\App\Personal::class,$cantidadOperadores)->create([
                    'idMes' => $mes,
                    'Salario_tarea'=>20
                    ]);
                  
                    //(numero de operadores con calificación 1)
                    $opc1=$cantidadTareas*0.02;
                    //(numero de operadores con calificacion 2 y 3)
                    $opc23=$cantidadTareas*0.08;
                    //(numero de operadores calificacion 4  y 5)
                    $op45=$cantidadTareas*0.9;
                    
                    for ($i=0; $i <$demanda ; $i++) 
                    { 
                        
                        $n=5; 
                        $j=1;
                        foreach ($team as $operador) 
                        {
                            
                            $calificacion=$faker->passthrough(mt_rand(1, 5)); 
                            
                            //Guardar tarea en BD
                            $task = new \App\Task();
                            $task->calificacion=$calificacion;
                            $task->idOperador = $operador->id;
                            $task->idMes=$mes;
                            $task->save();
                            
                            //j+1;
                            $j=$j+1;
                            
                            //If j=n then j=1;
                            if($j==$n)
                            $j=1;
                            
                            //Procedimiento de clasificación
                            if ($calificacion==5) 
                            $categoria="A";
                            if($calificacion>=4 && $calificacion<5) 
                            $categoria="B";
                            if($calificacion>=3 && $calificacion<4)
                            $categoria="C";
                            if ($calificacion>=2&&$calificacion<3)
                            $categoria="D";
                            if ($calificacion>=1 &&$calificacion<2)
                            $categoria="E";
                            $categoryResume = new \App\CategoryResume();
                            $categoryResume->idOperador=$operador->id;
                            $categoryResume->categoria=$categoria;
                            $categoryResume->save();
                        }
                        
                        $c5=\App\CategoryResume::where('Categoria','A')->get()->count();
                        $c4=\App\CategoryResume::where('Categoria','B')->get()->count();
                        $c3=\App\CategoryResume::where('Categoria','C')->get()->count();
                        $c2=\App\CategoryResume::where('Categoria','D')->get()->count();
                        $c1=\App\CategoryResume::where('Categoria','E')->get()->count();

                        if ($c4+$c5==$op45)
                         $setcalif45=false;
                        if ($c2+$c3==$opc23)
                            $setcalif23=false;
                        if ($c1==$opc1)
                            $setcalif1=false;
                        
                        if(\App\Task::where('idMes',$mes)->count() > \App\DemandMonthly::find($mes)->demanda)
                            break;
                    }
            }
            
            $mes=$mes+1;
        }




        /*

*Iniciar asignación de tareas
mes=1;
While mes<=12
For demanda
For each numero de operadores (asignar primeras tareas)
n=5; j=1;
calificacion=j; 
Guardar tarea en BD
j+1;
If j=n then j=1;

*Procedimiento de clasificación
If calificacion=5 then categoria=A;
If calificacion>=4y<5 then categoria=B;
If calificacion>=3y<4 then categoria=C;
If calificacion>=2y<3 then categoria=D;
If calificacion>=1 y<2 then categoria=E;
Guardar data en BD
End For

*Siguiente ronda de tareas
-Seleccionar de la BD el número de operadores por calificación
c5=numero operadores calificación 5
c4=numero operadores calificación 4
c3=numero operadores calificación 3
c2=numero operadores calificación 2
c1=numero operadores calificación 1

If c4+c5=op45 then setcalif45=false;
If c2+c3=op23 then setcalif23=false;
If c1=opc1 then setcalif1=false;

For each numero de operadores (clasificación A y B tomados de la BD)
n=5; j=1;

If j=1  && setcalif1=false then k=j+1;
calificacion=k;
j=k+1;
If j=n then j=1;
-Guardar tarea en la BD
-Seleccionar de la BD las calificaciones de las tareas anteriores del operador y calcular promedio
-Repetir procedimiento de clasificación utilizando la variable promedio y guardar la data en la BD

-Repetir procedimiento para las demas categorias
-La categoria E tiene una evaluacion adicional
If calificacion_tarea_anterior=1 && calificacion=1
Delete operador de la BD
End For
mes=mes+1;
End For
*/
    $demandMonthly= \App\DemandMonthly::get()->pluck('demanda','id')->flatten();
    
    return view('welcome')->with(['data_keys'=>$demandMonthly->keys(),"data"=>$demandMonthly->values(),"filter"=>$filter,'params'=>$params]);
    }
}
