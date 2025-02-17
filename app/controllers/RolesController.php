<?php
include_once 'app/models/RolesModel.php';

class RolesController {
    private $model;

    public function __construct() {
        $this->model = new RolesModel();
    }

    public function permisosAsignados($data) {
        return $this->model->permisosAsignados($data);
    }

    public function listaRoles() {
        return $this->model->listaRoles();
    }

    public function listaPermisos() {
        return $this->model->listaPermisos();
    }

    public function asignarPermisos($data) {
        return $this->model->asignarPermisos($data);
    }

    public function crearRol($data) {
        return $this->model->crearRol($data);
    }

    public function crearPermiso($data) {
        return $this->model->crearPermiso($data);
    }

    public function actualizarRol($data) {
        return $this->model->actualizarRol($data);
    }

    public function actualizarPermiso($data) {
        return $this->model->actualizarPermiso($data);
    }

    public function eliminarRol($data) {
        return $this->model->eliminarRol($data);
    }

    public function eliminarPermiso($data) {    
        return $this->model->eliminarPermiso($data);
    }   
}   