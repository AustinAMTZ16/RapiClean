<?php
include_once 'app/models/VentaModel.php';

class VentaController {
    private $model;

    public function __construct() {
        $this->model = new VentaModel();
    }

    public function registrarVenta($usuarioID, $clienteID, $metodoPago, $servicios, $codigoDescuento = null, $comentarios = null){
        return $this->model->registrarVenta($usuarioID, $clienteID, $metodoPago, $servicios, $codigoDescuento, $comentarios);
    }
    public function listarVentas($ventaID = null, $clienteID = null){
        return $this->model->listarVentas($ventaID, $clienteID);
    }   
    public function actualizarEstadoVenta($data){
        return $this->model->actualizarEstadoVenta($data);
    }   
    public function actualizarEstadoSemaforo($data){
        return $this->model->actualizarEstadoSemaforo($data);
    }   
    public function listarSemaforoServicios($data){
        return $this->model->listarSemaforoServicios($data);
    }
}
?>