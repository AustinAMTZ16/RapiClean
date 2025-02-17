<?php
include_once 'app/models/ServiciosInventarioModel.php';

class ServiciosInventarioController {
    private $model;

    public function __construct() {
        $this->model = new ServiciosInventarioModel();
    }
    // Obtener productos asociados a un servicio
    public function productosAsociados($data) {
        return $this->model->productosAsociados($data);
    }   
    // Lista de Servicios
    public function listaServicios() {
        return $this->model->listaServicios();
    }
    // Lista de Inventario
    public function listaInventario() { 
        return $this->model->listaInventario();
    }
    // Asociar productos a un servicio
    public function asociarProductos($data) {
        return $this->model->asociarProductos($data);
    }  
    // Crear Servicio
    public function crearServicio($data) {
        return $this->model->crearServicio($data);
    }
    // Crear Producto
    public function crearProducto($data) {
        return $this->model->crearProducto($data);
    }
    // Actualizar Servicio
    public function actualizarServicio($data) {
        return $this->model->actualizarServicio($data);
    }
    // Actualizar Producto
    public function actualizarProducto($data) {
        return $this->model->actualizarProducto($data);
    }
    // Eliminar Servicio
    public function eliminarServicio($data) {
        return $this->model->eliminarServicio($data);
    }
    // Eliminar Producto
    public function eliminarProducto($data) {
        return $this->model->eliminarProducto($data);
    }
}   