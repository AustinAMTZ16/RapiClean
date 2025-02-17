<?php
include_once 'app/models/RecompensaDescuentosModel.php';

class RecompensaRecompensaDescuentosController {
    private $model;

    public function __construct() {
        $this->model = new RecompensaDescuentosModel();
    }
    public function crearDescuento($data) {
        return $this->model->crearDescuento($data);
    }
    public function crearRecompensa($data) {
        return $this->model->crearRecompensa($data);
    }   
    public function listarDescuentos() {
        return $this->model->listarDescuentos();
    }   
    public function listarRecompensas() {
        return $this->model->listarRecompensas();
    }   
    public function actualizarDescuento($data) {
        return $this->model->actualizarDescuento($data);
    }   
    public function actualizarRecompensa($data) {
        return $this->model->actualizarRecompensa($data);
    }   
    public function eliminarDescuento($data) {
        return $this->model->eliminarDescuento($data);
    }   
    public function eliminarRecompensa($data) {
        return $this->model->eliminarRecompensa($data);
    }   
    public function crearCanjeRecompensa($data) {
        return $this->model->crearCanjeRecompensa($data);
    }   
    public function listarCanjesRecompensas() {
        return $this->model->listarCanjesRecompensas();
    }   
    public function actualizarCanjeRecompensa($data) {
        return $this->model->actualizarCanjeRecompensa($data);
    }
    public function eliminarCanjeRecompensa($data) {
        return $this->model->eliminarCanjeRecompensa($data);
    }
}   