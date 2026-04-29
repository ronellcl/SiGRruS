<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Usuario;

class UsuariosController extends Controller {
    
    public function perfil($id) {
        Auth::requireRole(['Superusuario', 'Responsable de Grupo', 'Asistente de Grupo']);
        
        $userModel = new Usuario();
        $dirigente = $userModel->findById($id);
        
        if (!$dirigente) {
            $this->redirect('/grupo/dirigentes');
        }
        
        $historial = $userModel->getRoleHistory($id);
        $certificados = $userModel->getCertificados($id);

        $this->view('usuarios/perfil', [
            'title' => 'Historial de Usuario - ' . $dirigente['nombre'],
            'dirigente' => $dirigente,
            'historial' => $historial,
            'certificados' => $certificados,
            'user' => Auth::user()
        ]);
    }
}
