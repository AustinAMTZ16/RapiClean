<?php
include_once 'app/models/LoginModel.php';

class LoginController {
    private $model;

    public function __construct() {
        $this->model = new LoginModel();
    }

    public function iniciarSesion($data) {
        try {
            $usuario = $this->model->iniciarSesion($data);
            return $usuario;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }   

    public function registrarUsuario($data) {
        try {
            $usuario = $this->model->registrarUsuario($data);
            return $usuario;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function registrarCliente($data) {
        try {
            $cliente = $this->model->registrarCliente($data);
            return $cliente;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function actualizarUsuario($data) {
        try {
            $usuario = $this->model->actualizarUsuario($data);
            return $usuario;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }   

    public function actualizarCliente($data) {
        try {
            $cliente = $this->model->actualizarCliente($data);
            return $cliente;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function eliminarUsuario($data) {
        try {
            $usuario = $this->model->eliminarUsuario($data);
            return $usuario;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }   

    public function eliminarCliente($data) {
        try {
            $cliente = $this->model->eliminarCliente($data);
            return $cliente;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function obtenerMenu($data) {
        try {
            $menu = $this->model->obtenerMenu($data);
            return $menu;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function obtenerListaUsuarios() {
        try {
            $usuarios = $this->model->obtenerListaUsuarios();
            return $usuarios;   
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function obtenerListaClientes() {
        try {
            $clientes = $this->model->obtenerListaClientes();
            return $clientes;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }   
}
