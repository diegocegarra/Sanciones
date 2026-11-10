<?php

require_once(__DIR__ . '/../../config.php');
require_once(FUNCIONES_FACTURACION);
// require_once (EVALUAR_TRABAJADORES);


class SancionesCalcular {
    public $ut = 43;

    public function calcularUTIngresos($trabajadoresExtemporaneos) {
        // Inicializamos contadores SIEMPRE al principio
        $ingresoSancion = 0;
        $egresoSancion = 0;

        // Validamos que el array no venga vacío
        if (!empty($trabajadoresExtemporaneos) && is_array($trabajadoresExtemporaneos)) {
            
            foreach ($trabajadoresExtemporaneos as $fila) {
                // Usamos IF separados para que si tiene ambas sanciones, cuente ambas
                if ($fila['sancion_ingreso'] == '3 días Ingreso') {
                    $ingresoSancion++;
                }
                
                if ($fila['sancion_egreso'] == '3 días Egreso') {
                    $egresoSancion++;
                }
            }
        }
        // echo $ingresoSancion."<br>";
        // echo $egresoSancion;  
        
        // Cálculos
        $montoIngresosUT = $ingresoSancion * 50;
        $montobs = $montoIngresosUT * $this->ut; // Usamos la propiedad de la clase

        $montoEgresosUT = $egresoSancion * 25;
        $montobsEgresos = $montoEgresosUT * $this->ut;

        // Retornamos el array para la desestructuración
        return [$montoIngresosUT, $montobs, $montoEgresosUT, $montobsEgresos];
    }

    public function CalcularSancionesExtraBs($tachaduras, $obstaculizacion, $inscripcion, $novedades, $interesesNoEnterados){


        $bs_tachaduras= $tachaduras * $this->ut;
        $bs_obstaculizacion = $obstaculizacion * $this->ut;
        $bs_inscripcion = $inscripcion * $this->ut;
        $bs_novedades = $novedades * $this->ut;
        $bs_noEnterados = $interesesNoEnterados * $this->ut;


        return[$bs_tachaduras, $bs_obstaculizacion, $bs_inscripcion, $bs_novedades, $bs_noEnterados];
    }

    public function concurrirSancion(array $montos) {
        // 1. Ordenamos de mayor a menor
        arsort($montos);

        // 2. Extraemos el concepto mayor
        $nombreMayor = array_key_first($montos);
        $valorMayor  = current($montos);

        // 3. Obtenemos el resto (excluyendo el primero)
        $elResto = array_slice($montos, 1, null, true);

        // 4. Sumamos el resto y aplicamos tu lógica de división
        $sancionMontoResto = array_sum($elResto);

        // esto lo divide el total entre 2 a la mitad la sancion que es menor 
        $sancionDesglosada = $sancionMontoResto / 2;


        // $this->calcularFinalDesglosado($valorMayor, $sancionDesglosada);

        // 5. Retornamos todo organizado
        return [
            'mayor' => [
                'nombre' => $nombreMayor,
                'valor'  => $valorMayor
            ],
            'resto_detallado' => $elResto,
            'total_resto'     => $sancionMontoResto,
            'desglose_final'  => $sancionDesglosada,
            'gran_total'      => $valorMayor + $sancionDesglosada
        ];
    }

    public function calcularFinalDesglosado($valorMayor, $sancionDesglosada){
        $total_ut = $valorMayor +$sancionDesglosada;
        $totalSancionFinal = $total_ut * $this->ut;

        return $totalSancionFinal;
    }

}

// $calculo = new SancionesCalcular();
// return $resultadoSancion = $calculo->calcularUTIngresos($evaluar_trabajadores_concurrir);

?>
