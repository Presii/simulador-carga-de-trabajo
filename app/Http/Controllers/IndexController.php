<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Faker\Generator as Faker;

class IndexController extends Controller
{
    public function index(Request $request,Faker $faker)
    {
        $params = $request->query();
        //select demanda para mes 1, año 1 de la BD
        //$demanda = \App\DemandMonthly::where('idMes',1)->where('idAnio',1)->get();
        //capacidad*8=capacidad_real
        $capacidadReal = $params['capacidad']*8;
        //capacidad_real/horas por tarea=no_tareas
        $cantidadTareas = $capacidadReal/$params['horas'];

        //-demanda/30=demanda diaria
        $dailyDemand = $cantidadTareas/30;

        //-demanda diaria/no_tareas=cantidad_operadores
        $cantidadOperadores = $dailyDemand/$cantidadTareas;
        //-guardar operadores en la base
        $team = factory(\App\Personal::class,$cantidadOperadores)->create([
            'idMes' => 1,
            'Salario_tarea'=>20
        ]);

        //(numero de operadores con calificación 1)
        $opc1=$cantidadTareas*0.02;
        //(numero de operadores con calificacion 2 y 3)
        $opc23=$cantidadTareas*0.08;
        //(numero de operadores calificacion 4  y 5)
        $op45=$cantidadTareas*0.9;

        //Iniciar asignación de tareas
        $mes = 1;

        while ($mes<=12) {
            $mes++;
            /*foreach ($team as $operador) 
            {
                $n=5; 
                $j=1;
                $calificacion=j; 
                $task = new \App\Task();
                $task->calificacion=$faker->optional()->passthrough(mt_rand(1, 5));
                $task->idOperador = $operador->id;
                $task->idMes=$mes;
                $task->save();
            }*/
        }

        foreach ($team as $operador) 
        {
            $n=5; 
            $j=1;
            $calificacion=j; 
            $task = new \App\Task();
            $task->calificacion=$faker->optional()->passthrough(mt_rand(1, 5));
            $task->idOperador = $operador->id;
            $task->idMes=$mes;
            $task->save();
        }





        /*

*Iniciar asignación de tareas
mes=1;
While mes<=12
For each demanda
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

        return view('welcome')->with($params);
    }
}
